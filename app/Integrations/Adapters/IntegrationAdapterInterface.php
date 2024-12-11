<?php

declare(strict_types=1);

namespace App\Integrations\Adapters;

use App\Integrations\Traits\HasApiKey;
use App\Models\ApiKey;

interface IntegrationAdapterInterface extends HasApiKey
{
    public function __construct(ApiKey $apiKey);

    public function fetchOrders(): array;
}
