<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MJKNController1 extends Controller
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
        return view('bpjs.mjkn1');
    }

    /*
    =========================
    AMBIL TOKEN (CACHE)
    =========================
    */
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

            return $json['response']['token'] ?? null;
        });
    }

    /*
    =========================
    REQUEST GENERIC MJKN
    =========================
    */
    private function requestMJKN($endpoint, $payload = [])
    {
        $token = $this->getTokenMJKN();

        if (!$token) {
            throw new \Exception("Token MJKN tidak tersedia");
        }

        return Http::timeout(60)
            ->withHeaders([
                'x-token'    => $token,
                'x-username' => $this->username,
                'Accept'     => 'application/json'
            ])
            ->post($this->baseUrl . '/' . $endpoint, $payload);
    }

    /*
    =========================
    AMBIL ANTREAN + INSERT KHANZA
    =========================
    */
    public function ambilAntrian(Request $request)
    {
        $request->validate([
            'nomorkartu'      => 'required',
            'nik'             => 'required',
            'nohp'            => 'required',
            'kodepoli'        => 'required',
            'norm'            => 'required',
            'tanggalperiksa'  => 'required|date',
            'kodedokter'      => 'required',
            'jampraktek'      => 'required',
            'jeniskunjungan'  => 'required|in:1,2,3,4',
            'nomorreferensi'  => 'required'
        ]);

        try {

            $response = $this->requestMJKN('ambilantrean', [
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
            ]);

            $result = $response->json();

            /*
            =========================
            CEK SUCCESS RESPONSE
            =========================
            */
            if (($result['metadata']['code'] ?? null) == 200) {

                $res = $result['response'] ?? [];

                $kodebooking = $res['kodebooking'] ?? null;

                if ($kodebooking) {

                    /*
                    =========================
                    INSERT KE KHANZA
                    =========================
                    */
                    DB::table('reg_periksa')->updateOrInsert(
                        [
                            'no_rawat' => $kodebooking
                        ],
                        [
                            'tgl_registrasi' => $request->tanggalperiksa,
                            'no_rkm_medis'   => $request->norm,
                            'kd_poli'        => $request->kodepoli,
                            'kd_dokter'      => $request->kodedokter,
                            'no_reg'         => $res['noantrean'] ?? null,
                            'no_hp'          => $request->nohp,
                        ]
                    );

                    // update task MJKN (step 1)
                    $this->updateTaskMJKN($kodebooking, 1);
                }
            }

            return response()->json(
                $result,
                $response->status()
            );

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

    /*
    =========================
    UPDATE TASK MJKN
    =========================
    */
    private function updateTaskMJKN($kodebooking, $taskid)
    {
        try {

            return $this->requestMJKN('updatewaktu', [
                'kodebooking' => $kodebooking,
                'taskid'      => $taskid,
                'waktu'       => round(microtime(true) * 1000)
            ])->json();

        } catch (\Exception $e) {

            Log::error('UPDATE TASK ERROR', [
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }
}