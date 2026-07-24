<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class MonitoringSignalController extends Controller
{
    public function index()
    {
        // Mendapatkan URL dari .env
        $services = [
            [
                'id' => 'vclaim',
                'name' => 'VClaim BPJS',
                'url' => env('API_BPJS_VCLAIM', 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/'),
                'icon' => 'fas fa-heartbeat'
            ],
            [
                'id' => 'antrean',
                'name' => 'Antrean JKN',
                'url' => env('API_BPJS_ANTROL', 'https://apijkn.bpjs-kesehatan.go.id/antreanrs/'),
                'icon' => 'fas fa-users'
            ],
            [
                'id' => 'icare',
                'name' => 'I-Care JKN',
                'url' => env('API_BPJS_ICARE', 'https://apijkn.bpjs-kesehatan.go.id/wsihs/'),
                'icon' => 'fas fa-notes-medical'
            ],
            [
                'id' => 'aplicare',
                'name' => 'Aplicare',
                'url' => env('API_BPJS_APLICARE', 'https://new-api.bpjs-kesehatan.go.id/'),
                'icon' => 'fas fa-bed'
            ],
            [
                'id' => 'inacbg',
                'name' => 'INA-CBG (Lokal)',
                'url' => env('INACBG_URL', 'http://192.168.1.153/E-Klaim/ws.php'),
                'icon' => 'fas fa-file-invoice-dollar'
            ],
            [
                'id' => 'fingerprint',
                'name' => 'Fingerprint BPJS',
                'url' => env('BPJS_FP_URL', 'https://fp.bpjs-kesehatan.go.id/finger-rest/'),
                'icon' => 'fas fa-fingerprint'
            ]
        ];

        return view('bpjs.monitoring_signal', compact('services'));
    }

    public function checkSignal(Request $request)
    {
        $url = $request->input('url');
        
        if (!$url) {
            return response()->json([
                'status' => 'error',
                'message' => 'URL tidak valid'
            ]);
        }

        $startTime = microtime(true);
        $isOnline = false;
        $statusCode = null;

        try {
            // Melakukan HTTP GET request sederhana untuk mengecek konektivitas.
            // Timeout diset 5 detik agar tidak membebani server
            $response = Http::timeout(5)
                ->withOptions(['verify' => false]) // Abaikan SSL check untuk lingkungan lokal/uji
                ->get($url);
            
            // Apapun HTTP statusnya, jika server memberikan respon, berarti server "UP" / Online
            $statusCode = $response->status();
            $isOnline = true;
            
        } catch (Exception $e) {
            // Jika masuk catch (biasanya karena Timeout atau Connection Refused), maka "DOWN" / Offline
            $isOnline = false;
        }

        $endTime = microtime(true);
        $latencyMs = round(($endTime - $startTime) * 1000);

        return response()->json([
            'status' => $isOnline ? 'online' : 'offline',
            'latency' => $latencyMs,
            'status_code' => $statusCode
        ]);
    }
}
