<?php

declare(strict_types=1);

namespace App\Integrations\Adapters\Amazon;

use App\Integrations\Adapters\IntegrationAdapterInterface;
use App\Integrations\APIs\Amazon\AmazonApiMethods;
use App\Models\ApiKey;

class AmazonAdapter extends AmazonApiMethods implements IntegrationAdapterInterface
{
    public function __construct(ApiKey $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function fetchOrders(): array
    {
        $createdMin = now()->subDays(30);
        $data = $this->getOrdersList(createdMin: $createdMin);
        dd($data);
    }
}
