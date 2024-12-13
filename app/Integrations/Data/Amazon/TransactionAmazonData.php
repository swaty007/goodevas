<?php

namespace App\Integrations\Data\Amazon;

use App\Integrations\Data\TransactionDataInterface;
use App\Integrations\Data\TransactionUnifiedData;
use Spatie\LaravelData\Data;

class TransactionAmazonData extends Data implements TransactionDataInterface
{
    public function __construct(
        public string $Title, // NumberOfItems
        public $ProductInfo, // NumberOfItems
        public string $OrderItemId, // NumberOfItems
        public string $ASIN,
        public ?string $SellerSKU,
        public int $QuantityOrdered,
    ) {}

    public static function convertToUnified(?TransactionAmazonData $data = null): TransactionUnifiedData
    {
        if (! $data instanceof TransactionAmazonData) {
            throw new \InvalidArgumentException('Ожидался объект типа TransactionAmazonData');
        }

        return new TransactionUnifiedData(
            id: null, // Amazon не предоставляет transaction_id напрямую
            title: $data->Title,
            quantity: $data->QuantityOrdered,
            sku: $data->SellerSKU,
            product_id: null,
            asin: $data->ASIN,
            seller_sku: $data->SellerSKU,
            quantity_ordered: $data->QuantityOrdered,
            order_item_id: $data->OrderItemId,
            shipping_method: [],
        );
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
