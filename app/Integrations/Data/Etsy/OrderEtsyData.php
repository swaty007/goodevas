<?php

namespace App\Integrations\Data\Etsy;

use App\Integrations\Data\Enums\UnifiedFulfilmentStatus;
use App\Integrations\Data\Enums\UnifiedMappedStatus;
use App\Integrations\Data\Enums\UnifiedOrderStatus;
use App\Integrations\Data\Enums\UnifiedRefundStatus;
use App\Integrations\Data\OrderDataInterface;
use App\Integrations\Data\OrderUnifiedData;
use App\Models\ApiKey;
use Spatie\LaravelData\Data;

class OrderEtsyData extends Data implements OrderDataInterface
{
    public function __construct(
        public $receipt_id,
        public $name,
        public $first_line,
        public $second_line,
        public $city,
        public $state,
        public $zip,
        public $formatted_address,
        public $status,
        public $country_iso,
        public $payment_method,
        public $payment_email,
        public $is_shipped,
        public $created_timestamp,
        public $updated_timestamp,
        public array $grandtotal, // amount divisor currency
        /** @var ItemEtsyData[] */
        public array $transactions,
        public array $refunds,
        public array $shipments,
        public mixed $original_object = null,

    ) {}

    public const array STATUS_MAP = [
        'Paid' => UnifiedMappedStatus::PAID,
        'Completed' => UnifiedMappedStatus::DONE,
        'Open' => UnifiedMappedStatus::PENDING,
        'Payment Processing' => UnifiedMappedStatus::PENDING,
        'Canceled' => UnifiedMappedStatus::CANCELED,
        'Fully Refunded' => UnifiedMappedStatus::REFUNDED,
        'Partially Refunded' => UnifiedMappedStatus::PARTIALLY_REFUND,
    ];

    public static function getStatusMap(): array
    {
        return self::STATUS_MAP;
    }

    public static function resolveOrderStatus(?OrderEtsyData $order = null): UnifiedOrderStatus
    {
        $fulfillmentStatus = self::resolveFulfillmentStatus($order);
        if ($fulfillmentStatus === UnifiedFulfilmentStatus::SHIPPED) {
            return UnifiedOrderStatus::DONE;
        }
        if ($order->status === 'Canceled') {
            return UnifiedOrderStatus::CANCELED;
        }
        if (in_array($fulfillmentStatus, [UnifiedFulfilmentStatus::PARTIALLY_SHIPPED, UnifiedFulfilmentStatus::NOT_SHIPPED])) {
            return UnifiedOrderStatus::PENDING;
        }

        return UnifiedOrderStatus::ERROR;
    }

    public static function resolveFulfillmentStatus(?OrderEtsyData $order = null): UnifiedFulfilmentStatus
    {
        if (! $order->is_shipped) {
            return UnifiedFulfilmentStatus::NOT_SHIPPED;
        }
        if (empty($order->shipments)) {
            return UnifiedFulfilmentStatus::NOT_SHIPPED;
        }
        if (count($order->transactions) > 0) {
            if (count($order->shipments) >= count($order->transactions)) {
                return UnifiedFulfilmentStatus::SHIPPED;
            }

            return UnifiedFulfilmentStatus::PARTIALLY_SHIPPED;
        }

        return UnifiedFulfilmentStatus::ERROR;
    }

    public static function resolveRefundStatus(?OrderEtsyData $order = null): UnifiedRefundStatus
    {
        if (in_array($order->status, ['Fully Refunded'])) {
            return UnifiedRefundStatus::REFUNDED;
        }
        if (in_array($order->status, ['Partially Refunded']) || ! empty($order->refunds)) {
            return UnifiedRefundStatus::PARTIALLY_REFUND;
        }

        return UnifiedRefundStatus::NOT_REFUNDED;
    }

    public static function convertToUnified(?OrderEtsyData $order = null): OrderUnifiedData
    {
        if (! $order instanceof OrderEtsyData) {
            throw new \InvalidArgumentException('Ожидался объект типа OrderEtsyData');
        }
        // Преобразуем транзакции Etsy в унифицированный формат
        $transactions = array_map(fn ($t) => $t::convertToUnified($t), $order->transactions);

        $allMinDays = [];
        $allMaxDays = [];
        $allShipDates = [];

        foreach ($order->transactions as $t) {
            if (! empty($t->min_processing_days)) {
                $allMinDays[] = $t->min_processing_days;
            }
            if (! empty($t->max_processing_days)) {
                $allMaxDays[] = $t->max_processing_days;
            }
            if (! empty($t->expected_ship_date)) {
                $allShipDates[] = date('c', $t->expected_ship_date);
            }
        }
        $minProcessing = ! empty($allMinDays) ? min($allMinDays) : null;
        $maxProcessing = ! empty($allMaxDays) ? max($allMaxDays) : null;
        $expectedShip = null;
        if (! empty($allShipDates)) {
            // Найдём самую раннюю дату среди собранных
            $parsedAllShipDates = array_map(fn ($d) => strtotime($d), $allShipDates);
            $earliest = min($parsedAllShipDates);
            $expectedShip = date('c', $earliest);
        }

        return OrderUnifiedData::from([
            'type' => ApiKey::TYPE_ETSY,
            'order_id' => (string) $order->receipt_id,
            'order_date' => $order->created_timestamp ? gmdate('c', $order->created_timestamp) : null,
            'update_date' => $order->updated_timestamp ? gmdate('c', $order->updated_timestamp) : null,

            'mapped_status' => $order->status,
            'fulfillment' => null, // У Etsy нет аналога FulfillmentChannel
            'sales_channel' => 'Etsy',

            'total_amount' => bcdiv($order->grandtotal['amount'], $order->grandtotal['divisor'], 2),
            'total_currency' => $order->grandtotal['currency_code'],
            'payment_method' => $order->payment_method,

            'buyer_name' => $order->name,
            'address_line_1' => $order->first_line,
            'address_line_2' => $order->second_line,
            'city' => $order->city,
            'state' => $order->state,
            'postal_code' => $order->zip,
            'country_code' => $order->country_iso,

            //            'min_processing_days' => $minProcessing,
            //            'max_processing_days' => $maxProcessing,
            'expected_ship_date' => $expectedShip,

            'is_shipped' => (bool) $order->is_shipped,
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
