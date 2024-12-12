<?php

namespace App\Integrations\Data\Shopify;

use Spatie\LaravelData\Data;

class OrderShopifyData extends Data
{
    public function __construct(
        public $id,
        public $created_at,
        public $updated_at,
        public $financial_status,
        public $fulfillment_status,
        public $total_price,
        public $currency,
        public array $payment_gateway_names,
        public ?array $customer,
        public ?array $shipping_address,
        public array $line_items,
        public array $refunds,
    ) {}
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
