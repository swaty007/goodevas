<?php

namespace App\Integrations\Data\Amazon;

use Spatie\LaravelData\Data;

class TransactionAmazonData extends Data
{
    public function __construct(
        public $Title, // NumberOfItems
        public $ProductInfo, // NumberOfItems
        public $OrderItemId, // NumberOfItems
        public $ASIN,
        public $SellerSKU,
        public $QuantityOrdered,
    ) {}
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
