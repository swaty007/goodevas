<?php

declare(strict_types=1);

namespace App\Integrations;

use App\Models\ApiKey;

class IntegrationFactory
{
    public static function make(string $type): IntegrationFactoryInterface
    {
        return match ($type) {
            ApiKey::TYPE_AMAZON => new AmazonFactory,
            ApiKey::TYPE_SHOPIFY => new ShopifyFactory,
            ApiKey::TYPE_ETSY => new EtsyFactory,
            default => throw new \InvalidArgumentException("Unsupported type: $type"),
        };
    }
}
