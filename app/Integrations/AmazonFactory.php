<?php

declare(strict_types=1);

namespace App\Integrations;

use App\Integrations\Adapters\Amazon\AmazonAdapter;
use App\Integrations\Mappers\Amazon\AmazonMapper;
use App\Integrations\Mappers\IntegrationMapperInterface;
use App\Models\ApiKey;

class AmazonFactory implements IntegrationFactoryInterface
{
    public function createAdapter(ApiKey $apiKey): AmazonAdapter
    {
        if ($apiKey->type !== ApiKey::TYPE_AMAZON) {
            throw new \InvalidArgumentException('Invalid API key type');
        }

        return new AmazonAdapter($apiKey);
    }

    public function createMapper(): IntegrationMapperInterface
    {
        return new AmazonMapper;
    }
}
