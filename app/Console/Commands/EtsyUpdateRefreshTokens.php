<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EtsyUpdateRefreshTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:etsy-update-refresh-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $keys = ApiKey::where(['type' => 'etsy'])->get();
        foreach ($keys as $key) {
            $this->updateRefreshToken($key);
        }

    }

    private function updateRefreshToken(ApiKey $apiKey): void
    {
        $client = new \Etsy\OAuth\Client($apiKey->key->get('client_id'));
        $refresh_token = $apiKey->additional_data->get('refresh_token');
        if ($refresh_token) {
            $result = $client->refreshAccessToken($refresh_token);
            $accessToken = $result['access_token'];
            $refreshToken = $result['refresh_token'];
            $apiKey->additional_data = [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ];
            $apiKey->save();
        } else {
            Log::error('No refresh token for key: '.$apiKey->id);
        }
    }
}
