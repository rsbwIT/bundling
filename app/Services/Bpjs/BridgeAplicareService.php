<?php

namespace App\Services\Bpjs;

use Dotenv\Dotenv;
use Bpjs\Bridging\GenerateBpjs;

class BridgeAplicareService
{
    protected $urlEndpoint;
    protected $consId;
    protected $secretKey;

    public function __construct()
    {
        $dotenv = Dotenv::createUnsafeImmutable(base_path());
        $dotenv->safeLoad();

        $this->urlEndpoint = rtrim(getenv('API_BPJS_APLICARE'), '/') . '/';
        $this->consId = getenv('CONS_ID');
        $this->secretKey = getenv('SECRET_KEY');
    }

    protected function request($endpoint, $method = 'GET', $data = null)
    {
        $maxAttempts = 3;
        $url = $this->urlEndpoint . ltrim($endpoint, '/');
        $payload = ($data !== null) ? (is_string($data) ? $data : json_encode($data)) : null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $timestamp = (string) (new \DateTime('now', new \DateTimeZone('UTC')))->getTimestamp();
            $signature = base64_encode(hash_hmac('sha256', $this->consId . '&' . $timestamp, $this->secretKey, true));

            $headers = [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-cons-id: ' . $this->consId,
                'X-timestamp: ' . $timestamp,
                'X-signature: ' . $signature,
            ];

            $ch = curl_init($url);
            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_CUSTOMREQUEST => $method,
            ];

            if ($payload !== null) {
                $options[CURLOPT_POSTFIELDS] = $payload;
            }

            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            $err = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($result !== false && $result !== '' && $code >= 200 && $code < 500) {
                return $result;
            }

            if ($attempt < $maxAttempts) {
                usleep(500000); // Wait 500ms before retrying
            }
        }

        return $result;
    }

    public function getRequest($endpoint)
    {
        return $this->request($endpoint, 'GET');
    }

    public function postRequest($endpoint, $data)
    {
        return $this->request($endpoint, 'POST', $data);
    }

    public function putRequest($endpoint, $data)
    {
        return $this->request($endpoint, 'PUT', $data);
    }

    public function deleteRequest($endpoint, $data)
    {
        return $this->request($endpoint, 'DELETE', $data);
    }
}
