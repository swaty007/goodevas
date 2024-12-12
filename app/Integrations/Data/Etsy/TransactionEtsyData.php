<?php

namespace App\Integrations\Data\Etsy;

use Spatie\LaravelData\Data;

class TransactionEtsyData extends Data
{
    public function __construct(
        public $transaction_id,
        public $title,
        public $quantity,
        public $sku, // double by +
        public $product_id,
//        array $price,
        array $shipping_method,
        array $min_processing_days,
        array $max_processing_days,
        array $expected_ship_date,
    ) {}
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
