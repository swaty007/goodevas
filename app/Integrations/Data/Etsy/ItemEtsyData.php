<?php

namespace App\Integrations\Data\Etsy;

use App\Integrations\Data\ItemDataInterface;
use App\Integrations\Data\ItemUnifiedData;
use Spatie\LaravelData\Data;

class ItemEtsyData extends Data implements ItemDataInterface
{
    public function __construct(
        public $transaction_id,
        public $title,
        public $quantity,
        public $sku, // double by +
        public $receipt_id,
        //        array $price,
        public ?array $shipping_method,
        public ?int $min_processing_days,
        public ?int $max_processing_days,
        public ?int $expected_ship_date,
    ) {}

    public static function convertToUnified(?ItemEtsyData $data = null): ItemUnifiedData
    {
        if (! $data instanceof ItemEtsyData) {
            throw new \InvalidArgumentException('Ожидался объект типа TransactionEtsyData');
        }

        return ItemUnifiedData::from([
            'item_id' => (string) $data->transaction_id,
            'api_order_id' => $data->receipt_id,
            'quantity' => $data->quantity,
            'title' => $data->title,
            'sku' => $data->sku,
            'barcode' => $data->sku,
        ]);
    }
}

// 'cc' (credit card), 'paypal', 'check', 'mo' (money order), 'bt' (bank transfer), 'other', 'ideal', 'sofort', 'apple_pay', 'google', 'android_pay', 'google_pay', 'klarna', 'k_pay_in_4' (klarna), 'k_pay_in_3' (klarna), or 'k_financing' (klarna).
