<?php

namespace App\Console\Commands;

use App\Integrations\IntegrationFactory;
use App\Models\ApiKey;
use Illuminate\Console\Command;

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
            $factory = IntegrationFactory::make($apiKey->type);

            $adapter = $factory->createAdapter($apiKey);
            $mapper = $factory->createMapper();
        }
    }
}
