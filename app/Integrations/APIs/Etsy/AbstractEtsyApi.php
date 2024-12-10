<?php

declare(strict_types=1);

namespace App\Integrations\APIs\Etsy;

use App\Integrations\APIs\AbstractApiRequest;
use App\Integrations\Traits\HasApiKey;
use App\Integrations\Traits\InteractsWithApiKey;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class AbstractEtsyApi implements HasApiKey
{
    use InteractsWithApiKey;

    protected function getClient(): \Etsy\Etsy
    {
        if (! $this->apiKey) {
            throw new RuntimeException('Api key is not set');
        }

        return new \Etsy\Etsy($this->apiKey->key->get('client_id'), $this->apiKey->additional_data->get('access_token'));
    }

    protected function getTradeServerLink(): string
    {
        return (string) 'https://api.etsy.com/v3/application/';
    }
}
