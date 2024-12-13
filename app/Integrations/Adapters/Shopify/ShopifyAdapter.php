<?php

declare(strict_types=1);

namespace App\Integrations\Adapters\Shopify;

use App\Integrations\Adapters\IntegrationAdapterInterface;
use App\Integrations\APIs\Shopify\ShopifyApiMethods;
use App\Models\ApiKey;

class ShopifyAdapter extends ShopifyApiMethods implements IntegrationAdapterInterface
{
    public function __construct(ApiKey $apiKey)
    {
        $this->setApiKey($apiKey);
    }

    public function fetchOrders(): array
    {
        $createdMin = now()->subDays(30);
        $data = $this->getOrdersList(createdMin: $createdMin);
        if ($data['pageInfo']) {
            // $this->getOrdersList(createdMin: $createdMin, pageInfo: $data['pageInfo']);
        }
//        dd($data['pageInfo'], $data['body']['orders']);
        return $data['body']['orders'];
    }
}
