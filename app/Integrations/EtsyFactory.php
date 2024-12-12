<?php

declare(strict_types=1);

namespace App\Integrations;

use App\Integrations\Adapters\Etsy\EtsyAdapter;
use App\Integrations\Mappers\Etsy\EtsyMapper;
use App\Integrations\Mappers\IntegrationMapperInterface;
use App\Models\ApiKey;

class EtsyFactory implements IntegrationFactoryInterface
{
    public function createAdapter(ApiKey $apiKey): EtsyAdapter
    {
        if ($apiKey->type !== ApiKey::TYPE_ETSY) {
            throw new \InvalidArgumentException('Invalid API key type');
        }

        return new EtsyAdapter($apiKey);
    }

    public function createMapper(): IntegrationMapperInterface
    {
        return new EtsyMapper;
    }
}
