<?php

namespace App\Http\Controllers\Pkpa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class MonitoringPkpaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter inputs
        $tgl_mulai   = $request->input('tgl_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tgl_selesai = $request->input('tgl_selesai', now()->endOfMonth()->format('Y-m-d'));
        $bangsal     = trim($request->input('bangsal', ''));
        $perPage     = (int) $request->input('per_page', 25);
        $page        = (int) $request->input('page', 1);

        // 2. Cache key berdasarkan kombinasi filter
        $cacheKey = 'pkpa_' . md5($tgl_mulai . $tgl_selesai . $bangsal);
        $cacheTTL = 300; // 5 menit

        // 3. Ambil data dari cache atau jalankan query
        $allResults = Cache::remember($cacheKey, $cacheTTL, function () use ($tgl_mulai, $tgl_selesai, $bangsal) {

            $query = "SELECT 
                p.no_rkm_medis AS `RM`,
                CONCAT(p.nm_pasien, ' (', rp.umurdaftar, ' ', rp.sttsumur, ')') AS `NAMA`,
                p.alamat AS `ALAMAT`,
                
                IFNULL((
                    SELECT peny.nm_penyakit 
                    FROM diagnosa_pasien dp 
                    JOIN penyakit peny ON dp.kd_penyakit = peny.kd_penyakit 
                    WHERE dp.no_rawat = dpo.no_rawat AND dp.prioritas = 1 
                    LIMIT 1
                ), '-') AS `DIAGNOSIS`,

                dok.nm_dokter AS `DPJP`,
                db.nama_brng AS `Nama Antibiotik`,
                
                IFNULL((
                    SELECT aturan 
                    FROM aturan_pakai ap 
                    WHERE ap.no_rawat = dpo.no_rawat AND ap.kode_brng = dpo.kode_brng 
                    LIMIT 1
                ), '-') AS `Regimen Dosis`,
                
                CASE 
                    WHEN COUNT(DISTINCT dpo.tgl_perawatan) > 0 THEN 
                        ROUND(((SUM(dpo.jml) * map.faktor_konversi_gram) / COUNT(DISTINCT dpo.tgl_perawatan)) * 1000, 0)
                    ELSE 0 
                END AS `Dosis per-hari`,
                
                who.kode_atc AS `Kode`,
                COUNT(DISTINCT dpo.tgl_perawatan) AS `Lama Terapi AB`,
                (SUM(dpo.jml) * map.faktor_konversi_gram) AS `Total Dosis`,
                who.nilai_ddd AS `Kode DDD`,
                
                CASE 
                    WHEN (rp.sttsumur = 'Th' AND rp.umurdaftar >= 18) AND who.nilai_ddd > 0 THEN 
                        ROUND((SUM(dpo.jml) * map.faktor_konversi_gram) / who.nilai_ddd, 3)
                    ELSE NULL 
                END AS `DDD`,

                dpo.no_rawat AS `No Rawat`,
                
                CASE 
                    WHEN rp.sttsumur = 'Th' AND rp.umurdaftar >= 18 THEN 'Dewasa'
                    ELSE 'Anak'
                END AS `Kategori Pasien`,

                IFNULL((
                    SELECT DATEDIFF(MAX(tgl_keluar), MIN(tgl_masuk)) + 1 
                    FROM kamar_inap 
                    WHERE no_rawat = dpo.no_rawat 
                    AND tgl_keluar <> '0000-00-00'
                ), 1) AS `Lama Rawat Inap`,

                GROUP_CONCAT(DISTINCT dpo.tgl_perawatan ORDER BY dpo.tgl_perawatan ASC SEPARATOR ', ') AS `Tanggal Pemberian Obat`,

                -- Ruangan = kamar terakhir yang ditempati pasien
                IFNULL((
                    SELECT CONCAT(k.kd_kamar, ' - ', b.nm_bangsal)
                    FROM kamar_inap ki
                    JOIN kamar k ON ki.kd_kamar = k.kd_kamar
                    JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
                    WHERE ki.no_rawat = dpo.no_rawat
                    ORDER BY ki.tgl_masuk DESC
                    LIMIT 1
                ), '-') AS `Ruangan`,

                CASE 
                    WHEN IFNULL((SELECT aturan FROM aturan_pakai ap WHERE ap.no_rawat = dpo.no_rawat AND ap.kode_brng = dpo.kode_brng LIMIT 1), '') LIKE '%HABISKAN%' THEN 'Resep Pulang'
                    WHEN IFNULL((SELECT aturan FROM aturan_pakai ap WHERE ap.no_rawat = dpo.no_rawat AND ap.kode_brng = dpo.kode_brng LIMIT 1), '') LIKE '%PULANG%' THEN 'Resep Pulang'
                    WHEN dpo.tgl_perawatan = (SELECT MAX(tgl_keluar) FROM kamar_inap WHERE no_rawat = dpo.no_rawat) THEN 'Resep Pulang'
                    ELSE 'Resep Ruangan'
                END AS `Status Resep`

            FROM detail_pemberian_obat dpo
            JOIN reg_periksa rp ON dpo.no_rawat = rp.no_rawat
            JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
            JOIN dokter dok ON rp.kd_dokter = dok.kd_dokter
            JOIN databarang db ON dpo.kode_brng = db.kode_brng
            JOIN bw_mapping_atc_obat map ON db.kode_brng = map.kode_brng
            JOIN bw_master_atc_ddd who ON map.kode_atc = who.kode_atc

            WHERE 
                dpo.tgl_perawatan BETWEEN ? AND ?
                
                " . ($bangsal !== '' ? "AND dpo.no_rawat IN (
                    SELECT ki.no_rawat 
                    FROM kamar_inap ki
                    JOIN kamar k ON ki.kd_kamar = k.kd_kamar
                    JOIN bangsal b ON k.kd_bangsal = b.kd_bangsal
                    WHERE b.nm_bangsal LIKE ?
                )" : "") . "
                
                AND db.nama_brng NOT LIKE '%SYR%'
                AND db.nama_brng NOT LIKE '%SYRUP%'

            GROUP BY 
                dpo.no_rawat, 
                db.kode_brng,
                dok.kd_dokter

            HAVING 
                `Status Resep` = 'Resep Ruangan'

            ORDER BY 
                `Kategori Pasien` DESC, dpo.no_rawat ASC";

            $bindings = [$tgl_mulai, $tgl_selesai];
            if ($bangsal !== '') {
                $bindings[] = '%' . $bangsal . '%';
            }

            return DB::select($query, $bindings);
        });

        // 4. Hitung statistik dari semua data (sebelum paginate)
        $collection  = collect($allResults);
        $totalRows   = $collection->count();
        $totalDewasa = $collection->where('Kategori Pasien', 'Dewasa')->count();
        $totalAnak   = $collection->where('Kategori Pasien', 'Anak')->count();
        $stats = compact('totalRows', 'totalDewasa', 'totalAnak');

        // 5. Handle Excel Export — kirim semua data tanpa paginate
        if ($request->input('export') === 'excel') {
            $label = $bangsal !== '' ? str_replace(' ', '_', $bangsal) : 'Semua_Ruangan';
            $filename = "Monitoring_PKPA_Antibiotik_{$label}_{$tgl_mulai}_to_{$tgl_selesai}.xls";

            header("Content-Type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Pragma: no-cache");
            header("Expires: 0");

            return view('pkpa.monitoring.export', [
                'results'     => $allResults,
                'tgl_mulai'   => $tgl_mulai,
                'tgl_selesai' => $tgl_selesai,
                'bangsal'     => $bangsal !== '' ? $bangsal : 'Semua Ruangan'
            ]);
        }

        // 6. Pagination manual
        $paginated = new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $totalRows,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->except('page')]
        );

        return view('pkpa.monitoring.index', [
            'results'      => $paginated,
            'allResults'   => $allResults,
            'stats'        => $stats,
            'tgl_mulai'    => $tgl_mulai,
            'tgl_selesai'  => $tgl_selesai,
            'bangsal'      => $bangsal,
            'perPage'      => $perPage,
        ]);
    }
}
