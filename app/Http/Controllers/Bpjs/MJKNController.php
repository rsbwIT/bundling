<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MJKNController extends Controller
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl  = rtrim(env('MJKN_RS'), '/');
        $this->username = env('X_USERNAME');
        $this->password = env('X_PASSWORD');
    }

    public function index()
    {
        return view('bpjs.mjkn');
    }

    public function token()
    {
        try {

            $response = Http::timeout(30)
                ->withHeaders([
                    'x-username' => $this->username,
                    'x-password' => $this->password
                ])
                ->get($this->baseUrl . '/auth');

            return response()->json(
                $response->json(),
                $response->status()
            );

        } catch (\Exception $e) {

            Log::error('MJKN TOKEN ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'metadata' => [
                    'code'    => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    private function getTokenMJKN()
    {
        return Cache::remember('mjkn_token', 600, function () {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-username' => $this->username,
                    'x-password' => $this->password
                ])
                ->get($this->baseUrl . '/auth');

            if (!$response->successful()) {
                Log::error('MJKN AUTH FAILED', [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ]);
                return null;
            }

            $json = $response->json();
            return $json['response']['token'] ?? $json['token'] ?? null;
        });
    }

    private function requestMJKN(string $endpoint, array $payload = [])
    {
        $token = $this->getTokenMJKN();

        if (!$token) {
            throw new \Exception('Token MJKN tidak tersedia');
        }

        $response = Http::timeout(60)
            ->withHeaders([
                'x-token'    => $token,
                'x-username' => $this->username
            ])
            ->post(
                $this->baseUrl . '/' . $endpoint,
                $payload
            );

        Log::info('MJKN REQUEST', [
            'endpoint' => $endpoint,
            'payload'  => $payload,
            'status'   => $response->status()
        ]);

        return $response;
    }

    public function cariPasien(Request $request)
    {
        $request->validate([
            'nomorkartu' => 'required'
        ]);

        try {

            $pasien = DB::table('pasien')
                ->where('no_peserta', $request->nomorkartu)
                ->first();

            if (!$pasien) {

                return response()->json([
                    'status'  => false,
                    'message' => 'Pasien tidak ditemukan'
                ]);
            }

            $surat = DB::table('bridging_surat_kontrol_bpjs as sk')
                ->join('bridging_sep as bs', 'bs.no_sep', '=', 'sk.no_sep')
                ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'bs.no_rawat')
                ->where('rp.no_rkm_medis', $pasien->no_rkm_medis)
                ->orderByDesc('sk.tgl_rencana')
                ->first();

            $jampraktek = '';

            if ($surat) {

                $jadwal = DB::table('jadwal')
                    ->join(
                        'maping_poli_bpjs',
                        'jadwal.kd_poli',
                        '=',
                        'maping_poli_bpjs.kd_poli_rs'
                    )
                    ->join(
                        'maping_dokter_dpjpvclaim',
                        'jadwal.kd_dokter',
                        '=',
                        'maping_dokter_dpjpvclaim.kd_dokter'
                    )
                    ->where(
                        'maping_poli_bpjs.kd_poli_bpjs',
                        $surat->kd_poli_bpjs
                    )
                    ->where(
                        'maping_dokter_dpjpvclaim.kd_dokter_bpjs',
                        $surat->kd_dokter_bpjs
                    )
                    ->select(
                        'jadwal.jam_mulai',
                        'jadwal.jam_selesai'
                    )
                    ->first();

                if ($jadwal) {

                    $jampraktek =
                        substr($jadwal->jam_mulai, 0, 5)
                        . '-'
                        . substr($jadwal->jam_selesai, 0, 5);
                }
            }

            return response()->json([
                'status'      => true,
                'nama'        => $pasien->nm_pasien,
                'nik'         => $pasien->no_ktp,
                'norm'        => $pasien->no_rkm_medis,
                'nohp'        => $pasien->no_tlp ?? '',
                'nomorsurat'  => $surat->no_surat ?? '',
                'kodepoli'    => $surat->kd_poli_bpjs ?? '',
                'namapoli'    => $surat->nm_poli_bpjs ?? '',
                'kodedokter'  => $surat->kd_dokter_bpjs ?? '',
                'namadokter'  => $surat->nm_dokter_bpjs ?? '',
                'tanggal'     => $surat->tgl_rencana ?? '',
                'jampraktek'  => $jampraktek
            ]);

        } catch (\Exception $e) {

            Log::error('CARI PASIEN ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function ambilAntrean(Request $request)
{
    $request->validate([
        'nomorkartu'     => 'required',
        'nik'            => 'required',
        'nohp'           => 'required',
        'kodepoli'       => 'required',
        'norm'           => 'required',
        'tanggalperiksa' => 'required|date',
        'kodedokter'     => 'required',
        'jampraktek'     => 'required',
        'jeniskunjungan' => 'required|in:1,2,3,4',
        'nomorreferensi' => 'required'
    ]);

    try {

        $payload = [
            "nomorkartu"     => $request->nomorkartu,
            "nik"            => $request->nik,
            "nohp"           => $request->nohp,
            "kodepoli"       => $request->kodepoli,
            "norm"           => $request->norm,
            "tanggalperiksa" => $request->tanggalperiksa,
            "kodedokter"     => $request->kodedokter,
            "jampraktek"     => $request->jampraktek,
            "jeniskunjungan" => $request->jeniskunjungan,
            "nomorreferensi" => $request->nomorreferensi
        ];
        
        $response = $this->requestMJKN('ambilantrean', $payload);

        $result = $response->json();

        if (
            isset($result['metadata']['code']) &&
            $result['metadata']['code'] == 200
        ) {

            $kodebooking = $result['response']['kodebooking'] ?? null;

            if ($kodebooking) {
                // Fetch the real slash no_rawat generated by the bridging API
                $slash_no_rawat = null;
                for ($i = 0; $i < 5; $i++) {
                    $slash_no_rawat = DB::table('referensi_mobilejkn_bpjs')
                        ->where('nobooking', $kodebooking)
                        ->value('no_rawat');
                    if ($slash_no_rawat) break;
                    sleep(1); // Wait for the bridging API to finish committing
                }

                if ($slash_no_rawat) {
                    // Insert the Task ID using the slash no_rawat (original behavior)
                    DB::table('referensi_mobilejkn_bpjs_taskid')
                        ->updateOrInsert(
                            [
                                'no_rawat' => $slash_no_rawat,
                                'taskid' => '3'
                            ],
                            [
                                'waktu' => date('Y-m-d H:i:s')
                            ]
                        );
                }
                
                // User wants Task ID to populate in Bundling Web App immediately
                $this->updateTaskMJKN($kodebooking, 3);
            }
        }

        return response()->json($result, $response->status());

    } catch (\Exception $e) {

        Log::error('AMBIL ANTREAN ERROR', [
            'message' => $e->getMessage()
        ]);

        return response()->json([
            'metadata' => [
                'code'    => 500,
                'message' => $e->getMessage()
            ]
        ], 500);
    }
}

    public function checkinAntrean(Request $request)
    {
        $request->validate([
            'kodebooking' => 'required'
        ]);

        try {

            $response = $this->requestMJKN(
                'checkinantrean',
                [
                    'kodebooking' => $request->kodebooking,
                    'waktu'       => round(microtime(true) * 1000)
                ]
            );

            $result = $response->json();

            if (
                isset($result['metadata']['code']) &&
                $result['metadata']['code'] == 200
            ) {
                $this->updateTaskMJKN(
                    $request->kodebooking,
                    2
                );
            }

            return response()->json(
                $result,
                $response->status()
            );

        } catch (\Exception $e) {

            return response()->json([
                'metadata' => [
                    'code'    => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function batalAntrean(Request $request)
    {
        $request->validate([
            'kodebooking' => 'required',
            'keterangan'  => 'required'
        ]);

        try {

            $response = $this->requestMJKN(
                'batalantrean',
                [
                    'kodebooking' => $request->kodebooking,
                    'keterangan'  => $request->keterangan
                ]
            );

            return response()->json(
                $response->json(),
                $response->status()
            );

        } catch (\Exception $e) {

            return response()->json([
                'metadata' => [
                    'code'    => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function sisaAntrean(Request $request)
    {
        $request->validate([
            'kodebooking' => 'required'
        ]);

        try {

            $response = $this->requestMJKN(
                'sisaantrean',
                [
                    'kodebooking' => $request->kodebooking
                ]
            );

            return response()->json(
                $response->json(),
                $response->status()
            );

        } catch (\Exception $e) {

            return response()->json([
                'metadata' => [
                    'code'    => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function updateTaskMJKN(
        string $kodebooking,
        int $taskid,
        $waktu = null
    ) {
        try {
            
            if (!$waktu) {
                $waktu = (int) round(microtime(true) * 1000);
            }

            $response = $this->requestMJKN(
                'updatewaktu',
                [
                    'kodebooking' => $kodebooking,
                    'taskid'      => $taskid,
                    'waktu'       => $waktu
                ]
            );

            return $response->json();

        } catch (\Exception $e) {

            Log::error(
                'MJKN UPDATE TASK ERROR',
                [
                    'message' => $e->getMessage()
                ]
            );

            return false;
        }
    }

    public function updateTask(Request $request)
    {
        $request->validate([
            'kodebooking' => 'required',
            'taskid'      => 'required|integer|min:1|max:7'
        ]);

        try {
            $kodebooking = $request->kodebooking;
            $taskid = (int) $request->taskid;
            $waktu = null;

            // Generate waktu berdasarkan jadwal praktek pasien
            $antrean = DB::table('referensi_mobilejkn_bpjs')->where('nobooking', $kodebooking)->first();
            if ($antrean) {
                $jampraktek = explode('-', $antrean->jampraktek)[0];
                $jadwal = \Carbon\Carbon::parse($antrean->tanggalperiksa . ' ' . $jampraktek);
                
                switch ($taskid) {
                    case 1: $jadwal->subMinutes(60); break; // Mulai Tunggu Admisi
                    case 2: $jadwal->subMinutes(45); break; // Selesai Admisi
                    case 3: $jadwal->subMinutes(30); break; // Mulai Tunggu Poli
                    case 4: $jadwal->addMinutes(30); break; // Selesai Poli
                    case 5: $jadwal->addMinutes(45); break; // Mulai Tunggu Farmasi
                    case 6: $jadwal->addMinutes(60); break; // Selesai Farmasi
                    case 7: $jadwal->addMinutes(75); break; // Selesai Dilayani (Pulang)
                }
                
                $waktu = $jadwal->timestamp * 1000;
            }

            $result = $this->updateTaskMJKN(
                $kodebooking,
                $taskid,
                $waktu
            );

            return response()->json([
                'metadata' => [
                    'code'    => 200,
                    'message' => 'Task berhasil diupdate'
                ],
                'response' => $result
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'metadata' => [
                    'code'    => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    private function getUTC()
{
    return time();
}

private function generateSignature($consid, $key, $utc)
{
    $data = $consid . "&" . $utc;

    $hash = hash_hmac('sha256', $data, $key, true);

    return base64_encode($hash);
}

    public function antreanMjknView()
    {
        return view('bpjs.antrean_mjkn');
    }

    public function antreanMjknData(Request $request)
    {
        $tanggal1 = $request->input('tanggal1', date('Y-m-d'));
        $tanggal2 = $request->input('tanggal2', date('Y-m-d'));
        
        $data = DB::table('referensi_mobilejkn_bpjs')
            ->join('reg_periksa', 'referensi_mobilejkn_bpjs.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->whereBetween('referensi_mobilejkn_bpjs.tanggalperiksa', [$tanggal1, $tanggal2])
            ->select(
                'referensi_mobilejkn_bpjs.*',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                'dokter.nm_dokter'
            )
            ->orderBy('referensi_mobilejkn_bpjs.tanggalperiksa', 'desc')
            ->get();
            
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function kirimAntreanMjknManual()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('mjkn:kirim-antrean');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            return response()->json([
                'success' => true, 
                'message' => 'Command pengiriman antrean berhasil dijalankan.',
                'log' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function kirimAntreanMjknSingle(Request $request)
    {
        $kodebooking = $request->input('kodebooking');
        if (!$kodebooking) {
            return response()->json(['success' => false, 'message' => 'Kode booking tidak ditemukan'], 400);
        }

        try {
            \Illuminate\Support\Facades\Artisan::call('mjkn:kirim-antrean', [
                'kodebooking' => $kodebooking
            ]);
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            return response()->json([
                'success' => true, 
                'message' => "Antrean $kodebooking berhasil diproses.",
                'log' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}