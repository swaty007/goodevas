<?php

namespace App\Exports\CraftablePro;

use App\Models\Product;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductsExport implements FromCollection, WithHeadings
{
    protected mixed $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        return QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id', 'ext_id', 'ean', 'additional_data', 'product_type_id'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id', 'ext_id', 'ean', 'additional_data', 'product_type_id')
            ->select(['id', 'ext_id', 'ean', 'additional_data', 'product_type_id'])
            ->get();
    }

    public function headings(): array
    {
        return [
            trans('craftable-pro.Id'),
            trans('craftable-pro.Ext Id'),
            trans('craftable-pro.Ean'),
            trans('craftable-pro.Additional Data'),
            trans('craftable-pro.Product Type Id'),
        ];
    }
}