<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Integrations\IntegrationFactory;
use App\Models\ApiKey;
use App\Models\Order;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessOrdersJob implements ShouldBeUnique, ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 4;

    public function backoff(): int|array
    {
        if ($this->apiKey->type === ApiKey::TYPE_AMAZON) {
            return [90, 120, 240];
        }
        return 30;
    }

    public int $timeout = 600;

    public function __construct(
        public ApiKey $apiKey,
        public ?\Carbon\Carbon $createdMin = null,
        public ?\Carbon\Carbon $createdMax = null,
        public ?array $options = [],
    ) {
        $this->createdMin = $this->createdMin ?? now()->subDays(7);
        // $this->createdMax = $this->createdMax ?? now()->subMinutes(10);
    }

    public function uniqueId(): string
    {
        return (string) $this->apiKey->id;
    }

    public function displayName(): string
    {
        return self::class.' ['.$this->apiKey->type.']';
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    public function tags(): array
    {
        return ['orders', 'api:'.$this->apiKey->type, 'api_key:'.$this->apiKey->id];
    }

    public function handle(): void
    {
        // Проверим, что job запущена внутри batch:
        if (! $this->batch()) {
            // fallback — если вдруг job не в batch, можно либо
            // return, либо выполнить логику всё равно
            Log::error('Job is not in batch', [
                'api_key_id' => $this->apiKey->id,
                'type' => $this->apiKey->type,
            ]);
        }

        $factory = IntegrationFactory::make($this->apiKey->type);
        $adapter = $factory->createAdapter($this->apiKey);
        $mapper = $factory->createMapper();

        $data = $adapter->fetchOrders(
            createdMin: $this->createdMin,
            createdMax: $this->createdMax,
            options: $this->options,
        );

        $orders = $data['orders'] ?? [];
        $ordersMapped = $mapper->transformToUnified($orders);
        self::saveChunkToModel($ordersMapped, $this->apiKey);

        $hasNextPage = $data['hasNextPage'] ?? null;
        if ($hasNextPage) {
            // Добавим новую job в ТОТ же batch, чтобы подгрузить следующую страницу
            $this->batch()->add([
                new ProcessOrdersJob(
                    apiKey: $this->apiKey,
                    createdMin: $this->createdMin,
                    createdMax: $this->createdMax,
                    options: array_merge($this->options, $data['options']),
                ),
            ]);
        } else {
            // Нет NextToken -> добавляем финальную джобу
            //            Log::info('All orders processed', [
            //                'api_key_id' => $this->apiKey->id,
            //                'type' => $this->apiKey->type,
            //            ]);
            //            $this->batch()->add([
            //                new ProcessAllOrdersJob($this->apiKey),
            //            ]);
        }
    }

    public static function saveChunkToModel(array $ordersMapped, ApiKey $apiKey): void
    {
        $chunked = collect($ordersMapped)->chunk(20);
        $errorsCount = 0;
        $maxErrorsAllowed = $chunked->count();
        $chunked->each(function ($orderChunk) use ($apiKey, &$errorsCount, $maxErrorsAllowed) {
            try {
                DB::transaction(function () use ($orderChunk, $apiKey) {
                    foreach ($orderChunk as $orderData) {
                        $orderAttributes = $orderData->toArray();
                        $itemsData = $orderData->items ?? [];
                        unset($orderAttributes['items']);

                        /** @var Order $order */
                        $order = Order::where(['order_id' => $orderAttributes['order_id']])->first();
                        if ($order && $order->manual_changed) {
                            unset($orderAttributes['order_status']);
                            unset($orderAttributes['fulfillment_status']);
                            unset($orderAttributes['refund_status']);
                        }
                        $order = Order::updateOrCreate(['order_id' => $orderAttributes['order_id']], array_merge($orderAttributes, [
                            'api_key_id' => $apiKey->id,
                        ]));
                        if (! empty($itemsData)) {
                            foreach ($itemsData as $itemData) {
                                $itemAttributes = $itemData->toArray();
                                $order->items()->updateOrCreate(
                                    ['item_id' => $itemAttributes['item_id']], // Уникальное поле для поиска
                                    $itemAttributes
                                );
                            }
                        }
                    }
                });
            } catch (\Throwable $e) {
                dump($e);
                Log::error('Error saving chunk to model', [
                    'error' => $e->getMessage(),
                    'api_key_id' => $apiKey->id,
                    'type' => $apiKey->type,
                ]);
                $errorsCount++;
                if ($errorsCount >= min($maxErrorsAllowed, 3)) {
                    throw $e;
                }
            }
        });
    }
}
