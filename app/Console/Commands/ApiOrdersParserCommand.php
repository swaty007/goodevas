<?php

namespace App\Console\Commands;

use App\Integrations\IntegrationFactory;
use App\Jobs\ProcessOrdersJob;
use App\Models\ApiKey;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
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
            if ($apiKey->type === 'etsy') {
                if (App::environment('production')) {
                    $batch = Bus::batch([
                        new ProcessOrdersJob($apiKey, createdMin: now()->subDays(30)),
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
                    $factory = IntegrationFactory::make($apiKey->type);
                    $adapter = $factory->createAdapter($apiKey);
                    $hasNextPage = true;
                    $options = [];
                    do {
                        $data = $adapter->fetchOrders(createdMin: now()->subDays(30), options: $options, createdMax: now());
                        $orders = $data['orders'];
                        $hasNextPage = $data['hasNextPage'] ?? null;
                        $options = $data['options'] ?? [];
                        $mapper = $factory->createMapper();
                        $ordersMapped = $mapper->transformToUnified($orders);
                    } while ($hasNextPage);
                }
            }

        }
    }
}
