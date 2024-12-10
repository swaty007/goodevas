<?php

declare(strict_types=1);

namespace App\Integrations\APIs\Amazon;

use App\Exceptions\InternalExchangeResponseException;
use App\Integrations\APIs\IntegrationApiInterface;
use GuzzleHttp\Exception\TransferException;
use RuntimeException;

/**
 * Success response example:
 * [
 *  'error' => null,
 *  'result' => [...],
 *  'id' => 1
 * ]
 *
 * @throws TransferException: if there is an error with server calling (Guzzle exception)
 * @throws InternalExchangeResponseException: if there is an error on external server side (Example: balance not enough)
 * @throws RuntimeException: if there is an error with processing response or given data
 */
class AmazonApiMethods extends AbstractAmazonApi implements IntegrationApiInterface
{
    public function getOrdersList(int $page = 1, int $perPage = 100): array
    {
        if ($perPage > 100) {
            throw new RuntimeException('Per page limit is 50');
        }
        $data = [
            'page' => $page,
            'per-page' => $perPage,
            'MarketplaceIds' => 'A1RKKUPIHCS9HS', // https://developer-docs.amazon.com/sp-api/docs/marketplace-ids
            'CreatedAfter' => now()->subDays(7)->toIso8601String(),
        ];
        $uri = '/orders/v0/orders';
        $response = $this->sendRequest('GET', $uri, $data);

        return $response;
    }

    public function getOrderDetails(string $order_id): array
    {
        $uri = "orders/v0/orders/$order_id/orderItems";
        $response = $this->sendRequest('GET', $uri);

        return $response;
    }
}
