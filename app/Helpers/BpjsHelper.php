<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BpjsHelper
{
    public static function getToken()
    {
        // Cek cache dulu
        if (Cache::has('bpjs_token')) {
            return Cache::get('bpjs_token');
        }

        try {
            $client = new Client();
            $response = $client->request('GET', env('BPJS_BASEURL') . '/auth', [
                'headers' => [
                    'x-username' => env('BPJS_USERNAME'),
                    'x-password' => env('BPJS_PASSWORD'),
                ],
                'timeout' => 10,
            ]);

            $result = json_decode($response->getBody(), true);

            if (isset($result['token'])) {
                $token = $result['token'];
                Cache::put('bpjs_token', $token, now()->addMinutes(55));
                return $token;
            }

            Log::error('Token BPJS tidak ditemukan dalam response.');
            return null;
        } catch (\Exception $e) {
            Log::error('Gagal ambil token BPJS: ' . $e->getMessage());
            return null;
        }
    }
}
