<?php

declare(strict_types=1);

namespace App\Integrations;

use App\Integrations\Adapters\Shopify\ShopifyAdapter;
use App\Integrations\Mappers\IntegrationMapperInterface;
use App\Models\ApiKey;

class ShopifyFactory implements IntegrationFactoryInterface
{
    public function createAdapter(ApiKey $apiKey): ShopifyAdapter
    {
        if ($apiKey->type !== ApiKey::TYPE_SHOPIFY) {
            throw new \InvalidArgumentException('Invalid API key type');
        }

        return new ShopifyAdapter($apiKey);
    }

    public function createMapper(): IntegrationMapperInterface
    {
        return new ShopifyMapper;
    }
}
