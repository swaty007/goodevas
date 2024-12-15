<?php

declare(strict_types=1);

namespace App\Integrations\APIs\Etsy;

use App\Integrations\APIs\IntegrationApiInterface;
use Illuminate\Support\Carbon;
use RuntimeException;

class EtsyApiMethods extends AbstractEtsyApi implements IntegrationApiInterface
{
    public function getOrdersList(Carbon $createdMin, ?Carbon $createdMax = null, int $perPage = 100, array $options = []): array
    {
        if ($perPage > 100) {
            throw new RuntimeException('Per page limit is 100');
        }
        $page = $options['page'] ?? 1;

        $client = $this->getClient();

        $receipts = \Etsy\Resources\Receipt::all((int) $this->apiKey->key->get('shop_id'), [
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
            // 'min_created' => $createdMin->getTimestamp(),
            // 'max_created' => $createdMax?->getTimestamp(),
            'min_last_modified' => $createdMin->getTimestamp(),
            'max_last_modified' => $createdMax?->getTimestamp(),
        ]);

        return [
            'orders' => $receipts->data,
            'count' => $receipts->count,
        ];
    }
}
