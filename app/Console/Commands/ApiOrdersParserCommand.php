<?php

namespace App\Console\Commands;

use App\Integrations\IntegrationFactory;
use App\Jobs\ProcessOrdersJob;
use App\Models\ApiKey;
use App\Models\Order;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiOrdersParserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:orders-parser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $keys = ApiKey::all();
        foreach ($keys as $apiKey) {
            if (App::environment('production')) {
                $batch = Bus::batch([
                    new ProcessOrdersJob($apiKey, createdMin: now()->subDays(360)),
                ])
                    ->then(function (Batch $batch) {
                        // Выполнится только если все задания внутри batch завершаются успехом
                        Log::info('All jobs in the batch completed successfully!');
                    })
                    ->catch(function (Batch $batch, Throwable $e) {
                        // Ошибка в одной из джоб
                        Log::error('Some job in the batch failed: '.$e->getMessage());
                    })
                    ->finally(function (Batch $batch) {
                        Log::info('Batch is finished (success or fail).');
                    })
                    ->onQueue($apiKey->type)
                    ->dispatch();
                //$batch->add(new ProcessOrdersJob($apiKey, createdMin: now()->subDays(30)));
            } else {
                if ($apiKey->type === 'etsy') {
                    $factory = IntegrationFactory::make($apiKey->type);
                    $adapter = $factory->createAdapter($apiKey);
                    $mapper = $factory->createMapper();
                    $hasNextPage = true;
                    $options = [];
                    do {
                        $data = $adapter->fetchOrders(createdMin: now()->subDays(30), options: $options);
                        $hasNextPage = $data['hasNextPage'] ?? null;
                        $options = $data['options'] ?? [];

                        $orders = $data['orders'];
                        $ordersMapped = $mapper->transformToUnified($orders);
                        // dd($ordersMapped[0], $ordersMapped[0]->toArray(), new Order($ordersMapped[0]->toArray()));
                        // dd(new Order($ordersMapped[0]->toArray()), count($ordersMapped));
                        DB::transaction(function () use ($ordersMapped, $apiKey) {
                            foreach ($ordersMapped as $orderData) {
                                $orderAttributes = $orderData->toArray();
                                $itemsData = $orderData->items ?? [];
                                unset($orderAttributes['items']);

                                /** @var Order $order */
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
                        $hasNextPage = false;
                    } while ($hasNextPage);
                }
            }
        }
    }
}
