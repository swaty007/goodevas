<?php

namespace App\Console\Commands;

use App\Facades\YsellApiFacade;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Console\Command;

class ProductsParserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:product-parser';

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

        $products = Product::all();

        $existingWarehouses = Warehouse::all()->keyBy('ysell_name'); // Загружаем все склады и индексируем по имени
        $productsApi = YsellApiFacade::getProductAllByAllYsellKeys();

        foreach ($products as $product) {
            /* @var $product Product */
            $productApi = array_filter($productsApi, function ($productApi) use ($product) {
                return $productApi['ext_id'] === $product->ext_id;
            });
            if (count($productApi) === 0) {
                continue;
            }
            $productApi = array_shift($productApi);
            $product->update([
                'additional_data' => $productApi,
            ]);
            if (! empty($productApi['warehouseStock'])) {
                foreach ($productApi['warehouseStock'] as $warehouseStock) {
                    $warehouseName = $warehouseStock['warehouse']['name'];
                    // Проверяем, существует ли склад в заранее загруженном списке
                    if (! $existingWarehouses->has($warehouseName)) {
                        $warehouseModel = Warehouse::create([
                            'name' => $warehouseName,
                            'ysell_name' => $warehouseName,
                            'country_id' => $warehouseStock['warehouse']['country_id'],
                        ]);
                        $existingWarehouses->put($warehouseName, $warehouseModel);
                    } else {
                        $warehouseModel = $existingWarehouses->get($warehouseName);
                    }

                    $product->warehouses()->syncWithoutDetaching([
                        $warehouseModel->id => [
                            'stock_quantity' => $warehouseStock['qty'],
                        ],
                    ]);
                }
            }
        }
    }
}
