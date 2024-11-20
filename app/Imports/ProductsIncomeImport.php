<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductIncome;
use App\Services\ProductService;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Throwable;

class ProductsIncomeImport implements SkipsOnError, ToCollection, WithHeadingRow
{
    use Importable;
    use SkipsErrors;

    protected ProductService $productService;

    public function __construct()
    {
        $this->productService = app(ProductService::class);
        HeadingRowFormatter::default('none');
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {

            $warehousesData = [];

            foreach ($row as $key => $value) {
                // Проверяем, содержит ли ключ двоеточие и значение не null
                if (strpos($key, ':') !== false && $value !== null) {
                    $parts = explode(':', $key);
                    if (count($parts) === 3) {
                        $data = [
                            'warehouse_id' => trim($parts[0]),
                            'name' => trim($parts[1]),
                            'income_date' => trim($parts[2]),
                            'income_quantity' => $value,
                        ];

                        if (
                            is_numeric($data['income_quantity']) &&
                            $this->isValidDate($data['income_date'])
                        ) {
                            $warehousesData[] = $data;
                        } else {
                            throw new \Exception('Invalid data format or value');
                        }
                    }
                }
            }

            if (! empty($warehousesData) && $product = Product::where(['ext_id' => $row['ext_id']])->first()) {
                foreach ($warehousesData as $data) {
                    ProductIncome::updateOrCreate([
                        'product_id' => $product->id,
                        'warehouse_id' => (int) $data['warehouse_id'],
                        'income_date' => $data['income_date'],
                    ], [
                        'quantity' => $data['income_quantity'],
                    ]);
                    //                        $product->incomes()->updateOrCreate([
                    //                            'warehouse_id' => (int)$data['warehouse_id'],
                    //                            'income_date' => $data['income_date'],
                    //                        ], [
                    //                            'quantity' => $data['income_quantity'],
                    //                        ]);
                }
            }
        }
    }

    private function isValidDate($date): bool
    {
        $format = 'Y-m-d';
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    public function onError(Throwable $e)
    {
        Log::error('ProductsIncomeImport Error: '.$e->getMessage());
        throw $e;
    }
}
