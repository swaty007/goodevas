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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiOrdersParserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:orders-parser {type=all} {--days=30} {--daysMax=}';

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
        $type = $this->argument('type');
        $days = $this->option('days');
        $daysMax = $this->option('daysMax');
        if (! in_array($type, ['all', ...ApiKey::TYPES]) || ! is_numeric($days) || $days < 1) {
            $this->error('Invalid type');

            return;
        }
        if ($type === 'all') {
            $keys = ApiKey::all();
        } else {
            $keys = ApiKey::where(['type' => $type])->get();
        }

        if ($daysMax) {
            $daysMax = now()->subDays($daysMax);
        }

        foreach ($keys as $apiKey) {
            if (App::environment('production')) {
                $cacheKey = "batch_active_{$apiKey->id}";
                if (Cache::has($cacheKey)) {
                    $this->info("Batch уже активен для API ключа ID {$apiKey->id} {$apiKey->name}, пропуск.");

                    continue;
                }
                $batch = Bus::batch([
                    new ProcessOrdersJob($apiKey, createdMin: now()->subDays($days), createdMax: $daysMax),
                ])
                    ->then(function (Batch $batch) {
                        // Выполнится только если все задания внутри batch завершаются успехом
                        // Log::info('All jobs in the batch completed successfully!');
                    })
                    ->catch(function (Batch $batch, Throwable $e) {
                        // Ошибка в одной из джоб
                        Log::error('Some job in the batch failed: '.$e->getMessage());
                    })
                    ->finally(function (Batch $batch) use ($cacheKey) {
                        // Log::info('Batch is finished (success or fail).');
                        Cache::forget($cacheKey);
                    })
                    // ->onQueue($apiKey->type)
                    ->dispatch();
                $hours = 2;
                if ($apiKey->type === ApiKey::TYPE_AMAZON) {
                    $hours = 36;
                }
                Cache::put($cacheKey, $batch->id, now()->addHours($hours));
                //$batch->add(new ProcessOrdersJob($apiKey, createdMin: now()->subDays(30)));
            } else {
                $factory = IntegrationFactory::make($apiKey->type);
                $adapter = $factory->createAdapter($apiKey);
                $mapper = $factory->createMapper();
                $hasNextPage = true;
                $options = [];
                do {
                    $data = $adapter->fetchOrders(createdMin: now()->subDays($days), createdMax: $daysMax, options: $options);
                    $hasNextPage = $data['hasNextPage'] ?? null;
                    $options = $data['options'] ?? [];

                    $orders = $data['orders'];
                    $ordersMapped = $mapper->transformToUnified($orders);
                    // dd($ordersMapped[0], $ordersMapped[0]->toArray(), new Order($ordersMapped[0]->toArray()));
                    // dd(new Order($ordersMapped[0]->toArray()), count($ordersMapped));
                    ProcessOrdersJob::saveChunkToModel($ordersMapped, $apiKey);

                    $hasNextPage = false;
                } while ($hasNextPage);
            }
        }
    }
}
