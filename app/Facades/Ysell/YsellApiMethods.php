<?php

declare(strict_types=1);

namespace App\Facades\Ysell;

use App\Exceptions\InternalExchangeResponseException;
use App\Facades\YsellApiFacade;
use App\Models\Ysell;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Facades\Cache;
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
    public function getProductList(int $page = 1, int $perPage = 50): array
    {
        if ($perPage > 50) {
            throw new RuntimeException('Per page limit is 50');
        }
        $data = [
            'page' => $page,
            'per-page' => $perPage,
            'expand' => 'warehouseStock',
        ];
        $uri = '/product';
        $response = $this->sendRequest('GET', $uri, $data);

        return $response;
    }

    public function getProductAllByAllYsellKeys(): array
    {
        $products = Cache::remember('products', 60 * 5, function () {
            $ysellKeys = Ysell::all();
            $productsApi = [];
            foreach ($ysellKeys as $ysellKey) {
                /* @var $ysellKey Ysell */
                YsellApiFacade::switchAuthKey($ysellKey->api_key);
                $productsApiKey = Cache::remember('products_'.$ysellKey->api_key, 60 * 5, function () {
                    return YsellApiFacade::getAllProducts();
                });
                $productsApi = array_merge($productsApi, $productsApiKey);
            }

            return $productsApi;
        });

        return $products;
    }
}
