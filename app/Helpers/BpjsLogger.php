<?php

namespace App\Helpers;

use App\Models\LogBridgingBpjs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BpjsLogger
{
    /**
     * Catat request & response bridging BPJS ke database
     *
     * @param string $layanan (contoh: 'INA-CBG', 'MJKN', 'VCLAIM')
     * @param string $endpoint (URL yang dipanggil)
     * @param string $method (GET, POST, PUT, DELETE)
     * @param mixed $requestPayload (Data yang dikirim)
     * @param mixed $responsePayload (Data yang diterima)
     * @param int $statusCode (Kode HTTP Response)
     * @param float $startTime (Waktu mulai request dari microtime(true))
     * @return void
     */
    public static function log($layanan, $endpoint, $method, $requestPayload, $responsePayload, $statusCode, $startTime)
    {
        try {
            $endTime = microtime(true);
            $durasiMs = round(($endTime - $startTime) * 1000); // ms

            // Convert payload ke string/json jika berupa array atau object
            $reqString = is_string($requestPayload) ? $requestPayload : json_encode($requestPayload);
            $resString = is_string($responsePayload) ? $responsePayload : json_encode($responsePayload);

            LogBridgingBpjs::create([
                'layanan' => $layanan,
                'endpoint' => $endpoint,
                'method' => strtoupper($method),
                'request_payload' => $reqString,
                'response_payload' => $resString,
                'status_code' => $statusCode,
                'durasi_ms' => $durasiMs,
                'waktu_request' => Carbon::createFromTimestamp($startTime)->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            // Jangan hentikan aplikasi jika gagal insert log
            Log::error('Gagal insert LogBridgingBpjs: ' . $e->getMessage());
        }
    }
}
