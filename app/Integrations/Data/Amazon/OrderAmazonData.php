<?php

namespace App\Integrations\Data\Amazon;

use Spatie\LaravelData\Data;

class OrderAmazonData extends Data
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

    ) {}
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
