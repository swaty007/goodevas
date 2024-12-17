<?php

namespace App\Integrations\Data\Amazon;

use App\Integrations\Data\Enums\UnifiedFulfilmentStatus;
use App\Integrations\Data\Enums\UnifiedMappedStatus;
use App\Integrations\Data\Enums\UnifiedOrderStatus;
use App\Integrations\Data\Enums\UnifiedRefundStatus;
use App\Integrations\Data\OrderDataInterface;
use App\Integrations\Data\OrderUnifiedData;
use App\Integrations\Data\Shopify\OrderShopifyData;
use App\Models\ApiKey;
use Spatie\LaravelData\Data;

class OrderAmazonData extends Data implements OrderDataInterface
{
    public function __construct(
        public $AmazonOrderId,
        public $PurchaseDate,
        public $LastUpdateDate,
        public $OrderStatus,
        public ?string $FulfillmentChannel, // AFN or MFN
        public ?string $SalesChannel,
        public ?array $OrderTotal,
        public ?string $LatestShipDate,
        public ?string $PaymentMethod,
        public ?array $ShippingAddress, // StateOrRegion PostalCode City CountryCode
        /** @var ItemAmazonData[] */
        public ?array $items,
        public mixed $original_object = null,
    ) {}

    public const array STATUS_MAP = [
        'PendingAvailability' => UnifiedMappedStatus::PENDING,
        'Pending' => UnifiedMappedStatus::PENDING,
        'Unshipped' => UnifiedMappedStatus::PAID,  // или создать отдельный статус "UNSHIPPED", но обычно сводят к PENDING
        'PartiallyShipped' => UnifiedMappedStatus::PARTIALLY_SHIPPED,
        'Shipped' => UnifiedMappedStatus::DONE,
        'InvoiceUnconfirmed' => UnifiedMappedStatus::PENDING,
        'Canceled' => UnifiedMappedStatus::CANCELED,
        'Unfulfillable' => UnifiedMappedStatus::CANCELED,
    ];

    public static function getStatusMap(): array
    {
        return self::STATUS_MAP;
    }

    public static function resolveOrderStatus(?OrderAmazonData $order = null): UnifiedOrderStatus
    {

    }

    public static function resolveFulfillmentStatus(?OrderAmazonData $order = null): UnifiedFulfilmentStatus
    {
        return UnifiedFulfilmentStatus::ERROR;
    }

    public static function resolveRefundStatus(?OrderAmazonData $order = null): UnifiedRefundStatus
    {
        return UnifiedRefundStatus::NOT_REFUNDED;
    }

    public static function convertToUnified(?OrderAmazonData $order = null): OrderUnifiedData
    {
        if (! $order instanceof OrderAmazonData) {
            throw new \InvalidArgumentException('Ожидался объект типа OrderAmazonData');
        }
        // Конвертируем транзакции (если есть)
        $transactions = array_map(fn ($t) => $t::convertToUnified($t), $order->items);

        return OrderUnifiedData::from([
            'type' => ApiKey::TYPE_AMAZON,
            'order_id' => $order->AmazonOrderId,
            'order_date' => $order->PurchaseDate,
            'update_date' => $order->LastUpdateDate,

            'mapped_status' => $order->OrderStatus,
            'fulfillment' => $order->FulfillmentChannel,
            'sales_channel' => $order->SalesChannel,

            'total_amount' => $order->OrderTotal['Amount'] ?? '0.00',
            'total_currency' => $order->OrderTotal['CurrencyCode'] ?? 'none',
            'payment_method' => $order->PaymentMethod,

            'buyer_name' => null, // В Amazon Orders нет имени покупателя напрямую
            'address_line_1' => $order->ShippingAddress['AddressLine1'] ?? null,
            'address_line_2' => $order->ShippingAddress['AddressLine2'] ?? null,
            'city' => $order->ShippingAddress['City'] ?? null,
            'state' => $order->ShippingAddress['StateOrRegion'] ?? null,
            'postal_code' => $order->ShippingAddress['PostalCode'] ?? null,
            'country_code' => $order->ShippingAddress['CountryCode'] ?? null,

            // Для Amazon эти поля не актуальны
            //            'min_processing_days' => null,
            //            'max_processing_days' => null,
            'expected_ship_date' => $order->LatestShipDate,

            'is_shipped' => ($order->OrderStatus === 'Shipped'),
            'items' => $transactions,
            'refunds' => [], // Amazon Orders API не даёт рефанды напрямую

            'original_object' => $order->original_object,

            'order_status' => self::resolveOrderStatus($order),
            'fulfillment_status' => self::resolveFulfillmentStatus($order),
            'refund_status' => self::resolveRefundStatus($order),
        ]);

    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
