<?php
namespace App\Exports\CraftablePro;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\ProductType;

class ProductTypesExport implements FromCollection,WithHeadings
{
    protected mixed $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
    {
        return QueryBuilder::for(ProductType::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id','name'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id','name')
            ->select(['id','name'])
            ->get();
    }

    public function headings(): array
    {
        return [
            trans("craftable-pro.Id"),
            trans("craftable-pro.Name"),
        ];
    }
}
