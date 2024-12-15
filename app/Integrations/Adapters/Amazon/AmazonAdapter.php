<?php

declare(strict_types=1);

namespace App\Integrations\Adapters\Amazon;

use App\Integrations\Adapters\IntegrationAdapterInterface;
use App\Integrations\APIs\Amazon\AmazonApiMethods;
use App\Models\ApiKey;
use Illuminate\Support\Carbon;

class AmazonAdapter extends AmazonApiMethods implements IntegrationAdapterInterface
{
    public function __construct(ApiKey $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function fetchOrders(?Carbon $createdMin = null, ?Carbon $createdMax = null, array $options = []): array
    {
        $createdMin = $createdMin ?? now()->subDays(30);
        $data = $this->getOrdersList(createdMin: $createdMin, createdMax: $createdMax, options: $options);

        $data['hasNextPage'] = (bool) $data['NextToken'];
        $data['options'] = [
            'NextToken' => $data['NextToken'],
        ];

        return $data;
    }
}
