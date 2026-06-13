<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MJKNController extends Controller
{
    protected $baseUrl;
    protected $username;
    protected $password;

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

            $response = Http::withHeaders([
                'x-username' => $this->username,
                'x-password' => $this->password
            ])->get($this->baseUrl . '/auth');

            return response()->json($response->json());
        } catch (\Exception $e) {

            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    private function getTokenMJKN()
    {
        $response = Http::withHeaders([
            'x-username' => $this->username,
            'x-password' => $this->password
        ])->get($this->baseUrl . '/auth');

        $json = $response->json();

        return $json['response']['token']
            ?? $json['token']
            ?? null;
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
                    'status' => false,
                    'message' => 'Pasien tidak ditemukan'
                ]);
            }

            // $surat = DB::table('bridging_surat_kontrol_bpjs as sk')
            //     ->join('bridging_sep as bs', 'bs.no_sep', '=', 'sk.no_sep')
            //     ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'bs.no_rawat')
            //     ->where('rp.no_rkm_medis', $pasien->no_rkm_medis)
            //     ->orderByDesc('sk.tgl_rencana')
            //     ->first();

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

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function ambilAntrean(Request $request)
    {
        $request->validate([
            'nomorkartu'     => 'required',
            'nik'            => 'required',
            'norm'           => 'required',
            'kodepoli'       => 'required',
            'kodedokter'     => 'required',
            'tanggalperiksa' => 'required',
            'jampraktek'     => 'required',
            'nomorreferensi' => 'required'
        ]);

        try {

            $token = $this->getTokenMJKN();

            if (!$token) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Token MJKN gagal diperoleh'
                    ]
                ]);
            }

            $payload = [
                "nomorkartu"     => $request->nomorkartu,
                "nik"            => $request->nik,
                "nohp"           => $request->nohp ?? '081234567890',
                "kodepoli"       => $request->kodepoli,
                "norm"           => $request->norm,
                "tanggalperiksa" => $request->tanggalperiksa,
                "kodedokter"     => $request->kodedokter,
                "jampraktek"     => $request->jampraktek,
                "jeniskunjungan" => 3,
                "nomorreferensi" => $request->nomorreferensi
            ];

            $response = Http::timeout(60)
                ->withHeaders([
                    'x-token'    => $token,
                    'x-username' => $this->username
                ])
                ->post(
                    $this->baseUrl . '/ambilantrean',
                    $payload
                );

            return response()->json(
                $response->json(),
                $response->status()
            );
        } catch (\Exception $e) {

            return response()->json([
                'metadata' => [
                    'code' => 500,
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

            $token = $this->getTokenMJKN();

            $payload = [
                'kodebooking' => $request->kodebooking,
                'waktu' => round(microtime(true) * 1000)
            ];

            $response = Http::withHeaders([
                'x-token' => $token,
                'x-username' => $this->username
            ])->post(
                $this->baseUrl . '/checkinantrean',
                $payload
            );

            return response()->json(
                $response->json(),
                $response->status()
            );
        } catch (\Exception $e) {

            return response()->json([
                'metadata' => [
                    'code' => 500,
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

            $token = $this->getTokenMJKN();

            $payload = [
                'kodebooking' => $request->kodebooking,
                'keterangan'  => $request->keterangan
            ];

            $response = Http::withHeaders([
                'x-token' => $token,
                'x-username' => $this->username
            ])->post(
                $this->baseUrl . '/batalantrean',
                $payload
            );

            return response()->json(
                $response->json(),
                $response->status()
            );
        } catch (\Exception $e) {

            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
        dd($request->all());
    }

    public function sisaAntrean(Request $request)
    {
        $request->validate([
            'kodebooking' => 'required'
        ]);

        try {

            $token = $this->getTokenMJKN();

            $response = Http::withHeaders([
                'x-token' => $token,
                'x-username' => $this->username
            ])->post(
                $this->baseUrl . '/sisaantrean',
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
                    'code' => 500,
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }
}
