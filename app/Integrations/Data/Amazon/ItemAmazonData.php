<?php

namespace App\Integrations\Data\Amazon;

use App\Integrations\Data\ItemDataInterface;
use App\Integrations\Data\ItemUnifiedData;
use Spatie\LaravelData\Data;

class ItemAmazonData extends Data implements ItemDataInterface
{
    public function __construct(
        public string $Title,
        public $ProductInfo,
        public string $OrderItemId,
        public ?string $AmazonOrderId,
        public string $ASIN,
        public ?string $SellerSKU,
        public int $QuantityOrdered,
    ) {}

    public static function convertToUnified(?ItemAmazonData $data = null): ItemUnifiedData
    {
        if (! $data instanceof ItemAmazonData) {
            throw new \InvalidArgumentException('Ожидался объект типа TransactionAmazonData');
        }

        return ItemUnifiedData::from([
            'item_id' => $data->OrderItemId,
            'api_order_id' => $data->AmazonOrderId,
            'quantity' => $data->QuantityOrdered,
            'title' => $data->Title,
            'sku' => $data->SellerSKU,
        ]);
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
