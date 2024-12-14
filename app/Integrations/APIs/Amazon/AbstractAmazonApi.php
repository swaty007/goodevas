<?php

declare(strict_types=1);

namespace App\Integrations\APIs\Amazon;

use App\Integrations\APIs\AbstractApiRequest;
use App\Integrations\Traits\HasApiKey;
use App\Integrations\Traits\InteractsWithApiKey;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class AbstractAmazonApi extends AbstractApiRequest implements HasApiKey
{
    use InteractsWithApiKey;

    protected function getClient(): PendingRequest
    {
        if (! $this->apiKey) {
            throw new RuntimeException('Api key is not set');
        }
        $clientId = $this->apiKey->key->get('client_id');
        $clientSecret = $this->apiKey->key->get('client_secret');

        $authKey = Cache::remember('amazon_api_token:'."$clientId-$clientSecret", 3500, function () use ($clientId, $clientSecret) {
            $json = Http::withOptions([
                'headers' => [
                    'Content-type' => 'application/json',
                ],
                'timeout' => $this->timeout, // Response timeout
                'connect_timeout' => 10, // Connection timeout
            ])
                ->post('https://api.amazon.com/auth/o2/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $this->apiKey->key->get('refresh_token'),
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ])
                ->throw()
                ->json();

            $this->apiKey->key->refresh_token = $json['refresh_token'];
            $this->apiKey->additional_data = $json;
            $this->apiKey->save();

            return $json['access_token'];
        });

        return Http::withOptions([
            'headers' => [
                'Content-type' => 'application/json',
                'x-amz-access-token' => $authKey,
            ],
            'base_uri' => $this->getTradeServerLink(),
            'timeout' => $this->timeout, // Response timeout
            'connect_timeout' => 10, // Connection timeout
        ]);
    }

    protected function getTradeServerLink(): string
    {
        $region = $this->apiKey->key->get('region') ?? 'eu';

        return (string) "https://sellingpartnerapi-$region.amazon.com/";
    }
}

//SPA_ID_EU=amzn1.application-oa2-client.df01e9a96e0f484fa65381743d14c260
//SPA_SECRET_EU=amzn1.oa2-cs.v1.0dddc452ee5a707e6d4c72354621556c901be3dbcec666f7ca328ef07ca04263
//SPA_REFRESH_EU=Atzr|IwEBIHMkgPoU_J6oMy7NFIkdyOipWe0UjSpfTUFiY40O-Rg8SPRZ4BY0RtSJhBRo712MnIdUKWgD_TuSinEC3ygdyGiB6Zc1We_vRtsbMdFOo58_urHK5ufGKN6pcd4uhuLrT60Dy95NhbU3FpicvNF_4SVa86NDGMkbve4Dj7PbBhGBWM2piYCEWY_K_NMT7f045Cp1wnFX2GAt_yQCpNdyGHP1JYeo64BTe_r7UupNzK5ELQfPeD9ZlGBIRjG93DvG6-vSIb2CHKAnQEfQixVYj58LVpeUmaRC_APcIFmEulh5-HATFckkgD2azP86OtUk3P3RimB_Kgd3pxvu2-fnAEFQ

//SPA_ID_US=amzn1.application-oa2-client.1b71843c76d54d38b5193d1251a01269
//SPA_SECRET_US=amzn1.oa2-cs.v1.6aee08b52b970b663eda2f0a60a64c46c5b4c2f3e261aab6e5093291733ab222
//SPA_REFRESH_US=Atzr|IwEBIB8PbvfUIxFCfAkeHIAvEklVScyUbjoofc0TjckB8sk3RbbjzZcNktZsFtwOd88cCVf8iHz6D1wtOOExBTWZvPOXiq80cJsO9j1MaePAoI7npbiy61O1M5ESu2Xfu_pPZnKpUDZLuV3M9l2u7-uAvdB8LxjWI0Ph66OA-_h7M5R4WNVF79yhk3SP3LVqDitYqEiutt5GpC-HvsKobGA30UXb8H_vHhNNCBES4uHmowGHozEqpWlWRmeFT76TPl-OCov6POmm8RsaRFN8B-eSk49DJSOTAULoKPNnI7CbvzzmzJ-00zlqJYyiPh86DKAQtqk
