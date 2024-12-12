<?php

declare(strict_types=1);

namespace App\Integrations;

use App\Integrations\Adapters\IntegrationAdapterInterface;
use App\Integrations\Mappers\IntegrationMapperInterface;
use App\Models\ApiKey;
use Spatie\LaravelData\Data;

interface IntegrationFactoryInterface
{
    public function createAdapter(ApiKey $apiKey): IntegrationAdapterInterface;

    public function createMapper(): IntegrationMapperInterface;
}
