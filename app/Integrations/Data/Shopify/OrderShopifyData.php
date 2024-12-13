<?php

namespace App\Integrations\Data\Shopify;

use App\Integrations\Data\OrderDataInterface;
use App\Integrations\Data\OrderUnifiedData;
use Spatie\LaravelData\Data;

class OrderShopifyData extends Data implements OrderDataInterface
{
    public function __construct(
        public $id,
        public $created_at,
        public $updated_at,
        public $financial_status,
        public $fulfillment_status,
        public $total_price,
        public $currency,
        public array $payment_gateway_names,
        public ?array $customer,
        public ?array $shipping_address,
        public array $line_items,
        public array $refunds,
        public mixed $originalObject = null,
    ) {}

    public static function convertToUnified(?OrderShopifyData $data = null): OrderUnifiedData
    {
        if (! $data instanceof OrderShopifyData) {
            throw new \InvalidArgumentException('Ожидался объект типа OrderShopifyData');
        }
        $order_status = $data->fulfillment_status ?: $data->financial_status;
        $sales_channel = 'Shopify';
        $payment_method = $data->payment_gateway_names[0] ?? null;
        $buyer_name = null;
        if (! empty($data->customer['first_name']) || ! empty($data->customer['last_name'])) {
            $buyer_name = trim(($data->customer['first_name'] ?? '').' '.($data->customer['last_name'] ?? ''));
        }

        $is_shipped = ($data->fulfillment_status === 'fulfilled');

        return new OrderUnifiedData(
            order_id: (string) $data->id,
            order_date: $data->created_at,
            update_date: $data->updated_at,
            order_status: $order_status,
            fulfillment: $data->fulfillment_status,
            sales_channel: $sales_channel,
            total: [
                'amount' => $data->total_price,
                'currency' => $data->currency,
            ],
            payment_method: $payment_method,

            buyer_name: $buyer_name,
            address_line_1: $data->shipping_address['address1'] ?? null,
            address_line_2: $data->shipping_address['address2'] ?? null,
            city: $data->shipping_address['city'] ?? null,
            state: $data->shipping_address['province'] ?? null,
            postal_code: $data->shipping_address['zip'] ?? null,
            country_code: $data->shipping_address['country_code'] ?? null,

            // Для Shopify отсутствуют эти параметры, оставим null
            min_processing_days: null,
            max_processing_days: null,
            expected_ship_date: null,

            is_shipped: $is_shipped,
            transactions: [],
            refunds: $data->refunds,
            originalObject: $data->originalObject,
        );
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
