<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use DatePeriod;
use DateTime;
use DateInterval;

class RekapPendapatanHarianController extends Controller
{
    public function index(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-01');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
        $kdPj = $request->kd_pj ?? 'UMU';
        $statusLanjut = $request->status_lanjut ?? 'Ranap'; // default 'Ranap', supports 'Ralan' and 'SEMUA'

        $penjaminLabel = match($kdPj) {
            'BPJ' => 'BPJS',
            'ASURANSI' => 'Asuransi',
            'INHEALTH' => 'Mandiri Inhealth & Askes Inhealth',
            'SEMUA' => 'Semua Penjamin',
            default => 'Umum',
        };

        $setting = \Illuminate\Support\Facades\DB::table('setting')->first();

        if ($statusLanjut === 'SEMUA') {
            $dataRekapRanap = $this->getRekapData($tgl1, $tgl2, $kdPj, 'Ranap');
            $dataRekapRalan = $this->getRekapData($tgl1, $tgl2, $kdPj, 'Ralan');
            return view('laporan.rekappendapatanharian', compact('tgl1', 'tgl2', 'kdPj', 'statusLanjut', 'penjaminLabel', 'dataRekapRanap', 'dataRekapRalan', 'setting'));
        } else {
            $dataRekap = $this->getRekapData($tgl1, $tgl2, $kdPj, $statusLanjut);
            return view('laporan.rekappendapatanharian', compact('tgl1', 'tgl2', 'kdPj', 'statusLanjut', 'penjaminLabel', 'dataRekap', 'setting'));
        }
    }

    public function printPdf(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-01');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
        $kdPj = $request->kd_pj ?? 'UMU';
        $statusLanjut = $request->status_lanjut ?? 'Ranap';

        $penjaminLabel = match($kdPj) {
            'BPJ' => 'BPJS',
            'ASURANSI' => 'Asuransi',
            'INHEALTH' => 'Mandiri Inhealth & Askes Inhealth',
            'SEMUA' => 'Semua Penjamin',
            default => 'Umum',
        };

        $dataRekapRanap = collect();
        $dataRekapRalan = collect();
        $dataRekap = collect();

        if ($statusLanjut === 'SEMUA') {
            $dataRekapRanap = $this->getRekapData($tgl1, $tgl2, $kdPj, 'Ranap');
            $dataRekapRalan = $this->getRekapData($tgl1, $tgl2, $kdPj, 'Ralan');
        } else {
            $dataRekap = $this->getRekapData($tgl1, $tgl2, $kdPj, $statusLanjut);
        }

        $setting = DB::table('setting')->first();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.rekappendapatanharian-pdf', compact(
            'tgl1', 'tgl2', 'kdPj', 'statusLanjut', 'penjaminLabel', 
            'setting', 'dataRekapRanap', 'dataRekapRalan', 'dataRekap'
        ))->setPaper('a1', 'landscape');

        return $pdf->stream('Rekap_Pendapatan_Harian_'.$tgl1.'_sd_'.$tgl2.'.pdf');
    }

    private function getRekapData($tgl1, $tgl2, $kdPj, $statusLanjut)
    {

        // Tentukan filter status_lanjut
        if ($statusLanjut === 'Ralan') {
            $statusLanjutClause = "AND rp.status_lanjut = 'Ralan'";
        } elseif ($statusLanjut === 'SEMUA') {
            $statusLanjutClause = "";
        } else {
            $statusLanjutClause = "AND rp.status_lanjut = 'Ranap'";
        }

        // Tentukan sumber tanggal transaksi berdasarkan penjamin:
        // - Umum (UMU): berdasarkan tanggal nota (nota_inap / nota_jalan)
        // - BPJS, Asuransi, Inhealth: berdasarkan tanggal pembayaran piutang (bayar_piutang.tgl_bayar)
        // - SEMUA: gabungan Umum (tanggal nota) dan Non-Umum (tanggal bayar piutang)
        if ($kdPj === 'UMU') {
            if ($statusLanjut === 'Ralan') {
                $notaSource = 'nota_jalan';
            } elseif ($statusLanjut === 'SEMUA') {
                $notaSource = "(SELECT no_rawat, tanggal FROM nota_inap WHERE tanggal BETWEEN '{$tgl1}' AND '{$tgl2}' UNION ALL SELECT no_rawat, tanggal FROM nota_jalan WHERE tanggal BETWEEN '{$tgl1}' AND '{$tgl2}')";
            } else {
                $notaSource = 'nota_inap';
            }
        } elseif ($kdPj === 'SEMUA') {
            if ($statusLanjut === 'Ralan') {
                $notaSource = "(
                    SELECT nj.no_rawat, nj.tanggal 
                    FROM nota_jalan nj 
                    JOIN reg_periksa rp ON rp.no_rawat = nj.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND nj.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT DISTINCT bp.no_rawat, bp.tgl_bayar as tanggal 
                    FROM bayar_piutang bp 
                    JOIN reg_periksa rp ON rp.no_rawat = bp.no_rawat 
                    WHERE rp.kd_pj != 'UMU' AND bp.tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}'
                )";
            } elseif ($statusLanjut === 'SEMUA') {
                $notaSource = "(
                    SELECT ni.no_rawat, ni.tanggal 
                    FROM nota_inap ni 
                    JOIN reg_periksa rp ON rp.no_rawat = ni.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND ni.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT nj.no_rawat, nj.tanggal 
                    FROM nota_jalan nj 
                    JOIN reg_periksa rp ON rp.no_rawat = nj.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND nj.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT DISTINCT bp.no_rawat, bp.tgl_bayar as tanggal 
                    FROM bayar_piutang bp 
                    JOIN reg_periksa rp ON rp.no_rawat = bp.no_rawat 
                    WHERE rp.kd_pj != 'UMU' AND bp.tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}'
                )";
            } else {
                // Ranap
                $notaSource = "(
                    SELECT ni.no_rawat, ni.tanggal 
                    FROM nota_inap ni 
                    JOIN reg_periksa rp ON rp.no_rawat = ni.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND ni.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT DISTINCT bp.no_rawat, bp.tgl_bayar as tanggal 
                    FROM bayar_piutang bp 
                    JOIN reg_periksa rp ON rp.no_rawat = bp.no_rawat 
                    WHERE rp.kd_pj != 'UMU' AND bp.tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}'
                )";
            }
        } else {
            // Untuk BPJS, ASURANSI, INHEALTH (atau kode penjamin non-umum lainnya)
            $notaSource = "(SELECT DISTINCT no_rawat, tgl_bayar as tanggal FROM bayar_piutang WHERE tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}')";
        }

        // NIP petugas/paramedis yang dihitung sebagai JM Dokter (sesuai instruksi & template JM)
        $nipDokterKhusus = [
            '12041999',
            '0518010327', '518010327',
            '1802081010970003', '1802081010970000',
            '10104020181',
            '0817010312', '817010312',
            '05084010153', '5084010153',
            '0106010608', '106010608',
            '19960223',
            '0224010675', '224010675',
            '1802086108980001', '1802086108980000',
            '1802054204000003', '1802054204000000',
            '09964020055', '9964020055',
            '88888',
            '0214010227', '214010227',
            '0512010199', '512010199',
            '1204020129'
        ];
        $nipIn = "'" . implode("','", array_unique($nipDokterKhusus)) . "'";

        // Daftar kode penjamin Mandiri Inhealth & Askes Inhealth
        $inhealthCodes = DB::table('penjab')
            ->where('png_jawab', 'like', '%inhealth%')
            ->pluck('kd_pj')
            ->toArray();
        if (empty($inhealthCodes)) {
            $inhealthCodes = ['106', '107', '108', 'A09', 'C52', 'CMI', 'D79', 'INH', 'PT2'];
        }
        $inhealthIn = "'" . implode("','", $inhealthCodes) . "'";

        // Closure filter penjamin untuk Query Builder (Billing, Lab, RO, Operasi)
        $applyPenjaminFilter = function ($query) use ($kdPj, $inhealthCodes) {
            if (!$kdPj || $kdPj === 'SEMUA') {
                return;
            }
            if ($kdPj === 'INHEALTH') {
                $query->whereIn('rp.kd_pj', $inhealthCodes);
            } elseif ($kdPj === 'ASURANSI') {
                $excludeCodes = array_merge(['UMU', 'BPJ', '-'], $inhealthCodes);
                $query->whereNotIn('rp.kd_pj', $excludeCodes);
            } else {
                $query->where('rp.kd_pj', $kdPj);
            }
        };

        // Filter penjamin untuk Raw SQL (Paket Tindakan)
        if (!$kdPj || $kdPj === 'SEMUA') {
            $pjClause = "";
        } elseif ($kdPj === 'INHEALTH') {
            $pjClause = "AND rp.kd_pj IN ({$inhealthIn})";
        } elseif ($kdPj === 'ASURANSI') {
            $excludeCodes = array_merge(['UMU', 'BPJ', '-'], $inhealthCodes);
            $excludeIn = "'" . implode("','", $excludeCodes) . "'";
            $pjClause = "AND rp.kd_pj NOT IN ({$excludeIn})";
        } else {
            $pjClause = "AND rp.kd_pj = '{$kdPj}'";
        }

        // 1. REGISTRASI
        $regQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Registrasi')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);

        $applyPenjaminFilter($regQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $regQuery->where('rp.status_lanjut', $statusLanjut);
        }

        $regPerTgl = $regQuery->select('ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_reg'))
            ->groupBy('ni.tanggal')
            ->pluck('total_reg', 'ni.tanggal');

        // 2. PAKET TINDAKAN (JS, BHP, JM DR, PR, KSO)

        $sql = "
            SELECT 
                combined.tanggal,
                SUM(combined.material) as js,
                SUM(combined.bhp) as bhp,
                SUM(combined.jm_dr) as jm_dr,
                SUM(combined.pr) as pr,
                SUM(combined.kso) as kso
            FROM (
                -- 1. rawat_jl_dr
                SELECT ni.tanggal, t.material, t.bhp, t.tarif_tindakandr as jm_dr, 0 as pr,
                       COALESCE(t.kso, 0) as kso
                FROM rawat_jl_dr t
                JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat
                JOIN jns_perawatan jp ON jp.kd_jenis_prw = t.kd_jenis_prw
                WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}

                UNION ALL

                -- 2. rawat_jl_pr
                SELECT ni.tanggal, t.material, t.bhp,
                       (CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr,
                       (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr,
                       COALESCE(t.kso, 0) as kso
                FROM rawat_jl_pr t
                JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat
                JOIN jns_perawatan jp ON jp.kd_jenis_prw = t.kd_jenis_prw
                WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}

                UNION ALL

                -- 3. rawat_jl_drpr
                SELECT ni.tanggal, t.material, t.bhp,
                       (t.tarif_tindakandr + CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr,
                       (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr,
                       COALESCE(t.kso, 0) as kso
                FROM rawat_jl_drpr t
                JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat
                JOIN jns_perawatan jp ON jp.kd_jenis_prw = t.kd_jenis_prw
                WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}

                UNION ALL

                -- 4. rawat_inap_dr
                SELECT ni.tanggal, t.material, t.bhp, t.tarif_tindakandr as jm_dr, 0 as pr,
                       COALESCE(t.kso, 0) as kso
                FROM rawat_inap_dr t
                JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat
                JOIN jns_perawatan_inap jp ON jp.kd_jenis_prw = t.kd_jenis_prw
                WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}

                UNION ALL

                -- 5. rawat_inap_pr
                SELECT ni.tanggal, t.material, t.bhp,
                       (CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr,
                       (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr,
                       COALESCE(t.kso, 0) as kso
                FROM rawat_inap_pr t
                JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat
                JOIN jns_perawatan_inap jp ON jp.kd_jenis_prw = t.kd_jenis_prw
                WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}

                UNION ALL

                -- 6. rawat_inap_drpr
                SELECT ni.tanggal, t.material, t.bhp,
                       (t.tarif_tindakandr + CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr,
                       (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr,
                       COALESCE(t.kso, 0) as kso
                FROM rawat_inap_drpr t
                JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat
                JOIN jns_perawatan_inap jp ON jp.kd_jenis_prw = t.kd_jenis_prw
                WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
            ) as combined
            GROUP BY combined.tanggal
            ORDER BY combined.tanggal
        ";

        $bindings = [
            $tgl1, $tgl2,
            $tgl1, $tgl2,
            $tgl1, $tgl2,
            $tgl1, $tgl2,
            $tgl1, $tgl2,
            $tgl1, $tgl2,
        ];

        $tindakanPerTgl = collect(DB::select($sql, $bindings))->keyBy('tanggal');

        // 3. OBAT + EMBALASE + TUSLAH
        $obatQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Obat')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);

        $applyPenjaminFilter($obatQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $obatQuery->where('rp.status_lanjut', $statusLanjut);
        }

        $obatPerTgl = $obatQuery->select('ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_obat'))
            ->groupBy('ni.tanggal')
            ->pluck('total_obat', 'ni.tanggal');

        // 4. RETUR OBAT
        $returQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Retur Obat')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);

        $applyPenjaminFilter($returQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $returQuery->where('rp.status_lanjut', $statusLanjut);
        }

        $returPerTgl = $returQuery->select('ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_retur'))
            ->groupBy('ni.tanggal')
            ->pluck('total_retur', 'ni.tanggal');

        // 5. LAB (periksa_lab: JS & BHP)
        $labQuery = DB::table('periksa_lab as pl')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'pl.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'pl.no_rawat')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);

        $applyPenjaminFilter($labQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $labQuery->where('rp.status_lanjut', $statusLanjut);
        }

        $labPerTgl = $labQuery->select(
            'ni.tanggal',
            DB::raw('SUM(pl.bagian_rs) as js'),
            DB::raw('SUM(pl.bhp) as bhp'),
            DB::raw('SUM(pl.tarif_tindakan_dokter + pl.tarif_tindakan_petugas + pl.tarif_perujuk) as jm_lab')
        )
        ->groupBy('ni.tanggal')
        ->get()
        ->keyBy('tanggal');

        // 6. RO / RADIOLOGI (periksa_radiologi: JS, BHP, JM PJ, PETUGAS, JM PR)
        $roQuery = DB::table('periksa_radiologi as pr')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'pr.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'pr.no_rawat')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);

        $applyPenjaminFilter($roQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $roQuery->where('rp.status_lanjut', $statusLanjut);
        }

        $roPerTgl = $roQuery->select(
            'ni.tanggal',
            DB::raw("SUM(pr.bagian_rs + CASE WHEN pr.dokter_perujuk = 'D0000091' THEN pr.tarif_perujuk ELSE 0 END) as js"),
            DB::raw('SUM(pr.bhp) as bhp'),
            DB::raw('SUM(pr.tarif_tindakan_dokter) as jm_pj'),
            DB::raw('SUM(pr.tarif_tindakan_petugas) as petugas'),
            DB::raw("SUM(CASE WHEN pr.dokter_perujuk = 'D0000091' THEN 0 ELSE pr.tarif_perujuk END) as perujuk")
        )
        ->groupBy('ni.tanggal')
        ->get()
        ->keyBy('tanggal');

        // 7. POTONGAN (POT)
        $potQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Potongan')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);

        $applyPenjaminFilter($potQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $potQuery->where('rp.status_lanjut', $statusLanjut);
        }

        $potPerTgl = $potQuery->select('ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_pot'))
            ->groupBy('ni.tanggal')
            ->pluck('total_pot', 'ni.tanggal');

        // 8. TAMBAHAN (TBM)
        $tbmQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Tambahan')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);

        $applyPenjaminFilter($tbmQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $tbmQuery->where('rp.status_lanjut', $statusLanjut);
        }

        $tbmPerTgl = $tbmQuery->select('ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_tbm'))
            ->groupBy('ni.tanggal')
            ->pluck('total_tbm', 'ni.tanggal');

        // 9. KAMAR + SERVICE
        $kmrQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Kamar')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);

        $applyPenjaminFilter($kmrQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $kmrQuery->where('rp.status_lanjut', $statusLanjut);
        }

        $kmrPerTgl = $kmrQuery->select('ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_kmr'))
            ->groupBy('ni.tanggal')
            ->pluck('total_kmr', 'ni.tanggal');

        // 10. OK / OPERASI
        $okQuery = DB::table('operasi as o')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'o.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'o.no_rawat')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);

        $applyPenjaminFilter($okQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $okQuery->where('rp.status_lanjut', $statusLanjut);
        }

        $okPerTgl = $okQuery->select(
            'ni.tanggal',
            DB::raw("SUM(
                o.biayaoperator1 + o.biayaoperator2 + o.biayaoperator3 + o.biayadokter_anak + o.biayadokter_anestesi + o.biaya_dokter_pjanak + o.biaya_dokter_umum
                + CASE WHEN o.asisten_operator1 IN ($nipIn) THEN o.biayaasisten_operator1 ELSE 0 END
                + CASE WHEN o.asisten_operator2 IN ($nipIn) THEN o.biayaasisten_operator2 ELSE 0 END
                + CASE WHEN o.asisten_operator3 IN ($nipIn) THEN o.biayaasisten_operator3 ELSE 0 END
                + CASE WHEN o.asisten_anestesi IN ($nipIn) THEN o.biayaasisten_anestesi ELSE 0 END
                + CASE WHEN o.asisten_anestesi2 IN ($nipIn) THEN o.biayaasisten_anestesi2 ELSE 0 END
                + CASE WHEN o.bidan IN ($nipIn) THEN o.biayabidan ELSE 0 END
                + CASE WHEN o.bidan2 IN ($nipIn) THEN o.biayabidan2 ELSE 0 END
                + CASE WHEN o.bidan3 IN ($nipIn) THEN o.biayabidan3 ELSE 0 END
                + CASE WHEN o.instrumen IN ($nipIn) THEN o.biayainstrumen ELSE 0 END
                + CASE WHEN o.perawaat_resusitas IN ($nipIn) THEN o.biayaperawaat_resusitas ELSE 0 END
                + CASE WHEN o.perawat_luar IN ($nipIn) THEN o.biayaperawat_luar ELSE 0 END
                + CASE WHEN o.omloop IN ($nipIn) THEN o.biaya_omloop ELSE 0 END
                + CASE WHEN o.omloop2 IN ($nipIn) THEN o.biaya_omloop2 ELSE 0 END
                + CASE WHEN o.omloop3 IN ($nipIn) THEN o.biaya_omloop3 ELSE 0 END
                + CASE WHEN o.omloop4 IN ($nipIn) THEN o.biaya_omloop4 ELSE 0 END
                + CASE WHEN o.omloop5 IN ($nipIn) THEN o.biaya_omloop5 ELSE 0 END
            ) as jm_dr"),
            DB::raw("SUM(
                CASE WHEN o.asisten_operator1 NOT IN ($nipIn) THEN o.biayaasisten_operator1 ELSE 0 END
                + CASE WHEN o.asisten_operator2 NOT IN ($nipIn) THEN o.biayaasisten_operator2 ELSE 0 END
                + CASE WHEN o.asisten_operator3 NOT IN ($nipIn) THEN o.biayaasisten_operator3 ELSE 0 END
                + CASE WHEN o.asisten_anestesi NOT IN ($nipIn) THEN o.biayaasisten_anestesi ELSE 0 END
                + CASE WHEN o.asisten_anestesi2 NOT IN ($nipIn) THEN o.biayaasisten_anestesi2 ELSE 0 END
                + CASE WHEN o.bidan NOT IN ($nipIn) THEN o.biayabidan ELSE 0 END
                + CASE WHEN o.bidan2 NOT IN ($nipIn) THEN o.biayabidan2 ELSE 0 END
                + CASE WHEN o.bidan3 NOT IN ($nipIn) THEN o.biayabidan3 ELSE 0 END
                + CASE WHEN o.instrumen NOT IN ($nipIn) THEN o.biayainstrumen ELSE 0 END
                + CASE WHEN o.perawaat_resusitas NOT IN ($nipIn) THEN o.biayaperawaat_resusitas ELSE 0 END
                + CASE WHEN o.perawat_luar NOT IN ($nipIn) THEN o.biayaperawat_luar ELSE 0 END
                + CASE WHEN o.omloop NOT IN ($nipIn) THEN o.biaya_omloop ELSE 0 END
                + CASE WHEN o.omloop2 NOT IN ($nipIn) THEN o.biaya_omloop2 ELSE 0 END
                + CASE WHEN o.omloop3 NOT IN ($nipIn) THEN o.biaya_omloop3 ELSE 0 END
                + CASE WHEN o.omloop4 NOT IN ($nipIn) THEN o.biaya_omloop4 ELSE 0 END
                + CASE WHEN o.omloop5 NOT IN ($nipIn) THEN o.biaya_omloop5 ELSE 0 END
            ) as jm_pr"),
            DB::raw('SUM(o.biayaalat + o.biayasewaok + o.akomodasi + o.bagian_rs + o.biayasarpras) as js')
        )
        ->groupBy('ni.tanggal')
        ->get()
        ->keyBy('tanggal');

        // 11. EKSES (Ekses Ranap & Ralan)
        $eksesRanapQuery = DB::table('piutang_pasien as pp')
            ->leftJoin('detail_nota_inap as dni', 'pp.no_rawat', '=', 'dni.no_rawat')
            ->join('reg_periksa as rp', 'pp.no_rawat', '=', 'rp.no_rawat')
            ->where('rp.status_lanjut', 'Ranap')
            ->whereBetween('pp.tgl_piutang', [$tgl1, $tgl2]);
        
        $eksesRanapPerTgl = $eksesRanapQuery->select('pp.tgl_piutang', DB::raw('SUM(dni.besar_bayar) as total_ekses'))
            ->groupBy('pp.tgl_piutang')
            ->pluck('total_ekses', 'pp.tgl_piutang');

        $eksesRalanQuery = DB::table('piutang_pasien as pp')
            ->leftJoin('detail_nota_jalan as dnj', 'pp.no_rawat', '=', 'dnj.no_rawat')
            ->join('reg_periksa as rp', 'pp.no_rawat', '=', 'rp.no_rawat')
            ->where('rp.status_lanjut', 'Ralan')
            ->whereBetween('pp.tgl_piutang', [$tgl1, $tgl2]);
        
        $eksesRalanPerTgl = $eksesRalanQuery->select('pp.tgl_piutang', DB::raw('SUM(dnj.besar_bayar) as total_ekses'))
            ->groupBy('pp.tgl_piutang')
            ->pluck('total_ekses', 'pp.tgl_piutang');

        // 12. PEMBAYARAN PJ
        $pjQuery = DB::table('tagihan_sadewa')
            ->where('no_nota', 'like', 'PJ%')
            ->whereDate('tgl_bayar', '>=', $tgl1)
            ->whereDate('tgl_bayar', '<=', $tgl2);
        
        $pjPerTgl = $pjQuery->select(DB::raw('DATE(tgl_bayar) as tanggal'), DB::raw('SUM(jumlah_bayar) as total_pj'))
            ->groupBy(DB::raw('DATE(tgl_bayar)'))
            ->pluck('total_pj', 'tanggal');

        // Susun baris per tanggal dari $tgl1 hingga $tgl2
        $period = new DatePeriod(
            new DateTime($tgl1),
            new DateInterval('P1D'),
            (new DateTime($tgl2))->modify('+1 day')
        );

        $dataRekap = collect();
        foreach ($period as $dt) {
            $tglStr = $dt->format('Y-m-d');
            $tindakan = $tindakanPerTgl->get($tglStr);
            $regVal = (float) $regPerTgl->get($tglStr, 0);

            $jsVal   = $tindakan ? (float)$tindakan->js : 0;
            $bhpVal  = $tindakan ? (float)$tindakan->bhp : 0;
            $jmDrVal = $tindakan ? (float)$tindakan->jm_dr : 0;
            $prVal   = $tindakan ? (float)$tindakan->pr : 0;
            $ksoVal  = $tindakan ? (float)$tindakan->kso : 0;

            $obatVal  = (float) $obatPerTgl->get($tglStr, 0);
            $returVal = (float) $returPerTgl->get($tglStr, 0);

            $labItem = $labPerTgl->get($tglStr);
            $labJsVal  = $labItem ? (float)$labItem->js : 0;
            $labBhpVal = $labItem ? (float)$labItem->bhp : 0;
            $labJmVal  = $labItem ? (float)$labItem->jm_lab : 0;

            $roItem = $roPerTgl->get($tglStr);
            $roJsVal      = $roItem ? (float)$roItem->js : 0;
            $roBhpVal     = $roItem ? (float)$roItem->bhp : 0;
            $roJmPjVal    = $roItem ? (float)$roItem->jm_pj : 0;
            $roPetugasVal = $roItem ? (float)$roItem->petugas : 0;
            $roPerujukVal = $roItem ? (float)$roItem->perujuk : 0;

            $potVal = (float) $potPerTgl->get($tglStr, 0);
            $tbmVal = (float) $tbmPerTgl->get($tglStr, 0);
            $kmrVal = (float) $kmrPerTgl->get($tglStr, 0);

            $okItem    = $okPerTgl->get($tglStr);
            $okJmDrVal = $okItem ? (float)$okItem->jm_dr : 0;
            $okJmPrVal = $okItem ? (float)$okItem->jm_pr : 0;
            $okJsVal   = $okItem ? (float)$okItem->js : 0;

            $eksesRanapVal = (float) $eksesRanapPerTgl->get($tglStr, 0);
            $eksesRalanVal = (float) $eksesRalanPerTgl->get($tglStr, 0);
            $eksesVal = 0;
            if ($statusLanjut === 'Ranap') {
                $eksesVal = $eksesRanapVal;
            } elseif ($statusLanjut === 'Ralan') {
                $eksesVal = $eksesRalanVal;
            } else {
                $eksesVal = $eksesRanapVal + $eksesRalanVal;
            }

            $pjVal = (float) $pjPerTgl->get($tglStr, 0);
            if ($statusLanjut === 'Ranap') {
                $pjVal = 0;
            }

            $rowTotal = $regVal + $jsVal + $bhpVal + $jmDrVal + $prVal + $ksoVal
                + $obatVal + $returVal
                + $labJsVal + $labBhpVal + $labJmVal
                + $roJsVal + $roBhpVal + $roJmPjVal + $roPetugasVal + $roPerujukVal
                + $potVal + $tbmVal + $kmrVal
                + $okJmDrVal + $okJmPrVal + $okJsVal;

            $dataRekap->push((object)[
                'tanggal'    => $tglStr,
                'reg'        => $regVal,
                'js'         => $jsVal,
                'bhp'        => $bhpVal,
                'jm_dr'      => $jmDrVal,
                'pr'         => $prVal,
                'kso'        => $ksoVal,
                'obat'       => $obatVal,
                'retur'      => $returVal,
                'lab_js'     => $labJsVal,
                'lab_bhp'    => $labBhpVal,
                'lab_jm'     => $labJmVal,
                'ro_js'      => $roJsVal,
                'ro_bhp'     => $roBhpVal,
                'ro_jm_pj'   => $roJmPjVal,
                'ro_petugas' => $roPetugasVal,
                'ro_perujuk' => $roPerujukVal,
                'pot'        => $potVal,
                'tbm'        => $tbmVal,
                'kamar'      => $kmrVal,
                'ok_jm_dr'   => $okJmDrVal,
                'ok_jm_pr'   => $okJmPrVal,
                'ok_js'      => $okJsVal,
                'total'      => $rowTotal,
                'pj'         => $pjVal,
                'ekses'      => $eksesVal,
                'grand_total'=> $rowTotal + $eksesVal + $pjVal,
            ]);
        }
        return $dataRekap;
    }

    public function detail(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $kdPj = $request->kd_pj ?? 'UMU';
        $statusLanjut = $request->status_lanjut ?? 'Ranap'; // Ranap or Ralan

        $penjaminLabel = match($kdPj) {
            'BPJ' => 'BPJS',
            'ASURANSI' => 'Asuransi',
            'INHEALTH' => 'Mandiri Inhealth & Askes Inhealth',
            'SEMUA' => 'Semua Penjamin',
            default => 'Umum',
        };

        $dataDetail = $this->getDetailData($tanggal, $kdPj, $statusLanjut);
        $dataDokter = $this->getDetailDokterData($tanggal, $kdPj, $statusLanjut);
        $setting = \Illuminate\Support\Facades\DB::table('setting')->first();

        return view('laporan.rekappendapatanharian-detail', compact('tanggal', 'kdPj', 'statusLanjut', 'penjaminLabel', 'dataDetail', 'dataDokter', 'setting'));
    }

    private function getDetailData($tanggal, $kdPj, $statusLanjut)
    {
        $tgl1 = $tanggal;
        $tgl2 = $tanggal;

        if ($statusLanjut === 'Ralan') {
            $statusLanjutClause = "AND rp.status_lanjut = 'Ralan'";
        } elseif ($statusLanjut === 'SEMUA') {
            $statusLanjutClause = "";
        } else {
            $statusLanjutClause = "AND rp.status_lanjut = 'Ranap'";
        }

        if ($kdPj === 'UMU') {
            if ($statusLanjut === 'Ralan') {
                $notaSource = 'nota_jalan';
            } elseif ($statusLanjut === 'SEMUA') {
                $notaSource = "(SELECT no_rawat, tanggal FROM nota_inap WHERE tanggal BETWEEN '{$tgl1}' AND '{$tgl2}' UNION ALL SELECT no_rawat, tanggal FROM nota_jalan WHERE tanggal BETWEEN '{$tgl1}' AND '{$tgl2}')";
            } else {
                $notaSource = 'nota_inap';
            }
        } elseif ($kdPj === 'SEMUA') {
            if ($statusLanjut === 'Ralan') {
                $notaSource = "(
                    SELECT nj.no_rawat, nj.tanggal 
                    FROM nota_jalan nj 
                    JOIN reg_periksa rp ON rp.no_rawat = nj.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND nj.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT DISTINCT bp.no_rawat, bp.tgl_bayar as tanggal 
                    FROM bayar_piutang bp 
                    JOIN reg_periksa rp ON rp.no_rawat = bp.no_rawat 
                    WHERE rp.kd_pj != 'UMU' AND bp.tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}'
                )";
            } elseif ($statusLanjut === 'SEMUA') {
                $notaSource = "(
                    SELECT ni.no_rawat, ni.tanggal 
                    FROM nota_inap ni 
                    JOIN reg_periksa rp ON rp.no_rawat = ni.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND ni.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT nj.no_rawat, nj.tanggal 
                    FROM nota_jalan nj 
                    JOIN reg_periksa rp ON rp.no_rawat = nj.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND nj.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT DISTINCT bp.no_rawat, bp.tgl_bayar as tanggal 
                    FROM bayar_piutang bp 
                    JOIN reg_periksa rp ON rp.no_rawat = bp.no_rawat 
                    WHERE rp.kd_pj != 'UMU' AND bp.tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}'
                )";
            } else {
                $notaSource = "(
                    SELECT ni.no_rawat, ni.tanggal 
                    FROM nota_inap ni 
                    JOIN reg_periksa rp ON rp.no_rawat = ni.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND ni.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT DISTINCT bp.no_rawat, bp.tgl_bayar as tanggal 
                    FROM bayar_piutang bp 
                    JOIN reg_periksa rp ON rp.no_rawat = bp.no_rawat 
                    WHERE rp.kd_pj != 'UMU' AND bp.tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}'
                )";
            }
        } else {
            $notaSource = "(SELECT DISTINCT no_rawat, tgl_bayar as tanggal FROM bayar_piutang WHERE tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}')";
        }

        $nipDokterKhusus = [
            '12041999', '0518010327', '518010327', '1802081010970003', '1802081010970000',
            '10104020181', '0817010312', '817010312', '05084010153', '5084010153',
            '0106010608', '106010608', '19960223', '0224010675', '224010675',
            '1802086108980001', '1802086108980000', '1802054204000003', '1802054204000000',
            '09964020055', '9964020055', '88888', '0214010227', '214010227', '0512010199', '512010199', '1204020129'
        ];
        $nipIn = "'" . implode("','", array_unique($nipDokterKhusus)) . "'";

        $inhealthCodes = DB::table('penjab')->where('png_jawab', 'like', '%inhealth%')->pluck('kd_pj')->toArray();
        if (empty($inhealthCodes)) {
            $inhealthCodes = ['106', '107', '108', 'A09', 'C52', 'CMI', 'D79', 'INH', 'PT2'];
        }
        $inhealthIn = "'" . implode("','", $inhealthCodes) . "'";

        $applyPenjaminFilter = function ($query) use ($kdPj, $inhealthCodes) {
            if (!$kdPj || $kdPj === 'SEMUA') {
                return;
            }
            if ($kdPj === 'INHEALTH') {
                $query->whereIn('rp.kd_pj', $inhealthCodes);
            } elseif ($kdPj === 'ASURANSI') {
                $excludeCodes = array_merge(['UMU', 'BPJ', '-'], $inhealthCodes);
                $query->whereNotIn('rp.kd_pj', $excludeCodes);
            } else {
                $query->where('rp.kd_pj', $kdPj);
            }
        };

        if (!$kdPj || $kdPj === 'SEMUA') {
            $pjClause = "";
        } elseif ($kdPj === 'INHEALTH') {
            $pjClause = "AND rp.kd_pj IN ({$inhealthIn})";
        } elseif ($kdPj === 'ASURANSI') {
            $excludeCodes = array_merge(['UMU', 'BPJ', '-'], $inhealthCodes);
            $excludeIn = "'" . implode("','", $excludeCodes) . "'";
            $pjClause = "AND rp.kd_pj NOT IN ({$excludeIn})";
        } else {
            $pjClause = "AND rp.kd_pj = '{$kdPj}'";
        }

        $regQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->where('b.status', 'Registrasi')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);

        $applyPenjaminFilter($regQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $regQuery->where('rp.status_lanjut', $statusLanjut);
        }

        $regPerTgl = $regQuery->select('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis', DB::raw('SUM(b.totalbiaya) as total_reg'))
            ->groupBy('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis')
            ->get()->keyBy('no_rawat');

        $sql = "
            SELECT 
                combined.no_rawat,
                combined.nm_pasien,
                combined.no_rkm_medis,
                SUM(combined.material) as js,
                SUM(combined.bhp) as bhp,
                SUM(combined.jm_dr) as jm_dr,
                SUM(combined.pr) as pr,
                SUM(combined.kso) as kso
            FROM (
                SELECT t.no_rawat, p.nm_pasien, p.no_rkm_medis, t.material, t.bhp, t.tarif_tindakandr as jm_dr, 0 as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_jl_dr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat JOIN pasien p ON p.no_rkm_medis = rp.no_rkm_medis WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.no_rawat, p.nm_pasien, p.no_rkm_medis, t.material, t.bhp, (CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_jl_pr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat JOIN pasien p ON p.no_rkm_medis = rp.no_rkm_medis WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.no_rawat, p.nm_pasien, p.no_rkm_medis, t.material, t.bhp, (t.tarif_tindakandr + CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_jl_drpr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat JOIN pasien p ON p.no_rkm_medis = rp.no_rkm_medis WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.no_rawat, p.nm_pasien, p.no_rkm_medis, t.material, t.bhp, t.tarif_tindakandr as jm_dr, 0 as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_inap_dr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat JOIN pasien p ON p.no_rkm_medis = rp.no_rkm_medis WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.no_rawat, p.nm_pasien, p.no_rkm_medis, t.material, t.bhp, (CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_inap_pr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat JOIN pasien p ON p.no_rkm_medis = rp.no_rkm_medis WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.no_rawat, p.nm_pasien, p.no_rkm_medis, t.material, t.bhp, (t.tarif_tindakandr + CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_inap_drpr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat JOIN pasien p ON p.no_rkm_medis = rp.no_rkm_medis WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
            ) as combined
            GROUP BY combined.no_rawat, combined.nm_pasien, combined.no_rkm_medis
        ";
        $bindings = [$tgl1, $tgl2, $tgl1, $tgl2, $tgl1, $tgl2, $tgl1, $tgl2, $tgl1, $tgl2, $tgl1, $tgl2];
        $tindakanPerTgl = collect(DB::select($sql, $bindings))->keyBy('no_rawat');

        $obatQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->where('b.status', 'Obat')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);
        $applyPenjaminFilter($obatQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $obatQuery->where('rp.status_lanjut', $statusLanjut);
        }
        $obatPerTgl = $obatQuery->select('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis', DB::raw('SUM(b.totalbiaya) as total_obat'))
            ->groupBy('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis')->get()->keyBy('no_rawat');

        $returQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->where('b.status', 'Retur Obat')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);
        $applyPenjaminFilter($returQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $returQuery->where('rp.status_lanjut', $statusLanjut);
        }
        $returPerTgl = $returQuery->select('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis', DB::raw('SUM(b.totalbiaya) as total_retur'))
            ->groupBy('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis')->get()->keyBy('no_rawat');

        $labQuery = DB::table('periksa_lab as pl')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'pl.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'pl.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);
        $applyPenjaminFilter($labQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $labQuery->where('rp.status_lanjut', $statusLanjut);
        }
        $labPerTgl = $labQuery->select('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis', DB::raw('SUM(pl.bagian_rs) as js'), DB::raw('SUM(pl.bhp) as bhp'), DB::raw('SUM(pl.tarif_tindakan_dokter + pl.tarif_tindakan_petugas + pl.tarif_perujuk) as jm_lab'))
            ->groupBy('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis')->get()->keyBy('no_rawat');

        $roQuery = DB::table('periksa_radiologi as pr')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'pr.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'pr.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);
        $applyPenjaminFilter($roQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $roQuery->where('rp.status_lanjut', $statusLanjut);
        }
        $roPerTgl = $roQuery->select('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis', DB::raw("SUM(pr.bagian_rs + CASE WHEN pr.dokter_perujuk = 'D0000091' THEN pr.tarif_perujuk ELSE 0 END) as js"), DB::raw('SUM(pr.bhp) as bhp'), DB::raw('SUM(pr.tarif_tindakan_dokter) as jm_pj'), DB::raw('SUM(pr.tarif_tindakan_petugas) as petugas'), DB::raw("SUM(CASE WHEN pr.dokter_perujuk = 'D0000091' THEN 0 ELSE pr.tarif_perujuk END) as perujuk"))
            ->groupBy('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis')->get()->keyBy('no_rawat');

        $potQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->where('b.status', 'Potongan')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);
        $applyPenjaminFilter($potQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $potQuery->where('rp.status_lanjut', $statusLanjut);
        }
        $potPerTgl = $potQuery->select('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis', DB::raw('SUM(b.totalbiaya) as total_pot'))
            ->groupBy('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis')->get()->keyBy('no_rawat');

        $tbmQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->where('b.status', 'Tambahan')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);
        $applyPenjaminFilter($tbmQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $tbmQuery->where('rp.status_lanjut', $statusLanjut);
        }
        $tbmPerTgl = $tbmQuery->select('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis', DB::raw('SUM(b.totalbiaya) as total_tbm'))
            ->groupBy('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis')->get()->keyBy('no_rawat');

        $kmrQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'b.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->where('b.status', 'Kamar')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);
        $applyPenjaminFilter($kmrQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $kmrQuery->where('rp.status_lanjut', $statusLanjut);
        }
        $kmrPerTgl = $kmrQuery->select('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis', DB::raw('SUM(b.totalbiaya) as total_kmr'))
            ->groupBy('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis')->get()->keyBy('no_rawat');

        $okQuery = DB::table('operasi as o')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'o.no_rawat')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'o.no_rawat')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->whereBetween('ni.tanggal', [$tgl1, $tgl2]);
        $applyPenjaminFilter($okQuery);
        if ($statusLanjut && $statusLanjut !== 'SEMUA') {
            $okQuery->where('rp.status_lanjut', $statusLanjut);
        }
        $okPerTgl = $okQuery->select('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis',
            DB::raw("SUM(o.biayaoperator1 + o.biayaoperator2 + o.biayaoperator3 + o.biayadokter_anak + o.biayadokter_anestesi + o.biaya_dokter_pjanak + o.biaya_dokter_umum + CASE WHEN o.asisten_operator1 IN ($nipIn) THEN o.biayaasisten_operator1 ELSE 0 END + CASE WHEN o.asisten_operator2 IN ($nipIn) THEN o.biayaasisten_operator2 ELSE 0 END + CASE WHEN o.asisten_operator3 IN ($nipIn) THEN o.biayaasisten_operator3 ELSE 0 END + CASE WHEN o.asisten_anestesi IN ($nipIn) THEN o.biayaasisten_anestesi ELSE 0 END + CASE WHEN o.asisten_anestesi2 IN ($nipIn) THEN o.biayaasisten_anestesi2 ELSE 0 END + CASE WHEN o.bidan IN ($nipIn) THEN o.biayabidan ELSE 0 END + CASE WHEN o.bidan2 IN ($nipIn) THEN o.biayabidan2 ELSE 0 END + CASE WHEN o.bidan3 IN ($nipIn) THEN o.biayabidan3 ELSE 0 END + CASE WHEN o.instrumen IN ($nipIn) THEN o.biayainstrumen ELSE 0 END + CASE WHEN o.perawaat_resusitas IN ($nipIn) THEN o.biayaperawaat_resusitas ELSE 0 END + CASE WHEN o.perawat_luar IN ($nipIn) THEN o.biayaperawat_luar ELSE 0 END + CASE WHEN o.omloop IN ($nipIn) THEN o.biaya_omloop ELSE 0 END + CASE WHEN o.omloop2 IN ($nipIn) THEN o.biaya_omloop2 ELSE 0 END + CASE WHEN o.omloop3 IN ($nipIn) THEN o.biaya_omloop3 ELSE 0 END + CASE WHEN o.omloop4 IN ($nipIn) THEN o.biaya_omloop4 ELSE 0 END + CASE WHEN o.omloop5 IN ($nipIn) THEN o.biaya_omloop5 ELSE 0 END) as jm_dr"),
            DB::raw("SUM(CASE WHEN o.asisten_operator1 NOT IN ($nipIn) THEN o.biayaasisten_operator1 ELSE 0 END + CASE WHEN o.asisten_operator2 NOT IN ($nipIn) THEN o.biayaasisten_operator2 ELSE 0 END + CASE WHEN o.asisten_operator3 NOT IN ($nipIn) THEN o.biayaasisten_operator3 ELSE 0 END + CASE WHEN o.asisten_anestesi NOT IN ($nipIn) THEN o.biayaasisten_anestesi ELSE 0 END + CASE WHEN o.asisten_anestesi2 NOT IN ($nipIn) THEN o.biayaasisten_anestesi2 ELSE 0 END + CASE WHEN o.bidan NOT IN ($nipIn) THEN o.biayabidan ELSE 0 END + CASE WHEN o.bidan2 NOT IN ($nipIn) THEN o.biayabidan2 ELSE 0 END + CASE WHEN o.bidan3 NOT IN ($nipIn) THEN o.biayabidan3 ELSE 0 END + CASE WHEN o.instrumen NOT IN ($nipIn) THEN o.biayainstrumen ELSE 0 END + CASE WHEN o.perawaat_resusitas NOT IN ($nipIn) THEN o.biayaperawaat_resusitas ELSE 0 END + CASE WHEN o.perawat_luar NOT IN ($nipIn) THEN o.biayaperawat_luar ELSE 0 END + CASE WHEN o.omloop NOT IN ($nipIn) THEN o.biaya_omloop ELSE 0 END + CASE WHEN o.omloop2 NOT IN ($nipIn) THEN o.biaya_omloop2 ELSE 0 END + CASE WHEN o.omloop3 NOT IN ($nipIn) THEN o.biaya_omloop3 ELSE 0 END + CASE WHEN o.omloop4 NOT IN ($nipIn) THEN o.biaya_omloop4 ELSE 0 END + CASE WHEN o.omloop5 NOT IN ($nipIn) THEN o.biaya_omloop5 ELSE 0 END) as jm_pr"),
            DB::raw('SUM(o.biayaalat + o.biayasewaok + o.akomodasi + o.bagian_rs + o.biayasarpras) as js')
        )->groupBy('rp.no_rawat', 'p.nm_pasien', 'p.no_rkm_medis')->get()->keyBy('no_rawat');

        $eksesRanapQuery = DB::table('piutang_pasien as pp')
            ->leftJoin('detail_nota_inap as dni', 'pp.no_rawat', '=', 'dni.no_rawat')
            ->join('reg_periksa as rp', 'pp.no_rawat', '=', 'rp.no_rawat')
            ->where('rp.status_lanjut', 'Ranap')
            ->whereBetween('pp.tgl_piutang', [$tgl1, $tgl2]);
        $eksesRanapPerTgl = $eksesRanapQuery->select('rp.no_rawat', DB::raw('SUM(dni.besar_bayar) as total_ekses'))
            ->groupBy('rp.no_rawat')->pluck('total_ekses', 'rp.no_rawat');

        $eksesRalanQuery = DB::table('piutang_pasien as pp')
            ->leftJoin('detail_nota_jalan as dnj', 'pp.no_rawat', '=', 'dnj.no_rawat')
            ->join('reg_periksa as rp', 'pp.no_rawat', '=', 'rp.no_rawat')
            ->where('rp.status_lanjut', 'Ralan')
            ->whereBetween('pp.tgl_piutang', [$tgl1, $tgl2]);
        $eksesRalanPerTgl = $eksesRalanQuery->select('rp.no_rawat', DB::raw('SUM(dnj.besar_bayar) as total_ekses'))
            ->groupBy('rp.no_rawat')->pluck('total_ekses', 'rp.no_rawat');

        $pjQuery = DB::table('tagihan_sadewa')
            ->join(DB::raw("{$notaSource} as ni"), function($join) {
                // Approximate connection, tagihan_sadewa uses no_nota. For specific no_rawat, it's hard to join directly if it's purely per-date. Let's omit PJ per-patient for now or skip if not possible.
            })->where('no_nota', 'like', 'PJ%')->whereDate('tgl_bayar', '>=', $tgl1)->whereDate('tgl_bayar', '<=', $tgl2);
        // PJ is typically per day, not per patient in this context. Let's set it to 0 for per-patient breakdown.

        $allKeys = collect(array_keys($tindakanPerTgl->toArray()))
            ->merge(array_keys($regPerTgl->toArray()))
            ->merge(array_keys($obatPerTgl->toArray()))
            ->merge(array_keys($returPerTgl->toArray()))
            ->merge(array_keys($labPerTgl->toArray()))
            ->merge(array_keys($roPerTgl->toArray()))
            ->merge(array_keys($potPerTgl->toArray()))
            ->merge(array_keys($tbmPerTgl->toArray()))
            ->merge(array_keys($kmrPerTgl->toArray()))
            ->merge(array_keys($okPerTgl->toArray()))
            ->unique()->values();

        $dataRekap = collect();
        foreach ($allKeys as $noRawat) {
            $tindakan = $tindakanPerTgl->get($noRawat);
            $regItem  = $regPerTgl->get($noRawat);
            $obatItem = $obatPerTgl->get($noRawat);
            $returItem = $returPerTgl->get($noRawat);
            $labItem  = $labPerTgl->get($noRawat);
            $roItem   = $roPerTgl->get($noRawat);
            $potItem  = $potPerTgl->get($noRawat);
            $tbmItem  = $tbmPerTgl->get($noRawat);
            $kmrItem  = $kmrPerTgl->get($noRawat);
            $okItem   = $okPerTgl->get($noRawat);

            $nmPasien = $tindakan->nm_pasien ?? $regItem->nm_pasien ?? $obatItem->nm_pasien ?? $returItem->nm_pasien ?? $labItem->nm_pasien ?? $roItem->nm_pasien ?? $potItem->nm_pasien ?? $tbmItem->nm_pasien ?? $kmrItem->nm_pasien ?? $okItem->nm_pasien ?? '-';
            $noRkmMedis = $tindakan->no_rkm_medis ?? $regItem->no_rkm_medis ?? $obatItem->no_rkm_medis ?? $returItem->no_rkm_medis ?? $labItem->no_rkm_medis ?? $roItem->no_rkm_medis ?? $potItem->no_rkm_medis ?? $tbmItem->no_rkm_medis ?? $kmrItem->no_rkm_medis ?? $okItem->no_rkm_medis ?? '-';

            $regVal = $regItem ? (float)$regItem->total_reg : 0;
            $jsVal   = $tindakan ? (float)$tindakan->js : 0;
            $bhpVal  = $tindakan ? (float)$tindakan->bhp : 0;
            $jmDrVal = $tindakan ? (float)$tindakan->jm_dr : 0;
            $prVal   = $tindakan ? (float)$tindakan->pr : 0;
            $ksoVal  = $tindakan ? (float)$tindakan->kso : 0;
            $obatVal  = $obatItem ? (float)$obatItem->total_obat : 0;
            $returVal = $returItem ? (float)$returItem->total_retur : 0;
            $labJsVal  = $labItem ? (float)$labItem->js : 0;
            $labBhpVal = $labItem ? (float)$labItem->bhp : 0;
            $labJmVal  = $labItem ? (float)$labItem->jm_lab : 0;
            $roJsVal      = $roItem ? (float)$roItem->js : 0;
            $roBhpVal     = $roItem ? (float)$roItem->bhp : 0;
            $roJmPjVal    = $roItem ? (float)$roItem->jm_pj : 0;
            $roPetugasVal = $roItem ? (float)$roItem->petugas : 0;
            $roPerujukVal = $roItem ? (float)$roItem->perujuk : 0;
            $potVal = $potItem ? (float)$potItem->total_pot : 0;
            $tbmVal = $tbmItem ? (float)$tbmItem->total_tbm : 0;
            $kmrVal = $kmrItem ? (float)$kmrItem->total_kmr : 0;
            $okJmDrVal = $okItem ? (float)$okItem->jm_dr : 0;
            $okJmPrVal = $okItem ? (float)$okItem->jm_pr : 0;
            $okJsVal   = $okItem ? (float)$okItem->js : 0;

            $eksesRanapVal = (float) $eksesRanapPerTgl->get($noRawat, 0);
            $eksesRalanVal = (float) $eksesRalanPerTgl->get($noRawat, 0);
            $eksesVal = 0;
            if ($statusLanjut === 'Ranap') {
                $eksesVal = $eksesRanapVal;
            } elseif ($statusLanjut === 'Ralan') {
                $eksesVal = $eksesRalanVal;
            } else {
                $eksesVal = $eksesRanapVal + $eksesRalanVal;
            }

            $pjVal = 0;

            $rowTotal = $regVal + $jsVal + $bhpVal + $jmDrVal + $prVal + $ksoVal
                + $obatVal + $returVal
                + $labJsVal + $labBhpVal + $labJmVal
                + $roJsVal + $roBhpVal + $roJmPjVal + $roPetugasVal + $roPerujukVal
                + $potVal + $tbmVal + $kmrVal
                + $okJmDrVal + $okJmPrVal + $okJsVal;

            $dataRekap->push((object)[
                'tanggal'    => $noRkmMedis . '<br>' . $nmPasien . '<br><small class="text-muted">' . $noRawat . '</small>',
                'no_rawat'   => $noRawat,
                'reg'        => $regVal,
                'js'         => $jsVal,
                'bhp'        => $bhpVal,
                'jm_dr'      => $jmDrVal,
                'pr'         => $prVal,
                'kso'        => $ksoVal,
                'obat'       => $obatVal,
                'retur'      => $returVal,
                'lab_js'     => $labJsVal,
                'lab_bhp'    => $labBhpVal,
                'lab_jm'     => $labJmVal,
                'ro_js'      => $roJsVal,
                'ro_bhp'     => $roBhpVal,
                'ro_jm_pj'   => $roJmPjVal,
                'ro_petugas' => $roPetugasVal,
                'ro_perujuk' => $roPerujukVal,
                'pot'        => $potVal,
                'tbm'        => $tbmVal,
                'kamar'      => $kmrVal,
                'ok_jm_dr'   => $okJmDrVal,
                'ok_jm_pr'   => $okJmPrVal,
                'ok_js'      => $okJsVal,
                'total'      => $rowTotal,
                'pj'         => $pjVal,
                'ekses'      => $eksesVal,
                'grand_total'=> $rowTotal + $eksesVal + $pjVal,
            ]);
        }


        $pjVal = (float) DB::table('tagihan_sadewa')
            ->where('no_nota', 'like', 'PJ%')
            ->whereDate('tgl_bayar', $tanggal)
            ->sum('jumlah_bayar');

        if ($statusLanjut === 'Ranap') {
            $pjVal = 0;
        }

        if ($pjVal > 0) {
            $dataRekap->push((object)[
                'tanggal'    => 'PEMBAYARAN PJ<br>-<br><small class="text-muted">-</small>',
                'no_rawat'   => '-',
                'reg'        => 0,
                'js'         => 0,
                'bhp'        => 0,
                'jm_dr'      => 0,
                'pr'         => 0,
                'kso'        => 0,
                'obat'       => 0,
                'retur'      => 0,
                'lab_js'     => 0,
                'lab_bhp'    => 0,
                'lab_jm'     => 0,
                'ro_js'      => 0,
                'ro_bhp'     => 0,
                'ro_jm_pj'   => 0,
                'ro_petugas' => 0,
                'ro_perujuk' => 0,
                'pot'        => 0,
                'tbm'        => 0,
                'kamar'      => 0,
                'ok_jm_dr'   => 0,
                'ok_jm_pr'   => 0,
                'ok_js'      => 0,
                'total'      => 0,
                'pj'         => $pjVal,
                'ekses'      => 0,
                'grand_total'=> $pjVal,
            ]);
        }
        return $dataRekap;
    }

    private function getDetailDokterData($tanggal, $kdPj, $statusLanjut)
    {
        $tgl1 = $tanggal;
        $tgl2 = $tanggal;

        if ($statusLanjut === 'Ralan') {
            $statusLanjutClause = "AND rp.status_lanjut = 'Ralan'";
        } elseif ($statusLanjut === 'SEMUA') {
            $statusLanjutClause = "";
        } else {
            $statusLanjutClause = "AND rp.status_lanjut = 'Ranap'";
        }

        if ($kdPj === 'UMU') {
            if ($statusLanjut === 'Ralan') {
                $notaSource = 'nota_jalan';
            } elseif ($statusLanjut === 'SEMUA') {
                $notaSource = "(SELECT no_rawat, tanggal FROM nota_inap WHERE tanggal BETWEEN '{$tgl1}' AND '{$tgl2}' UNION ALL SELECT no_rawat, tanggal FROM nota_jalan WHERE tanggal BETWEEN '{$tgl1}' AND '{$tgl2}')";
            } else {
                $notaSource = 'nota_inap';
            }
        } elseif ($kdPj === 'SEMUA') {
            if ($statusLanjut === 'Ralan') {
                $notaSource = "(
                    SELECT nj.no_rawat, nj.tanggal 
                    FROM nota_jalan nj 
                    JOIN reg_periksa rp ON rp.no_rawat = nj.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND nj.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT DISTINCT bp.no_rawat, bp.tgl_bayar as tanggal 
                    FROM bayar_piutang bp 
                    JOIN reg_periksa rp ON rp.no_rawat = bp.no_rawat 
                    WHERE rp.kd_pj != 'UMU' AND bp.tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}'
                )";
            } elseif ($statusLanjut === 'SEMUA') {
                $notaSource = "(
                    SELECT ni.no_rawat, ni.tanggal 
                    FROM nota_inap ni 
                    JOIN reg_periksa rp ON rp.no_rawat = ni.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND ni.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT nj.no_rawat, nj.tanggal 
                    FROM nota_jalan nj 
                    JOIN reg_periksa rp ON rp.no_rawat = nj.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND nj.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT DISTINCT bp.no_rawat, bp.tgl_bayar as tanggal 
                    FROM bayar_piutang bp 
                    JOIN reg_periksa rp ON rp.no_rawat = bp.no_rawat 
                    WHERE rp.kd_pj != 'UMU' AND bp.tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}'
                )";
            } else {
                $notaSource = "(
                    SELECT ni.no_rawat, ni.tanggal 
                    FROM nota_inap ni 
                    JOIN reg_periksa rp ON rp.no_rawat = ni.no_rawat 
                    WHERE rp.kd_pj = 'UMU' AND ni.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
                    UNION ALL
                    SELECT DISTINCT bp.no_rawat, bp.tgl_bayar as tanggal 
                    FROM bayar_piutang bp 
                    JOIN reg_periksa rp ON rp.no_rawat = bp.no_rawat 
                    WHERE rp.kd_pj != 'UMU' AND bp.tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}'
                )";
            }
        } else {
            $notaSource = "(SELECT DISTINCT no_rawat, tgl_bayar as tanggal FROM bayar_piutang WHERE tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}')";
        }

        $nipDokterKhusus = [
            '12041999', '0518010327', '518010327', '1802081010970003', '1802081010970000',
            '10104020181', '0817010312', '817010312', '05084010153', '5084010153',
            '0106010608', '106010608', '19960223', '0224010675', '224010675',
            '1802086108980001', '1802086108980000', '1802054204000003', '1802054204000000',
            '09964020055', '9964020055', '88888', '0214010227', '214010227', '0512010199', '512010199', '1204020129'
        ];
        $nipIn = "'" . implode("','", array_unique($nipDokterKhusus)) . "'";

        $inhealthCodes = DB::table('penjab')->where('png_jawab', 'like', '%inhealth%')->pluck('kd_pj')->toArray();
        if (empty($inhealthCodes)) {
            $inhealthCodes = ['106', '107', '108', 'A09', 'C52', 'CMI', 'D79', 'INH', 'PT2'];
        }
        $inhealthIn = "'" . implode("','", $inhealthCodes) . "'";

        if (!$kdPj || $kdPj === 'SEMUA') {
            $pjClause = "";
        } elseif ($kdPj === 'INHEALTH') {
            $pjClause = "AND rp.kd_pj IN ({$inhealthIn})";
        } elseif ($kdPj === 'ASURANSI') {
            $excludeCodes = array_merge(['UMU', 'BPJ', '-'], $inhealthCodes);
            $excludeIn = "'" . implode("','", $excludeCodes) . "'";
            $pjClause = "AND rp.kd_pj NOT IN ({$excludeIn})";
        } else {
            $pjClause = "AND rp.kd_pj = '{$kdPj}'";
        }

        $bindings = [$tgl1, $tgl2];
        $bindings2 = [$tgl1, $tgl2, $tgl1, $tgl2];

        // TINDAKAN DOKTER
        $sqlTindakan = "
            SELECT 
                kd_dokter,
                SUM(jm_dr) as jm_dr
            FROM (
                SELECT t.kd_dokter, t.tarif_tindakandr as jm_dr
                FROM rawat_jl_dr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.nip as kd_dokter, t.tarif_tindakanpr as jm_dr
                FROM rawat_jl_pr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND t.nip IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.kd_dokter, t.tarif_tindakandr as jm_dr
                FROM rawat_jl_drpr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.nip as kd_dokter, t.tarif_tindakanpr as jm_dr
                FROM rawat_jl_drpr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND t.nip IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.kd_dokter, t.tarif_tindakandr as jm_dr
                FROM rawat_inap_dr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.nip as kd_dokter, t.tarif_tindakanpr as jm_dr
                FROM rawat_inap_pr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND t.nip IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.kd_dokter, t.tarif_tindakandr as jm_dr
                FROM rawat_inap_drpr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT t.nip as kd_dokter, t.tarif_tindakanpr as jm_dr
                FROM rawat_inap_drpr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat JOIN reg_periksa rp ON rp.no_rawat = t.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND t.nip IN ($nipIn) {$pjClause} {$statusLanjutClause}
            ) as combined
            GROUP BY kd_dokter
        ";
        $tindakanResult = DB::select($sqlTindakan, array_merge($bindings2, $bindings2, $bindings2, $bindings2));

        // LAB
        $sqlLab = "
            SELECT pl.kd_dokter, SUM(pl.tarif_tindakan_dokter) as jm_lab_tindakan
            FROM periksa_lab pl JOIN {$notaSource} ni ON ni.no_rawat = pl.no_rawat JOIN reg_periksa rp ON rp.no_rawat = pl.no_rawat
            WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
            GROUP BY pl.kd_dokter
        ";
        $labTindakanResult = DB::select($sqlLab, $bindings);

        $sqlLabPerujuk = "
            SELECT pl.dokter_perujuk as kd_dokter, SUM(pl.tarif_perujuk) as jm_lab_perujuk
            FROM periksa_lab pl JOIN {$notaSource} ni ON ni.no_rawat = pl.no_rawat JOIN reg_periksa rp ON rp.no_rawat = pl.no_rawat
            WHERE ni.tanggal BETWEEN ? AND ? AND pl.dokter_perujuk != '-' {$pjClause} {$statusLanjutClause}
            GROUP BY pl.dokter_perujuk
        ";
        $labPerujukResult = DB::select($sqlLabPerujuk, $bindings);

        // RAD
        $sqlRad = "
            SELECT pr.kd_dokter, SUM(pr.tarif_tindakan_dokter) as jm_rad_tindakan
            FROM periksa_radiologi pr JOIN {$notaSource} ni ON ni.no_rawat = pr.no_rawat JOIN reg_periksa rp ON rp.no_rawat = pr.no_rawat
            WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
            GROUP BY pr.kd_dokter
        ";
        $radTindakanResult = DB::select($sqlRad, $bindings);

        $sqlRadPerujuk = "
            SELECT pr.dokter_perujuk as kd_dokter, SUM(pr.tarif_perujuk) as jm_rad_perujuk
            FROM periksa_radiologi pr JOIN {$notaSource} ni ON ni.no_rawat = pr.no_rawat JOIN reg_periksa rp ON rp.no_rawat = pr.no_rawat
            WHERE ni.tanggal BETWEEN ? AND ? AND pr.dokter_perujuk != '-' AND pr.dokter_perujuk != 'D0000091' {$pjClause} {$statusLanjutClause}
            GROUP BY pr.dokter_perujuk
        ";
        $radPerujukResult = DB::select($sqlRadPerujuk, $bindings);

        // OPERASI
        $sqlOk = "
            SELECT 
                kd_dokter,
                SUM(jm_ok) as jm_ok
            FROM (
                SELECT operator1 as kd_dokter, biayaoperator1 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT operator2 as kd_dokter, biayaoperator2 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT operator3 as kd_dokter, biayaoperator3 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT dokter_anak as kd_dokter, biayadokter_anak as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT dokter_anestesi as kd_dokter, biayadokter_anestesi as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT dokter_pjanak as kd_dokter, biaya_dokter_pjanak as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT dokter_umum as kd_dokter, biaya_dokter_umum as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT asisten_operator1 as kd_dokter, biayaasisten_operator1 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND asisten_operator1 IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT asisten_operator2 as kd_dokter, biayaasisten_operator2 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND asisten_operator2 IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT asisten_operator3 as kd_dokter, biayaasisten_operator3 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND asisten_operator3 IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT asisten_anestesi as kd_dokter, biayaasisten_anestesi as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND asisten_anestesi IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT asisten_anestesi2 as kd_dokter, biayaasisten_anestesi2 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND asisten_anestesi2 IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT bidan as kd_dokter, biayabidan as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND bidan IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT bidan2 as kd_dokter, biayabidan2 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND bidan2 IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT bidan3 as kd_dokter, biayabidan3 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND bidan3 IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT instrumen as kd_dokter, biayainstrumen as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND instrumen IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT perawaat_resusitas as kd_dokter, biayaperawaat_resusitas as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND perawaat_resusitas IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT perawat_luar as kd_dokter, biayaperawat_luar as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND perawat_luar IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT omloop as kd_dokter, biaya_omloop as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND omloop IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT omloop2 as kd_dokter, biaya_omloop2 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND omloop2 IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT omloop3 as kd_dokter, biaya_omloop3 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND omloop3 IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT omloop4 as kd_dokter, biaya_omloop4 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND omloop4 IN ($nipIn) {$pjClause} {$statusLanjutClause}
                UNION ALL
                SELECT omloop5 as kd_dokter, biaya_omloop5 as jm_ok FROM operasi o JOIN {$notaSource} ni ON ni.no_rawat = o.no_rawat JOIN reg_periksa rp ON rp.no_rawat = o.no_rawat WHERE ni.tanggal BETWEEN ? AND ? AND omloop5 IN ($nipIn) {$pjClause} {$statusLanjutClause}
            ) as combined
            WHERE kd_dokter != '-' AND kd_dokter != ''
            GROUP BY kd_dokter
        ";
        $okResult = DB::select($sqlOk, array_fill(0, 23 * 2, $tgl1)); // 23 UNIONs, each takes 2 parameters

        $doctors = [];

        $mergeDoctor = function($kd_dokter, $key, $value) use (&$doctors) {
            if (!$kd_dokter || $kd_dokter == '-') return;
            if (!isset($doctors[$kd_dokter])) {
                $doctors[$kd_dokter] = [
                    'kd_dokter' => $kd_dokter,
                    'jm_tindakan' => 0,
                    'jm_lab' => 0,
                    'jm_rad' => 0,
                    'jm_ok' => 0,
                    'total' => 0
                ];
            }
            $doctors[$kd_dokter][$key] += $value;
            $doctors[$kd_dokter]['total'] += $value;
        };

        foreach ($tindakanResult as $row) { $mergeDoctor($row->kd_dokter, 'jm_tindakan', $row->jm_dr); }
        foreach ($labTindakanResult as $row) { $mergeDoctor($row->kd_dokter, 'jm_lab', $row->jm_lab_tindakan); }
        foreach ($labPerujukResult as $row) { $mergeDoctor($row->kd_dokter, 'jm_lab', $row->jm_lab_perujuk); }
        foreach ($radTindakanResult as $row) { $mergeDoctor($row->kd_dokter, 'jm_rad', $row->jm_rad_tindakan); }
        foreach ($radPerujukResult as $row) { $mergeDoctor($row->kd_dokter, 'jm_rad', $row->jm_rad_perujuk); }
        foreach ($okResult as $row) { $mergeDoctor($row->kd_dokter, 'jm_ok', $row->jm_ok); }

        $dokters = DB::table('dokter')->whereIn('kd_dokter', array_keys($doctors))->pluck('nm_dokter', 'kd_dokter');
        $pegawais = DB::table('pegawai')->whereIn('nik', array_keys($doctors))->pluck('nama', 'nik');

        $result = collect();
        foreach ($doctors as $kd => $data) {
            if ($data['total'] <= 0) continue;
            
            $nama = $dokters->get($kd) ?? $pegawais->get($kd) ?? $kd;
            $data['nama_dokter'] = $nama;
            $result->push((object)$data);
        }

        return $result->sortBy('nama_dokter')->values();
    }
}

