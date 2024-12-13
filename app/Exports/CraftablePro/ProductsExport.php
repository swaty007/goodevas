<?php

namespace App\Exports\CraftablePro;

use App\Models\Warehouse;
use App\Services\ProductService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    protected mixed $request;

    protected ProductService $productService;

    protected Collection $warehouses;

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
            $row->additional_data,
        ];
        $this->warehouses->each(function (Warehouse $warehouse) use (&$data, $row) {
            if (! $warehouse->virtual) {
                $data[] = $row->warehouses->where('id', $warehouse->id)->first()?->pivot->stock_quantity;
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
            'additional_data',
        ];
        $this->warehouses->each(function (Warehouse $warehouse) use (&$headings) {
            if (! $warehouse->virtual) {
                $headings[] = $warehouse->name;
            }
        });

        return $headings;
    }
}
