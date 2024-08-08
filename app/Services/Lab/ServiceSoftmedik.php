<?php

namespace App\Services\Lab;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class ServiceSoftmedik
{

    public $url;
    public $version;
    public $user_id;
    public $key;
    public function __construct()
    {
        $dotenv = Dotenv::createUnsafeImmutable(getcwd());
        $dotenv->safeLoad();

        $this->url = getenv('URL_SOFTMEDIX');
        $this->version = getenv('SOFTMEDIX_VERSION');
        $this->user_id = getenv('SOFTMEDIX_USERID');
        $this->key = getenv('SOFTMEDIX_KEY');
    }

    public function url()
    {
        return $this->url;
    }
    public function version()
    {
        return $this->version;
    }
    public function user_id()
    {
        return $this->user_id;
    }
    public function key()
    {
        return $this->key;
    }

    public function ServiceSoftmedixPOST($sendToLis)
    {
        $client = new Client();

        try {
            $response = $client->post(self::url() . '/wslis/bridging/order', [
                'json' => $sendToLis,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            $responseBody = $response->getBody();
            $responseData = json_decode($responseBody, true);

            dd($responseData);
        } catch (\Exception $e) {
            dd('Error: ' . $e->getMessage());
        }
    }
}
