<?php

declare(strict_types=1);

namespace App\Facades\Shopify;

use App\Facades\AbstractApiRequest;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class AbstractShopifyApi extends AbstractApiRequest
{

    protected function getClient(): PendingRequest
    {
        if (! $this->authKey) {
            throw new RuntimeException('Auth key is not set');
        }

        return Http::withOptions([
            'headers' => [
                'Content-type' => 'application/json',
                'X-Shopify-Access-Token' => $this->authKey, //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
            ],
            'base_uri' => $this->getTradeServerLink(),
            'timeout' => $this->timeout, // Response timeout
            'connect_timeout' => 10, // Connection timeout
        ]);
    }

    protected function getTradeServerLink(): string
    {
        return (string) 'https://goodevas.myshopify.com/';
    }
}

//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
//d59fc9b1332695765ebcc6660b6a5ef6
//fa7833590fdc653c856af0dfd0865aa2
