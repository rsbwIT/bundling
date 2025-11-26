<?php

namespace App\Http\Controllers\Fisioterapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Fisioterapi extends Controller
{
    /**
     * =============================================================
     * LIST PASIEN
     * =============================================================
     */
    public function listPasien(Request $request)
    {
        $tanggalMulai   = $request->tanggal_mulai ?? date('Y-m-d');
        $tanggalSelesai = $request->tanggal_selesai ?? date('Y-m-d');

        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->whereIn('reg_periksa.kd_poli', ['FIS', 'FISI'])
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('reg_periksa.tgl_registrasi', 'DESC')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                'dokter.nm_dokter'
            )
            ->get();

        return view('fisioterapi.fisioterapi', compact('data', 'tanggalMulai', 'tanggalSelesai'));
    }

    /**
     * =============================================================
     * TAMPIL FORM
     * =============================================================
     */
    public function form($tahun, $bulan, $hari, $no_rawat)
    {
        $full_no_rawat = "$tahun/$bulan/$hari/$no_rawat";

        $rawat = DB::table('reg_periksa')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('no_rawat', $full_no_rawat)
            ->select('reg_periksa.no_rkm_medis', 'dokter.nm_dokter')
            ->first();

        if (!$rawat) {
            return redirect()->route('fisioterapi.pasien')->with('error', 'Data pasien tidak ditemukan.');
        }

        $rm = $rawat->no_rkm_medis;

        // Ambil lembar terakhir
        $lembarMax = DB::table('fisioterapi_kunjungan')
            ->where('no_rkm_medis', $rm)
            ->max('lembar');

        if (!$lembarMax) $lembarMax = 1;

        // Ambil data kunjungan 1–8 pada lembar tersebut
        $kunjungan = DB::table('fisioterapi_kunjungan')
            ->where('no_rkm_medis', $rm)
            ->where('lembar', $lembarMax)
            ->orderBy('kunjungan')
            ->get()
            ->keyBy('kunjungan');

        // Cek apakah lembar penuh
        $full = true;
        for ($i = 1; $i <= 8; $i++) {
            if (
                empty($kunjungan[$i]) ||
                empty($kunjungan[$i]->program) ||
                empty($kunjungan[$i]->tanggal)
            ) {
                $full = false;
                break;
            }
        }

        // Jika penuh → buat lembar baru
        if ($full) {

            $lembarBaru = $lembarMax + 1;

            DB::table('fisioterapi_form')->updateOrInsert(
                ['no_rkm_medis' => $rm, 'lembar' => $lembarBaru],
                ['diagnosa' => '', 'ft' => '', 'st' => '', 'created_at' => now(), 'updated_at' => now()]
            );

            // Tidak auto-create semua kunjungan → BIARKAN KOSONG
            $kunjungan = collect();
            $lembarMax = $lembarBaru;
        }

        // Ambil form
        $form = DB::table('fisioterapi_form')
            ->where('no_rkm_medis', $rm)
            ->where('lembar', $lembarMax)
            ->first();

        if (!$form) $form = (object)['diagnosa' => '', 'ft' => '', 'st' => ''];

        return view('fisioterapi.form', [
            'data'          => DB::table('pasien')->where('no_rkm_medis', $rm)->first(),
            'form'          => $form,
            'kunjungan'     => $kunjungan,
            'lembar'        => $lembarMax,
            'tahun'         => $tahun,
            'bulan'         => $bulan,
            'hari'          => $hari,
            'no_rawat'      => $no_rawat,
            'full_no_rawat' => $full_no_rawat,
            'dokter'        => $rawat->nm_dokter
        ]);
    }

    /**
     * =============================================================
     * SAVE FORM (HANYA SIMPAN BARIS YANG DIISI)
     * =============================================================
     */
    public function saveForm(Request $request, $tahun, $bulan, $hari, $no_rawat)
    {
        $full_no_rawat = "$tahun/$bulan/$hari/$no_rawat";

        $rawat = DB::table('reg_periksa')->where('no_rawat', $full_no_rawat)->first();
        if (!$rawat) return back()->with('error', 'Pasien tidak ditemukan.');

        $rm     = $rawat->no_rkm_medis;
        $lembar = $request->lembar ?? 1;

        DB::beginTransaction();
        try {

            DB::table('fisioterapi_form')->updateOrInsert(
                ['no_rkm_medis' => $rm, 'lembar' => $lembar],
                [
                    'diagnosa'   => $request->diagnosa,
                    'ft'         => $request->ft,
                    'st'         => $request->st,
                    'updated_at' => now()
                ]
            );

            // ======================================================
            // SIMPAN HANYA BARIS YANG DIISI
            // ======================================================
            for ($i = 1; $i <= 8; $i++) {

                $program = $request->program[$i] ?? null;
                $tanggal = $request->tanggal[$i] ?? null;
                $ttd_ps  = $request->ttd_pasien[$i] ?? null;
                $ttd_dk  = $request->ttd_dokter[$i] ?? null;
                $ttd_tr  = $request->ttd_terapis[$i] ?? null;

                // Jika tidak ada data → lewati
                if (!$program && !$tanggal && !$ttd_ps && !$ttd_dk && !$ttd_tr) {
                    continue;
                }

                // Ambil data lama
                $old = DB::table('fisioterapi_kunjungan')
                    ->where('no_rkm_medis', $rm)
                    ->where('kunjungan', $i)
                    ->where('lembar', $lembar)
                    ->first();

                DB::table('fisioterapi_kunjungan')->updateOrInsert(
                    ['no_rkm_medis' => $rm, 'kunjungan' => $i, 'lembar' => $lembar],
                    [
                        'no_rawat'    => $full_no_rawat,
                        'program'     => $program ?? ($old->program ?? null),
                        'tanggal'     => $tanggal ? date('Y-m-d', strtotime($tanggal)) : ($old->tanggal ?? null),

                        'ttd_pasien'  => $ttd_ps
                            ? $this->saveBase64($ttd_ps, "pasien_{$rm}_{$lembar}_{$i}")
                            : ($old->ttd_pasien ?? null),

                        'ttd_dokter'  => $ttd_dk
                            ? $this->saveBase64($ttd_dk, "dokter_{$rm}_{$lembar}_{$i}")
                            : ($old->ttd_dokter ?? null),

                        'ttd_terapis' => $ttd_tr
                            ? $this->saveBase64($ttd_tr, "terapis_{$rm}_{$lembar}_{$i}")
                            : ($old->ttd_terapis ?? null),

                        'updated_at'  => now(),
                        'created_at'  => $old->created_at ?? now(),
                    ]
                );
            }

            DB::commit();
            return back()->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("FISIOTERAPI SAVE ERROR", ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * =============================================================
     * SIMPAN TTD BASE64
     * =============================================================
     */
    private function saveBase64($data, $prefix)
    {
        try {
            if (!$data || !str_contains($data, 'base64')) return null;

            $data = preg_replace('#^data:image/\w+;base64,#i', '', $data);
            $image = base64_decode($data);

            if (!$image) return null;

            $folder = storage_path('app/public/ttd/');
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $filename = $prefix . "_" . time() . ".png";
            file_put_contents($folder . $filename, $image);

            return $filename;
        } catch (\Exception $e) {
            Log::error("BASE64 ERROR: " . $e->getMessage());
            return null;
        }
    }


    
}
