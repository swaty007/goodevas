<?php

declare(strict_types=1);

namespace App\Integrations\APIs\Shopify;

use App\Exceptions\InternalExchangeResponseException;
use App\Integrations\APIs\IntegrationApiInterface;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Shopify\Clients\PageInfo;

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
class ShopifyApiMethods extends AbstractShopifyApi implements IntegrationApiInterface
{
    public function getOrdersList(Carbon $createdMin, ?Carbon $createdMax = null, int $page = 1, int $perPage = 250, ?PageInfo $pageInfo = null): array
    {
        if ($perPage > 250) {
            throw new RuntimeException('Per page limit is 250');
        }
        $client = $this->getClient();
        if ($pageInfo) {
            $response = $client->get(path: 'orders', query: $pageInfo->getNextPageQuery());
        } else {
            $response = $client->get(path: 'orders', query: [
                'limit' => $perPage,
                'status' => 'any',
                'created_at_min' => $createdMin->toISOString(),
                'created_at_max' => $createdMax?->toISOString(),
            ]);
        }

        $pageInfoNext = $response->getPageInfo();

        //        $uri = '/admin/api/2024-10/orders.json?status=any';
        //        $response = $this->sendRequest('GET', $uri, ['limit' => $perPage]);
        return [
            'body' => $response->getDecodedBody(),
            'pageInfo' => $pageInfoNext,
        ];
        $fieldsString = collect($this->getFieldList('Order'))->pluck('name')->implode("\n");
        $query = <<<QUERY
  query {
    orders(first: 10, query: "updated_at:>2019-12-01") {
      edges {
        cursor
        node {
          $fieldsString
        }
      }
        pageInfo {
          endCursor
          hasNextPage
          hasPreviousPage
          startCursor
        }
    }
  }
QUERY;
    }

    public function getOrdersCount(): array
    {
        $client = $this->getClient();
        $response = $client->get(path: 'orders/count', query: ['status' => 'any']);

        return $response->getDecodedBody();
        $query = <<<'QUERY'
  query OrdersCount {
    ordersCount(limit: 10000) {
      count
      precision
    }

  }
QUERY;
    }

    public function getFieldList(string $type): array
    {
        return Cache::remember('shopify_fields:'.$type, now()->addDay(), function () use ($type) {
            $client = $this->getClient();
            $query = <<<QUERY
    query {
        __type(name: "$type") {
            fields {
                name
                type {
                name
                kind
                }
            }
        }
    }
QUERY;
            $client = $this->getClient();
            $response = $client->query([
                'query' => $query,
            ]);

            dd($response->getDecodedBody()['data']);

            return $response->getDecodedBody()['data']['__type']['fields'];
        });
    }
}
