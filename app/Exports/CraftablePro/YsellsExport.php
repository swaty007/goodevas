<?php

namespace App\Exports\CraftablePro;

use App\Models\Ysell;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class YsellsExport implements FromCollection, WithHeadings
{
    protected mixed $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        return QueryBuilder::for(Ysell::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id', 'api_key', 'name'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id', 'api_key', 'name')
            ->select(['id', 'api_key', 'name'])
            ->get();
    }

    public function headings(): array
    {
        return [
            trans('craftable-pro.Id'),
            trans('craftable-pro.Api Key'),
            trans('craftable-pro.Name'),
        ];
    }
}
