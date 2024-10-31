<?php

declare(strict_types=1);

namespace App\Facades\Ysell;

use App\Exceptions\InternalExchangeResponseException;
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
class YsellApiMethods extends AbstractYsellApi
{
    public function getProductList(int $page = 1, int $perPage = 100): array
    {
        $data = [
            'page' => $page,
            'per-page' => $perPage,
            'expand' => 'warehouseStock',
        ];
        $uri = '/product';
        $response = $this->sendRequest('GET', $uri, $data);

        return $response;
    }
}
