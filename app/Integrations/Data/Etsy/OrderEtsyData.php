<?php

namespace App\Integrations\Data\Etsy;

use App\Integrations\Data\OrderDataInterface;
use App\Integrations\Data\OrderUnifiedData;
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
        /** @var TransactionEtsyData[] */
        public array $transactions,
        public array $refunds,
        public mixed $originalObject = null,

    ) {}

    public static function convertToUnified(?OrderEtsyData $data = null): OrderUnifiedData
    {
        if (! $data instanceof OrderEtsyData) {
            throw new \InvalidArgumentException('Ожидался объект типа OrderEtsyData');
        }
        // Преобразуем транзакции Etsy в унифицированный формат
        $transactions = array_map(fn ($t) => $t::convertToUnified($t), $data->transactions);

        // Агрегируем min_processing_days, max_processing_days и expected_ship_date из транзакций
        // Предполагается, что min_processing_days, max_processing_days, expected_ship_date - это массивы в транзакциях,
        // содержащие числовые значения (для min/max) или даты (для expected_ship_date).
        // Возьмём минимальное/максимальное значение по всем транзакциям, а для expected_ship_date - самую раннюю дату.

        $allMinDays = [];
        $allMaxDays = [];
        $allShipDates = [];

        foreach ($data->transactions as $t) {
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

        return new OrderUnifiedData(
            order_id: (string) $data->receipt_id,
            order_date: $data->created_timestamp ? gmdate('c', $data->created_timestamp) : null,
            update_date: $data->updated_timestamp ? gmdate('c', $data->updated_timestamp) : null,
            order_status: $data->status,
            fulfillment: null, // У Etsy нет аналога FulfillmentChannel
            sales_channel: 'Etsy',
            total: [
                'amount' => $data->grandtotal['amount'] ?? null,
                'currency' => $data->grandtotal['currency'] ?? null,
                'divisor' => $data->grandtotal['divisor'] ?? null,
            ],
            payment_method: $data->payment_method,

            buyer_name: $data->name,
            address_line_1: $data->first_line,
            address_line_2: $data->second_line,
            city: $data->city,
            state: $data->state,
            postal_code: $data->zip,
            country_code: $data->country_iso,

            min_processing_days: $minProcessing,
            max_processing_days: $maxProcessing,
            expected_ship_date: $expectedShip,

            is_shipped: (bool) $data->is_shipped,
            transactions: $transactions,
            refunds: $data->refunds,
            originalObject: $data->originalObject,
        );
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
