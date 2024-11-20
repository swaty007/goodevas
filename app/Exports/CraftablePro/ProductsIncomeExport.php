<?php

namespace App\Exports\CraftablePro;

use App\Models\Warehouse;
use App\Services\ProductService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsIncomeExport implements FromCollection, WithHeadings, WithMapping
{
    protected mixed $request;

    protected ProductService $productService;

    protected Collection $warehouses;

    public const separator = ':'; // separator for warehouse id and date

    public function __construct($request)
    {
        $this->request = $request;
        $this->productService = app(ProductService::class);
        $this->warehouses = $this->productService->getWarehouses();
    }

    public function collection(): Collection
    {
        $productsQuery = $this->productService->getProductionQuery();
        $products = $this->productService->getProductsFromQuery($productsQuery, 1000);

        return $products->getCollection();
    }

    public function map($row): array
    {
        $data = [
            $row->ext_id,
            $row->ean,
            $row->type->name,
            // $row->additional_data,
        ];
        $this->warehouses->each(function (Warehouse $warehouse) use (&$data, $row) {
            if (! $warehouse->virtual) {
                foreach ($warehouse->futureIncomesDates as $date) {
                    $data[] = $row->incomes->where('warehouse_id', $warehouse->id)->where('income_date', $date)
//                        ->first()?->quantity;
                        ->sum('quantity');
                }
            }
        });

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            'ext_id',
            'ean',
            'type',
            // 'additional_data',
        ];
        $this->warehouses->each(function (Warehouse $warehouse) use (&$headings) {
            if (! $warehouse->virtual) {
                foreach ($warehouse->futureIncomesDates as $date) {
                    $headings[] = $warehouse->id.self::separator.$warehouse->name.self::separator.' '.$date;
                }
            }
        });

        return $headings;
    }
}
