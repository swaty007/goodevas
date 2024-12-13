<?php

namespace App\Integrations\Data\Shopify;

use App\Integrations\Data\TransactionDataInterface;
use App\Integrations\Data\TransactionUnifiedData;
use Spatie\LaravelData\Data;

class TransactionShopifyData extends Data implements TransactionDataInterface
{
    public function __construct(
        public $id,
        public $title,
        public $quantity,
        public $sku,
        public $product_id
    ) {}

    public static function convertToUnified(?TransactionShopifyData $data = null): TransactionUnifiedData
    {
        if (! $data instanceof TransactionShopifyData) {
            throw new \InvalidArgumentException('Ожидался объект типа TransactionShopifyData');
        }

        return new TransactionUnifiedData(
            id: (string) $data->id,
            title: $data->title,
            quantity: $data->quantity,
            sku: $data->sku,
            product_id: $data->product_id,
            quantity_ordered: $data->quantity,
            order_item_id: (string) $data->id
        );
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
