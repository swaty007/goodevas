<?php

declare(strict_types=1);

namespace App\Integrations\Adapters\Shopify;

use App\Integrations\Adapters\IntegrationAdapterInterface;
use App\Integrations\APIs\Shopify\ShopifyApiMethods;
use App\Models\ApiKey;
use Illuminate\Support\Carbon;

class ShopifyAdapter extends ShopifyApiMethods implements IntegrationAdapterInterface
{
    public function __construct(ApiKey $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function fetchOrders(?Carbon $createdMin = null, ?Carbon $createdMax = null, array $options = []): array
    {
        $createdMin = $createdMin ?? now()->subDays(30);
        $data = $this->getOrdersList(createdMin: $createdMin, createdMax: $createdMax, options: $options);
        $data['hasNextPage'] = (bool) $data['hasNextPage'];
        $data['options'] = [
            'pageInfo' => $data['pageInfo'],
        ];

        //        dd($data['pageInfo'], $data['body']['orders']);
        return $data;
    }
}
