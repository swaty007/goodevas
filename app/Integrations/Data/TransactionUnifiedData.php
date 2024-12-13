<?php

namespace App\Integrations\Data;

use Spatie\LaravelData\Data;

class TransactionUnifiedData extends Data
{
    public function __construct(
        public ?string $id,
        public ?string $title,
        public ?int $quantity,
        public ?string $sku,
        public ?string $product_id,
        public ?string $asin = null,
        public ?string $seller_sku = null,
        public ?int $quantity_ordered = null,
        public ?string $order_item_id = null,
        public ?array $shipping_method = [],
    ) {}
}
