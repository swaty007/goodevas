<?php

declare(strict_types=1);

namespace App\Facades\Ysell;

use App\Facades\AbstractApiRequest;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class AbstractYsellApi extends AbstractApiRequest
{
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
}
