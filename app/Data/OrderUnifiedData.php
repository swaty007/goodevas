<?php

namespace App\Data;

use App\Integrations\Data\Amazon\OrderAmazonData;
use App\Integrations\Data\Etsy\OrderEtsyData;
use App\Integrations\Data\Shopify\OrderShopifyData;
use Spatie\LaravelData\Data;

class OrderUnifiedData extends Data
{
    public function __construct(
        public string $order_id,
        public ?string $order_date,
        public ?string $update_date,
        public string $order_status,
        public ?string $fulfillment,
        public ?string $sales_channel,
        public array $total,
        public ?string $payment_method,

        // Адрес и данные покупателя
        public ?string $buyer_name,
        public ?string $address_line_1,
        public ?string $address_line_2,
        public ?string $city,
        public ?string $state,
        public ?string $postal_code,
        public ?string $country_code,

        // Показатели обработки и отгрузки (актуально для Etsy)
        public ?int $min_processing_days = null,
        public ?int $max_processing_days = null,
        public ?string $expected_ship_date = null,

        public bool $is_shipped = false,
        /** @var TransactionUnifiedData[] */
        public array $transactions = [],
        public array $refunds = [],
    ) {}

    public static function fromAmazon(OrderAmazonData $amazon, array $amazonTransactions = []): self
    {
        // Конвертируем транзакции (если есть)
        $transactions = array_map(fn($t) => TransactionUnifiedData::fromAmazon($t), $amazonTransactions);

        return new self(
            order_id: $amazon->AmazonOrderId,
            order_date: $amazon->PurchaseDate,
            update_date: $amazon->LastUpdateDate,
            order_status: $amazon->OrderStatus,
            fulfillment: $amazon->FulfillmentChannel,
            sales_channel: $amazon->SalesChannel,
            total: [
                'amount' => $amazon->OrderTotal['Amount'] ?? null,
                'currency' => $amazon->OrderTotal['CurrencyCode'] ?? null
            ],
            payment_method: $amazon->PaymentMethod,

            buyer_name: null, // В Amazon Orders нет имени покупателя напрямую
            address_line_1: $amazon->ShippingAddress['AddressLine1'] ?? null,
            address_line_2: $amazon->ShippingAddress['AddressLine2'] ?? null,
            city: $amazon->ShippingAddress['City'] ?? null,
            state: $amazon->ShippingAddress['StateOrRegion'] ?? null,
            postal_code: $amazon->ShippingAddress['PostalCode'] ?? null,
            country_code: $amazon->ShippingAddress['CountryCode'] ?? null,

            // Для Amazon эти поля не актуальны
            min_processing_days: null,
            max_processing_days: null,
            expected_ship_date: null,

            is_shipped: ($amazon->OrderStatus === 'Shipped'),
            transactions: $transactions,
            refunds: [], // Amazon Orders API не даёт рефанды напрямую
        );
    }

    public static function fromEtsy(OrderEtsyData $etsy, array $etsyTransactions = []): self
    {
        // Преобразуем транзакции Etsy в унифицированный формат
        $transactions = array_map(fn($t) => TransactionUnifiedData::fromEtsy($t), $etsyTransactions);

        // Агрегируем min_processing_days, max_processing_days и expected_ship_date из транзакций
        // Предполагается, что min_processing_days, max_processing_days, expected_ship_date - это массивы в транзакциях,
        // содержащие числовые значения (для min/max) или даты (для expected_ship_date).
        // Возьмём минимальное/максимальное значение по всем транзакциям, а для expected_ship_date - самую раннюю дату.

        $allMinDays = [];
        $allMaxDays = [];
        $allShipDates = [];

        foreach ($etsyTransactions as $t) {
            // min_processing_days, max_processing_days, expected_ship_date - массивы, возьмём первый элемент если он есть
            if (!empty($t->min_processing_days)) {
                // Предполагаем, что в min_processing_days либо одно значение, либо берем минимум из массива
                $allMinDays[] = min($t->min_processing_days);
            }

            if (!empty($t->max_processing_days)) {
                $allMaxDays[] = max($t->max_processing_days);
            }
            if (!empty($t->expected_ship_date)) {
                // Если expected_ship_date - это массив дат, возьмем самую раннюю
                // Предполагаем, что в expected_ship_date либо одна дата, либо набор дат
                $parsedDates = array_map(fn($d) => strtotime($d), $t->expected_ship_date);
                $earliestTimestamp = min($parsedDates);
                $allShipDates[] = date('c', $earliestTimestamp);
            }
        }

        $minProcessing = !empty($allMinDays) ? min($allMinDays) : null;
        $maxProcessing = !empty($allMaxDays) ? max($allMaxDays) : null;
        $expectedShip = null;
        if (!empty($allShipDates)) {
            // Найдём самую раннюю дату среди собранных
            $parsedAllShipDates = array_map(fn($d) => strtotime($d), $allShipDates);
            $earliest = min($parsedAllShipDates);
            $expectedShip = date('c', $earliest);
        }

        return new self(
            order_id: (string)$etsy->receipt_id,
            order_date: $etsy->created_timestamp ? gmdate("c", $etsy->created_timestamp) : null,
            update_date: $etsy->updated_timestamp ? gmdate("c", $etsy->updated_timestamp) : null,
            order_status: $etsy->status,
            fulfillment: null, // У Etsy нет аналога FulfillmentChannel
            sales_channel: 'Etsy',
            total: [
                'amount' => $etsy->grandtotal['amount'] ?? null,
                'currency' => $etsy->grandtotal['currency'] ?? null,
                'divisor' => $etsy->grandtotal['divisor'] ?? null
            ],
            payment_method: $etsy->payment_method,

            buyer_name: $etsy->name,
            address_line_1: $etsy->first_line,
            address_line_2: $etsy->second_line,
            city: $etsy->city,
            state: $etsy->state,
            postal_code: $etsy->zip,
            country_code: $etsy->country_iso,

            min_processing_days: $minProcessing,
            max_processing_days: $maxProcessing,
            expected_ship_date: $expectedShip,

            is_shipped: (bool)$etsy->is_shipped,
            transactions: $transactions,
            refunds: $etsy->refunds,
        );
    }

    public static function fromShopify(OrderShopifyData $shopify): self
    {
        $order_status = $shopify->fulfillment_status ?: $shopify->financial_status;
        $sales_channel = 'Shopify';
        $payment_method = $shopify->payment_gateway_names[0] ?? null;
        $buyer_name = null;
        if (!empty($shopify->customer['first_name']) || !empty($shopify->customer['last_name'])) {
            $buyer_name = trim(($shopify->customer['first_name'] ?? '') . ' ' . ($shopify->customer['last_name'] ?? ''));
        }

        $is_shipped = ($shopify->fulfillment_status === 'fulfilled');

        return new self(
            order_id: (string)$shopify->id,
            order_date: $shopify->created_at,
            update_date: $shopify->updated_at,
            order_status: $order_status,
            fulfillment: $shopify->fulfillment_status,
            sales_channel: $sales_channel,
            total: [
                'amount' => $shopify->total_price,
                'currency' => $shopify->currency,
            ],
            payment_method: $payment_method,

            buyer_name: $buyer_name,
            address_line_1: $shopify->shipping_address['address1'] ?? null,
            address_line_2: $shopify->shipping_address['address2'] ?? null,
            city: $shopify->shipping_address['city'] ?? null,
            state: $shopify->shipping_address['province'] ?? null,
            postal_code: $shopify->shipping_address['zip'] ?? null,
            country_code: $shopify->shipping_address['country_code'] ?? null,

            // Для Shopify отсутствуют эти параметры, оставим null
            min_processing_days: null,
            max_processing_days: null,
            expected_ship_date: null,

            is_shipped: $is_shipped,
            transactions: [],
            refunds: $shopify->refunds,
        );
    }

}

