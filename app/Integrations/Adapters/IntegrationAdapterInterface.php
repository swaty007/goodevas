<?php

declare(strict_types=1);

namespace App\Integrations\Adapters;

use App\Models\ApiKey;

interface IntegrationAdapterInterface
{
    public function __construct(ApiKey $apiKey);

    public function fetchOrders(): array;
}
