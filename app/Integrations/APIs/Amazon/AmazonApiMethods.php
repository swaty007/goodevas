<?php

declare(strict_types=1);

namespace App\Integrations\APIs\Amazon;

use App\Exceptions\InternalExchangeResponseException;
use App\Integrations\APIs\IntegrationApiInterface;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Carbon;
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
    public function getOrdersList(Carbon $createdMin, ?Carbon $createdMax = null, int $page = 1, int $perPage = 100, $withItems = true): array
    {
        if ($perPage > 100) {
            throw new RuntimeException('Per page limit is 100');
        }
        $data = [
            'page' => $page,
            'per-page' => $perPage,
            'MarketplaceIds' => implode(',', $this->apiKey->key->get('marketplace_ids')), // https://developer-docs.amazon.com/sp-api/docs/marketplace-ids
            // 'CreatedAfter' => $createdMin->toISOString(),
            // 'CreatedBefore' => $createdMax?->toISOString(),
            'LastUpdatedAfter' => $createdMin->toISOString(),
            'LastUpdatedBefore' => $createdMax?->toISOString(),
        ];
        $uri = '/orders/v0/orders';
        $response = $this->sendRequest('GET', $uri, $data);

        $orders = $response['payload']['Orders'];
        if ($withItems) {
            foreach ($orders as &$order) {
                $order['transactions'] = $this->getOrderDetails($order['AmazonOrderId']);
                sleep(2);
//                usleep(500000); // 0.5 sec
            }
        }

        return [
            'orders' => $orders,
            'NextToken' => $response['payload']['NextToken'] ?? null,
        ];
    }

    public function getOrderDetails(string $order_id): array
    {
        $uri = "orders/v0/orders/$order_id/orderItems";
        $response = $this->sendRequest('GET', $uri);

        return $response['payload']['OrderItems'];
    }
}
