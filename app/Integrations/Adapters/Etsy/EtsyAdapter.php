<?php

declare(strict_types=1);

namespace App\Integrations\Adapters\Etsy;

use App\Integrations\Adapters\IntegrationAdapterInterface;
use App\Integrations\APIs\Etsy\EtsyApiMethods;
use App\Models\ApiKey;
use Illuminate\Support\Carbon;

class EtsyAdapter extends EtsyApiMethods implements IntegrationAdapterInterface
{
    public function __construct(ApiKey $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function fetchOrders(?Carbon $createdMin = null, ?Carbon $createdMax = null, array $options = []): array
    {
        $createdMin = $createdMin ?? now()->subDays(30);

        $perPage = 100;
        $data = $this->getOrdersList(createdMin: $createdMin, createdMax: $createdMax, perPage: $perPage, options: $options);

        $page = $options['page'] ?? 1;
        $count = $data['count'];

        $data['hasNextPage'] = $count > $perPage * $page;
        $data['options'] = [
            'page' => $page + 1,
        ];

        return $data;
    }
}
