<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
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
            ],
            [
                'id' => 'iforte',
                'name' => 'Koneksi Internet (iForte 1)',
                'url' => env('API_IFORTE_TEST', 'ping://103.182.206.142'),
                'icon' => 'fas fa-wifi'
            ],
            [
                'id' => 'iforte_2',
                'name' => 'Koneksi Internet (iForte 2)',
                'url' => env('API_IFORTE_TEST_2', 'ping://103.182.206.141'),
                'icon' => 'fas fa-wifi'
            ],
            [
                'id' => 'web_apps',
                'name' => 'Server Web Apps',
                'url' => env('API_WEBAPPS_TEST', 'ping://192.168.5.88'),
                'icon' => 'fas fa-server'
            ],
            [
                'id' => 'server_khanza',
                'name' => 'Server Utama Khanza',
                'url' => env('API_KHANZA_TEST', 'ping://192.168.10.88'),
                'icon' => 'fas fa-database'
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
        $latencyMs = 0;

        if (strpos($url, 'ping://') === 0) {
            $host = substr($url, 7);
            $output = [];
            $result = -1;
            
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("ping -n 1 -w 5000 " . escapeshellarg($host), $output, $result);
            } else {
                exec("ping -c 1 -W 5 " . escapeshellarg($host), $output, $result);
            }
            
            $isOnline = ($result === 0);
            $statusCode = $isOnline ? 200 : null;
            $endTime = microtime(true);
            $latencyMs = round(($endTime - $startTime) * 1000);
            
            if ($isOnline) {
                $outputStr = implode(" ", $output);
                if (preg_match('/time[=<]\s*(\d+)\s*ms/i', $outputStr, $matches)) {
                    $latencyMs = (int)$matches[1];
                }
            }
        } else {
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
        }

        if (!$isOnline) {
            // Cek log terakhir untuk service ini agar tidak spam (jeda 10 menit)
            $lastLog = DB::table('log_monitoring_bpjs')
                ->where('service_id', $request->input('id'))
                ->where('waktu_gangguan', '>=', now()->subMinutes(10))
                ->first();

            if (!$lastLog) {
                DB::table('log_monitoring_bpjs')->insert([
                    'service_id' => $request->input('id'),
                    'service_name' => $request->input('name') ?? $url,
                    'url' => $url,
                    'waktu_gangguan' => now(),
                    'keterangan' => 'Disconnected / Timeout',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json([
            'status' => $isOnline ? 'online' : 'offline',
            'latency' => $latencyMs,
            'status_code' => $statusCode
        ]);
    }

    public function getLogs()
    {
        $logs = DB::table('log_monitoring_bpjs')
            ->orderBy('waktu_gangguan', 'desc')
            ->limit(100)
            ->get();
            
        return response()->json($logs);
    }
}
