<?php

declare(strict_types=1);

namespace App\Integrations\Adapters\Etsy;

use App\Integrations\Adapters\IntegrationAdapterInterface;
use App\Integrations\APIs\Amazon\AmazonApiMethods;
use App\Integrations\Traits\HasApiKey;
use App\Models\ApiKey;

class EtsyAdapter extends AmazonApiMethods implements HasApiKey, IntegrationAdapterInterface
{
    public function __construct(ApiKey $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function fetchOrders(): array {}
}
