<?php

declare(strict_types=1);

namespace App\Facades\Etsy;

use App\Facades\AbstractApiRequest;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class AbstractEtsyApi extends AbstractApiRequest
{

    protected function getClient(): PendingRequest
    {
        if (! $this->authKey) {
            throw new RuntimeException('Auth key is not set');
        }

        return Http::withOptions([
            'headers' => [
                'Content-type' => 'application/json',
                'x-api-key' => $this->authKey, // jzae6zlwpzxaany52klvyr33
            ],
            'base_uri' => $this->getTradeServerLink(),
            'timeout' => $this->timeout, // Response timeout
            'connect_timeout' => 10, // Connection timeout
        ]);
    }

    protected function getTradeServerLink(): string
    {
        return (string) 'https://api.etsy.com/v3/application/';
    }
}
