<?php

declare(strict_types=1);

namespace App\Integrations\Adapters\Etsy;

use App\Integrations\Adapters\IntegrationAdapterInterface;
use App\Integrations\APIs\Etsy\EtsyApiMethods;
use App\Models\ApiKey;

class EtsyAdapter extends EtsyApiMethods implements IntegrationAdapterInterface
{
    public function __construct(ApiKey $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function fetchOrders(): array
    {
        $createdMin = now()->subDays(30);
        $data = $this->getOrdersList(createdMin: $createdMin);

        return $data['orders'];
    }
}
