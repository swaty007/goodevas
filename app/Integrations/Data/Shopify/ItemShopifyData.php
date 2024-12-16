<?php

namespace App\Integrations\Data\Shopify;

use App\Integrations\Data\ItemDataInterface;
use App\Integrations\Data\ItemUnifiedData;
use Spatie\LaravelData\Data;

class ItemShopifyData extends Data implements ItemDataInterface
{
    public function __construct(
        public $id,
        public $title,
        public $quantity,
        public $sku,
        public ?string $barcode,
        public ?string $order_id,
    ) {}

    public static function convertToUnified(?ItemShopifyData $data = null): ItemUnifiedData
    {
        if (! $data instanceof ItemShopifyData) {
            throw new \InvalidArgumentException('Ожидался объект типа TransactionShopifyData');
        }

        return ItemUnifiedData::from([
            'item_id' => (string) $data->id,
            'api_order_id' => $data->order_id,
            'quantity' => $data->quantity,
            'title' => $data->title,
            'sku' => $data->sku ?? 'null',
            'barcode' => $data->barcode,
        ]);
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
