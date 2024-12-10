<?php

declare(strict_types=1);

namespace App\Integrations\APIs\Shopify;

use App\Integrations\APIs\AbstractApiRequest;
use App\Integrations\Traits\HasApiKey;
use App\Integrations\Traits\InteractsWithApiKey;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class AbstractShopifyApi extends AbstractApiRequest implements HasApiKey
{
    use InteractsWithApiKey;

    protected string $domain;

    public function setClientData(string $domain): void
    {
        $this->domain = $domain;
    }

    protected function getClient(): PendingRequest
    {
        if (! $this->apiKey) {
            throw new RuntimeException('Api key is not set');
        }

        return Http::withOptions([
            'headers' => [
                'Content-type' => 'application/json',
                'X-Shopify-Access-Token' => $this->apiKey->key->get('access_token'),
            ],
            'base_uri' => $this->getTradeServerLink(),
            'timeout' => $this->timeout, // Response timeout
            'connect_timeout' => 10, // Connection timeout
        ]);
    }

    protected function getTradeServerLink(): string
    {
        return (string) 'https://'.$this->apiKey->key->get('domain').'.myshopify.com/';
    }
}
