<?php

namespace App\Http\Controllers\AntrianFarmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AntrianFarmasi2026 extends Controller
{
    // ============================================================
    // 🖥️ LAYAR UMUM
    // ============================================================
    public function index(Request $request)
    {
        $tanggal = $request->filled('tanggal')
            ? $request->tanggal
            : Carbon::today()->format('Y-m-d');

        $antrian = $this->ambilDataAntrian($tanggal);

        if ($request->wantsJson()) {
            return response()->json($antrian);
        }

        return view('antrian-farmasi.antrianfarmasi2026', compact('antrian', 'tanggal'));
    }

    // ============================================================
    // 🔥 AMBIL DATA + AUTO CREATE NOMOR
    // ============================================================
    private function ambilDataAntrian($tanggal)
    {
        // =============================
        // 1. DATA DASAR
        // =============================
        $data = DB::select("
            SELECT
                rp.no_rawat,
                p.no_rkm_medis,
                p.nm_pasien,
                MIN(STR_TO_DATE(CONCAT(ro.tgl_peresepan,' ',ro.jam_peresepan),'%Y-%m-%d %H:%i:%s')) AS tgl_pertama,

                CASE WHEN COUNT(dor.no_rawat) > 0 THEN 'Racikan' ELSE 'Non Racikan' END AS jenis_obat,
                CASE WHEN pj.png_jawab LIKE '%BPJS%' THEN 'BPJS' ELSE 'NON BPJS' END AS kelompok_pj,

                CASE
                    WHEN pj.png_jawab LIKE '%BPJS%' AND COUNT(dor.no_rawat) > 0 THEN 'A'
                    WHEN pj.png_jawab LIKE '%BPJS%' AND COUNT(dor.no_rawat) = 0 THEN 'B'
                    WHEN pj.png_jawab NOT LIKE '%BPJS%' AND COUNT(dor.no_rawat) = 0 THEN 'C'
                    WHEN pj.png_jawab NOT LIKE '%BPJS%' AND COUNT(dor.no_rawat) > 0 THEN 'D'
                END AS jalur
            FROM reg_periksa rp
            JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
            JOIN penjab pj ON rp.kd_pj = pj.kd_pj
            JOIN resep_obat ro ON rp.no_rawat = ro.no_rawat
            LEFT JOIN detail_obat_racikan dor ON rp.no_rawat = dor.no_rawat AND dor.tgl_perawatan = ?
            LEFT JOIN detail_pemberian_obat dpo ON rp.no_rawat = dpo.no_rawat AND dpo.tgl_perawatan = ?
            WHERE
                rp.status_lanjut = 'ralan'
                AND ro.tgl_penyerahan IS NULL
                AND (dor.no_rawat IS NOT NULL OR dpo.no_rawat IS NOT NULL)
            GROUP BY rp.no_rawat, p.no_rkm_medis, p.nm_pasien, pj.png_jawab
        ", [$tanggal, $tanggal]);

        // =============================
        // 2. BUAT NOMOR (SEKALI SAJA)
        // =============================
        DB::beginTransaction();
        foreach ($data as $row) {
            $this->buatNomorJikaBelumAda($row, $tanggal);
        }
        DB::commit();

        // =============================
        // 3. AMBIL FINAL (NOMOR TETAP)
        // =============================
        $final = DB::select("
            SELECT
                x.jalur,
                ap.nomor_antrian AS no_antrian,
                x.no_rawat,
                x.no_rkm_medis,
                x.nm_pasien,
                x.kelompok_pj,
                x.jenis_obat,
                COALESCE(ap.status_panggil,'MENUNGGU') AS status_panggil,
                ap.waktu_panggil
            FROM (
                SELECT
                    rp.no_rawat,
                    p.no_rkm_medis,
                    p.nm_pasien,
                    CASE WHEN pj.png_jawab LIKE '%BPJS%' THEN 'BPJS' ELSE 'NON BPJS' END AS kelompok_pj,
                    CASE WHEN COUNT(dor.no_rawat) > 0 THEN 'Racikan' ELSE 'Non Racikan' END AS jenis_obat,
                    CASE
                        WHEN pj.png_jawab LIKE '%BPJS%' AND COUNT(dor.no_rawat) > 0 THEN 'A'
                        WHEN pj.png_jawab LIKE '%BPJS%' AND COUNT(dor.no_rawat) = 0 THEN 'B'
                        WHEN pj.png_jawab NOT LIKE '%BPJS%' AND COUNT(dor.no_rawat) = 0 THEN 'C'
                        WHEN pj.png_jawab NOT LIKE '%BPJS%' AND COUNT(dor.no_rawat) > 0 THEN 'D'
                    END AS jalur
                FROM reg_periksa rp
                JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
                JOIN penjab pj ON rp.kd_pj = pj.kd_pj
                JOIN resep_obat ro ON rp.no_rawat = ro.no_rawat
                LEFT JOIN detail_obat_racikan dor ON rp.no_rawat = dor.no_rawat AND dor.tgl_perawatan = ?
                LEFT JOIN detail_pemberian_obat dpo ON rp.no_rawat = dpo.no_rawat AND dpo.tgl_perawatan = ?
                WHERE
                    rp.status_lanjut = 'ralan'
                    AND ro.tgl_penyerahan IS NULL
                    AND (dor.no_rawat IS NOT NULL OR dpo.no_rawat IS NOT NULL)
                GROUP BY rp.no_rawat, p.no_rkm_medis, p.nm_pasien, pj.png_jawab
            ) x
            JOIN antrian_farmasi_panggil ap ON ap.no_rawat = x.no_rawat
            WHERE ap.status_panggil IN ('MENUNGGU','DIPANGGIL')
            ORDER BY x.jalur, ap.nomor_antrian
        ", [$tanggal, $tanggal]);

        return collect($final)->groupBy('jalur');
    }

    // ============================================================
    // 🔢 CREATE NOMOR ANTRIAN
    // ============================================================
    private function buatNomorJikaBelumAda($row, $tanggal)
    {
        $ada = DB::table('antrian_farmasi_panggil')
            ->where('no_rawat', $row->no_rawat)
            ->exists();

        if ($ada) return;

        $last = DB::table('antrian_farmasi_panggil')
            ->where('tanggal', $tanggal)
            ->where('jalur', $row->jalur)
            ->lockForUpdate()
            ->max('nomor_antrian');

        DB::table('antrian_farmasi_panggil')->insert([
            'no_rawat'       => $row->no_rawat,
            'tanggal'        => $tanggal,
            'jalur'          => $row->jalur,
            'nomor_antrian'  => ($last ?? 0) + 1,
            'status_panggil' => 'MENUNGGU',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    // ============================================================
    // 🔊 PANGGIL
    // ============================================================
    public function panggilAntrian(Request $request)
    {
        $request->validate(['no_rawat' => 'required']);

        DB::table('antrian_farmasi_panggil')
            ->where('no_rawat', $request->no_rawat)
            ->update([
                'status_panggil' => 'DIPANGGIL',
                'waktu_panggil'  => now(),
                'updated_at'     => now(),
            ]);

        return response()->json(['status' => 'ok']);
    }

    // ============================================================
    // ✅ SELESAI
    // ============================================================
    public function selesaiAntrian(Request $request)
    {
        $request->validate(['no_rawat' => 'required']);

        DB::beginTransaction();
        try {
            DB::table('antrian_farmasi_panggil')
                ->where('no_rawat', $request->no_rawat)
                ->update([
                    'status_panggil' => 'SELESAI',
                    'updated_at' => now()
                ]);

            DB::table('resep_obat')
                ->where('no_rawat', $request->no_rawat)
                ->whereNull('tgl_penyerahan')
                ->update([
                    'tgl_penyerahan' => Carbon::today()->format('Y-m-d'),
                    'jam_penyerahan' => now()->format('H:i:s'),
                ]);

            DB::commit();
            return response()->json(['status' => 'ok']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status'=>'error','msg'=>$e->getMessage()],500);
        }
    }

    // ============================================================
    // 👨‍⚕️ PETUGAS
    // ============================================================
    public function halamanPanggil()
    {
        $tanggal = Carbon::today()->format('Y-m-d');

        return view('antrian-farmasi.antrianpanggil2026', [
            'antrian' => $this->ambilDataAntrian($tanggal),
            'tanggal' => $tanggal
        ]);
    }

    // ============================================================
    // 📺 TV
    // ============================================================
    public function dataDisplay()
    {
        return response()->json(
            $this->ambilDataAntrian(Carbon::today()->format('Y-m-d'))
        );
    }

    // ============================================================
    // ALIAS
    // ============================================================
    public function halamanPanggilPetugas()
    {
        return $this->halamanPanggil();
    }
}
