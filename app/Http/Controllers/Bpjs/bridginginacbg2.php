<?php

namespace App\Http\Controllers\bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class bridginginacbg2 extends Controller
{
    private function getKey()
    {
        return env('INACBG_KEY', '');
    }

    private function getUrlWS()
    {
        return rtrim(env('INACBG_URL', ''), '/');
    }

    private function getKelasRS()
    {
        return env('INACBG_KELAS_RS', 'CS');
    }

    /*
    |--------------------------------------------------------------------------
    | ENCRYPT
    |--------------------------------------------------------------------------
    */

    private function mc_encrypt($data, $strkey)
    {
        $key = hex2bin($strkey);

        if (strlen($key) !== 32) {
            throw new \Exception("Key harus 256-bit");
        }

        $iv = openssl_random_pseudo_bytes(16);

        $encrypted = openssl_encrypt(
            $data,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        $signature = substr(hash_hmac('sha256', $encrypted, $key, true), 0, 10);

        return base64_encode($signature . $iv . $encrypted);
    }

    /*
    |--------------------------------------------------------------------------
    | DECRYPT
    |--------------------------------------------------------------------------
    */

    // private function mc_decrypt($str, $strkey)
    // {
    //     $key = hex2bin($strkey);

    //     $data = base64_decode($str);

    //     $ivLength = openssl_cipher_iv_length('AES-256-CBC');

    //     $iv = substr($data, 0, $ivLength);

    //     $encrypted = substr($data, $ivLength);

    //     return openssl_decrypt(
    //         $encrypted,
    //         'AES-256-CBC',
    //         $key,
    //         OPENSSL_RAW_DATA,
    //         $iv
    //     );

    //      dd([
    //     'key_valid_hex' => ctype_xdigit($strkey),
    //     'key_length' => strlen($key),
    //     'iv_length' => strlen($iv),
    //     'encrypted_length' => strlen($encrypted),
    //     'decrypt_result' => $result
    // ]);

    // return $result;
    // }

    //     private function mc_decrypt($str, $strkey)
    // {
    //     $key = hex2bin($strkey);

    //     $data = base64_decode($str);

    //     if ($data === false) {
    //         dd('BASE64 decode gagal', $str);
    //     }

    //     $ivLength = openssl_cipher_iv_length('AES-256-CBC');

    //     // $iv = substr($data, 0, $ivLength);
    //     // $encrypted = substr($data, $ivLength);

    //     $iv = substr($data, 0, 16);
    // $encrypted = substr($data, 16);

    //     // $result = openssl_decrypt(
    //     //     $encrypted,
    //     //     'AES-256-CBC',
    //     //     $key,
    //     //     OPENSSL_RAW_DATA,
    //     //     $iv
    //     // );

    //     return openssl_decrypt(
    //     base64_decode($str),
    //     'AES-256-CBC',
    //     $key,
    //     OPENSSL_RAW_DATA,
    //     $key // dummy IV (test)
    // );



    private function mc_decrypt($str, $strkey)
    {
        $key = hex2bin($strkey);

        if (strlen($key) !== 32) {
            throw new \Exception("Key harus 32 byte AES-256");
        }

        $str = trim($str);

        $decoded = base64_decode($str, true);

        if ($decoded === false) {
            throw new \Exception("Base64 decode gagal");
        }

        $ivLength = openssl_cipher_iv_length('aes-256-cbc');

        // 🔥 Coba deteksi format otomatis

        // FORMAT 1: IV + CIPHERTEXT
        $iv1 = substr($decoded, 0, $ivLength);
        $ct1 = substr($decoded, $ivLength);

        $try1 = openssl_decrypt(
            $ct1,
            'aes-256-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv1
        );

        if ($try1 !== false) {
            return $try1;
        }

        // FORMAT 2: SIGNATURE 10 + IV + CIPHERTEXT
        $iv2 = substr($decoded, 10, $ivLength);
        $ct2 = substr($decoded, 10 + $ivLength);

        $try2 = openssl_decrypt(
            $ct2,
            'aes-256-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv2
        );

        if ($try2 !== false) {
            return $try2;
        }

        throw new \Exception("Decrypt gagal (format INACBG tidak cocok semua)");
    }
    /*
    |--------------------------------------------------------------------------
    | DECRYPT RESPONSE INACBG
    |--------------------------------------------------------------------------
    */

    private function decryptResponse($response)
    {
        $body = trim($response->body());

        // 1. kalau sudah JSON langsung return
        $json = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        // 2. ambil isi encrypted saja
        $body = str_replace([
            "----BEGIN ENCRYPTED DATA----",
            "----END ENCRYPTED DATA----",
            "\r",
            "\n",
            " "
        ], '', $body);

        // 3. validasi karakter base64 dulu
        if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $body)) {
            Log::error('INACBG NOT BASE64', [
                'body_sample' => substr($body, 0, 200)
            ]);

            throw new \Exception('Response bukan base64 valid');
        }

        // 4. decode base64
        $decoded = base64_decode($body, true);

        if ($decoded === false) {
            Log::error('BASE64 FAIL', [
                'body_sample' => substr($body, 0, 200)
            ]);

            throw new \Exception('Base64 decode gagal (data rusak / tidak valid)');
        }

        // 5. decrypt
        $decrypted = $this->mc_decrypt($body, $this->getKey());


        if (!$decrypted) {
            throw new \Exception('Decrypt gagal');
        }

        // 6. parse JSON
        $result = json_decode($decrypted, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON INVALID', [
                'raw' => $decrypted
            ]);

            throw new \Exception('Hasil decrypt bukan JSON valid');
        }

        return $result;
    }

    // private function requestInacbg($payload)
    // {
    //     // 1. Encode JSON
    //     $json = json_encode($payload);

    //     // 2. Encrypt
    //     $encrypted = $this->mc_encrypt($json, $this->getKey());

    //     // 3. Kirim ke INACBG (WAJIB pakai raw body + content-type benar)
    //     $response = Http::withHeaders([
    //         'Content-Type' => 'text/plain'
    //     ])
    //         ->withBody($encrypted, 'text/plain')
    //         ->timeout(60)
    //         ->post($this->getUrlWS());

    //     // 4. Decode response (decrypt + json parse)
    //     return $this->decryptResponse($response);
    // }

    private function requestInacbg($payload)
    {
        $json = json_encode($payload);

        $encrypted = $this->mc_encrypt($json, $this->getKey());

        $response = Http::withHeaders([
            'Content-Type' => 'text/plain'
        ])
            ->withBody($encrypted, 'text/plain')
            ->timeout(60)
            ->post($this->getUrlWS());

        return $this->decryptResponse($response);
    }

    public function index($norawat)
    {
        $pasien = DB::table('reg_periksa')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->where('reg_periksa.no_rawat', $norawat)
            ->select(
                'reg_periksa.*',
                'dokter.nm_dokter',
                'pasien.nm_pasien',
                'pasien.jk',
                'reg_periksa.tgl_registrasi',
                'pasien.tgl_lahir',
                'pasien.no_peserta',
                'poliklinik.nm_poli',
                'penjab.png_jawab',
                'kamar.kelas',
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.jam_keluar'
            )
            ->first();

        if (!$pasien) {
            abort(404, 'Pasien tidak ditemukan');
        }

        // $coder = null;

        // if (auth()->check()) {
        //     $coder = DB::table('inacbg_coder_nik')
        //         ->join('petugas', 'inacbg_coder_nik.nik', '=', 'petugas.nip')
        //         ->where('inacbg_coder_nik.userid', auth()->id())
        //         ->select(
        //             'inacbg_coder_nik.nik',
        //             'inacbg_coder_nik.no_ik',
        //             'petugas.nama'
        //         )
        //         ->first();
        // }

        // $coderNik = $coder->nik ?? null;
        // dd($coderNik);

        $coder = DB::table('inacbg_coder_nik')->first();

        $coderNik = $coder->nik;

        $nosep = DB::table('bridging_sep')
            ->where('no_rawat', $norawat)
            ->value('no_sep');

        $diagnosa = DB::table('diagnosa_pasien')
            ->where('no_rawat', $norawat)
            ->orderBy('prioritas')
            ->pluck('kd_penyakit')
            ->implode('#');

        $procedure = DB::table('prosedur_pasien')
            ->where('no_rawat', $norawat)
            ->orderBy('prioritas')
            ->get()
            ->map(function ($item) {
                return $item->jumlah > 1
                    ? $item->kode . '+' . $item->jumlah
                    : $item->kode;
            })
            ->implode('#');

        $sistole = "120";
        $diastole = "90";

        $tensi = $pasien->status_lanjut == "Ranap"
            ? DB::table('pemeriksaan_ranap')
            ->where('no_rawat', $norawat)
            ->orderByDesc('tgl_perawatan')
            ->orderByDesc('jam_rawat')
            ->value('tensi')
            : DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $norawat)
            ->orderByDesc('tgl_perawatan')
            ->orderByDesc('jam_rawat')
            ->value('tensi');

        if (!empty($tensi) && str_contains($tensi, '/')) {
            $pecah = explode('/', $tensi);
            $sistole = trim($pecah[0] ?? 120);
            $diastole = trim($pecah[1] ?? 90);
        }

        $prosedur_non_bedah = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->whereIn('status', ['Ralan Dokter Paramedis', 'Ranap Dokter Paramedis'])
            ->where('nm_perawatan', 'not like', '%terapi%')
            ->sum('totalbiaya');

        $prosedur_bedah = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->where('status', 'Operasi')
            ->sum('totalbiaya');

        $konsultasi = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->whereIn('status', ['Ranap Dokter', 'Ralan Dokter'])
            ->sum('totalbiaya');

        $tenaga_ahli = 0;

        $keperawatan = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->whereIn('status', ['Ranap Paramedis', 'Ralan Paramedis'])
            ->sum('totalbiaya');

        $penunjang = 0;

        $radiologi = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->where('status', 'Radiologi')
            ->sum('totalbiaya');

        $laboratorium = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->where('status', 'Laborat')
            ->sum('totalbiaya');

        $pelayanan_darah = 0;

        $rehabilitasi = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->where(function ($q) {
                $q->where('nm_perawatan', 'like', '%terapi%')
                    ->orWhere('nm_perawatan', 'like', '%Electrical%');
            })
            ->sum('totalbiaya');

        $kamar = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->where('status', 'Kamar')
            ->sum('totalbiaya')
            + DB::table('reg_periksa')
            ->where('no_rawat', $norawat)
            ->value('biaya_reg');

        $rawat_intensif = 0;

        $obat_kronis = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->where('nm_perawatan', 'like', '%kronis%')
            ->sum('totalbiaya');

        $obat_kemoterapi = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->where('nm_perawatan', 'like', '%kemo%')
            ->sum('totalbiaya');

        $obat = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->whereIn('status', ['Obat', 'Retur Obat', 'Resep Pulang'])
            ->sum('totalbiaya')
            - $obat_kronis
            - $obat_kemoterapi;

        $alkes = 0;

        $bmhp = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->where('status', 'Tambahan')
            ->sum('totalbiaya');

        $sewa_alat = DB::table('billing')
            ->where('no_rawat', $norawat)
            ->whereIn('status', ['Harian', 'Service'])
            ->sum('totalbiaya');

        return view('bpjs.bridginginacbg2', compact(
            'pasien',
            'nosep',
            'diagnosa',
            'procedure',
            'sistole',
            'diastole',
            'prosedur_non_bedah',
            'prosedur_bedah',
            'konsultasi',
            'tenaga_ahli',
            'keperawatan',
            'penunjang',
            'radiologi',
            'laboratorium',
            'pelayanan_darah',
            'rehabilitasi',
            'kamar',
            'rawat_intensif',
            'obat',
            'obat_kronis',
            'obat_kemoterapi',
            'alkes',
            'bmhp',
            'sewa_alat',
            'coder',
            'coderNik'
        ));
    }

    public function simpan(Request $request)
    {
        $norawat = $request->no_rawat;

        $request->validate([
            'nosep'      => 'required',
            'nokartu'    => 'required',
            'diagnosa'   => 'required',
            'coder_nik'  => 'required',
            'sistole'    => 'required',
            'diastole'   => 'required'
        ]);

        DB::beginTransaction();

        try {

            $pasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->where('reg_periksa.no_rawat', $norawat)
                ->select('reg_periksa.*', 'pasien.nm_pasien', 'pasien.jk', 'pasien.tgl_lahir', 'pasien.no_peserta', 'kamar.kelas', 'kamar_inap.tgl_keluar', 'dokter.nm_dokter')
                ->first();

            if (!$pasien) {
                throw new Exception('Pasien tidak ditemukan');
            }

            $url = $this->getUrlWS();

            /*
        |------------------------------------------------
        | 1. NEW CLAIM
        |------------------------------------------------
        */
            $newClaim = [
                "metadata" => ["method" => "new_claim"],
                "data" => [
                    "nomor_kartu" => $request->nokartu,
                    "nomor_sep"   => $request->nosep,
                    "nomor_rm"    => $pasien->no_rkm_medis,
                    "nama_pasien" => $pasien->nm_pasien,
                    "tgl_lahir"   => $pasien->tgl_lahir,
                    "gender"      => $pasien->jk == 'L' ? '1' : '2'
                ]
            ];

            $res1 = $this->requestInacbg($newClaim);

            if (($res1['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception('NEW CLAIM GAGAL: ' . json_encode($res1));
            }

            /*
        |------------------------------------------------
        | 2. SET CLAIM DATA
        |------------------------------------------------
        */
            $setClaim = [
                "metadata" => [
                    "method" => "set_claim_data",
                    "nomor_sep" => $request->nosep
                ],
                "data" => [
                    "nomor_sep"   => $request->nosep,
                    "nomor_kartu" => $request->nokartu,
                    "tgl_masuk"   => $pasien->tgl_registrasi . " 00:00:00",
                    "tgl_pulang" => ($pasien->status_lanjut == 'Ralan'
                        ? $pasien->tgl_registrasi
                        : ($pasien->tgl_keluar ?? $pasien->tgl_registrasi)
                    ) . " 00:00:00",
                    "jenis_rawat" => $pasien->status_lanjut == 'Ranap' ? '1' : '2',
                    "kelas_rawat" => $pasien->kelas ?? '3',
                    "adl_sub_acute" => "0",
                    "adl_chronic" => "0",
                    "nama_dokter" => $pasien->nm_dokter,

                    "sistole"  => (string) $request->sistole,
                    "diastole" => (string) $request->diastole,
                    // "discharge_status" => "1",
                    "discharge_status" => $request->discharge_status,

                    "tarif_rs" => [
                        "prosedur_non_bedah" => $request->prosedur_non_bedah ?? 0,
                        "prosedur_bedah"     => $request->prosedur_bedah ?? 0,
                        "konsultasi"         => $request->konsultasi ?? 0,
                        "keperawatan"        => $request->keperawatan ?? 0,
                        "radiologi"          => $request->radiologi ?? 0,
                        "laboratorium"       => $request->laboratorium ?? 0,
                        "obat"               => $request->obat ?? 0,
                        "kamar"              => $request->kamar ?? 0,
                    ],

                    // "diagnosa" => array_map(function ($item) {
                    //     return ["kode" => trim($item)];
                    // }, array_filter(explode('#', $request->diagnosa))),

                    // "procedure" => !empty($request->procedure)
                    //     ? array_map(function ($item) {
                    //         return ["kode" => trim($item)];
                    //     }, array_filter(explode('#', $request->procedure)))
                    //     : [],
                    "coder_nik" => $request->coder_nik,
                    "kode_tarif" => $this->getKelasRS(),
                    "payor_id" => "3",
                    "payor_cd" => "JKN"
                ]
            ];

            $res2 = $this->requestInacbg($setClaim);

            if (($res2['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception('SET CLAIM GAGAL: ' . json_encode($res2));
            }

            /*
            |--------------------------------------------------------------------------
            | 3. SET DIAGNOSA IDRG
            |--------------------------------------------------------------------------
            */
            $this->setDiagnosa(
                $request->nosep,
                $request->diagnosa
            );

            /*
            |--------------------------------------------------------------------------
            | 4. SET PROCEDURE IDRG
            |--------------------------------------------------------------------------
            */
            if (!empty($request->procedure)) {
                $this->setProcedure(
                    $request->nosep,
                    $request->procedure
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 5. GROUPING IDRG
            |--------------------------------------------------------------------------
            */
            $idrg = $this->groupingIdrg($request->nosep);

            if (($idrg['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'GROUPING IDRG GAGAL : ' .
                        json_encode($idrg)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 6. FINAL IDRG
            |--------------------------------------------------------------------------
            */
            $idrgFinal = $this->idrgFinal(
                $request->nosep
            );


            if (($idrgFinal['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'IDRG FINAL GAGAL : ' .
                        json_encode($idrgFinal)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 7. IMPORT IDRG -> INACBG
            |--------------------------------------------------------------------------
            */
            $import = $this->importIdrgToInacbg(
                $request->nosep
            );


            if (($import['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'IMPORT IDRG GAGAL : ' .
                        json_encode($import)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 8. GROUPER INACBG
            |--------------------------------------------------------------------------
            */
            // $grouper = [
            //     "metadata" => [
            //         "method" => "grouper",
            //         "stage" => "1",
            //         "grouper" => "inacbg"
            //     ],
            //     "data" => [
            //         "nomor_sep" => $request->nosep
            //     ]
            // ];

            // $res3 = $this->requestInacbg($grouper);

            // if (($res3['metadata']['message'] ?? '') !== 'Ok') {
            //     throw new Exception(
            //         'GROUPER INACBG GAGAL : ' .
            //             json_encode($res3)
            //     );
            // }

            /*
            |--------------------------------------------------------------------------
            | 9. FINAL CLAIM
            |--------------------------------------------------------------------------
            */
            // $final = [
            //     "metadata" => [
            //         "method" => "claim_final"
            //     ],
            //     "data" => [
            //         "nomor_sep" => $request->nosep,
            //         "coder_nik" => $request->coder_nik
            //     ]
            // ];

            // $res4 = $this->requestInacbg($final);

            // if (($res4['metadata']['message'] ?? '') !== 'Ok') {
            //     throw new Exception(
            //         'FINAL CLAIM GAGAL : ' .
            //             json_encode($res4)
            //     );
            // }

            /*
        |------------------------------------------------
        | 3. GROUPER
        |------------------------------------------------
        */
            // $grouper = [
            //     "metadata" => [
            //         "method" => "grouper",
            //         "stage" => "1",
            //         "grouper" => "inacbg"
            //     ],
            //     "data" => ["nomor_sep" => $request->nosep]
            // ];

            // $res3 = $this->requestInacbg($grouper);

            // if (($res3['metadata']['message'] ?? '') !== 'Ok') {
            //     throw new Exception('GROUPER GAGAL: ' . json_encode($res3));
            // }


            // $grouperInacbg = [
            //     "metadata" => [
            //         "method" => "grouper",
            //         "stage"  => "1",
            //         "grouper" => "inacbg"
            //     ],
            //     "data" => [
            //         "nomor_sep" => $request->nosep
            //     ]
            // ];

            // $resInacbg = $this->requestInacbg($grouperInacbg);

            // if (($resInacbg['metadata']['message'] ?? '') !== 'Ok') {
            //     throw new Exception(
            //         'GROUPING INACBG GAGAL: ' . json_encode($resInacbg)
            //     );
            // }

            $grouperInacbg = [
                "metadata" => [
                    "method" => "grouper",
                    "stage"  => "1",
                    "grouper" => "inacbg"
                ],
                "data" => [
                    "nomor_sep" => $request->nosep
                ]
            ];

            $resInacbg = $this->requestInacbg($grouperInacbg);

            if (($resInacbg['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'GROUPING INACBG GAGAL: ' . json_encode($resInacbg)
                );
            }

            

            /*
        |------------------------------------------------
        | 4. FINAL CLAIM
        |------------------------------------------------
        */
            $final = [
                "metadata" => ["method" => "claim_final"],
                "data" => [
                    "nomor_sep" => $request->nosep,
                    "coder_nik" => $request->coder_nik
                ]
            ];

            $res4 = $this->requestInacbg($final);


            if (($res4['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception('FINAL CLAIM GAGAL: ' . json_encode($res4));
            }

            DB::commit();

            return back()->with(
                'success',
                'Berhasil kirim dan final klaim INACBG'
            );
        } catch (Exception $e) {

            DB::rollBack();

            Log::error('INACBG ERROR', [
                'msg' => $e->getMessage()
            ]);

            return back()->with('error', $e->getMessage());
        }
    }


    private function setDiagnosa($sep, $diagnosa)
    {
        // reset dulu
        $this->requestInacbg([
            "metadata" => [
                "method" => "idrg_diagnosa_set",
                "nomor_sep" => $sep
            ],
            "data" => [
                "diagnosa" => "#"
            ]
        ]);

        // set ulang
        return $this->requestInacbg([
            "metadata" => [
                "method" => "idrg_diagnosa_set",
                "nomor_sep" => $sep
            ],
            "data" => [
                "diagnosa" => $diagnosa
            ]
        ]);
    }

    private function setProcedure($sep, $procedure)
    {
        $this->requestInacbg([
            "metadata" => [
                "method" => "idrg_procedure_set",
                "nomor_sep" => $sep
            ],
            "data" => [
                "procedure" => "#"
            ]
        ]);

        return $this->requestInacbg([
            "metadata" => [
                "method" => "idrg_procedure_set",
                "nomor_sep" => $sep
            ],
            "data" => [
                "procedure" => $procedure
            ]
        ]);
    }

    private function groupingIdrg($sep)
    {
        return $this->requestInacbg([
            "metadata" => [
                "method" => "grouper",
                "stage" => "1",
                "grouper" => "idrg"
            ],
            "data" => [
                "nomor_sep" => $sep
            ]
        ]);
    }

    private function idrgFinal($sep)
    {
        return $this->requestInacbg([
            "metadata" => [
                "method" => "idrg_grouper_final"
            ],
            "data" => [
                "nomor_sep" => $sep
            ]
        ]);
    }

    private function importIdrgToInacbg($sep)
    {
        return $this->requestInacbg([
            "metadata" => [
                "method" => "idrg_to_inacbg_import"
            ],
            "data" => [
                "nomor_sep" => $sep
            ]
        ]);
    }
}
