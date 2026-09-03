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

    //  ENCRYPT

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

    //  DECRYPT RESPONSE INACBG

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

    private function requestInacbg($payload)
    {
        $json = json_encode($payload);

        $encrypted = $this->mc_encrypt($json, $this->getKey());

        $url = $this->getUrlWS();

        $start = microtime(true);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/plain',
                'Connection' => 'keep-alive'
            ])
                ->withOptions(['connect_timeout' => 3])
                ->retry(2, 100)
                ->withBody($encrypted, 'text/plain')
                ->timeout(30)
                ->post($url);

            $elapsed = round(microtime(true) - $start, 3);
            Log::info('INACBG request', [
                'method' => $payload['metadata']['method'] ?? null,
                'url' => $url,
                'elapsed_s' => $elapsed
            ]);

            return $this->decryptResponse($response);
        } catch (\Throwable $e) {
            $elapsed = round(microtime(true) - $start, 3);
            Log::error('INACBG request failed', [
                'method' => $payload['metadata']['method'] ?? null,
                'url' => $url,
                'error' => $e->getMessage(),
                'elapsed_s' => $elapsed
            ]);
            throw $e;
        }
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
                'pasien.no_ktp',
                'pasien.alamat',
                'pasien.pekerjaan',
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

        $idUser = session('auth')['id_user'] ?? null;
        $namaUser = session('user')->nama ?? null;

        if (!$idUser || !$namaUser) {
            return redirect('/')
                ->with('error', 'Session user tidak ditemukan.');
        }

        // Coba cari berdasarkan nama (cara lama)
        $coder = DB::table('petugas')
            ->join('inacbg_coder_nik', 'petugas.nip', '=', 'inacbg_coder_nik.nik')
            ->where('petugas.nama', $namaUser)
            ->select(
                'petugas.nip',
                'petugas.nama',
                'inacbg_coder_nik.no_ik',
                'inacbg_coder_nik.nik'
            )
            ->first();

        // Jika tidak ketemu berdasarkan nama, coba berdasarkan NIK/ID User
        if (!$coder) {
            $coderNik = DB::table('inacbg_coder_nik')
                ->where('nik', $idUser)
                ->first();
                
            if ($coderNik) {
                $coder = (object) [
                    'nip' => $coderNik->nik,
                    'nama' => $namaUser, // Gunakan nama dari session
                    'no_ik' => $coderNik->no_ik,
                    'nik' => $coderNik->nik
                ];
            }
        }

        if (!$coder) {
            return redirect()->back()
                ->with('error', 'Anda belum terdaftar sebagai Coder INACBG.');
        }

        $status_kirim = DB::table('bridging_inacbg_terkirim')
            ->where('no_rawat', $norawat)
            ->exists();

        $penyakit = DB::table('penyakit')
            ->select('kd_penyakit', 'nm_penyakit', 'ciri_ciri', 'status')
            ->where('status', '1')
            ->orderBy('nm_penyakit')
            ->limit(200)
            ->get();


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

        $diagnosainacbg = DB::table('diagnosa_pasien')
            ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->where('diagnosa_pasien.no_rawat', $norawat)
            ->orderBy('diagnosa_pasien.prioritas')
            ->pluck('diagnosa_pasien.kd_penyakit')
            ->implode('#');

        $procedureinacbg = DB::table('prosedur_pasien')
            ->join('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
            ->where('prosedur_pasien.no_rawat', $norawat)
            ->orderBy('prosedur_pasien.prioritas')
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

        $prosedur_non_bedah = 0;

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
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('reg_periksa')
                    ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                    ->whereColumn('reg_periksa.no_rawat', 'billing.no_rawat')
                    ->where(function ($w) {
                        $w->where('poliklinik.nm_poli', 'like', '%fisio%')
                            ->orWhere('poliklinik.nm_poli', 'like', '%rehab%');
                    });
            })
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

        $resume = null;
        if ($pasien->status_lanjut == 'Ranap') {
            $resume = DB::table('resume_pasien_ranap')->where('no_rawat', $norawat)->first();
        } else {
            $resume = DB::table('resume_pasien')->where('no_rawat', $norawat)->first();
        }

        $getSetting = DB::table('setting')->first();

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
            'status_kirim',
            'diagnosainacbg',
            'procedureinacbg',
            'resume',
            'getSetting'
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

            // --- PROSES UPDATE DIAGNOSA DARI FORM ---
            $arrDiag = [];
            if ($request->filled('diagnosa')) {
                DB::table('diagnosa_pasien')->where('no_rawat', $norawat)->delete();
                $items = explode('#', $request->diagnosa);
                $prioritas = 1;
                foreach ($items as $diag) {
                    $diag = trim($diag);
                    if ($diag === '' || !DB::table('penyakit')->where('kd_penyakit', $diag)->exists()) continue;
                    DB::table('diagnosa_pasien')->insert([
                        'no_rawat'    => $norawat,
                        'kd_penyakit' => $diag,
                        'status'      => 'R',
                        'prioritas'   => $prioritas++
                    ]);
                    $arrDiag[] = $diag;
                }
            }

            // --- PROSES UPDATE PROSEDUR DARI FORM ---
            $arrProc = [];
            DB::table('prosedur_pasien')->where('no_rawat', $norawat)->delete();
            if ($request->filled('procedure')) {
                $procs = explode('#', $request->procedure);
                foreach ($procs as $proc) {
                    $proc = trim($proc);
                    // Jika ada format jumlah (misal 39.95+2) ambil kodenya saja untuk dicek
                    $procCode = $proc;
                    if (str_contains($proc, '+')) {
                        $parts = explode('+', $proc);
                        $procCode = trim($parts[0]);
                    }
                    if ($procCode === '' || !DB::table('icd9')->where('kode', $procCode)->exists()) continue;
                    
                    DB::table('prosedur_pasien')->insert([
                        'no_rawat' => $norawat,
                        'kode'     => $procCode
                    ]);
                    $arrProc[] = $procCode;
                }
            }

            $this->updateResumePasien($norawat, $arrDiag, $arrProc);

            // --- RE-CALCULATE DIAGNOSA INACBG & PROCEDURE INACBG ---
            $newDiagnosainacbg = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $norawat)
                ->orderBy('diagnosa_pasien.prioritas')
                ->pluck('diagnosa_pasien.kd_penyakit')
                ->implode('#');

            $newProcedureinacbg = DB::table('prosedur_pasien')
                ->join('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
                ->where('prosedur_pasien.no_rawat', $norawat)
                ->orderBy('prosedur_pasien.prioritas')
                ->get()
                ->map(function ($item) {
                    return $item->jumlah > 1
                        ? $item->kode . '+' . $item->jumlah
                        : $item->kode;
                })
                ->implode('#');

            //  1. NEW CLAIM

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

            // 1.5 SITB VALIDATE
            if (!empty($request->nomor_register_sitb)) {
                $sitbPayload = [
                    "metadata" => ["method" => "sitb_validate"],
                    "data" => [
                        "nomor_sep" => $request->nosep,
                        "nomor_register_sitb" => $request->nomor_register_sitb
                    ]
                ];
                $resSitb = $this->requestInacbg($sitbPayload);
                if (($resSitb['metadata']['message'] ?? '') !== 'Ok') {
                    throw new Exception('SITB VALIDATE GAGAL: ' . json_encode($resSitb));
                }
            }

            // 2. SET CLAIM DATA

            $setClaim = [
                "metadata" => [
                    "method" => "set_claim_data",
                    "nomor_sep" => $request->nosep
                ],
                "data" => [
                    "nomor_sep"   => $request->nosep,
                    "nomor_kartu" => $request->nokartu,
                    "tgl_masuk"   => $pasien->tgl_registrasi . " 00:00:00",
                    "tgl_pulang" => (($pasien->status_lanjut ?? '') == 'Ralan'
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
                    "discharge_status" => $request->discharge_status,
                    "dializer_single_use" => $request->dializer_single_use ?? "0",

                    "tarif_rs" => [
                        "prosedur_non_bedah" => $request->prosedur_non_bedah ?? 0,
                        "prosedur_bedah"     => $request->prosedur_bedah ?? 0,
                        "konsultasi"         => $request->konsultasi ?? 0,
                        "tenaga_ahli"        => $request->tenaga_ahli ?? 0,
                        "keperawatan"        => $request->keperawatan ?? 0,
                        "penunjang"          => $request->penunjang ?? 0,
                        "radiologi"          => $request->radiologi ?? 0,
                        "laboratorium"       => $request->laboratorium ?? 0,
                        "pelayanan_darah"    => $request->pelayanan_darah ?? 0,
                        "rehabilitasi"       => $request->rehabilitasi ?? 0,
                        "kamar"              => $request->kamar ?? 0,
                        "rawat_intensif"     => $request->rawat_intensif ?? 0,
                        "obat"               => $request->obat ?? 0,
                        "obat_kronis"        => $request->obat_kronis ?? 0,
                        "obat_kemoterapi"    => $request->obat_kemoterapi ?? 0,
                        "alkes"              => $request->alkes ?? 0,
                        "bmhp"               => $request->bmhp ?? 0,
                        "sewa_alat"          => $request->sewa_alat ?? 0
                    ],

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

            //  3. SET DIAGNOSA IDRG

            $this->setDiagnosa(
                $request->nosep,
                $request->diagnosa
            );

            //  4. SET PROCEDURE IDRG

            if (!empty($request->procedure)) {
                $this->setProcedure(
                    $request->nosep,
                    $request->procedure
                );
            }

            // CEK HEMODIALISA
            $isHd = preg_match(
                '/(^|#)39\.95(\+\d+)?($|#)/',
                $request->procedure ?? ''
            );

            //  5. GROUPING IDRG

            $idrg = $this->groupingIdrg($request->nosep);

            if (($idrg['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'GROUPING IDRG GAGAL : ' .
                        json_encode($idrg)
                );
            }

            //  6. FINAL IDRG

            $idrgFinal = $this->idrgFinal(
                $request->nosep,
                $request->coder_nik
            );


            if (($idrgFinal['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'IDRG FINAL GAGAL : ' .
                        json_encode($idrgFinal)
                );
            }

            // 7. IMPORT IDRG -> INACBG

            $import = $this->importIdrgToInacbg(
                $request->nosep
            );

            if (($import['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'IMPORT IDRG GAGAL : ' .
                        json_encode($import)
                );
            }

            $updateClaim = [
                "metadata" => [
                    "method" => "set_claim_data",
                    "nomor_sep" => $request->nosep
                ],
                "data" => [
                    "nomor_sep" => $request->nosep,

                    // override diagnosa INACBG
                    "diagnosa" => $newDiagnosainacbg,

                    // override prosedur INACBG
                    "procedure" => $newProcedureinacbg ?? ""
                ]
            ];


            // 8. GROUPING INACBG STAGE 1
            $grouperInacbg1 = [
                "metadata" => [
                    "method"  => "grouper",
                    "stage"   => "1",
                    "grouper" => "inacbg"
                ],
                "data" => [
                    "nomor_sep" => $request->nosep
                ]
            ];

            // OVERRIDE PROCEDURE INACBG
            $this->setProcedureInacbg(
                $request->nosep,
                $newProcedureinacbg
            );

            $resInacbg1 = $this->requestInacbg($grouperInacbg1);

            if (($resInacbg1['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'GROUPING INACBG STAGE 1 GAGAL : ' .
                        json_encode($resInacbg1)
                );
            }

            //  9. GROUPING INACBG STAGE 2

            $grouperInacbg2 = [
                "metadata" => [
                    "method"  => "grouper",
                    "stage"   => "2",
                    "grouper" => "inacbg"
                ],
                "data" => [
                    "nomor_sep" => $request->nosep
                ]
            ];

            $resFinalInacbg = $this->inacbgFinal(
                $request->nosep
            );

            if (($resFinalInacbg['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'INACBG GROUPER FINAL GAGAL : ' .
                        json_encode($resFinalInacbg)
                );
            }

            // 10. FINAL CLAIM

            // PASIEN HEMODIALISA
            if ($isHd) {

                Log::info('HEMODIALISA TERDETEKSI', [
                    'sep' => $request->nosep
                ]);

                /*
                * TEMPAT LOGIKA DIALIZER SINGLE USE
                * JIKA SUDAH TAHU API / FIELD E-KLAIM
                */
            }

            $res4 = $this->final_cbg(
                $request->nosep,
                $request->coder_nik
            );

            if (($res4['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'FINAL CLAIM GAGAL : ' . json_encode($res4)
                );
            }

            //  11. SEND CLAIM INDIVIDUAL

            $sendClaim = $this->sendClaimIndividual(
                $request->nosep
            );

            if (($sendClaim['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    'SEND CLAIM GAGAL : ' .
                        json_encode($sendClaim)
                );
            }




            //  SIMPAN LOG KIRIM INACBG

            DB::table('bridging_inacbg_terkirim')->updateOrInsert(
                [
                    'no_rawat' => $norawat
                ],
                [
                    'no_sep'      => $request->nosep,
                    'nik_coder'   => $request->coder_nik,
                    'nama_coder'  => DB::table('inacbg_coder_nik')
                        ->join('petugas', 'petugas.nip', '=', 'inacbg_coder_nik.nik')
                        ->where('inacbg_coder_nik.no_ik', $request->coder_nik)
                        ->value('petugas.nama'),
                    'tgl_kirim'   => now()
                ]
            );

            // AUTO SIMPAN PDF E-KLAIM
            $this->autoSavePdf($request->nosep);

            DB::commit();

            return redirect()->back()
                ->with(
                    'success',
                    'Berhasil kirim, grouping dan final claim INACBG'
                )
                ->with(
                    'print_url',
                    url('/inacbg/print/' . $request->nosep)
                );
        } catch (Exception $e) {

            DB::rollBack();

            Log::error('INACBG ERROR', [
                'msg' => $e->getMessage()
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    private function final_cbg($nomor_sep, $coder_nik)
    {
        return $this->requestInacbg([
            "metadata" => [
                "method" => "claim_final"
            ],
            "data" => [
                "nomor_sep" => $nomor_sep,
                "coder_nik" => $coder_nik
            ]
        ]);
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

    private function idrgFinal($sep, $coderNik)
    {
        return $this->requestInacbg([
            "metadata" => [
                "method" => "idrg_grouper_final"
            ],
            "data" => [
                "nomor_sep" => $sep,
                "coder_nik" => $coderNik
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

    private function setDiagnosaInacbg($sep, $diagnosa)
    {
        return $this->requestInacbg([
            "metadata" => [
                "method" => "inacbg_diagnosa_set",
                "nomor_sep" => $sep
            ],
            "data" => [
                "diagnosa" => $diagnosa
            ]
        ]);
    }

    private function setProcedureInacbg($sep, $procedure)
    {
        return $this->requestInacbg([
            "metadata" => [
                "method" => "inacbg_procedure_set",
                "nomor_sep" => $sep
            ],
            "data" => [
                "procedure" => $procedure
            ]
        ]);
    }

    private function inacbgFinal($sep)
    {
        return $this->requestInacbg([
            "metadata" => [
                "method" => "inacbg_grouper_final"
            ],
            "data" => [
                "nomor_sep" => $sep
            ]
        ]);
    }

    private function sendClaimIndividual($sep)
    {
        return $this->requestInacbg([
            "metadata" => [
                "method" => "send_claim_individual"
            ],
            "data" => [
                "nomor_sep" => $sep
            ]
        ]);
    }

    private function claimPrint($sep)
    {
        return $this->requestInacbg([
            "metadata" => [
                "method" => "claim_print"
            ],
            "data" => [
                "nomor_sep" => $sep
            ]
        ]);
    }

    private function autoSavePdf($nosep)
    {
        try {
            $result = $this->claimPrint($nosep);
            if (($result['metadata']['message'] ?? '') !== 'Ok' || empty($result['data'])) return false;

            $pdf = base64_decode($result['data']);
            if ($pdf === false) return false;

            $no_rawat = DB::table('bridging_sep')->where('no_sep', $nosep)->value('no_rawat');
            if ($no_rawat) {
                $kodeBerkas = DB::table('master_berkas_digital')->where('nama', 'INACBG')->value('kode');
                if ($kodeBerkas) {
                    $filename = $kodeBerkas . '-' . $nosep . '.pdf';
                    $remote_path = 'pages/upload/' . $filename;
                    
                    $cekSCAN = DB::table('berkas_digital_perawatan')
                        ->where('no_rawat', $no_rawat)
                        ->where('kode', $kodeBerkas)
                        ->first();
                    
                    if ($cekSCAN) {
                        DB::table('berkas_digital_perawatan')
                            ->where('no_rawat', $no_rawat)
                            ->where('kode', $kodeBerkas)
                            ->update(['lokasi_file' => $remote_path]);
                    } else {
                        DB::table('berkas_digital_perawatan')->insert([
                            'no_rawat' => $no_rawat,
                            'kode' => $kodeBerkas,
                            'lokasi_file' => $remote_path
                        ]);
                    }

                    try {
                        if (env('SFTP_HOST')) {
                            $sftp = new \phpseclib3\Net\SFTP(env('SFTP_HOST'), env('SFTP_PORT', 22));
                            if ($sftp->login(env('SFTP_USERNAME'), env('SFTP_PASSWORD'))) {
                                $sftp_full_path = '/opt/lampp/htdocs/webapps/berkasrawat/' . $remote_path;
                                $sftp->put($sftp_full_path, $pdf);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('SFTP Error INACBG: ' . $e->getMessage());
                    }

                    $public_storage_path = public_path('storage/file_scan/' . $filename);
                    if (!file_exists(dirname($public_storage_path))) {
                        mkdir(dirname($public_storage_path), 0755, true);
                    }
                    file_put_contents($public_storage_path, $pdf);

                    // SIMPAN JUGA UNTUK CASEMIX BUNDLING
                    $public_inacbg_path = public_path('storage/file_inacbg/' . $filename);
                    if (!file_exists(dirname($public_inacbg_path))) {
                        mkdir(dirname($public_inacbg_path), 0755, true);
                    }
                    file_put_contents($public_inacbg_path, $pdf);

                    $cekCasemix = DB::table('bw_file_casemix_inacbg')->where('no_rawat', $no_rawat)->first();
                    if ($cekCasemix) {
                        DB::table('bw_file_casemix_inacbg')->where('no_rawat', $no_rawat)->update(['file' => $filename]);
                    } else {
                        DB::table('bw_file_casemix_inacbg')->insert([
                            'no_rawat' => $no_rawat,
                            'file' => $filename
                        ]);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error('AUTO SAVE PDF ERROR', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    public function printClaim($nosep)
    {
        try {

            $result = $this->claimPrint($nosep);

            if (($result['metadata']['message'] ?? '') !== 'Ok') {
                throw new Exception(
                    $result['metadata']['message'] ?? 'Gagal print klaim'
                );
            }

            if (empty($result['data'])) {
                throw new Exception('PDF kosong');
            }

            $pdf = base64_decode($result['data']);

            if ($pdf === false) {
                throw new Exception('Base64 PDF tidak valid');
            }

            // SIMPAN PDF KE DB DAN STORAGE SEBAGAI BERKAS INACBG (Menggunakan method terpisah untuk fallback jika diperlukan)
            $this->autoSavePdf($nosep);

            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header(
                    'Content-Disposition',
                    'inline; filename="klaim_' . $nosep . '.pdf"'
                );
        } catch (Exception $e) {

            Log::error('PRINT CLAIM ERROR', [
                'nosep' => $nosep,
                'msg'   => $e->getMessage()
            ]);

            abort(500, $e->getMessage());
        }
    }

    public function updateDiagnosa(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'diagnosa' => 'nullable',
            'prosedur' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $arrDiag = [];
            // --- PROSES DIAGNOSA ---
            // Selalu hapus dulu semua diagnosa lama
            DB::table('diagnosa_pasien')->where('no_rawat', $request->no_rawat)->delete();

            if ($request->filled('diagnosa')) {
                $items = explode('#', $request->diagnosa);
                $prioritas = 1;

                foreach ($items as $diag) {
                    $diag = trim($diag);
                    if ($diag === '' || !DB::table('penyakit')->where('kd_penyakit', $diag)->exists()) continue;

                    DB::table('diagnosa_pasien')->insert([
                        'no_rawat'    => $request->no_rawat,
                        'kd_penyakit' => $diag,
                        'status'      => 'R',
                        'prioritas'   => $prioritas++
                    ]);
                    $arrDiag[] = $diag;
                }
            }

            $arrProc = [];
            // --- PROSES PROSEDUR ---
            DB::table('prosedur_pasien')->where('no_rawat', $request->no_rawat)->delete();

            if ($request->filled('prosedur')) {
                $procs = explode('#', $request->prosedur);

                foreach ($procs as $proc) {
                    $proc = trim($proc);
                    if ($proc === '' || !DB::table('icd9')->where('kode', $proc)->exists()) continue;

                    DB::table('prosedur_pasien')->insert([
                        'no_rawat' => $request->no_rawat,
                        'kode'     => $proc
                    ]);
                    $arrProc[] = $proc;
                }
            }

            $this->updateResumePasien($request->no_rawat, $arrDiag, $arrProc);

            DB::commit();
            return back()->with('success', 'Data diagnosa dan prosedur berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('UPDATE DIAGNOSA/PROSEDUR ERROR: ' . $e->getMessage());
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
    
    private function updateResumePasien($no_rawat, $diagnosaCodes, $prosedurCodes)
    {
        $resumeData = [
            'kd_diagnosa_utama' => '', 'diagnosa_utama' => '',
            'kd_diagnosa_sekunder' => '', 'diagnosa_sekunder' => '',
            'kd_diagnosa_sekunder2' => '', 'diagnosa_sekunder2' => '',
            'kd_diagnosa_sekunder3' => '', 'diagnosa_sekunder3' => '',
            'kd_diagnosa_sekunder4' => '', 'diagnosa_sekunder4' => '',
            'kd_prosedur_utama' => '', 'prosedur_utama' => '',
            'kd_prosedur_sekunder' => '', 'prosedur_sekunder' => '',
            'kd_prosedur_sekunder2' => '', 'prosedur_sekunder2' => '',
            'kd_prosedur_sekunder3' => '', 'prosedur_sekunder3' => '',
        ];

        $diagFields = [
            ['kd' => 'kd_diagnosa_utama', 'nm' => 'diagnosa_utama'],
            ['kd' => 'kd_diagnosa_sekunder', 'nm' => 'diagnosa_sekunder'],
            ['kd' => 'kd_diagnosa_sekunder2', 'nm' => 'diagnosa_sekunder2'],
            ['kd' => 'kd_diagnosa_sekunder3', 'nm' => 'diagnosa_sekunder3'],
            ['kd' => 'kd_diagnosa_sekunder4', 'nm' => 'diagnosa_sekunder4'],
        ];

        foreach ($diagnosaCodes as $index => $kd) {
            if ($index >= count($diagFields)) break;
            $penyakit = DB::table('penyakit')->where('kd_penyakit', $kd)->first();
            if ($penyakit) {
                $resumeData[$diagFields[$index]['kd']] = $penyakit->kd_penyakit;
                $resumeData[$diagFields[$index]['nm']] = $penyakit->nm_penyakit;
            }
        }

        $procFields = [
            ['kd' => 'kd_prosedur_utama', 'nm' => 'prosedur_utama'],
            ['kd' => 'kd_prosedur_sekunder', 'nm' => 'prosedur_sekunder'],
            ['kd' => 'kd_prosedur_sekunder2', 'nm' => 'prosedur_sekunder2'],
            ['kd' => 'kd_prosedur_sekunder3', 'nm' => 'prosedur_sekunder3'],
        ];

        foreach ($prosedurCodes as $index => $kd) {
            if ($index >= count($procFields)) break;
            if (str_contains($kd, '+')) {
                $kd = trim(explode('+', $kd)[0]);
            }
            $icd9 = DB::table('icd9')->where('kode', $kd)->first();
            if ($icd9) {
                $resumeData[$procFields[$index]['kd']] = $icd9->kode;
                $resumeData[$procFields[$index]['nm']] = $icd9->deskripsi_panjang ?: ($icd9->deskripsi_pendek ?: '');
            }
        }

        $reg = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
        if (!$reg) return;

        $tableName = $reg->status_lanjut == 'Ranap' ? 'resume_pasien_ranap' : 'resume_pasien';

        if (DB::table($tableName)->where('no_rawat', $no_rawat)->exists()) {
            DB::table($tableName)->where('no_rawat', $no_rawat)->update($resumeData);
        } else {
            $insertData = array_merge([
                'no_rawat' => $no_rawat,
                'kd_dokter' => $reg->kd_dokter,
                'keluhan_utama' => '',
                'jalannya_penyakit' => '',
                'pemeriksaan_penunjang' => '',
                'hasil_laborat' => '',
            ], $resumeData);

            if ($tableName == 'resume_pasien') {
                $insertData['kondisi_pulang'] = 'Hidup';
                $insertData['obat_pulang'] = '';
            } else {
                $insertData['diagnosa_awal'] = '';
                $insertData['alasan'] = '';
                $insertData['pemeriksaan_fisik'] = '';
                $insertData['tindakan_dan_operasi'] = '';
                $insertData['obat_di_rs'] = '';
                $insertData['alergi'] = '';
                $insertData['diet'] = '';
                $insertData['lab_belum'] = '';
                $insertData['edukasi'] = '';
                $insertData['cara_keluar'] = 'Sembuh';
                $insertData['ket_keluar'] = '';
                $insertData['keadaan'] = 'Sehat';
                $insertData['ket_keadaan'] = '';
                $insertData['dilanjutkan'] = 'Poliklinik';
                $insertData['ket_dilanjutkan'] = '';
                $insertData['kontrol'] = date('Y-m-d');
                $insertData['obat_pulang'] = '';
            }

            DB::table($tableName)->insert($insertData);
        }
    }
    public function sitbValidate(Request $request)
    {
        try {
            if (!$request->nomor_sep || !$request->nomor_register_sitb) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor SEP atau Register SITB kosong'
                ], 400);
            }

            // Execute new_claim first so INACBG recognizes the SEP
            if ($request->no_rawat) {
                $pasien = DB::table('reg_periksa')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->where('reg_periksa.no_rawat', $request->no_rawat)
                    ->select('reg_periksa.no_rkm_medis', 'pasien.nm_pasien', 'pasien.jk', 'pasien.tgl_lahir', 'pasien.no_peserta', 'pasien.no_ktp')
                    ->first();

                if ($pasien) {
                    $newClaim = [
                        "metadata" => ["method" => "new_claim"],
                        "data" => [
                            "nomor_kartu" => $pasien->no_peserta,
                            "nomor_sep"   => $request->nomor_sep,
                            "nomor_rm"    => $pasien->no_rkm_medis,
                            "nama_pasien" => $pasien->nm_pasien,
                            "tgl_lahir"   => $pasien->tgl_lahir,
                            "gender"      => $pasien->jk == 'L' ? '1' : '2'
                        ]
                    ];
                    $this->requestInacbg($newClaim);
                }
            }

            $sitbPayload = [
                "metadata" => ["method" => "sitb_validate"],
                "data" => [
                    "nomor_sep" => $request->nomor_sep,
                    "nomor_register_sitb" => $request->nomor_register_sitb
                ]
            ];
            
            $res = $this->requestInacbg($sitbPayload);
            
            if (($res['metadata']['code'] ?? 0) == 200 && ($res['response']['status'] ?? '') === 'VALID') {
                $patientData = null;
                if (isset($pasien)) {
                    $patientData = [[
                        'nama' => $pasien->nm_pasien,
                        'nik' => $pasien->no_ktp,
                        'jenis_kelamin_id' => $pasien->jk == 'L' ? '1' : '2'
                    ]];
                } else {
                    $patientData = $res['validation']['data'] ?? $res['response']['data'] ?? $res['data'] ?? null;
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Validasi SITB Berhasil: ' . ($res['response']['detail'] ?? ''),
                    'data' => $patientData
                ]);
            }
            
            $errMessage = $res['response']['detail'] ?? ($res['metadata']['message'] ?? 'Kesalahan pada server INACBG');
            return response()->json([
                'success' => false,
                'message' => $errMessage
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sitbInvalidate(Request $request)
    {
        try {
            if (!$request->nomor_sep) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor SEP kosong'
                ], 400);
            }

            $sitbPayload = [
                "metadata" => ["method" => "sitb_invalidate"],
                "data" => [
                    "nomor_sep" => $request->nomor_sep
                ]
            ];
            
            $res = $this->requestInacbg($sitbPayload);
            
            if (($res['metadata']['code'] ?? 0) == 200) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembatalan SITB Berhasil'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan: ' . json_encode($res)
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateResumeData(Request $request)
    {
        try {
            $no_rawat = $request->no_rawat;
            $reg = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
            if (!$reg) {
                return response()->json(['success' => false, 'message' => 'Data pendaftaran tidak ditemukan.']);
            }

            $tableName = $reg->status_lanjut == 'Ranap' ? 'resume_pasien_ranap' : 'resume_pasien';
            
            $updateData = [
                'keluhan_utama' => $request->keluhan_utama ?? '',
                'jalannya_penyakit' => $request->jalannya_penyakit ?? '',
                'pemeriksaan_penunjang' => $request->pemeriksaan_penunjang ?? '',
                'hasil_laborat' => $request->hasil_laborat ?? '',
                'obat_pulang' => $request->obat_pulang ?? '',
            ];

            if ($request->has('pemeriksaan_fisik')) {
                $updateData['pemeriksaan_fisik'] = $request->pemeriksaan_fisik;
            }
            if ($request->has('tindakan_dan_operasi')) {
                $updateData['tindakan_dan_operasi'] = $request->tindakan_dan_operasi;
            }

            // Diagnosa & Prosedur fields
            $diagProcFields = [
                'diagnosa_utama', 'kd_diagnosa_utama',
                'diagnosa_sekunder', 'kd_diagnosa_sekunder',
                'diagnosa_sekunder2', 'kd_diagnosa_sekunder2',
                'diagnosa_sekunder3', 'kd_diagnosa_sekunder3',
                'diagnosa_sekunder4', 'kd_diagnosa_sekunder4',
                'prosedur_utama', 'kd_prosedur_utama',
                'prosedur_sekunder', 'kd_prosedur_sekunder',
                'prosedur_sekunder2', 'kd_prosedur_sekunder2',
                'prosedur_sekunder3', 'kd_prosedur_sekunder3'
            ];

            foreach ($diagProcFields as $f) {
                if ($request->has($f)) {
                    $updateData[$f] = $request->$f;
                }
            }

            DB::table($tableName)->where('no_rawat', $no_rawat)->update($updateData);

            return response()->json(['success' => true, 'message' => 'Resume berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
