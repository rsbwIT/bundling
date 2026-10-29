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
        // SEMENTARA DIMATIKAN: tampilkan pesan dashboard belum bisa diakses.
        // Hapus baris return di bawah untuk mengaktifkan kembali.
        return view('bpjs.monitoring_signal_off');

        // Daftar Endpoint Layanan Terintegrasi
        $services = [
            // Kategori: BPJS Kesehatan Cloud
            [
                'id' => 'vclaim',
                'name' => 'VClaim BPJS',
                'category' => 'bpjs',
                'category_label' => 'BPJS Cloud',
                'description' => 'Bridging SEP, Rujukan & Kontrol',
                'url' => env('API_BPJS_VCLAIM', 'https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/'),
                'icon' => 'fas fa-heartbeat',
                'accent' => '#0284c7'
            ],
            [
                'id' => 'antrean',
                'name' => 'Antrean JKN RS',
                'category' => 'bpjs',
                'category_label' => 'BPJS Cloud',
                'description' => 'Antrean Online Mobile JKN & Poli',
                'url' => env('API_BPJS_ANTROL', 'https://apijkn.bpjs-kesehatan.go.id/antreanrs/'),
                'icon' => 'fas fa-users-cog',
                'accent' => '#059669'
            ],
            [
                'id' => 'icare',
                'name' => 'I-Care JKN',
                'category' => 'bpjs',
                'category_label' => 'BPJS Cloud',
                'description' => 'Riwayat Pelayanan Pasien JKN',
                'url' => env('API_BPJS_ICARE', 'https://apijkn.bpjs-kesehatan.go.id/wsihs/'),
                'icon' => 'fas fa-notes-medical',
                'accent' => '#7c3aed'
            ],
            [
                'id' => 'aplicare',
                'name' => 'Aplicare BPJS',
                'category' => 'bpjs',
                'category_label' => 'BPJS Cloud',
                'description' => 'Ketersediaan Bed Rawat Inap',
                'url' => env('API_BPJS_APLICARE', 'https://new-api.bpjs-kesehatan.go.id/'),
                'icon' => 'fas fa-bed',
                'accent' => '#d97706'
            ],
            [
                'id' => 'fingerprint',
                'name' => 'Fingerprint BPJS',
                'category' => 'bpjs',
                'category_label' => 'BPJS Cloud',
                'description' => 'Validasi Biometrik Sidik Jari',
                'url' => env('BPJS_FP_URL', 'https://fp.bpjs-kesehatan.go.id/finger-rest/'),
                'icon' => 'fas fa-fingerprint',
                'accent' => '#dc2626'
            ],
            [
                'id' => 'mjkn_fktl',
                'name' => 'MJKN FKTL RS',
                'category' => 'bpjs',
                'category_label' => 'BPJS Cloud',
                'description' => 'Gateway Endpoint Mobile JKN FKTL',
                'url' => env('MJKN_RS', 'https://bpjsfktl.rsbumiwaras.co.id'),
                'icon' => 'fas fa-shield-alt',
                'accent' => '#0891b2'
            ],

            // Kategori: Server Lokal & SIMRS
            [
                'id' => 'inacbg',
                'name' => 'INA-CBG E-Klaim',
                'category' => 'local',
                'category_label' => 'Server Lokal / SIMRS',
                'description' => 'Web Service Grouper & Tarif Klaim',
                'url' => env('INACBG_URL', 'http://192.168.1.153/E-Klaim/ws.php'),
                'icon' => 'fas fa-file-invoice-dollar',
                'accent' => '#2563eb'
            ],
            [
                'id' => 'web_apps',
                'name' => 'Server Web Apps',
                'category' => 'local',
                'category_label' => 'Server Lokal / SIMRS',
                'description' => 'Aplikasi Berkas Rawat & Digitalisasi',
                'url' => env('API_WEBAPPS_TEST', 'ping://192.168.5.88'),
                'icon' => 'fas fa-server',
                'accent' => '#4f46e5'
            ],
            [
                'id' => 'server_khanza',
                'name' => 'Server DB Khanza',
                'category' => 'local',
                'category_label' => 'Server Lokal / SIMRS',
                'description' => 'Database Utama SIMRS Rumah Sakit',
                'url' => env('API_KHANZA_TEST', 'ping://192.168.10.88'),
                'icon' => 'fas fa-database',
                'accent' => '#0d9488'
            ],
            [
                'id' => 'softmedix',
                'name' => 'LIS Lab Softmedix',
                'category' => 'local',
                'category_label' => 'Server Lokal / SIMRS',
                'description' => 'Laboratorium Information System',
                'url' => 'ping://' . (env('URL_SOFTMEDIX', '192.168.12.37')),
                'icon' => 'fas fa-vial',
                'accent' => '#db2777'
            ],

            // Kategori: Koneksi Jaringan & Gateway Internet
            [
                'id' => 'iforte',
                'name' => 'Internet Utama (iForte 1)',
                'category' => 'network',
                'category_label' => 'Jaringan & ISP',
                'description' => 'Koneksi Fiber Optik Utama RS',
                'url' => env('API_IFORTE_TEST', 'ping://103.182.206.142'),
                'icon' => 'fas fa-wifi',
                'accent' => '#16a34a'
            ],
            [
                'id' => 'iforte_2',
                'name' => 'Internet Backup (iForte 2)',
                'category' => 'network',
                'category_label' => 'Jaringan & ISP',
                'description' => 'Koneksi Cadangan / Failover ISP',
                'url' => env('API_IFORTE_TEST_2', 'ping://103.182.206.141'),
                'icon' => 'fas fa-network-wired',
                'accent' => '#eab308'
            ]
        ];

        // Ringkasan data untuk header & status card (Default ringan tanpa query database yang berat)
        $todayIncidentsCount = 0;
        $unresolvedCount = 0;
        $recentLogs = collect();
        $setting = null;
        $bpjsTvStatus = null;

        // Ambil setting rumah sakit dengan cache agar tidak query database berulang kali
        try {
            $setting = \Illuminate\Support\Facades\Cache::remember('setting_instansi_light', 3600, function() {
                return DB::table('setting')->first();
            });
        } catch (Exception $e) {
            // Ignore
        }

        try {
            if (class_exists(\App\Http\Controllers\AntrianPendaftaran\AntrianPendaftaranBaru::class)) {
                $bpjsTvStatus = \App\Http\Controllers\AntrianPendaftaran\AntrianPendaftaranBaru::getBpjsTvStatus();
            }
        } catch (Exception $e) {
            // Ignore
        }

        return view('bpjs.monitoring_signal', compact(
            'services',
            'todayIncidentsCount',
            'unresolvedCount',
            'recentLogs',
            'setting',
            'bpjsTvStatus'
        ));
    }

    public function checkSignal(Request $request)
    {
        // Tutup session agar request AJAX bisa berjalan paralel (tidak saling tunggu)
        session_write_close();
        
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
        $errorMessage = null;

        if (strpos($url, 'ping://') === 0) {
            $host = substr($url, 7);
            
            // ⚡ FAST SOCKET PROBE: Sangat cepat (<5ms), native PHP tanpa spawn cmd.exe
            // Mendukung port web, database, SSH, Windows RPC/SMB LAN
            $ports = [80, 443, 3306, 22, 135, 445, 8080];
            $connected = false;
            
            foreach ($ports as $port) {
                $socket = @fsockopen($host, $port, $errno, $errstr, 0.25);
                if ($socket) {
                    fclose($socket);
                    $connected = true;
                    break;
                }
            }
            
            if ($connected) {
                $isOnline = true;
                $statusCode = 200;
                $latencyMs = max(1, round((microtime(true) - $startTime) * 1000));
            } else {
                // Fallback ping singkat (timeout 250ms) agar Apache thread tidak tertahan
                $output = [];
                $result = -1;
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    exec("ping -n 1 -w 250 " . escapeshellarg($host), $output, $result);
                } else {
                    exec("ping -c 1 -W 1 " . escapeshellarg($host), $output, $result);
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
                } else {
                    $errorMessage = 'Host Unreachable';
                }
            }
        } else {
            try {
                // Timeout 1.5 detik sangat cepat
                $response = Http::connectTimeout(1.2)
                    ->timeout(1.5)
                    ->withOptions([
                        'verify' => false,
                        'headers' => [
                            'User-Agent' => 'BPJS-Monitoring-RSBW/2.0'
                        ]
                    ])
                    ->get($url);
                
                $statusCode = $response->status();
                $isOnline = true;
                
            } catch (Exception $e) {
                $isOnline = false;
                $errorMessage = $e->getMessage();
            }

            $endTime = microtime(true);
            $latencyMs = round(($endTime - $startTime) * 1000);
        }

        return response()->json([
            'id' => $request->input('id'),
            'status' => $isOnline ? 'online' : 'offline',
            'latency' => $latencyMs,
            'status_code' => $statusCode,
            'timestamp' => now()->format('H:i:s'),
            'message' => $errorMessage
        ]);
    }

    public function getLogs(Request $request)
    {
        $query = DB::table('log_monitoring_bpjs')
            ->orderBy('waktu_gangguan', 'desc');

        if ($request->has('layanan') && !empty($request->layanan)) {
            $query->whereIn('service_id', (array)$request->layanan);
        }

        if ($request->has('tanggal_awal') && $request->tanggal_awal != '') {
            $query->whereDate('waktu_gangguan', '>=', $request->tanggal_awal);
            
            if ($request->has('tanggal_akhir') && $request->tanggal_akhir != '') {
                $query->whereDate('waktu_gangguan', '<=', $request->tanggal_akhir);
            } else {
                // Jika hanya tanggal awal yang diisi, asumsikan filter 1 hari itu saja
                $query->whereDate('waktu_gangguan', '<=', $request->tanggal_awal);
            }
        } else {
            $query->limit(100);
        }
            
        return response()->json($query->get());
    }

    public function exportPdf(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', date('Y-m-d'));
        $tanggal_akhir = $request->input('tanggal_akhir', date('Y-m-d'));
        $layanan = $request->input('layanan');

        $query = DB::table('log_monitoring_bpjs')->orderBy('waktu_gangguan', 'desc');

        if ($request->filled('layanan') && !empty($request->layanan)) {
            $query->whereIn('service_id', (array)$request->layanan);
        }

        if ($request->filled('tanggal_awal')) {
            $query->whereDate('waktu_gangguan', '>=', $tanggal_awal);
            if ($request->filled('tanggal_akhir')) {
                $query->whereDate('waktu_gangguan', '<=', $tanggal_akhir);
            } else {
                $query->whereDate('waktu_gangguan', '<=', $tanggal_awal);
                $tanggal_akhir = $tanggal_awal;
            }
        } else {
            $query->whereDate('waktu_gangguan', '>=', $tanggal_awal)
                  ->whereDate('waktu_gangguan', '<=', $tanggal_akhir);
        }

        $logs = $query->get();
        $getSetting = DB::table('setting')->first();

        // Gunakan facade barryvdh dompdf
        $pdf = \PDF::loadView('bpjs.monitoring_signal_pdf', compact('logs', 'getSetting', 'tanggal_awal', 'tanggal_akhir'));
        
        return $pdf->stream('Laporan_Riwayat_Gangguan_Koneksi_BPJS_' . $tanggal_awal . '.pdf');
    }
}
