<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Services\QueryServiceInterface;
use App\Utils\FilterWithNull;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderQueryService implements QueryServiceInterface
{
    public function getInitQuery(?array $ids = null): Builder
    {
        $initQuery = Order::query();

        return $initQuery;
    }

    /**
     * @param  string|null  $defaultSort
     */
    public function getIndexQuery($initQuery = false): QueryBuilder
    {
        if (empty($initQuery)) {
            $initQuery = $this->getInitQuery();
        }
        $orderQuery = QueryBuilder::for($initQuery)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id',
                    'order_id',
                    'type',
                    'order_date',
                    'update_date',
                    'order_status',
                    'fulfillment_status',
                    'refund_status',
                    'fulfillment',
                    'sales_channel',
                    'total_amount',
                    'total_currency',
                    'payment_method',
                    'buyer_name',
                    'address_line_1',
                    'address_line_2',
                    'city',
                    'state',
                    'postal_code',
                    'country_code',
                    'expected_ship_date',
                    'is_shipped',
                )),
                AllowedFilter::custom('order_status', new FilterWithNull),
                AllowedFilter::custom('fulfillment_status', new FilterWithNull),
                AllowedFilter::custom('refund_status', new FilterWithNull),
                AllowedFilter::custom('type', new FilterWithNull),
                AllowedFilter::custom('fulfillment', new FilterWithNull),
                AllowedFilter::custom('sales_channel', new FilterWithNull),
                AllowedFilter::custom('total_currency', new FilterWithNull),
                AllowedFilter::custom('payment_method', new FilterWithNull),
                AllowedFilter::custom('state', new FilterWithNull),
                AllowedFilter::custom('country_code', new FilterWithNull),
                AllowedFilter::custom('is_shipped', new FilterWithNull),
                AllowedFilter::scope('start_date'),
                AllowedFilter::scope('end_date'),
            ])
            ->defaultSort('id')
            ->allowedSorts([
                'id',
                'order_id',
                'type',
                'order_date',
                'update_date',
                'order_status',
                'fulfillment_status',
                'refund_status',
                'fulfillment',
                'sales_channel',
                'total_amount',
                'total_currency',
                'payment_method',
                'buyer_name',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'postal_code',
                'country_code',
                'expected_ship_date',
                'is_shipped',
            ]);

        return $orderQuery;
    }

    public function getPluckIndex(): Collection
    {
        return $this->getIndexQuery()->select(['id'])->pluck('id');
    }

    public function getIndexPagination($perPage = 50, $initQuery = false): AbstractPaginator
    {
        return $this->getIndexQuery($initQuery)
            ->with([
                'items',
                'apiKey' => function ($query) {
                    $query->select('id', 'name', 'type');
                },
            ])
            ->select(
                'id',
                'api_key_id',
                'order_id',
                'type',
                'order_date',
                'update_date',
                'order_status',
                'fulfillment_status',
                'refund_status',
                'fulfillment',
                'sales_channel',
                'total_amount',
                'total_currency',
                'payment_method',
                'buyer_name',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'postal_code',
                'country_code',
                'expected_ship_date',
                'is_shipped',
                'original_object')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getFilterOptions(): array
    {
        return $this->getFiltersArray();
    }

    protected array $filterKeys = [
        'order_status',
        'fulfillment_status',
        'refund_status',
        'type',
        'fulfillment',
        'sales_channel',
        'total_currency',
        'payment_method',
        'state',
        'country_code',
        'is_shipped',
    ];

    public function getFiltersArray(): array
    {
        $result = Cache::remember('order_filters_data', 300, function () {
            $data = [];
            foreach ($this->filterKeys as $filterKey) {
                $item = Order::select($filterKey)
                    ->distinct()
                    ->toBase() // To disable CountryName castings
                    ->pluck($filterKey)
                    ->filter()
                    ->map(fn ($value) => [
                        'value' => $value,
                        'label' => $value,
                    ])
                    ->values();

                $item->prepend([
                    'value' => null,
                    'label' => ___('global', 'null'),
                ]);
                $data[$filterKey] = $item;
            }

            return $data;
        });

        return $result;
    }
}
