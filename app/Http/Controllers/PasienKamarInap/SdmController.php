<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class SdmController extends Controller
{
    public function ambilDataSdm()
{
    $kodeRs   = '0801R002';
    $password = 'RSbw_2025';
    $md5pass  = md5($password);
    $timestamp = now()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');

    // Cek header dan timestamp sebelum kirim
    dd([
        'timestamp' => $timestamp,
        'headers' => [
            'X-rs-id'     => $kodeRs,
            'X-pass'      => $md5pass,
            'X-Timestamp' => $timestamp,
        ]
    ]);

    try {
        $response = Http::withHeaders([
            'X-rs-id'     => $kodeRs,
            'X-pass'      => $md5pass,
            'X-Timestamp' => $timestamp,
        ])->get('https://sirs.kemkes.go.id/fo/index.php/Fasyankes/sdm');

        if ($response->successful()) {
            $data = $response->json();
            return response()->json([
                'status' => 'sukses',
                'data' => $data['sdm'] ?? []
            ]);
        } else {
            return response()->json([
                'status' => 'gagal',
                'code' => $response->status(),
                'error' => $response->body()
            ], $response->status());
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'gagal',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
