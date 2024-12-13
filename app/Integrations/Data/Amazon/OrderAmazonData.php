<?php

namespace App\Integrations\Data\Amazon;

use App\Integrations\Data\OrderDataInterface;
use App\Integrations\Data\OrderUnifiedData;
use Spatie\LaravelData\Data;

class OrderAmazonData extends Data implements OrderDataInterface
{
    public function __construct(
        public $AmazonOrderId,
        public $PurchaseDate,
        public $LastUpdateDate,
        public $OrderStatus,
        public $FulfillmentChannel, // AFN or MFN
        public $SalesChannel,
        public array $OrderTotal,
        public $LatestShipDate,
        public $PaymentMethod,
        public array $ShippingAddress, // StateOrRegion PostalCode City CountryCode
        public array $OrderItems, // StateOrRegion PostalCode City CountryCode
        public mixed $originalObject = null,
    ) {}

    public static function convertToUnified(?OrderAmazonData $data = null): OrderUnifiedData
    {
        if (! $data instanceof OrderAmazonData) {
            throw new \InvalidArgumentException('Ожидался объект типа OrderAmazonData');
        }
        // Конвертируем транзакции (если есть)
        $transactions = array_map(fn ($t) => $t::convertToUnified($t), $data->transactions);

        return new OrderUnifiedData(
            order_id: $data->AmazonOrderId,
            order_date: $data->PurchaseDate,
            update_date: $data->LastUpdateDate,
            order_status: $data->OrderStatus,
            fulfillment: $data->FulfillmentChannel,
            sales_channel: $data->SalesChannel,
            total: [
                'amount' => $data->OrderTotal['Amount'] ?? null,
                'currency' => $data->OrderTotal['CurrencyCode'] ?? null,
            ],
            payment_method: $data->PaymentMethod,

            buyer_name: null, // В Amazon Orders нет имени покупателя напрямую
            address_line_1: $data->ShippingAddress['AddressLine1'] ?? null,
            address_line_2: $data->ShippingAddress['AddressLine2'] ?? null,
            city: $data->ShippingAddress['City'] ?? null,
            state: $data->ShippingAddress['StateOrRegion'] ?? null,
            postal_code: $data->ShippingAddress['PostalCode'] ?? null,
            country_code: $data->ShippingAddress['CountryCode'] ?? null,

            // Для Amazon эти поля не актуальны
            min_processing_days: null,
            max_processing_days: null,
            expected_ship_date: null,

            is_shipped: ($data->OrderStatus === 'Shipped'),
            transactions: $transactions,
            refunds: [], // Amazon Orders API не даёт рефанды напрямую
            originalObject: $data->originalObject,
        );
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
