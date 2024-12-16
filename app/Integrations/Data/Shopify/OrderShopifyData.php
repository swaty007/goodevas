<?php

namespace App\Integrations\Data\Shopify;

use App\Integrations\Data\Enums\UnifiedOrderStatus;
use App\Integrations\Data\OrderDataInterface;
use App\Integrations\Data\OrderUnifiedData;
use App\Models\ApiKey;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class OrderShopifyData extends Data implements OrderDataInterface
{
    public function __construct(
        public $id,
        public $order_number,
        public $created_at,
        public $updated_at,
        public ?string $fulfillment_status,
        public $financial_status,
        public $total_price,
        public $currency,
        public array $payment_gateway_names,
        public ?array $customer,
        public ?array $shipping_address,
        /** @var ItemShopifyData[] */
        public array $line_items,
        public array $refunds,
        public mixed $original_object = null,
    ) {}

    public const array STATUS_MAP = [
        // $fulfillment_status
        'partial' => UnifiedOrderStatus::PARTIALLY_SHIPPED,
        'shipped' => UnifiedOrderStatus::DONE,
        'unshipped' => UnifiedOrderStatus::PAID,
        'unfulfilled' => UnifiedOrderStatus::PAID,

        'fulfilled' => UnifiedOrderStatus::DONE,
        'in_progress' => UnifiedOrderStatus::PENDING,
        'on_hold' => UnifiedOrderStatus::PENDING,
        'open' => UnifiedOrderStatus::PENDING,
        'partially_fulfilled' => UnifiedOrderStatus::PARTIALLY_SHIPPED,
        'pending_fulfillment' => UnifiedOrderStatus::PENDING,
        'request_declined' => UnifiedOrderStatus::CANCELED,
        'restocked' => UnifiedOrderStatus::REFUNDED,  // или другой статус, если есть "returned"
        'scheduled' => UnifiedOrderStatus::PENDING,

        // $financial_status
        'authorized' => UnifiedOrderStatus::PENDING,
        'expired' => UnifiedOrderStatus::CANCELED,
        'paid' => UnifiedOrderStatus::PAID,
        'partially_paid' => UnifiedOrderStatus::PARTIALLY_PAID,
        'partially_refunded' => UnifiedOrderStatus::PARTIALLY_REFUND,
        'pending' => UnifiedOrderStatus::PENDING,
        'refunded' => UnifiedOrderStatus::REFUNDED,
        'voided' => UnifiedOrderStatus::CANCELED,
    ];

    public static function getStatusMap(): array
    {
        return self::STATUS_MAP;
    }

    public static function convertToUnified(?OrderShopifyData $data = null): OrderUnifiedData
    {
        if (! $data instanceof OrderShopifyData) {
            throw new \InvalidArgumentException('Ожидался объект типа OrderShopifyData');
        }
        $transactions = array_map(fn ($t) => $t::convertToUnified($t), $data->line_items);

        $order_status = $data->fulfillment_status ?: $data->financial_status;
        $sales_channel = 'Shopify';
        $payment_method = $data->payment_gateway_names[0] ?? null;
        $buyer_name = null;
        if (! empty($data->customer['first_name']) || ! empty($data->customer['last_name'])) {
            $buyer_name = trim(($data->customer['first_name'] ?? '').' '.($data->customer['last_name'] ?? ''));
        }

        $is_shipped = ($data->fulfillment_status === 'fulfilled');

        return OrderUnifiedData::from([
            'type' => ApiKey::TYPE_SHOPIFY,
            'order_id' => (string) $data->order_number,
            'order_date' => $data->created_at,
            'update_date' => $data->updated_at,

            'order_status' => $order_status,
            'fulfillment' => $data->fulfillment_status,
            'sales_channel' => $sales_channel,
            'total_amount' => $data->total_price,
            'total_currency' => $data->currency,
            'payment_method' => $payment_method,
            'buyer_name' => $buyer_name,

            'address_line_1' => $data->shipping_address['address1'] ?? null,
            'address_line_2' => $data->shipping_address['address2'] ?? null,
            'city' => $data->shipping_address['city'] ?? null,
            'state' => $data->shipping_address['province'] ?? null,
            'postal_code' => $data->shipping_address['zip'] ?? null,
            'country_code' => $data->shipping_address['country_code'] ?? null,

            // Для Shopify отсутствуют эти параметры, оставим null
            //            'min_processing_days' => null,
            //            'max_processing_days' => null,
            'expected_ship_date' => Carbon::parse($data->created_at)->addDays(2)->toISOString(),
            'is_shipped' => $is_shipped,
            'items' => $transactions,
            'refunds' => $data->refunds,
            'original_object' => $data->original_object,
        ]);
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
