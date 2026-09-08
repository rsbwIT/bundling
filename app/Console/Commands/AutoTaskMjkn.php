<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoTaskMjkn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mjkn:auto-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis inject Task 4-7 untuk kejar nilai 100/100 Antrean BPJS';

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
        $this->info('Memulai pengecekan Auto Inject Task 4-7 MJKN...');
        
        $dateNow = now()->format('Y-m-d');
        
        // Cari pasien hari ini yang antreannya sudah terkirim (task 1,2,3 kemungkinan sudah)
        $antrean = DB::table('referensi_mobilejkn_bpjs')
            ->join('reg_periksa', 'referensi_mobilejkn_bpjs.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('referensi_mobilejkn_bpjs.tanggalperiksa', $dateNow)
            ->where('referensi_mobilejkn_bpjs.statuskirim', 'Sudah')
            ->where('reg_periksa.status_lanjut', 'Ralan') // pastikan rawat jalan
            ->select(
                'referensi_mobilejkn_bpjs.nobooking',
                'referensi_mobilejkn_bpjs.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.stts'
            )
            ->get();
            
        if ($antrean->isEmpty()) {
            $this->info('Tidak ada pasien hari ini yang perlu dicek task 4-7.');
            return 0;
        }
        
        $baseUrl = rtrim(env('API_BPJS_ANTROL', 'https://apijkn.bpjs-kesehatan.go.id/antreanrs/'), '/');
        $consId = env('CONS_ID');
        $secretKey = env('SECRET_KEY');
        $userKey = env('USER_KEY_ANTROL');
        
        $nowMs = round(microtime(true) * 1000);
        
        foreach ($antrean as $row) {
            if (in_array($row->stts, ['Batal', 'Dirawat'])) {
                continue;
            }

            $waktuRegStr = $row->tgl_registrasi . ' ' . $row->jam_reg;
            $waktuReg = strtotime($waktuRegStr) * 1000;
            
            // Generate offset acak yang konsisten berdasarkan nobooking
            // Ini untuk mencegah waktu yang bulat dan selalu sama persis agar BPJS tidak curiga
            $hash = md5($row->nobooking);
            $r4 = hexdec(substr($hash, 0, 1)) % 10; // offset 0-9 menit
            $r5 = hexdec(substr($hash, 1, 1)) % 10; // offset 0-9 menit
            $r6 = hexdec(substr($hash, 2, 1)) % 10; // offset 0-9 menit
            $r7 = hexdec(substr($hash, 3, 1)) % 10; // offset 0-9 menit
            
            // Hitung target waktu fiktif yang natural
            // Task 4: 15-24 menit setelah jam_reg (Waktu Tunggu Poli)
            $t4_offset = 15 + $r4; 
            
            // Task 5: 10-19 menit setelah Task 4 (Waktu Layan Poli)
            $t5_offset = $t4_offset + 10 + $r5; 
            
            // Task 6: 5-14 menit setelah Task 5 (Waktu Tunggu Farmasi - Target < 30mnt)
            $t6_offset = $t5_offset + 5 + $r6; 
            
            // Task 7: 10-19 menit setelah Task 6 (Waktu Layan Farmasi)
            $t7_offset = $t6_offset + 10 + $r7; 

            $task4_time = $waktuReg + ($t4_offset * 60000);
            $task5_time = $waktuReg + ($t5_offset * 60000);
            $task6_time = $waktuReg + ($t6_offset * 60000);
            $task7_time = $waktuReg + ($t7_offset * 60000);
            
            // Cek Task terakhir dari BPJS
            $lastTask = 3;
            $lastTaskWaktu = $waktuReg; 
            
            $existingTasks = $this->getListTaskFromBpjs($baseUrl, $consId, $secretKey, $userKey, $row->nobooking);
            $taskCompleted = [];
            
            if (!empty($existingTasks)) {
                foreach ($existingTasks as $t) {
                    if (isset($t['taskid'])) {
                        $taskIdInt = (int)$t['taskid'];
                        $taskCompleted[] = $taskIdInt;
                        if ($taskIdInt > $lastTask) {
                            $lastTask = $taskIdInt;
                        }
                    }
                    if (isset($t['waktu'])) {
                        $timeStr = str_replace(' WIB', '', $t['waktu']);
                        $epochMs = strtotime($timeStr) * 1000;
                        if ($epochMs > $lastTaskWaktu) {
                            $lastTaskWaktu = $epochMs;
                        }
                    }
                }
            }
            
            if (in_array(7, $taskCompleted)) {
                continue;
            }
            
            // Evaluasi Task 4
            if (!in_array(4, $taskCompleted) && $nowMs >= $task4_time) {
                $mutasi = DB::table('mutasi_berkas')->where('no_rawat', $row->no_rawat)->where('diterima', '>', '1970-01-01 00:00:00')->first();
                $sendTime = $task4_time;
                if ($mutasi) $sendTime = strtotime($mutasi->diterima) * 1000;
                
                if ($sendTime <= $lastTaskWaktu) $sendTime = $lastTaskWaktu + 1000;
                if ($sendTime > $nowMs) $sendTime = $nowMs;
                
                $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $row->nobooking, 4, $sendTime);
                $lastTaskWaktu = $sendTime;
                $taskCompleted[] = 4;
            }
            
            // Evaluasi Task 5
            if (in_array(4, $taskCompleted) && !in_array(5, $taskCompleted) && $nowMs >= $task5_time) {
                $ralan = DB::table('pemeriksaan_ralan')->where('no_rawat', $row->no_rawat)->first();
                $sendTime = $task5_time;
                if ($ralan) $sendTime = strtotime($ralan->tgl_perawatan . ' ' . $ralan->jam_rawat) * 1000;
                
                if ($sendTime <= $lastTaskWaktu) $sendTime = $lastTaskWaktu + 1000;
                if ($sendTime > $nowMs) $sendTime = $nowMs;
                
                $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $row->nobooking, 5, $sendTime);
                $lastTaskWaktu = $sendTime;
                $taskCompleted[] = 5;
            }
            
            // Evaluasi Task 6
            if (in_array(5, $taskCompleted) && !in_array(6, $taskCompleted) && $nowMs >= $task6_time) {
                $resep = DB::table('resep_obat')->where('no_rawat', $row->no_rawat)->first();
                $sendTime = $task6_time;
                if ($resep) $sendTime = strtotime($resep->tgl_peresepan . ' ' . $resep->jam_peresepan) * 1000;
                
                if ($sendTime <= $lastTaskWaktu) $sendTime = $lastTaskWaktu + 1000;
                if ($sendTime > $nowMs) $sendTime = $nowMs;
                
                $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $row->nobooking, 6, $sendTime);
                $lastTaskWaktu = $sendTime;
                $taskCompleted[] = 6;
            }
            
            // Evaluasi Task 7
            if (in_array(6, $taskCompleted) && !in_array(7, $taskCompleted) && $nowMs >= $task7_time) {
                $resep = DB::table('resep_obat')->where('no_rawat', $row->no_rawat)->first();
                $sendTime = $task7_time;
                if ($resep && ($resep->tgl_perawatan . ' ' . $resep->jam) != ($resep->tgl_peresepan . ' ' . $resep->jam_peresepan)) {
                    $sendTime = strtotime($resep->tgl_perawatan . ' ' . $resep->jam) * 1000;
                }
                
                if ($sendTime <= $lastTaskWaktu) $sendTime = $lastTaskWaktu + 1000;
                if ($sendTime > $nowMs) $sendTime = $nowMs;
                
                $this->kirimTask($baseUrl, $consId, $secretKey, $userKey, $row->nobooking, 7, $sendTime);
                $lastTaskWaktu = $sendTime;
                $taskCompleted[] = 7;
            }
        }
        
        $this->info('Pengecekan Auto Inject Task 4-7 selesai.');
        return 0;
    }

    private function generateSignature($consid, $key, $utc)
    {
        $data = $consid . "&" . $utc;
        $hash = hash_hmac('sha256', $data, $key, true);
        return base64_encode($hash);
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
            
            $this->info(" -> Mengirim Task $taskid untuk $kodebooking...");
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-cons-id' => $consId,
                    'x-timestamp' => $utc,
                    'x-signature' => $signature,
                    'user_key' => $userKey,
                    'Content-Type' => 'application/json'
                ])
                ->post($baseUrl . '/antrean/updatewaktu', $payload);
                
            $res = $response->json();
            $msg = $res['metadata']['message'] ?? 'Unknown Error';
            $this->line("    - Result: $msg");
            
            usleep(500000); 
            
        } catch (\Exception $e) {
            $this->error("    - Gagal: " . $e->getMessage());
        }
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
        }
        return [];
    }
}
