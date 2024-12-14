<?php

namespace App\Integrations\Data;

use Spatie\LaravelData\Data;

class ItemUnifiedData extends Data
{
    public function __construct(
        public ?string $order_item_id,
        public ?string $api_order_id,
        public ?int $quantity,
        public ?string $title,
        public ?string $sku,
        public ?int $quantity_ordered = null,
        public ?array $shipping_method = [],
    ) {}
}
