<?php

namespace App\Integrations\Data\Shopify;

use Spatie\LaravelData\Data;

class TransactionShopifyData extends Data
{
    public function __construct(
        public $id,
        public $title,
        public $quantity,
        public $sku,
        public $product_id
    ) {}
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
