<?php

namespace App\Http\Controllers\BriggingBpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Faceid extends Controller
{
    public function frista(Request $request)
    {
        $tanggal = date('Y-m-d'); // default hari ini
        // kalau mau pilih tanggal manual bisa pakai $request->input('tanggal')

        // 🔑 isi credential dari BPJS
        $cons_id    = "26519";   // isi cons_id Anda
        $secretKey  = "3kB2D07001";   // isi secret_key Anda
        $user_key   = "5e51bb79a4c6abcacde7ab1c48362adc";   // isi user_key Anda

        $tStamp     = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature  = base64_encode(hash_hmac('sha256', $cons_id . "&" . $tStamp, $secretKey, true));

        $headers = [
            "X-cons-id: " . $cons_id,
            "X-timestamp: " . $tStamp,
            "X-signature: " . $signature,
            "user_key: " . $user_key,
            "Content-Type: application/json; charset=utf-8"
        ];

        $url = "https://new-api.bpjs-kesehatan.go.id:8080/new-vclaim-rest/Rujukan/Frista/tgl/" . $tanggal;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $list = [];
        $meta = [];

        if ($httpCode == 200 && $response) {
            $result = json_decode($response, true);
            $meta   = $result['metaData'] ?? [];

            if (isset($result['response']['list'])) {
                $list = $result['response']['list'];
            }
        }

        return view('livewire.briging-bpjs.faceid', compact('list', 'tanggal', 'meta'));
    }
}
