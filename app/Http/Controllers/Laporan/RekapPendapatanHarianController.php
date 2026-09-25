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
            DB::raw('SUM(pl.bhp) as bhp')
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

            $rowTotal = $regVal + $jsVal + $bhpVal + $jmDrVal + $prVal + $ksoVal
                + $obatVal + $returVal
                + $labJsVal + $labBhpVal
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
            ]);
        }

        $penjaminLabel = match($kdPj) {
            'BPJ' => 'BPJS',
            'ASURANSI' => 'Asuransi',
            'INHEALTH' => 'Mandiri Inhealth & Askes Inhealth',
            'SEMUA' => 'Semua Penjamin',
            default => 'Umum',
        };

        return view('laporan.rekappendapatanharian', compact('tgl1', 'tgl2', 'kdPj', 'statusLanjut', 'penjaminLabel', 'dataRekap'));
    }
}
