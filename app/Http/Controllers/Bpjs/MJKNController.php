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
        return Cache::remember('mjkn_token', 300, function () {

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

            return $json['response']['token']
                ?? $json['token']
                ?? null;
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

                DB::table('referensi_mobilejkn_bpjs')
                    ->updateOrInsert(
                        ['nobooking' => $kodebooking],
                        [
                            'nomorkartu'     => $request->nomorkartu,
                            'norm'           => $request->norm,
                            'kodepoli'       => $request->kodepoli,
                            'kodedokter'     => $request->kodedokter,
                            'tanggalperiksa' => $request->tanggalperiksa
                        ]
                    );

                $this->updateTaskMJKN($kodebooking, 1);
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

    private function updateTaskMJKN(
        string $kodebooking,
        int $taskid
    ) {
        try {

            $response = $this->requestMJKN(
                'updatewaktu',
                [
                    'kodebooking' => $kodebooking,
                    'taskid'      => $taskid,
                    'waktu'       => round(microtime(true) * 1000)
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

            $result = $this->updateTaskMJKN(
                $request->kodebooking,
                $request->taskid
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
}