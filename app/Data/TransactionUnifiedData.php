<?php
namespace App\Data;

use App\Integrations\Data\Amazon\TransactionAmazonData;
use App\Integrations\Data\Etsy\TransactionEtsyData;
use App\Integrations\Data\Shopify\TransactionShopifyData;
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
        public array $shipping_method = [],
    ) {}

    public static function fromAmazon(TransactionAmazonData $amazon): self
    {
        return new self(
            id: null, // Amazon не предоставляет transaction_id напрямую
            title: $amazon->Title,
            quantity: $amazon->QuantityOrdered,
            sku: $amazon->SellerSKU,
            product_id: null,
            asin: $amazon->ASIN,
            seller_sku: $amazon->SellerSKU,
            quantity_ordered: $amazon->QuantityOrdered,
            order_item_id: $amazon->OrderItemId,
            shipping_method: [],
        );
    }

    public static function fromEtsy(TransactionEtsyData $etsy): self
    {
        return new self(
            id: (string)$etsy->transaction_id,
            title: $etsy->title,
            quantity: $etsy->quantity,
            sku: $etsy->sku,
            product_id: $etsy->product_id,
            asin: null,
            seller_sku: null,
            quantity_ordered: $etsy->quantity,
            order_item_id: null,
            shipping_method: $etsy->shipping_method,
        );
    }

    public static function fromShopify(TransactionShopifyData $shopify): self
    {
        return new self(
            id: (string)$shopify->id,
            title: $shopify->title,
            quantity: $shopify->quantity,
            sku: $shopify->sku,
            product_id: $shopify->product_id,
            quantity_ordered: $shopify->quantity,
            order_item_id: (string)$shopify->id
        );
    }
}
