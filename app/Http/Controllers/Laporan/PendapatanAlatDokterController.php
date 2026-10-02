<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PendapatanAlatDokterController extends Controller
{
    public function index(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-01');
        $tgl2 = $request->tgl2 ?? date('Y-m-t');
        $kd_dokter = $request->kd_dokter ?? '';

        $result = $this->getData($tgl1, $tgl2, $kd_dokter);
        $data = $result['summary'];
        $details = $result['details'];
        $dokters = $result['dokters'];

        return view('laporan.pendapatan-alat-dokter', compact('tgl1', 'tgl2', 'kd_dokter', 'data', 'details', 'dokters'));
    }

    public function printPdf(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-01');
        $tgl2 = $request->tgl2 ?? date('Y-m-t');
        $kd_dokter = $request->kd_dokter ?? '';

        $result = $this->getData($tgl1, $tgl2, $kd_dokter);
        $data = $result['summary'];
        $details = $result['details'];
        $setting = DB::table('setting')->first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pendapatan-alat-dokter-pdf', compact('tgl1', 'tgl2', 'data', 'details', 'setting'))
                ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Pendapatan_Alat_Dokter_'.$tgl1.'_sd_'.$tgl2.'.pdf');
    }

    private function getData($tgl1, $tgl2, $kd_dokter = '')
    {
        // Exclude ESWL
        $excludeEswl = "AND jp.nm_perawatan NOT LIKE '%ESWL%'";

        // Kumpulkan data dari rawat jalan dan rawat inap
        $sql = "
            SELECT 
                d.kd_dokter,
                d.nm_dokter,
                t.tgl_tindakan,
                t.no_rawat,
                rp.no_rkm_medis,
                pasien.nm_pasien,
                t.nm_perawatan,
                pj.png_jawab,
                (
                    CASE 
                        WHEN d.nm_dokter LIKE '%Nasrul%' OR d.nm_dokter LIKE '%Boby%' OR d.nm_dokter LIKE '%Bobby%' OR d.nm_dokter LIKE '%Exsa%' THEN t.rvp_kso 
                        WHEN t.selisih < 0 THEN t.master_kso
                        ELSE t.rvp_kso
                    END
                ) as total_biaya
            FROM (
                SELECT r.no_rawat, r.kd_dokter, r.tgl_perawatan as tgl_tindakan, r.kso as rvp_kso, jp.kso as master_kso, jp.nm_perawatan, (COALESCE(piu.uangmuka, 0) - COALESCE(piu.totalpiutang, 0)) as selisih 
                FROM rawat_jl_dr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
                UNION ALL
                SELECT r.no_rawat, r.kd_dokter, r.tgl_perawatan as tgl_tindakan, r.kso as rvp_kso, jp.kso as master_kso, jp.nm_perawatan, (COALESCE(piu.uangmuka, 0) - COALESCE(piu.totalpiutang, 0)) as selisih 
                FROM rawat_jl_drpr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
                UNION ALL
                SELECT r.no_rawat, r.kd_dokter, r.tgl_perawatan as tgl_tindakan, r.kso as rvp_kso, jpi.kso as master_kso, jpi.nm_perawatan, (COALESCE(piu.uangmuka, 0) - COALESCE(piu.totalpiutang, 0)) as selisih 
                FROM rawat_inap_dr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
                UNION ALL
                SELECT r.no_rawat, r.kd_dokter, r.tgl_perawatan as tgl_tindakan, r.kso as rvp_kso, jpi.kso as master_kso, jpi.nm_perawatan, (COALESCE(piu.uangmuka, 0) - COALESCE(piu.totalpiutang, 0)) as selisih 
                FROM rawat_inap_drpr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
            ) as t
            JOIN dokter d ON t.kd_dokter = d.kd_dokter
            JOIN reg_periksa rp ON t.no_rawat = rp.no_rawat
            JOIN pasien ON rp.no_rkm_medis = pasien.no_rkm_medis
            JOIN penjab pj ON rp.kd_pj = pj.kd_pj
            LEFT JOIN nota_jalan nj ON t.no_rawat = nj.no_rawat
            LEFT JOIN nota_inap ni ON t.no_rawat = ni.no_rawat
            LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON t.no_rawat = bp.no_rawat
            WHERE (t.nm_perawatan LIKE '%alat%' OR t.nm_perawatan LIKE '%Alat%')
            AND t.nm_perawatan NOT LIKE '%ESWL%'
            AND (
                (pj.png_jawab LIKE '%UMUM%' AND COALESCE(ni.tanggal, nj.tanggal) BETWEEN ? AND ?)
                OR 
                (pj.png_jawab NOT LIKE '%UMUM%' AND bp.tgl_bayar BETWEEN ? AND ?)
            )
            ORDER BY d.nm_dokter ASC, t.tgl_tindakan ASC
        ";

        $results = DB::select($sql, [$tgl1, $tgl2, $tgl1, $tgl2]);

        $grouped = [];
        $details = [];
        $availableDokters = [];

        foreach ($results as $row) {
            $biaya = (float) $row->total_biaya;
            
            // Skip jika biaya 0
            if ($biaya <= 0) {
                continue;
            }

            // Kumpulkan dokter untuk dropdown
            $availableDokters[$row->kd_dokter] = (object)[
                'kd_dokter' => $row->kd_dokter,
                'nm_dokter' => $row->nm_dokter
            ];

            // Filter by kd_dokter jika dipilih
            if ($kd_dokter != '' && $row->kd_dokter != $kd_dokter) {
                continue;
            }

            $dokter = $row->nm_dokter;
            if (!isset($grouped[$dokter])) {
                $grouped[$dokter] = [
                    'umum' => 0,
                    'asuransi' => 0,
                    'bpjs' => 0,
                    'inhealth' => 0,
                    'kemenkes' => 0,
                    'total' => 0
                ];
            }

            $penjab = strtoupper($row->png_jawab);

            if (strpos($penjab, 'UMUM') !== false) {
                $grouped[$dokter]['umum'] += $biaya;
                $kategoriStr = 'UMUM';
            } elseif (strpos($penjab, 'BPJS') !== false || $penjab == 'BPJ' || strpos($penjab, 'COB') !== false) {
                $grouped[$dokter]['bpjs'] += $biaya;
                $kategoriStr = 'BPJS';
            } elseif (strpos($penjab, 'INHEALTH') !== false) {
                $grouped[$dokter]['inhealth'] += $biaya;
                $kategoriStr = 'INHEALTH';
            } elseif (strpos($penjab, 'KEMENKES') !== false) {
                $grouped[$dokter]['kemenkes'] += $biaya;
                $kategoriStr = 'KEMENKES';
            } else {
                $grouped[$dokter]['asuransi'] += $biaya;
                $kategoriStr = 'ASURANSI/PERUSAHAAN';
            }

            $grouped[$dokter]['total'] += $biaya;
            
            // Add to details
            $details[] = (object) [
                'nm_dokter' => $row->nm_dokter,
                'tgl_tindakan' => $row->tgl_tindakan,
                'no_rawat' => $row->no_rawat,
                'no_rkm_medis' => $row->no_rkm_medis,
                'nm_pasien' => $row->nm_pasien,
                'nm_perawatan' => $row->nm_perawatan,
                'jasa_medis' => $biaya,
                'png_jawab' => $row->png_jawab,
                'kategori' => $kategoriStr
            ];
        }

        // Sort by doctor name
        ksort($grouped);
        
        // Urutkan dropdown dokter sesuai abjad
        usort($availableDokters, function ($a, $b) {
            return strcmp($a->nm_dokter, $b->nm_dokter);
        });

        return [
            'summary' => $grouped, 
            'details' => $details,
            'dokters' => $availableDokters
        ];
    }
}
