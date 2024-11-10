<?php

namespace App\Console\Commands;

use App\Facades\YsellApiFacade;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Ysell;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ProductsSnapshotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:product-snapshot';

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

    }
}
