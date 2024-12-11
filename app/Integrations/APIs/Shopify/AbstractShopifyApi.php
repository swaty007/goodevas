<?php

declare(strict_types=1);

namespace App\Integrations\APIs\Shopify;

use App\Integrations\APIs\AbstractApiRequest;
use App\Integrations\Traits\HasApiKey;
use App\Integrations\Traits\InteractsWithApiKey;
use RuntimeException;
use Shopify\Auth\FileSessionStorage;
use Shopify\Auth\Session;
use Shopify\Clients\Graphql;
use Shopify\Clients\Rest;
use Shopify\Context;

abstract class AbstractShopifyApi implements HasApiKey // extends AbstractApiRequest
{
    use InteractsWithApiKey;

    protected function getClient(): Rest
    {
        if (! $this->apiKey) {
            throw new RuntimeException('Api key is not set');
        }
        Context::initialize(
            apiKey: $this->apiKey->key->get('api_key'),
            apiSecretKey: $this->apiKey->key->get('api_secret_key'),
            scopes: ['orders', 'marketplace_orders', '*'],
            hostName: $this->apiKey->key->get('domain').'.myshopify.com',
            sessionStorage: new FileSessionStorage(storage_path('app/shopify')),
            apiVersion: '2024-10',
            isEmbeddedApp: true,
            isPrivateApp: false,
        );

        //        $session = new Session(
        //            id: '1',
        //            shop: $this->apiKey->key->get('domain'),
        //            isOnline: true,
        //            state: 'state'
        //        );

        return new Rest($this->apiKey->key->get('domain').'.myshopify.com', $this->apiKey->key->get('access_token'));
        //        return new Graphql($this->apiKey->key->get('domain').'.myshopify.com', $this->apiKey->key->get('access_token'));

        //        return Http::withOptions([
        //            'headers' => [
        //                'Content-type' => 'application/json',
        //                'X-Shopify-Access-Token' => $this->apiKey->key->get('access_token'),
        //            ],
        //            'base_uri' => $this->getTradeServerLink(),
        //            'timeout' => $this->timeout, // Response timeout
        //            'connect_timeout' => 10, // Connection timeout
        //        ]);
    }

    protected function getTradeServerLink(): string
    {
        return (string) 'https://'.$this->apiKey->key->get('domain').'.myshopify.com/';
    }
}
