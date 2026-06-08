<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LaporanKlaimIndividual extends Controller
{
    public function index()
    {
        return view('bpjs.laporanklaimindividual');
    }

    private function encryptData($data, $key)
    {
        $key = hex2bin($key);

        $iv = openssl_random_pseudo_bytes(16);

        $encrypted = openssl_encrypt(
            $data,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        $signature = substr(
            hash_hmac('sha256', $encrypted, $key, true),
            0,
            10
        );

        return base64_encode(
            $signature . $iv . $encrypted
        );
    }

    private function decryptData($encrypted, $key)
    {
        $key = hex2bin($key);

        $decoded = base64_decode(trim($encrypted));

        $iv = substr($decoded, 10, 16);
        $cipher = substr($decoded, 26);

        return openssl_decrypt(
            $cipher,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }

    private function requestInacbg(array $payload)
    {
        $json = json_encode($payload);

        $encrypted = $this->encryptData(
            $json,
            env('INACBG_KEY')
        );

        $response = Http::timeout(120)
            ->withHeaders([
                'Content-Type' => 'text/plain'
            ])
            ->withBody($encrypted, 'text/plain')
            ->post(env('INACBG_URL'));

        $body = trim($response->body());

        $jsonBody = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $jsonBody;
        }

        $decrypted = $this->decryptData(
            $body,
            env('INACBG_KEY')
        );

        return json_decode($decrypted, true);
    }

//     public function data(Request $request)
// {
//     try {

//         $tgl1 = $request->tgl1 ?: date('Y-m-d');
//         $tgl2 = $request->tgl2 ?: date('Y-m-d');

//         $payload = [
//             'metadata' => [
//                 'method' => 'search_claim'
//             ],
//             'data' => [
//                 'start_dt' => $tgl1,
//                 'stop_dt'  => $tgl2
//             ]
//         ];

//         $result = $this->requestInacbg($payload);

//         dd($result);

//     } catch (\Throwable $e) {

//         dd($e->getMessage());

//     }
// }

public function data(Request $request)
{
    dd('MASUK CONTROLLER');
}
}