<?php

namespace App\Integrations\Data\Amazon;

use App\Integrations\Data\Enums\UnifiedOrderStatus;
use App\Integrations\Data\OrderDataInterface;
use App\Integrations\Data\OrderUnifiedData;
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
        'PendingAvailability' => UnifiedOrderStatus::PENDING,
        'Pending' => UnifiedOrderStatus::PENDING,
        'Unshipped' => UnifiedOrderStatus::PAID,  // или создать отдельный статус "UNSHIPPED", но обычно сводят к PENDING
        'PartiallyShipped' => UnifiedOrderStatus::PARTIALLY_SHIPPED,
        'Shipped' => UnifiedOrderStatus::DONE,
        'InvoiceUnconfirmed' => UnifiedOrderStatus::PENDING,
        'Canceled' => UnifiedOrderStatus::CANCELED,
        'Unfulfillable' => UnifiedOrderStatus::CANCELED,
    ];

    public static function getStatusMap(): array
    {
        return self::STATUS_MAP;
    }

    public static function convertToUnified(?OrderAmazonData $data = null): OrderUnifiedData
    {
        if (! $data instanceof OrderAmazonData) {
            throw new \InvalidArgumentException('Ожидался объект типа OrderAmazonData');
        }
        // Конвертируем транзакции (если есть)
        $transactions = array_map(fn ($t) => $t::convertToUnified($t), $data->items);

        return OrderUnifiedData::from([
            'type' => ApiKey::TYPE_AMAZON,
            'order_id' => $data->AmazonOrderId,
            'order_date' => $data->PurchaseDate,
            'update_date' => $data->LastUpdateDate,

            'order_status' => $data->OrderStatus,
            'fulfillment' => $data->FulfillmentChannel,
            'sales_channel' => $data->SalesChannel,

            'total_amount' => $data->OrderTotal['Amount'] ?? '0.00',
            'total_currency' => $data->OrderTotal['CurrencyCode'] ?? 'none',
            'payment_method' => $data->PaymentMethod,

            'buyer_name' => null, // В Amazon Orders нет имени покупателя напрямую
            'address_line_1' => $data->ShippingAddress['AddressLine1'] ?? null,
            'address_line_2' => $data->ShippingAddress['AddressLine2'] ?? null,
            'city' => $data->ShippingAddress['City'] ?? null,
            'state' => $data->ShippingAddress['StateOrRegion'] ?? null,
            'postal_code' => $data->ShippingAddress['PostalCode'] ?? null,
            'country_code' => $data->ShippingAddress['CountryCode'] ?? null,

            // Для Amazon эти поля не актуальны
            //            'min_processing_days' => null,
            //            'max_processing_days' => null,
            'expected_ship_date' => $data->LatestShipDate,

            'is_shipped' => ($data->OrderStatus === 'Shipped'),
            'items' => $transactions,
            'refunds' => [], // Amazon Orders API не даёт рефанды напрямую

            'original_object' => $data->original_object,
        ]);

    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
