<?php

namespace App\Integrations\Data\Shopify;

use App\Integrations\Data\Enums\UnifiedFulfilmentStatus;
use App\Integrations\Data\Enums\UnifiedMappedStatus;
use App\Integrations\Data\Enums\UnifiedOrderStatus;
use App\Integrations\Data\Enums\UnifiedRefundStatus;
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
        public $cancel_reason, // shopify_draft_order
        public $source_name, // shopify_draft_order
        public $total_price,
        public $currency,
        public array $payment_gateway_names,
        public ?array $customer,
        public ?array $shipping_address,
        /** @var ItemShopifyData[] */
        public array $line_items,
        public array $refunds,
        public array $fulfillments,
        public mixed $original_object = null,
    ) {}

    public const array STATUS_MAP = [
        // $fulfillment_status
        'partial' => UnifiedMappedStatus::PARTIALLY_SHIPPED,
        'shipped' => UnifiedMappedStatus::DONE,
        'unshipped' => UnifiedMappedStatus::PAID,
        'unfulfilled' => UnifiedMappedStatus::PAID,

        'fulfilled' => UnifiedMappedStatus::DONE,
        'in_progress' => UnifiedMappedStatus::PENDING,
        'on_hold' => UnifiedMappedStatus::PENDING,
        'open' => UnifiedMappedStatus::PENDING,
        'partially_fulfilled' => UnifiedMappedStatus::PARTIALLY_SHIPPED,
        'pending_fulfillment' => UnifiedMappedStatus::PENDING,
        'request_declined' => UnifiedMappedStatus::CANCELED,
        'restocked' => UnifiedMappedStatus::REFUNDED,
        'scheduled' => UnifiedMappedStatus::PENDING,
        // $financial_status
        'authorized' => UnifiedMappedStatus::PENDING,
        'expired' => UnifiedMappedStatus::CANCELED,
        'paid' => UnifiedMappedStatus::PAID,
        'partially_paid' => UnifiedMappedStatus::PARTIALLY_PAID,
        'partially_refunded' => UnifiedMappedStatus::PARTIALLY_REFUND,
        'pending' => UnifiedMappedStatus::PENDING,
        'refunded' => UnifiedMappedStatus::REFUNDED,
        'voided' => UnifiedMappedStatus::CANCELED,
    ];

    public static function getStatusMap(): array
    {
        return self::STATUS_MAP;
    }

    public static function resolveOrderStatus(?OrderShopifyData $order = null): UnifiedOrderStatus
    {
        $fulfillmentStatus = self::resolveFulfillmentStatus($order);
        if ($fulfillmentStatus === UnifiedFulfilmentStatus::SHIPPED) {
            return UnifiedOrderStatus::DONE;
        }
        if (in_array($order->financial_status, ['voided', 'expired']) || ! empty($order->cancel_reason)) {
            return UnifiedOrderStatus::CANCELED;
        }
        if (in_array($fulfillmentStatus, [UnifiedFulfilmentStatus::PARTIALLY_SHIPPED, UnifiedFulfilmentStatus::NOT_SHIPPED])) {
            return UnifiedOrderStatus::PENDING;
        }

        return UnifiedOrderStatus::ERROR;
    }

    public static function resolveFulfillmentStatus(?OrderShopifyData $order = null): UnifiedFulfilmentStatus
    {
        if (empty($order->fulfillments)) {
            return UnifiedFulfilmentStatus::NOT_SHIPPED;
        }
        $countFulfillments = 0;
        foreach ($order->fulfillments as $fulfillment) {
            $countFulfillments += count($fulfillment['tracking_numbers']);
        }
        if (count($order->line_items) > 0) {
            if ($countFulfillments >= count($order->line_items)) {
                return UnifiedFulfilmentStatus::SHIPPED;
            }

            return UnifiedFulfilmentStatus::PARTIALLY_SHIPPED;
        }

        return UnifiedFulfilmentStatus::ERROR;
    }

    public static function resolveRefundStatus(?OrderShopifyData $order = null): UnifiedRefundStatus
    {
        if ($order->financial_status === 'refunded') {
            return UnifiedRefundStatus::REFUNDED;
        }
        if ($order->financial_status === 'partially_refunded') {
            return UnifiedRefundStatus::PARTIALLY_REFUND;
        }

        return UnifiedRefundStatus::NOT_REFUNDED;
    }

    public static function convertToUnified(?OrderShopifyData $order = null): OrderUnifiedData
    {
        if (! $order instanceof OrderShopifyData) {
            throw new \InvalidArgumentException('Ожидался объект типа OrderShopifyData');
        }
        $transactions = array_map(fn ($t) => $t::convertToUnified($t), $order->line_items);

        $order_status = $order->fulfillment_status ?: $order->financial_status;
        $sales_channel = 'Shopify';
        $payment_method = $order->payment_gateway_names[0] ?? null;
        $buyer_name = null;
        if (! empty($order->customer['first_name']) || ! empty($order->customer['last_name'])) {
            $buyer_name = trim(($order->customer['first_name'] ?? '').' '.($order->customer['last_name'] ?? ''));
        }

        $is_shipped = ($order->fulfillment_status === 'fulfilled');

        return OrderUnifiedData::from([
            'type' => ApiKey::TYPE_SHOPIFY,
            'order_id' => (string) $order->order_number,
            'order_date' => $order->created_at,
            'update_date' => $order->updated_at,

            'mapped_status' => $order_status,
            'fulfillment' => $order->fulfillment_status,
            'sales_channel' => $sales_channel,
            'total_amount' => $order->total_price,
            'total_currency' => $order->currency,
            'payment_method' => $payment_method,
            'buyer_name' => $buyer_name,

            'address_line_1' => $order->shipping_address['address1'] ?? null,
            'address_line_2' => $order->shipping_address['address2'] ?? null,
            'city' => $order->shipping_address['city'] ?? null,
            'state' => $order->shipping_address['province'] ?? null,
            'postal_code' => $order->shipping_address['zip'] ?? null,
            'country_code' => $order->shipping_address['country_code'] ?? null,

            // Для Shopify отсутствуют эти параметры, оставим null
            //            'min_processing_days' => null,
            //            'max_processing_days' => null,
            'expected_ship_date' => Carbon::parse($order->created_at)->addDays(2)->toISOString(),
            'is_shipped' => $is_shipped,
            'items' => $transactions,
            'refunds' => $order->refunds,
            'original_object' => $order->original_object,

            'order_status' => self::resolveOrderStatus($order),
            'fulfillment_status' => self::resolveFulfillmentStatus($order),
            'refund_status' => self::resolveRefundStatus($order),
        ]);
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
