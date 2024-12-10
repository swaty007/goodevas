<?php

declare(strict_types=1);

namespace App\Integrations\APIs\Etsy;

use App\Integrations\APIs\IntegrationApiInterface;

class EtsyApiMethods extends AbstractEtsyApi implements IntegrationApiInterface
{
    public function getOrdersList(int $page = 1, int $perPage = 100): array {
        $client = $this->getClient();
        $receipts = \Etsy\Resources\Receipt::all($this->apiKey->key->get('shop_id'));
    }
}
