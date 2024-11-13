<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Console\Command;

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
        Product::query()
            ->with([
                'warehouses',
            ])
            ->get()
            ->each(function (Product $product) {
                $income = [];
                $stock = [];
                $product->warehouses->each(function (Warehouse $warehouse) use (&$stock, &$income) {
                    $stock[$warehouse->name] = $warehouse->pivot->stock_quantity;
                    $income[$warehouse->name] = $warehouse->pivot->income_quantity;
                });

                $product->stockSnapshots()->updateOrCreate([
                    'snapshot_date' => now()->format('Y-m-d'),
                ],
                    [
                        'snapshot_date' => now()->format('Y-m-d'),
                        'warehouse_stock' => [
                            'stock' => $stock,
                            'income' => $income,
                        ],
                    ]);
            });
    }
}
