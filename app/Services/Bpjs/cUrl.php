<?php
namespace App\Services\Bpjs;

use Bpjs\Bridging\Icare\ConfigIcare;
use Bpjs\Bridging\Icare\ResponseIcare;

class cUrl
{
    protected $config;
    protected $response;
    protected $header;
    protected $headers;

    public function __construct()
    {
        // parent::__construct();
        $this->config = new ConfigIcare;
        $this->response = new ResponseIcare;
        $this->header = $this->config->setHeader();
    }
    public function requestIcare($endpoint, $headers, $method = "", $payload = "")
    {
        $headers = $this->setHeader($headers);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER =>  $headers,
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    protected function setHeader($headers)
    {
        $header = [];
        $header[] = 'Content-Type: application/json';
        $header[] = 'X-cons-id:' . $headers['X-cons-id'];
        $header[] = 'X-timestamp:' . $headers['X-timestamp'];
        $header[] = 'X-signature:' . $headers['X-signature'];
        $header[] = 'user_key:' . $headers['user_key'];
        return $header;
    }

    public function postRequest($endpoint, $data)
    {
        $result = $this->requestIcare($this->config->setUrl() . $endpoint, $this->header, "POST", $data);
        $result = $this->response->responseIcare($result, $this->config->keyDecrypt($this->header['X-timestamp']));
        return $result;
    }
}
