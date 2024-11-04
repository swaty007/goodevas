<?php

declare(strict_types=1);

namespace App\Facades\Ysell;

use App\Exceptions\InternalExchangeResponseException;
use App\Traits\TelegramSystemLogTrait;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class AbstractYsellApi
{
    use TelegramSystemLogTrait;

    private string $authKey;

    protected int $timeout = 10;

    public function switchAuthKey(string $newAuthKey): void
    {
        $this->authKey = $newAuthKey;
    }

    protected function getClient(): PendingRequest
    {
        if (! $this->authKey) {
            throw new RuntimeException('Auth key is not set');
        }

        return Http::withOptions([
            'headers' => [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer '.$this->authKey,
            ],
            'base_uri' => $this->getTradeServerLink(),
            'timeout' => $this->timeout, // Response timeout
            'connect_timeout' => 10, // Connection timeout
        ]);
    }

    protected function getTradeServerLink(): string
    {
        return (string) 'https://daansamol.ysell.pro/api/v1/';
    }

    /**
     * @return array|int[]|array[]
     *
     * @throws InternalExchangeResponseException
     */
    protected function sendRequest(string $method = 'POST', string $uri = '', ?array $params = [], ?bool $throwError = false): array
    {
        try {
            $response = $this->callServer($method, $uri, $params);
        } catch (InternalExchangeResponseException $exception) {
            throw $exception;
        }

        return $response;
    }

    private function callServer(string $method = 'post', string $uri = '', array $requestData = []): array
    {
        $response = [];
        try {
            $result = $this->getClient();
            if ($method === 'GET') {
                $result->retry(3, 500, function (Exception $exception, PendingRequest $request) {
                    return $exception instanceof ConnectionException;
                });
            }

            $response = $result->{$method}($uri, $requestData)->throw()->json();
        } catch (HttpClientException $exception) {
            $this->log($exception, array_merge([
                'method' => $method,
                'uri' => $uri,
            ], $requestData));
        }

        return $response ?? [];
    }

    /**
     * @param  array[][]  $requestData
     *
     * @throws Exception
     */
    private function log(Exception $exception, array $requestData): void
    {
        try {
            $message = $exception->response->json()['message'];
        } catch (Exception $e) {
            $message = $exception->getMessage();
        }
        $code = $exception->getCode();
        if ($exception instanceof ConnectionException) {
            throw new InternalExchangeResponseException(json_encode([
                'type' => __('Trading').' ConnectionException',
                'message' => $message,
                'request_data' => $requestData,
                'code' => $code,
            ], JSON_UNESCAPED_UNICODE), (int) $code, $exception);
        }

        throw new InternalExchangeResponseException(json_encode([
            'message' => $message,
            'request_data' => $requestData,
            'code' => $code,
        ], JSON_UNESCAPED_UNICODE), (int) $code, $exception);
        //        throw $exception;
    }
}
