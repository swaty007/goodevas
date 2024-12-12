<?php

namespace App\Integrations\Data\Etsy;

use Spatie\LaravelData\Data;

class OrderEtsyData extends Data
{
    /**
     * @param array<int, TransactionEtsyData> $transactions
     */
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
        public array $transactions,
        public array $refunds,

    ) {}
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
