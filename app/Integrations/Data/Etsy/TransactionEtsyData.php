<?php

namespace App\Integrations\Data\Etsy;

use App\Integrations\Data\TransactionDataInterface;
use App\Integrations\Data\TransactionUnifiedData;
use Spatie\LaravelData\Data;

class TransactionEtsyData extends Data implements TransactionDataInterface
{
    public function __construct(
        public $transaction_id,
        public $title,
        public $quantity,
        public $sku, // double by +
        public $product_id,
        //        array $price,
        public ?array $shipping_method,
        public ?int $min_processing_days,
        public ?int $max_processing_days,
        public ?int $expected_ship_date,
    ) {}

    public static function convertToUnified(?TransactionEtsyData $data = null): TransactionUnifiedData
    {
        if (! $data instanceof TransactionEtsyData) {
            throw new \InvalidArgumentException('Ожидался объект типа TransactionEtsyData');
        }

        return new TransactionUnifiedData(
            id: (string) $data->transaction_id,
            title: $data->title,
            quantity: $data->quantity,
            sku: $data->sku,
            product_id: $data->product_id,
            asin: null,
            seller_sku: null,
            quantity_ordered: $data->quantity,
            order_item_id: null,
            shipping_method: $data->shipping_method,
        );
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
