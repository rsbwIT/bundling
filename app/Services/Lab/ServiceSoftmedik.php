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
    public $client;
    public function __construct()
    {
        $this->client = new Client();
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


        try {
            $response = $this->client->post(self::url() . '/wslis/bridging/order', [
                'json' => $sendToLis,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            $responseBody = $response->getBody();
            return json_decode($responseBody, true);
        } catch (\Exception $e) {
            dd('Error: ' . $e->getMessage());
        }
    }

    public function ServiceSoftmedixGet($noorder)
    {
        try {
            $response = $this->client->get(self::url() . '/wslis/bridging/result/'.self::user_id().'/'.self::key().'/'.$noorder);
            $responseBody = $response->getBody();
            return json_decode($responseBody, true);
        } catch (\Exception $e) {
            dd('Error: ' . $e->getMessage());
        }
    }
}
