<?php

namespace App\Http\Controllers\Fisioterapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Milon\Barcode\Facades\DNS2D;

class Fisioterapi extends Controller
{
    /**
     * =============================================================
     * LIST PASIEN
     * =============================================================
     */
    public function listPasien(Request $request)
    {
        $tanggalMulai   = $request->tanggal_mulai   ?? date('Y-m-d');
        $tanggalSelesai = $request->tanggal_selesai ?? date('Y-m-d');

        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->whereIn('reg_periksa.kd_poli', ['FIS', 'FISI'])
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggalMulai, $tanggalSelesai])
            ->orderByDesc('reg_periksa.tgl_registrasi')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                'dokter.nm_dokter'
            )
            ->get();

        return view('fisioterapi.fisioterapi', compact(
            'data',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    /**
     * =============================================================
     * TAMPIL FORM FISIOTERAPI
     * =============================================================
     */
    public function form($tahun, $bulan, $hari, $no_rawat)
    {
        $full_no_rawat = "$tahun/$bulan/$hari/$no_rawat";

        $rawat = DB::table('reg_periksa')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('no_rawat', $full_no_rawat)
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_dokter',
                'reg_periksa.tgl_registrasi',
                'dokter.nm_dokter'
            )
            ->first();

        if (!$rawat) {
            return redirect()
                ->route('fisioterapi.pasien')
                ->with('error', 'Data pasien tidak ditemukan.');
        }

        $rm = $rawat->no_rkm_medis;

        /** CEK LEMBAR TERAKHIR **/
        $lembar = DB::table('fisioterapi_kunjungan')
            ->where('no_rkm_medis', $rm)
            ->max('lembar') ?? 1;

        $kunjungan = DB::table('fisioterapi_kunjungan')
            ->where(['no_rkm_medis' => $rm, 'lembar' => $lembar])
            ->orderBy('kunjungan')
            ->get()
            ->keyBy('kunjungan');

        // Apabila 8 kunjungan penuh → buat lembar baru
        $full = collect(range(1, 8))->every(function ($i) use ($kunjungan) {
            return !empty($kunjungan[$i]) && !empty($kunjungan[$i]->program) && !empty($kunjungan[$i]->tanggal);
        });

        if ($full) {
            $lembar++;

            DB::table('fisioterapi_form')->updateOrInsert(
                ['no_rkm_medis' => $rm, 'lembar' => $lembar],
                [
                    'diagnosa'    => '',
                    'ft'          => '',
                    'st'          => '',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );

            $kunjungan = collect();
        }

        /** HEADER **/
        $form = DB::table('fisioterapi_form')
            ->where(['no_rkm_medis' => $rm, 'lembar' => $lembar])
            ->first() ?? (object)[
                'diagnosa' => '',
                'ft'       => '',
                'st'       => ''
            ];

        return view('fisioterapi.form', [
            'data'          => DB::table('pasien')->where('no_rkm_medis', $rm)->first(),
            'form'          => $form,
            'kunjungan'     => $kunjungan,
            'lembar'        => $lembar,
            'tahun'         => $tahun,
            'bulan'         => $bulan,
            'hari'          => $hari,
            'no_rawat'      => $no_rawat,
            'full_no_rawat' => $full_no_rawat,
            'dokter'        => $rawat->nm_dokter,
            'kd_dokter'     => $rawat->kd_dokter,
            'tgl_registrasi'=> $rawat->tgl_registrasi,
            'getSetting'    => DB::table('setting')->first(),
        ]);
    }

    /**
     * =============================================================
     * SAVE + GENERATE QR TTD DOKTER
     * =============================================================
     */
    public function saveForm(Request $request, $tahun, $bulan, $hari, $no_rawat)
    {
        $full_no_rawat = "$tahun/$bulan/$hari/$no_rawat";

        $rawat = DB::table('reg_periksa')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('no_rawat', $full_no_rawat)
            ->select('reg_periksa.*', 'dokter.nm_dokter', 'dokter.kd_dokter as dokter_kd')
            ->first();

        if (!$rawat) {
            return back()->with('error', 'Pasien tidak ditemukan.');
        }

        $rm      = $rawat->no_rkm_medis;
        $lembar  = $request->lembar ?? 1;
        $setting = DB::table('setting')->first();

        DB::beginTransaction();

        try {

            /** HEADER **/
            DB::table('fisioterapi_form')->updateOrInsert(
                ['no_rkm_medis' => $rm, 'lembar' => $lembar],
                [
                    'diagnosa'   => $request->diagnosa,
                    'ft'         => $request->ft,
                    'st'         => $request->st,
                    'updated_at' => now()
                ]
            );

            /** 1–8 KUNJUNGAN **/
            for ($i = 1; $i <= 8; $i++) {

                $program     = $request->program[$i] ?? null;
                $tanggal     = $request->tanggal[$i] ?? null;
                $ttd_pasien  = $request->ttd_pasien[$i] ?? null;
                $ttd_dokter  = $request->ttd_dokter[$i] ?? null;
                $ttd_terapis = $request->ttd_terapis[$i] ?? null;

                if (!$program && !$tanggal && !$ttd_pasien && !$ttd_dokter && !$ttd_terapis) {
                    continue;
                }

                $old = DB::table('fisioterapi_kunjungan')
                    ->where(['no_rkm_medis' => $rm, 'lembar' => $lembar, 'kunjungan' => $i])
                    ->first();

                $saveProgram = $program ?? ($old->program ?? null);
                $saveTanggal = $tanggal ? date('Y-m-d', strtotime($tanggal)) : ($old->tanggal ?? null);

                /** TTD PASIEN **/
                $savePasien = $ttd_pasien
                    ? $this->saveBase64($ttd_pasien, "pasien_{$rm}_{$lembar}_{$i}")
                    : ($old->ttd_pasien ?? null);

                /** TTD TERAPIS **/
                $saveTerapis = $ttd_terapis
                    ? $this->saveBase64($ttd_terapis, "terapis_{$rm}_{$lembar}_{$i}")
                    : ($old->ttd_terapis ?? null);

                /** TTD DOKTER / QR **/
                $saveDokter = $ttd_dokter
                    ? $this->storeQrFromBase64($ttd_dokter, $full_no_rawat, $lembar, $i)
                    : $this->generateDoctorQR($rawat, $setting, $full_no_rawat, $lembar, $i, $old);

                /** SIMPAN **/
                DB::table('fisioterapi_kunjungan')->updateOrInsert(
                    [
                        'no_rkm_medis' => $rm,
                        'lembar'       => $lembar,
                        'kunjungan'    => $i
                    ],
                    [
                        'no_rawat'    => $full_no_rawat,
                        'program'     => $saveProgram,
                        'tanggal'     => $saveTanggal,
                        'ttd_pasien'  => $savePasien,
                        'ttd_dokter'  => $saveDokter,
                        'ttd_terapis' => $saveTerapis,
                        'created_at'  => $old->created_at ?? now(),
                        'updated_at'  => now(),
                    ]
                );
            }

            DB::commit();
            return back()->with('success', 'Data berhasil disimpan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("FISIOTERAPI SAVE ERROR", ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * =============================================================
     * SIMPAN QR DARI VIEW
     * =============================================================
     */
    private function storeQrFromBase64($base64, $full_no_rawat, $lembar, $kunjungan)
    {
        try {
            if (!$base64) return null;

            if (strpos($base64, 'base64,') !== false) {
                $parts = explode(',', $base64);
                $rawBase64 = $parts[1] ?? '';
            } else {
                $rawBase64 = $base64;
            }

            $binary = base64_decode($rawBase64);
            if (!$binary) return null;

            $folder = storage_path('app/public/qr/');
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $filename = "qr_{$full_no_rawat}_{$lembar}_{$kunjungan}_" . time() . ".png";
            file_put_contents($folder . $filename, $binary);

            DB::table('qr_doktor')->updateOrInsert(
                [
                    'no_rawat'  => $full_no_rawat,
                    'lembar'    => $lembar,
                    'kunjungan' => $kunjungan
                ],
                [
                    'file'       => $filename,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            return $filename;

        } catch (\Throwable $e) {
            Log::warning("STORE QR BASE64 ERROR: " . $e->getMessage());
            return null;
        }
    }

    /**
     * =============================================================
     * AUTO-GENERATE QR DOKTER (DNS2D)
     * =============================================================
     */
    private function generateDoctorQR($rawat, $setting, $full_no_rawat, $lembar, $kunjungan, $old)
    {
        try {
            $nm_dokter = $rawat->nm_dokter ?? 'DOKTER';
            $kd_dokter = $rawat->dokter_kd ?? $rawat->kd_dokter ?? '0000';

            $qrText = "Dikeluarkan di {$setting->nama_instansi}, Kabupaten/Kota {$setting->kabupaten} "
                    . "Ditandatangani secara elektronik oleh {$nm_dokter} "
                    . "ID {$kd_dokter} {$rawat->tgl_registrasi}";

            $rawPng = DNS2D::getBarcodePNG($qrText, "QRCODE");
            if (!$rawPng) return $old->ttd_dokter ?? null;

            $folder = storage_path('app/public/qr/');
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $filename = "qr_{$full_no_rawat}_{$lembar}_{$kunjungan}_" . time() . ".png";
            file_put_contents($folder . $filename, $rawPng);

            DB::table('qr_doktor')->updateOrInsert(
                [
                    'no_rawat'  => $full_no_rawat,
                    'lembar'    => $lembar,
                    'kunjungan' => $kunjungan
                ],
                [
                    'file'       => $filename,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            return $filename;

        } catch (\Throwable $e) {
            Log::warning("QR ERROR: " . $e->getMessage());
            return $old->ttd_dokter ?? null;
        }
    }

    /**
     * =============================================================
     * SIMPAN BASE64 SIGNATURE (PASIEN/TERAPIS)
     * =============================================================
     */
    private function saveBase64($data, $prefix)
    {
        try {
            if (!$data) return null;

            if (strpos($data, 'base64,') !== false) {
                $parts = explode(',', $data);
                $clean = $parts[1] ?? '';
            } else {
                $clean = $data;
            }

            $binary = base64_decode($clean);
            if (!$binary) return null;

            $folder = storage_path('app/public/ttd/');
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $filename = "{$prefix}_" . time() . ".png";
            file_put_contents($folder . $filename, $binary);

            return $filename;

        } catch (\Throwable $e) {
            Log::error('BASE64 ERROR: ' . $e->getMessage());
            return null;
        }
    }
    
}
