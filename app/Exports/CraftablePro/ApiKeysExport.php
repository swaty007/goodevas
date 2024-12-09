<?php

namespace App\Exports\CraftablePro;

use App\Models\ApiKey;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApiKeysExport implements FromCollection, WithHeadings
{
    protected mixed $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        return QueryBuilder::for(ApiKey::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id', 'name', 'type', 'key', 'additional_data'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id', 'name', 'type', 'key', 'additional_data')
            ->select(['id', 'name', 'type', 'key', 'additional_data'])
            ->get();
    }

    public function headings(): array
    {
        return [
            trans('craftable-pro.Id'),
            trans('craftable-pro.Name'),
            trans('craftable-pro.Type'),
            trans('craftable-pro.Key'),
            trans('craftable-pro.Additional Data'),
        ];
    }
}
