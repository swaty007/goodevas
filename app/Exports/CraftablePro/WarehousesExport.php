<?php

namespace App\Exports\CraftablePro;

use App\Models\Warehouse;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class WarehousesExport implements FromCollection, WithHeadings
{
    protected mixed $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        return QueryBuilder::for(Warehouse::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id', 'name', 'country_id', 'ysell_name'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id', 'name', 'country_id', 'ysell_name')
            ->select(['id', 'name', 'country_id', 'ysell_name'])
            ->get();
    }

    public function headings(): array
    {
        return [
            trans('craftable-pro.Id'),
            trans('craftable-pro.Name'),
            trans('craftable-pro.Country Id'),
            trans('craftable-pro.Ysell Name'),
        ];
    }
}
