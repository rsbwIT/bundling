<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KirimAntreanMjkn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mjkn:kirim-antrean {kodebooking?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim data antrean Mobile JKN ke BPJS. Bisa spesifik 1 nobooking jika argumen diisi.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $kodebooking = $this->argument('kodebooking');
        if ($kodebooking) {
            $this->info("Memulai pengecekan antrean spesifik: $kodebooking...");
        } else {
            $this->info('Memulai pengecekan semua antrean Mobile JKN yang Belum terkirim...');
        }
        
        $dateStart = now()->subDays(6)->format('Y-m-d');
        $dateEnd = now()->format('Y-m-d');
        
        $query = DB::table('referensi_mobilejkn_bpjs')
            ->join('reg_periksa', 'referensi_mobilejkn_bpjs.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter');
            
        if ($kodebooking) {
            $query->where('referensi_mobilejkn_bpjs.nobooking', $kodebooking);
        } else {
            $query->where('referensi_mobilejkn_bpjs.statuskirim', 'Belum')
                  ->whereBetween('referensi_mobilejkn_bpjs.tanggalperiksa', [$dateStart, $dateEnd]);
        }
        
        $antrean = $query->select(
                'referensi_mobilejkn_bpjs.nobooking',
                'referensi_mobilejkn_bpjs.nomorkartu',
                'referensi_mobilejkn_bpjs.nik',
                'referensi_mobilejkn_bpjs.nohp',
                'referensi_mobilejkn_bpjs.kodepoli',
                'poliklinik.nm_poli',
                'referensi_mobilejkn_bpjs.pasienbaru',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'referensi_mobilejkn_bpjs.tanggalperiksa',
                'referensi_mobilejkn_bpjs.kodedokter',
                'dokter.nm_dokter',
                'referensi_mobilejkn_bpjs.jampraktek',
                'referensi_mobilejkn_bpjs.jeniskunjungan',
                'referensi_mobilejkn_bpjs.nomorreferensi',
                'referensi_mobilejkn_bpjs.nomorantrean',
                'referensi_mobilejkn_bpjs.angkaantrean',
                'referensi_mobilejkn_bpjs.estimasidilayani',
                'referensi_mobilejkn_bpjs.sisakuotajkn',
                'referensi_mobilejkn_bpjs.kuotajkn',
                'referensi_mobilejkn_bpjs.sisakuotanonjkn',
                'referensi_mobilejkn_bpjs.kuotanonjkn'
            )
            ->orderBy('referensi_mobilejkn_bpjs.tanggalperiksa')
            ->get();
            
        if ($antrean->isEmpty()) {
            $this->info('Tidak ada antrean baru yang perlu dikirim.');
            return 0;
        }
        
        $baseUrl = rtrim(env('API_BPJS_ANTROL', 'https://apijkn.bpjs-kesehatan.go.id/antreanrs/'), '/');
        $consId = env('CONS_ID');
        $secretKey = env('SECRET_KEY');
        $userKey = env('USER_KEY_ANTROL');
        
        foreach ($antrean as $row) {
            try {
                $utc = time();
                $signature = $this->generateSignature($consId, $secretKey, $utc);
                
                $payload = [
                    "kodebooking" => $row->nobooking,
                    "jenispasien" => "JKN",
                    "nomorkartu" => $row->nomorkartu,
                    "nik" => $row->nik,
                    "nohp" => $row->nohp,
                    "kodepoli" => $row->kodepoli,
                    "namapoli" => $row->nm_poli,
                    "pasienbaru" => (int) $row->pasienbaru,
                    "norm" => $row->no_rkm_medis,
                    "tanggalperiksa" => $row->tanggalperiksa,
                    "kodedokter" => (int) $row->kodedokter,
                    "namadokter" => $row->nm_dokter,
                    "jampraktek" => $row->jampraktek,
                    "jeniskunjungan" => (int) substr($row->jeniskunjungan, 0, 1),
                    "nomorreferensi" => $row->nomorreferensi,
                    "nomorantrean" => $row->nomorantrean,
                    "angkaantrean" => (int) $row->angkaantrean,
                    "estimasidilayani" => (int) $row->estimasidilayani,
                    "sisakuotajkn" => (int) $row->sisakuotajkn,
                    "kuotajkn" => (int) $row->kuotajkn,
                    "sisakuotanonjkn" => (int) $row->sisakuotanonjkn,
                    "kuotanonjkn" => (int) $row->kuotanonjkn,
                    "keterangan" => "Peserta harap 30 menit lebih awal guna pencatatan administrasi."
                ];
                
                $this->info("Mengirim antrean {$row->nobooking}...");
                
                $response = Http::timeout(60)
                    ->withHeaders([
                        'x-cons-id' => $consId,
                        'x-timestamp' => $utc,
                        'x-signature' => $signature,
                        'user_key' => $userKey,
                    ])
                    ->post($baseUrl . '/antrean/add', $payload);
                    
                $result = $response->json();
                
                $this->info("Response BPJS: " . json_encode($result));
                
                if (isset($result['metadata']['code'])) {
                    $code = $result['metadata']['code'];
                    $msg = $result['metadata']['message'] ?? '';
                    $msgLower = strtolower($msg);
                    
                    // BPJS mengembalikan 208 jika duplikat (sudah pernah masuk)
                    // Jika pesannya mengandung "sudah terbit sep" atau "rujukan tidak valid", kita anggap "Selesai" saja 
                    // agar sistem tidak mengulang-ulang pengiriman yang pasti akan terus ditolak BPJS.
                    if ($code == 200 || $code == 208 || $msgLower == 'ok' || strpos($msgLower, 'sudah terbit sep') !== false || strpos($msgLower, 'rujukan tidak valid') !== false) {
                        
                        DB::table('referensi_mobilejkn_bpjs')
                            ->where('nobooking', $row->nobooking)
                            ->update([
                                'statuskirim' => 'Sudah',
                                'validasi' => date('Y-m-d H:i:s')
                            ]);
                            
                        if (strpos($msgLower, 'sudah terbit sep') !== false) {
                            $this->info("Dilewati (Sudah ada SEP): {$row->nobooking}");
                        } elseif (strpos($msgLower, 'rujukan tidak valid') !== false) {
                            $this->info("Dilewati (Rujukan Tidak Valid): {$row->nobooking}");
                        } else {
                            $this->info("Sukses: {$row->nobooking}");
                        }
                        
                        // Eksekusi Bypass Task 1-7
                        $this->autoBypassTask($row->nobooking, $row->tanggalperiksa, $row->no_rkm_medis, $row->no_rawat, $baseUrl, $consId, $secretKey, $userKey);
                        
                    } else {
                        $this->error("Gagal: {$row->nobooking} - " . $msg);
                        Log::error('KIRIM ANTREAN BPJS ERROR', [
                            'kodebooking' => $row->nobooking,
                            'payload' => $payload,
                            'response' => $result
                        ]);
                    }
                } else {
                    $this->error("Gagal: {$row->nobooking} - Format respon tidak sesuai");
                }
                
            } catch (\Exception $e) {
                $this->error("Error: {$row->nobooking} - {$e->getMessage()}");
                Log::error('KIRIM ANTREAN BPJS EXCEPTION', [
                    'kodebooking' => $row->nobooking,
                    'message' => $e->getMessage()
                ]);
            }
        }
        
        $this->info('Pengecekan selesai.');
        return 0;
    }
    
    private function generateSignature($consid, $key, $utc)
    {
        $data = $consid . "&" . $utc;
        $hash = hash_hmac('sha256', $data, $key, true);
        return base64_encode($hash);
    }
    
    private function autoBypassTask($kodebooking, $tanggalperiksa, $no_rkm_medis, $no_rawat, $baseUrl, $consId, $secretKey, $userKey)
    {
        try {
            $this->info(" -> Memulai Task 1-7 otomatis berdasarkan data riil untuk: $kodebooking");

            $lastTaskWaktu = 0;
            // Tarik task id existing dari BPJS agar kita tahu waktu terakhirnya
            $existingTasks = $this->getListTaskFromBpjs($baseUrl, $consId, $secretKey, $userKey, $kodebooking);
            if (!empty($existingTasks)) {
                foreach ($existingTasks as $t) {
                    if (isset($t['waktu'])) {
                        $timeStr = str_replace(' WIB', '', $t['waktu']);
                        $epochMs = strtotime($timeStr) * 1000;
                        if ($epochMs > $lastTaskWaktu) {
                            $lastTaskWaktu = $epochMs;
                        }
                    }
                }
            }
            $this->info("    - [DEBUG] lastTaskWaktu: " . $lastTaskWaktu);
            
            // Task 1 & 2 dari reg_periksa
            $loket = DB::table('reg_periksa')
                ->where('no_rawat', $no_rawat)
                ->first();
                
            if ($loket) {
                $waktuReg = strtotime($loket->tgl_registrasi . ' ' . $loket->jam_reg) * 1000;
                if ($waktuReg <= $lastTaskWaktu) $waktuReg = $lastTaskWaktu + 1000;
                $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $kodebooking, 1, $waktuReg);
                $lastTaskWaktu = $waktuReg;
                
                $waktuTask2 = $waktuReg + 60000;
                if ($waktuTask2 <= $lastTaskWaktu) $waktuTask2 = $lastTaskWaktu + 1000;
                $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $kodebooking, 2, $waktuTask2);
                $lastTaskWaktu = $waktuTask2;
            }
            
            // Task 3 & 4 dari mutasi_berkas
            $mutasi = DB::table('mutasi_berkas')
                ->where('no_rawat', $no_rawat)
                ->where('dikirim', '>', '1970-01-01 00:00:00')
                ->first();
                
            if ($mutasi) {
                $waktuTask3 = strtotime($mutasi->dikirim) * 1000;
                if ($waktuTask3 <= $lastTaskWaktu) $waktuTask3 = $lastTaskWaktu + 1000;
                $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $kodebooking, 3, $waktuTask3);
                $lastTaskWaktu = $waktuTask3;
                
                if ($mutasi->diterima > '1970-01-01 00:00:00') {
                    $waktuTask4 = strtotime($mutasi->diterima) * 1000;
                    if ($waktuTask4 <= $lastTaskWaktu) $waktuTask4 = $lastTaskWaktu + 1000;
                    $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $kodebooking, 4, $waktuTask4);
                    $lastTaskWaktu = $waktuTask4;
                }
            }
            
            // Task 5 dari pemeriksaan_ralan
            $ralan = DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $no_rawat)
                ->first();
                
            if ($ralan) {
                $waktuTask5 = strtotime($ralan->tgl_perawatan . ' ' . $ralan->jam_rawat) * 1000;
                if ($waktuTask5 <= $lastTaskWaktu) $waktuTask5 = $lastTaskWaktu + 1000;
                $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $kodebooking, 5, $waktuTask5);
                $lastTaskWaktu = $waktuTask5;
            }
            
            // Task 6 & 7 dari resep_obat
            $resep = DB::table('resep_obat')
                ->where('no_rawat', $no_rawat)
                ->first();
                
            if ($resep) {
                $waktuTask6 = strtotime($resep->tgl_peresepan . ' ' . $resep->jam_peresepan) * 1000;
                if ($waktuTask6 <= $lastTaskWaktu) {
                    $waktuTask6 = $lastTaskWaktu + 1000;
                }
                $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $kodebooking, 6, $waktuTask6);
                $lastTaskWaktu = $waktuTask6;
                
                // Cek Task 7: tgl_perawatan + jam != tgl_peresepan + jam_peresepan
                if (($resep->tgl_perawatan . ' ' . $resep->jam) != ($resep->tgl_peresepan . ' ' . $resep->jam_peresepan)) {
                    $waktuTask7 = strtotime($resep->tgl_perawatan . ' ' . $resep->jam) * 1000;
                    if ($waktuTask7 <= $lastTaskWaktu) $waktuTask7 = $lastTaskWaktu + 1000;
                    $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $kodebooking, 7, $waktuTask7);
                    $lastTaskWaktu = $waktuTask7;
                }
            }
        } catch (\Exception $e) {
            $this->error("    - Gagal auto bypass task: " . $e->getMessage());
        }
    }

    private function kirimTask($baseUrl, $consId, $secretKey, $userKey, $kodebooking, $taskid, $waktuEpoch)
    {
        try {
            $utc = time();
            $signature = $this->generateSignature($consId, $secretKey, $utc);
            
            $payload = [
                "kodebooking" => $kodebooking,
                "taskid" => $taskid,
                "waktu" => $waktuEpoch
            ];
            
            $response = Http::timeout(30) // set timeout ke 30 detik saja agar tidak nyangkut terlalu lama
                ->withHeaders([
                    'x-cons-id' => $consId,
                    'x-timestamp' => $utc,
                    'x-signature' => $signature,
                    'user_key' => $userKey,
                    'Content-Type' => 'application/json' // tambahkan content-type untuk jaga-jaga
                ])
                ->post($baseUrl . '/antrean/updatewaktu', $payload);
                
            $res = $response->json();
            $msg = $res['metadata']['message'] ?? 'Unknown Error';
            $this->line("    - Task $taskid: $msg");
            
        } catch (\Exception $e) {
            $this->error("    - Task $taskid: Gagal (" . $e->getMessage() . ")");
        }
        
        usleep(1000000); // 1000 ms (1 detik) jeda antar task untuk cegah rate limit
    }

    private function getListTaskFromBpjs($baseUrl, $consId, $secretKey, $userKey, $kodebooking)
    {
        try {
            $utc = time();
            $signature = $this->generateSignature($consId, $secretKey, $utc);
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-cons-id' => $consId,
                    'x-timestamp' => $utc,
                    'x-signature' => $signature,
                    'user_key' => $userKey,
                    'Content-Type' => 'application/json'
                ])
                ->post($baseUrl . '/antrean/getlisttask', [
                    'kodebooking' => $kodebooking
                ]);
                
            $result = $response->json();
            
            if (isset($result['metadata']['code']) && $result['metadata']['code'] == 200) {
                $decryptKey = $consId . $secretKey . $utc;
                $encrypt_method = 'AES-256-CBC';
                $key_hash = hex2bin(hash('sha256', $decryptKey));
                $iv = substr(hex2bin(hash('sha256', $decryptKey)), 0, 16);
                $output = openssl_decrypt(base64_decode($result['response']), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
                if ($output) {
                    $decompressed = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
                    $finalStr = $decompressed ? $decompressed : $output;
                    return json_decode($finalStr, true);
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika error
        }
        return [];
    }
}
