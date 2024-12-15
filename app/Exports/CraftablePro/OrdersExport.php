<?php

namespace App\Exports\CraftablePro;

use App\Models\Order;
use Brackets\CraftablePro\Queries\Filters\FuzzyFilter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrdersExport implements FromCollection, WithHeadings
{
    protected mixed $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        return QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FuzzyFilter(
                    'id', 'order_id', 'type', 'order_date', 'update_date', 'order_status', 'fulfillment', 'sales_channel', 'total_amount', 'total_currency', 'payment_method', 'buyer_name', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country_code', 'expected_ship_date', 'is_shipped', 'original_object'
                )),
            ])
            ->defaultSort('id')
            ->allowedSorts('id', 'order_id', 'type', 'order_date', 'update_date', 'order_status', 'fulfillment', 'sales_channel', 'total_amount', 'total_currency', 'payment_method', 'buyer_name', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country_code', 'expected_ship_date', 'is_shipped', 'original_object')
            ->select(['id', 'order_id', 'type', 'order_date', 'update_date', 'order_status', 'fulfillment', 'sales_channel', 'total_amount', 'total_currency', 'payment_method', 'buyer_name', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country_code', 'expected_ship_date', 'is_shipped', 'original_object'])
            ->get();
    }

    public function headings(): array
    {
        return [
            trans('craftable-pro.Id'),
            trans('craftable-pro.Order Id'),
            trans('craftable-pro.Type'),
            trans('craftable-pro.Order Date'),
            trans('craftable-pro.Update Date'),
            trans('craftable-pro.Order Status'),
            trans('craftable-pro.Fulfillment'),
            trans('craftable-pro.Sales Channel'),
            trans('craftable-pro.Total Amount'),
            trans('craftable-pro.Total Currency'),
            trans('craftable-pro.Payment Method'),
            trans('craftable-pro.Buyer Name'),
            trans('craftable-pro.Address Line 1'),
            trans('craftable-pro.Address Line 2'),
            trans('craftable-pro.City'),
            trans('craftable-pro.State'),
            trans('craftable-pro.Postal Code'),
            trans('craftable-pro.Country Code'),
            trans('craftable-pro.Expected Ship Date'),
            trans('craftable-pro.Is Shipped'),
            trans('craftable-pro.Original Object'),
        ];
    }
}
