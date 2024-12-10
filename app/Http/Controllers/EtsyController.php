<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Response;

class EtsyController extends Controller
{
    public function oAuth(Request $request, ApiKey $apiKey): Response|RedirectResponse
    {
        if ($apiKey->type !== 'etsy') {
            abort(404);
        }
        $client = new \Etsy\OAuth\Client('jzae6zlwpzxaany52klvyr33');

        //        $etsy = new \Etsy\Etsy('jzae6zlwpzxaany52klvyr33', '336302751.VVU4NVlhW-gKfVCTDlLyBXPO4Yu2LYjTWtWOgm6F7HquDovtOuVzG0aN_PhsBLADWqVWtjzI2tTgPoIh_kj_lhjXVa');
        //        $user = \Etsy\Resources\Receipt::all(24163865);
        //        $user2 = \Etsy\Resources\Receipt::get(24163865, 3511659439);

        //        dd($user->data[0], $user2);

        [$verifier, $code_challenge] = $client->generateChallengeCode();
        Cache::put('etsy_verifier', $verifier, 120);
        $nonce = $client->createNonce();
        $url = $client->getAuthorizationUrl(
            route('etsy.auth-callback'),
            [
                'transactions_r', 'transactions_w',
            ],
            $code_challenge,
            $nonce
        );

        return redirect($url);
    }

    public function authCallback(Request $request): Response
    {
        try {
            $data = $request->all();
            $code = $data['code'];
            dump($data, $code);
            //$state = $data['state'];
            $client = new \Etsy\OAuth\Client('jzae6zlwpzxaany52klvyr33');
            $verifier = Cache::get('etsy_verifier');
            dump($verifier);
            $result = $client->requestAccessToken(
                route('etsy.auth-callback'),
                $code,
                $verifier
            );
            $accessToken = $result['access_token'];
            $refreshToken = $result['refresh_token'];
            dd($accessToken, $refreshToken);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getEtsyKeys(): \Illuminate\Http\JsonResponse
    {
        $keys = ApiKey::find(1);

        return response()->json($keys);
    }
}
