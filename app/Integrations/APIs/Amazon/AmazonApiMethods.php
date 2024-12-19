<?php

declare(strict_types=1);

namespace App\Integrations\APIs\Amazon;

use App\Exceptions\InternalExchangeResponseException;
use App\Integrations\APIs\IntegrationApiInterface;
use App\Models\ApiKey;
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
    public function getOrdersList(Carbon $createdMin, ?Carbon $createdMax = null, int $perPage = 30, array $options = [], $withItems = true): array
    {
        if ($perPage > 100) {
            throw new RuntimeException('Per page limit is 100');
        }
        $data = [
            'MaxResultsPerPage' => $perPage,
            'MarketplaceIds' => implode(',', $this->apiKey->key->get('marketplace_ids')), // https://developer-docs.amazon.com/sp-api/docs/marketplace-ids
            // 'CreatedAfter' => $createdMin->toISOString(),
            // 'CreatedBefore' => $createdMax?->toISOString(),
            'LastUpdatedAfter' => $createdMin->toISOString(),
            'LastUpdatedBefore' => $createdMax?->toISOString(),
            'NextToken' => $options['NextToken'] ?? null,
        ];
        $uri = '/orders/v0/orders';
        $response = $this->sendRequest('GET', $uri, $data);

        $orders = $response['payload']['Orders'];
        if ($withItems) {
            $apiKeysCount = ApiKey::where('type', ApiKey::TYPE_AMAZON)->count();
            foreach ($orders as &$order) {
                $order['items'] = $this->getOrderDetails($order['AmazonOrderId']);
                usleep((int) ((2.5 * $apiKeysCount) * 1000000)); // 2.5 sec
                $order['financialEvents'] = $this->getOrderFinancialEvents($order['AmazonOrderId']);
                usleep((int) ((2.5 * $apiKeysCount) * 1000000)); // 2.5 sec
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
        $data = $response['payload']['OrderItems'];
        foreach ($data as &$item) {
            $item['AmazonOrderId'] = $order_id;
        }

        return $data;
    }

    public function getOrderFinancialEvents(string $order_id): array
    {
        $uri = "finances/v0/orders/$order_id/financialEvents";
        $response = $this->sendRequest('GET', $uri);
        $data = $response['payload']['FinancialEvents'];

        return $data;
    }
}
