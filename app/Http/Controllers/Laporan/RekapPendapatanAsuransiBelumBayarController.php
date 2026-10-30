<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use DateTime;

class RekapPendapatanAsuransiBelumBayarController extends Controller
{
    public function index(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-01');
        $tgl2 = $request->tgl2 ?? date('Y-m-t');
        $kd_pj = $request->kd_pj ?? 'SEMUA';

        // Ambil daftar penjamin asuransi untuk filter dropdown
        $penjaminList = DB::table('penjab')
            ->whereNotIn('kd_pj', ['UMU', 'BPJ', '-'])
            ->where('status', '1')
            ->orderBy('png_jawab', 'asc')
            ->get(['kd_pj', 'png_jawab']);

        // Jangan proses data jika filter belum diklik agar halaman dapat dibuka instan
        if (!$request->has('filter')) {
            $dataRekap = collect();
            $rincianBayarPiutangGrouped = collect();
            return view('laporan.rekappendapatanasuransibelumbayar', compact('tgl1', 'tgl2', 'kd_pj', 'penjaminList', 'dataRekap', 'rincianBayarPiutangGrouped'));
        }

        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        // Filter Penjamin Asuransi
        $penjaminCondition = ($kd_pj !== 'SEMUA')
            ? "AND rp.kd_pj = '{$kd_pj}'"
            : "AND rp.kd_pj NOT IN ('UMU', 'BPJ', '-')";

        // $notaSource: Transaksi Asuransi berdasarkan TANGGAL CETAK NOTA yang BELUM DIBAYAR (piutang_pasien.status = 'Belum Lunas')
        $notaSource = "(
            SELECT DISTINCT
                ni.no_rawat, 
                'Ranap' as status_lanjut,
                DATE_FORMAT(ni.tanggal, '%Y-%m-01') as tanggal
            FROM nota_inap ni
            JOIN reg_periksa rp ON ni.no_rawat = rp.no_rawat
            JOIN piutang_pasien pp ON ni.no_rawat = pp.no_rawat
            WHERE ni.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
            {$penjaminCondition}
            AND pp.status = 'Belum Lunas'
            UNION
            SELECT DISTINCT
                nj.no_rawat, 
                'Ralan' as status_lanjut,
                DATE_FORMAT(nj.tanggal, '%Y-%m-01') as tanggal
            FROM nota_jalan nj
            JOIN reg_periksa rp ON nj.no_rawat = rp.no_rawat
            JOIN piutang_pasien pp ON nj.no_rawat = pp.no_rawat
            WHERE nj.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
            {$penjaminCondition}
            AND pp.status = 'Belum Lunas'
        )";

        $nipDokterKhusus = [
            '12041999', '0518010327', '518010327', '1802081010970003', '1802081010970000',
            '10104020181', '0817010312', '817010312', '05084010153', '5084010153',
            '0106010608', '106010608', '19960223', '0224010675', '224010675',
            '1802086108980001', '1802086108980000', '1802054204000003', '1802054204000000',
            '09964020055', '9964020055', '88888', '0214010227', '214010227', '0512010199', '512010199', '1204020129'
        ];
        $nipIn = "'" . implode("','", array_unique($nipDokterKhusus)) . "'";

        // 1. REGISTRASI
        $regQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Registrasi');

        $regPerTgl = $regQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_reg'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_reg];
            });

        // 2. PAKET TINDAKAN
        $sql = "
            SELECT 
                combined.status_lanjut,
                combined.tanggal,
                SUM(combined.material) as js,
                SUM(combined.bhp) as bhp,
                SUM(combined.jm_dr) as jm_dr,
                SUM(combined.pr) as pr,
                SUM(combined.kso) as kso
            FROM (
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, t.tarif_tindakandr as jm_dr, 0 as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_jl_dr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                UNION ALL
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, (CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_jl_pr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                UNION ALL
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, (t.tarif_tindakandr + CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_jl_drpr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                UNION ALL
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, t.tarif_tindakandr as jm_dr, 0 as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_inap_dr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                UNION ALL
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, (CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_inap_pr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                UNION ALL
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, (t.tarif_tindakandr + CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_inap_drpr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
            ) as combined
            GROUP BY combined.status_lanjut, combined.tanggal
        ";
        $tindakanPerTgl = collect(DB::select($sql))->keyBy(function($item) {
            return $item->status_lanjut . '|' . $item->tanggal;
        });

        // 3. OBAT
        $obatQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Obat');
        $obatPerTgl = $obatQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_obat'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_obat];
            });

        // 4. RETUR OBAT
        $returQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Retur Obat');
        $returPerTgl = $returQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_retur'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_retur];
            });

        // 5. LAB
        $labQuery = DB::table('periksa_lab as pl')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'pl.no_rawat');
        $labPerTgl = $labQuery->select(
            'ni.status_lanjut',
            'ni.tanggal',
            DB::raw('SUM(pl.bagian_rs) as js'),
            DB::raw('SUM(pl.bhp) as bhp'),
            DB::raw('SUM(pl.tarif_tindakan_dokter + pl.tarif_tindakan_petugas) as jm_pj')
        )->groupBy('ni.status_lanjut', 'ni.tanggal')
        ->get()->keyBy(function($item) {
            return $item->status_lanjut . '|' . $item->tanggal;
        });

        // 6. RADIOLOGI
        $roQuery = DB::table('periksa_radiologi as pr')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'pr.no_rawat');
        $roPerTgl = $roQuery->select(
            'ni.status_lanjut',
            'ni.tanggal',
            DB::raw("SUM(pr.bagian_rs + CASE WHEN pr.dokter_perujuk = 'D0000091' THEN pr.tarif_perujuk ELSE 0 END) as js"),
            DB::raw('SUM(pr.bhp) as bhp'),
            DB::raw('SUM(pr.tarif_tindakan_dokter) as jm_pj'),
            DB::raw('SUM(pr.tarif_tindakan_petugas) as petugas'),
            DB::raw("SUM(CASE WHEN pr.dokter_perujuk = 'D0000091' THEN 0 ELSE pr.tarif_perujuk END) as perujuk")
        )->groupBy('ni.status_lanjut', 'ni.tanggal')
        ->get()->keyBy(function($item) {
            return $item->status_lanjut . '|' . $item->tanggal;
        });

        // 7. POTONGAN
        $potQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Potongan');
        $potPerTgl = $potQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_pot'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_pot];
            });

        // 8. TAMBAHAN
        $tbmQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Tambahan');
        $tbmPerTgl = $tbmQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_tbm'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_tbm];
            });

        // 9. KAMAR
        $kmrQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Kamar');
        $kmrPerTgl = $kmrQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_kmr'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_kmr];
            });

        // 10. OPERASI
        $okQuery = DB::table('operasi as o')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'o.no_rawat');
        $okPerTgl = $okQuery->select(
            'ni.status_lanjut',
            'ni.tanggal',
            DB::raw("SUM(
                o.biayaalat + o.biayasewaok + o.akomodasi + o.bagian_rs + o.biayasarpras
                + (CASE WHEN o.operator1 IN ('-', '') THEN o.biayaoperator1 ELSE 0 END)
                + (CASE WHEN o.operator2 IN ('-', '') THEN o.biayaoperator2 ELSE 0 END)
                + (CASE WHEN o.operator3 IN ('-', '') THEN o.biayaoperator3 ELSE 0 END)
                + (CASE WHEN o.dokter_anak IN ('-', '') THEN o.biayadokter_anak ELSE 0 END)
                + (CASE WHEN o.dokter_anestesi IN ('-', '') THEN o.biayadokter_anestesi ELSE 0 END)
                + (CASE WHEN o.dokter_pjanak IN ('-', '') THEN o.biaya_dokter_pjanak ELSE 0 END)
                + (CASE WHEN o.dokter_umum IN ('-', '') THEN o.biaya_dokter_umum ELSE 0 END)
                + (CASE WHEN o.asisten_operator1 IN ('-', '') THEN o.biayaasisten_operator1 ELSE 0 END)
                + (CASE WHEN o.asisten_operator2 IN ('-', '') THEN o.biayaasisten_operator2 ELSE 0 END)
                + (CASE WHEN o.asisten_operator3 IN ('-', '') THEN o.biayaasisten_operator3 ELSE 0 END)
                + (CASE WHEN o.asisten_anestesi IN ('-', '') THEN o.biayaasisten_anestesi ELSE 0 END)
                + (CASE WHEN o.asisten_anestesi2 IN ('-', '') THEN o.biayaasisten_anestesi2 ELSE 0 END)
                + (CASE WHEN o.bidan IN ('-', '') THEN o.biayabidan ELSE 0 END)
                + (CASE WHEN o.bidan2 IN ('-', '') THEN o.biayabidan2 ELSE 0 END)
                + (CASE WHEN o.bidan3 IN ('-', '') THEN o.biayabidan3 ELSE 0 END)
                + (CASE WHEN o.instrumen IN ('-', '') THEN o.biayainstrumen ELSE 0 END)
                + (CASE WHEN o.perawaat_resusitas IN ('-', '') THEN o.biayaperawaat_resusitas ELSE 0 END)
                + (CASE WHEN o.perawat_luar IN ('-', '') THEN o.biayaperawat_luar ELSE 0 END)
                + (CASE WHEN o.omloop IN ('-', '') THEN o.biaya_omloop ELSE 0 END)
                + (CASE WHEN o.omloop2 IN ('-', '') THEN o.biaya_omloop2 ELSE 0 END)
                + (CASE WHEN o.omloop3 IN ('-', '') THEN o.biaya_omloop3 ELSE 0 END)
                + (CASE WHEN o.omloop4 IN ('-', '') THEN o.biaya_omloop4 ELSE 0 END)
                + (CASE WHEN o.omloop5 IN ('-', '') THEN o.biaya_omloop5 ELSE 0 END)
            ) as js"),
            DB::raw("SUM(
                (CASE WHEN o.operator1 NOT IN ('-', '') THEN o.biayaoperator1 ELSE 0 END)
                + (CASE WHEN o.operator2 NOT IN ('-', '') THEN o.biayaoperator2 ELSE 0 END)
                + (CASE WHEN o.operator3 NOT IN ('-', '') THEN o.biayaoperator3 ELSE 0 END)
                + (CASE WHEN o.dokter_anak NOT IN ('-', '') THEN o.biayadokter_anak ELSE 0 END)
                + (CASE WHEN o.dokter_anestesi NOT IN ('-', '') THEN o.biayadokter_anestesi ELSE 0 END)
                + (CASE WHEN o.dokter_pjanak NOT IN ('-', '') THEN o.biaya_dokter_pjanak ELSE 0 END)
                + (CASE WHEN o.dokter_umum NOT IN ('-', '') THEN o.biaya_dokter_umum ELSE 0 END)
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
                CASE WHEN o.asisten_operator1 NOT IN ($nipIn, '-', '') THEN o.biayaasisten_operator1 ELSE 0 END
                + CASE WHEN o.asisten_operator2 NOT IN ($nipIn, '-', '') THEN o.biayaasisten_operator2 ELSE 0 END
                + CASE WHEN o.asisten_operator3 NOT IN ($nipIn, '-', '') THEN o.biayaasisten_operator3 ELSE 0 END
                + CASE WHEN o.asisten_anestesi NOT IN ($nipIn, '-', '') THEN o.biayaasisten_anestesi ELSE 0 END
                + CASE WHEN o.asisten_anestesi2 NOT IN ($nipIn, '-', '') THEN o.biayaasisten_anestesi2 ELSE 0 END
                + CASE WHEN o.bidan NOT IN ($nipIn, '-', '') THEN o.biayabidan ELSE 0 END
                + CASE WHEN o.bidan2 NOT IN ($nipIn, '-', '') THEN o.biayabidan2 ELSE 0 END
                + CASE WHEN o.bidan3 NOT IN ($nipIn, '-', '') THEN o.biayabidan3 ELSE 0 END
                + CASE WHEN o.instrumen NOT IN ($nipIn, '-', '') THEN o.biayainstrumen ELSE 0 END
                + CASE WHEN o.perawaat_resusitas NOT IN ($nipIn, '-', '') THEN o.biayaperawaat_resusitas ELSE 0 END
                + CASE WHEN o.perawat_luar NOT IN ($nipIn, '-', '') THEN o.biayaperawat_luar ELSE 0 END
                + CASE WHEN o.omloop NOT IN ($nipIn, '-', '') THEN o.biaya_omloop ELSE 0 END
                + CASE WHEN o.omloop2 NOT IN ($nipIn, '-', '') THEN o.biaya_omloop2 ELSE 0 END
                + CASE WHEN o.omloop3 NOT IN ($nipIn, '-', '') THEN o.biaya_omloop3 ELSE 0 END
                + CASE WHEN o.omloop4 NOT IN ($nipIn, '-', '') THEN o.biaya_omloop4 ELSE 0 END
                + CASE WHEN o.omloop5 NOT IN ($nipIn, '-', '') THEN o.biaya_omloop5 ELSE 0 END
            ) as jm_pr")
        )->groupBy('ni.status_lanjut', 'ni.tanggal')
        ->get()->keyBy(function($item) {
            return $item->status_lanjut . '|' . $item->tanggal;
        });

        // 11. SISA PIUTANG & BAYAR PIUTANG (Jika ada cicilan berjalan)
        $piutangQuery = DB::table('piutang_pasien as pp')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'pp.no_rawat');
        $piutangPerTgl = $piutangQuery->select(
            'ni.status_lanjut', 
            'ni.tanggal', 
            DB::raw('SUM(pp.totalpiutang) as total_piutang'),
            DB::raw('SUM(pp.sisapiutang) as total_sisa_piutang'),
            DB::raw('SUM(pp.uangmuka) as total_uangmuka')
        )->groupBy('ni.status_lanjut', 'ni.tanggal')
        ->get()->keyBy(function($item) {
            return $item->status_lanjut . '|' . $item->tanggal;
        });

        $bayarPiutangQuery = DB::table('bayar_piutang as bp')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'bp.no_rawat');
        $bayarPiutangPerTgl = $bayarPiutangQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(bp.besar_cicilan) as total_bayar_piutang'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_bayar_piutang];
            });

        // 12. RINCIAN NOTA PIUTANG BELUM BAYAR (Detail Pasien Lengkap)
        $rincianBayarPiutang = DB::table('reg_periksa')
            ->select(
                DB::raw("COALESCE(bayar_piutang.tgl_bayar, '-') as tgl_bayar"),
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                DB::raw("COALESCE(bayar_piutang.besar_cicilan, 0) as besar_cicilan"),
                DB::raw("COALESCE(bayar_piutang.catatan, '') as catatan"),
                'reg_periksa.no_rawat',
                DB::raw("COALESCE(bayar_piutang.diskon_piutang, 0) as diskon_piutang"),
                DB::raw("COALESCE(bayar_piutang.tidak_terbayar, 0) as tidak_terbayar"),
                'reg_periksa.kd_pj',
                'penjab.png_jawab',
                'piutang_pasien.status as status_piutang',
                DB::raw("COALESCE(piutang_pasien.totalpiutang, 0) as totalpiutang"),
                DB::raw("COALESCE(piutang_pasien.sisapiutang, 0) as sisapiutang"),
                DB::raw("COALESCE(piutang_pasien.uangmuka, 0) as uangmuka"),
                'reg_periksa.status_lanjut',
                'n.tanggal as tgl_nota'
            )
            ->join(DB::raw("{$notaSource} as n"), 'n.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->orderBy('n.tanggal', 'asc')
            ->orderBy('reg_periksa.no_rawat', 'asc')
            ->get();

        $noRawatsRincian = $rincianBayarPiutang->pluck('no_rawat')->unique()->filter()->toArray();

        // Eager load bridging_sep
        $seps = collect();
        if (!empty($noRawatsRincian)) {
            $seps = DB::table('bridging_sep')
                ->select('no_rawat', 'no_sep', 'jnspelayanan')
                ->whereIn('no_rawat', $noRawatsRincian)
                ->get()
                ->groupBy('no_rawat');
        }

        // Eager load billing
        $billings = collect();
        if (!empty($noRawatsRincian)) {
            $billings = DB::table('billing')
                ->select('no_rawat', 'no', 'status', 'totalbiaya', 'nm_perawatan')
                ->whereIn('no_rawat', $noRawatsRincian)
                ->get()
                ->groupBy('no_rawat');
        }

        $rincianBayarPiutang->map(function ($item) use ($seps, $billings) {
            $itemSeps = $seps->get($item->no_rawat, collect());
            
            // NOMOR SEP
            $item->getNoSep = $itemSeps->filter(function($sep) use ($item) {
                if ($item->status_lanjut == 'Ralan') {
                    return $sep->jnspelayanan == '2';
                } else {
                    return $sep->jnspelayanan == '1';
                }
            })->values();

            $itemBillings = $billings->get($item->no_rawat, collect());
            $byStatus = $itemBillings->groupBy('status');
            $byNo = $itemBillings->groupBy('no');

            // NOMOR NOTA
            $item->getNomorNota = $byNo->get('No.Nota', collect());
            // REGISTRASI
            $item->getRegistrasi = $byStatus->get('Registrasi', collect());
            // Obat+Emb+Tsl / OBAT
            $item->getObat = $byStatus->get('Obat', collect());
            // Retur Obat
            $item->getReturObat = $byStatus->get('Retur Obat', collect());
            // Resep Pulang
            $item->getResepPulang = $byStatus->get('Resep Pulang', collect());
            // RALAN DOKTER / 1 Paket Tindakan
            $item->getRalanDokter = $byStatus->get('Ralan Dokter', collect());
            // RALAN DOKTER PARAMEDIS / 2 Paket Tindakan
            $item->getRalanDrParamedis = $byStatus->get('Ralan Dokter Paramedis', collect());
            // RALAN PARAMEDIS / 3 Paket Tindakan
            $item->getRalanParamedis = $byStatus->get('Ralan Paramedis', collect());
            // RANAP DOKTER / 4 Paket Tindakan
            $item->getRanapDokter = $byStatus->get('Ranap Dokter', collect());
            // RANAP DOKTER PARAMEDIS / 5 Paket Tindakan
            $item->getRanapDrParamedis = $byStatus->get('Ranap Dokter Paramedis', collect());
            // RANAP PARAMEDIS / 6 Ranap Paramedis
            $item->getRanapParamedis = $byStatus->get('Ranap Paramedis', collect());
            // OPRASI
            $item->getOprasi = $byStatus->get('Operasi', collect());
            // LABORAT
            $item->getLaborat = $byStatus->get('Laborat', collect());
            // RADIOLOGI
            $item->getRadiologi = $byStatus->get('Radiologi', collect());
            // TAMBAHAN
            $item->getTambahan = $byStatus->get('Tambahan', collect());
            // POTONGAN
            $item->getPotongan = $byStatus->get('Potongan', collect());
            // KAMAR INAP
            $item->getKamarInap = $byStatus->get('Kamar', collect());

            return $item;
        });

        // GROUP BY BULAN NOTA
        $rincianBayarPiutangGrouped = $rincianBayarPiutang->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->tgl_nota)->translatedFormat('F Y');
        });

        // =========================================================================
        // 13. SUSUN DATA REKAP FINAL
        // =========================================================================
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
            ->merge(array_keys($piutangPerTgl->toArray()))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $namaBulan = [
            1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 5 => 'MEI', 6 => 'JUNI',
            7 => 'JULI', 8 => 'AGT', 9 => 'SEPT', 10 => 'OKT', 11 => 'NOV', 12 => 'DES'
        ];

        $dataRekap = collect();
        foreach ($allKeys as $keyStr) {
            list($statusLanjut, $tglStr) = explode('|', $keyStr);
            
            $dt = new DateTime($tglStr);
            $monthNum = (int)$dt->format('n');
            $year = $dt->format('Y');
            
            $statusPrefix = ($statusLanjut === 'Ralan') ? 'RJ' : 'RI';

            $tindakan = $tindakanPerTgl->get($keyStr);
            $regVal = (float) $regPerTgl->get($keyStr, 0);
            $jsVal   = $tindakan ? (float)$tindakan->js : 0;
            $bhpVal  = $tindakan ? (float)$tindakan->bhp : 0;
            $jmDrVal = $tindakan ? (float)$tindakan->jm_dr : 0;
            $prVal   = $tindakan ? (float)$tindakan->pr : 0;
            $ksoVal  = $tindakan ? (float)$tindakan->kso : 0;

            $obatVal  = (float) $obatPerTgl->get($keyStr, 0);
            $returVal = (float) $returPerTgl->get($keyStr, 0);

            $labItem = $labPerTgl->get($keyStr);
            $labJsVal   = $labItem ? (float)$labItem->js : 0;
            $labBhpVal  = $labItem ? (float)$labItem->bhp : 0;
            $labJmPjVal = $labItem ? (float)$labItem->jm_pj : 0;

            $roItem = $roPerTgl->get($keyStr);
            $roJsVal      = $roItem ? (float)$roItem->js : 0;
            $roBhpVal     = $roItem ? (float)$roItem->bhp : 0;
            $roJmPjVal    = $roItem ? (float)$roItem->jm_pj : 0;
            $roPetugasVal = $roItem ? (float)$roItem->petugas : 0;
            $roPerujukVal = $roItem ? (float)$roItem->perujuk : 0;

            $potVal = (float) $potPerTgl->get($keyStr, 0);
            $tbmVal = (float) $tbmPerTgl->get($keyStr, 0);
            $kmrVal = (float) $kmrPerTgl->get($keyStr, 0);

            $okItem    = $okPerTgl->get($keyStr);
            $okJsVal   = $okItem ? (float)$okItem->js : 0;
            $okJmDrVal = $okItem ? (float)$okItem->jm_dr : 0;
            $okJmPrVal = $okItem ? (float)$okItem->jm_pr : 0;

            $piutangItem = $piutangPerTgl->get($keyStr);
            $sisaPiutangVal = $piutangItem ? (float)$piutangItem->total_sisa_piutang : 0;
            $totalPiutangVal = $piutangItem ? (float)$piutangItem->total_piutang : 0;
            $bayarPiutangVal = (float) $bayarPiutangPerTgl->get($keyStr, 0);

            $rowTotal = $regVal + $jsVal + $bhpVal + $jmDrVal + $prVal + $ksoVal
                + $obatVal + $returVal
                + $labJsVal + $labBhpVal + $labJmPjVal
                + $roJsVal + $roBhpVal + $roJmPjVal + $roPetugasVal + $roPerujukVal
                + $potVal + $tbmVal + $kmrVal
                + $okJmDrVal + $okJmPrVal + $okJsVal;

            $dataRekap->push((object)[
                'group_key'     => $statusPrefix . '.' . $year,
                'nama_bulan'    => $namaBulan[$monthNum],
                'tgl_nota'      => $tglStr,
                'status_lanjut' => $statusLanjut,
                'reg'           => $regVal,
                'js'            => $jsVal,
                'bhp'           => $bhpVal,
                'jm_dr'         => $jmDrVal,
                'pr'            => $prVal,
                'kso'           => $ksoVal,
                'obat'          => $obatVal,
                'retur'         => $returVal,
                'lab_js'        => $labJsVal,
                'lab_bhp'       => $labBhpVal,
                'lab_jm_pj'     => $labJmPjVal,
                'ro_js'         => $roJsVal,
                'ro_bhp'        => $roBhpVal,
                'ro_jm_pj'      => $roJmPjVal,
                'ro_petugas'    => $roPetugasVal,
                'ro_perujuk'    => $roPerujukVal,
                'pot'           => $potVal,
                'tbm'           => $tbmVal,
                'kamar'         => $kmrVal,
                'ok_jm_dr'      => $okJmDrVal,
                'ok_jm_pr'      => $okJmPrVal,
                'ok_js'         => $okJsVal,
                'total'         => $rowTotal,
                'total_piutang' => $totalPiutangVal,
                'bayar_piutang' => $bayarPiutangVal,
                'sisa_piutang'  => $sisaPiutangVal,
                'grand_total'   => $sisaPiutangVal > 0 ? $sisaPiutangVal : $rowTotal
            ]);
        }

        return view('laporan.rekappendapatanasuransibelumbayar', compact('tgl1', 'tgl2', 'kd_pj', 'penjaminList', 'dataRekap', 'rincianBayarPiutangGrouped'));
    }

    public function detailTindakan(Request $request)
    {
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $tgl_nota = $request->tgl_nota;
        $status_lanjut = $request->status_lanjut;
        $kd_pj = $request->kd_pj ?? 'SEMUA';

        $penjaminCondition = ($kd_pj !== 'SEMUA')
            ? "AND rp.kd_pj = '{$kd_pj}'"
            : "AND rp.kd_pj NOT IN ('UMU', 'BPJ', '-')";

        $notaSource = "(
            SELECT DISTINCT 
                ni.no_rawat, 
                'Ranap' as status_lanjut,
                DATE_FORMAT(ni.tanggal, '%Y-%m-01') as bulan,
                p.no_rkm_medis,
                p.nm_pasien,
                ni.tanggal as tgl_tindakan,
                d.nm_dokter as pj
            FROM nota_inap ni
            JOIN reg_periksa rp ON ni.no_rawat = rp.no_rawat
            JOIN piutang_pasien pp ON ni.no_rawat = pp.no_rawat
            JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
            LEFT JOIN dokter d ON rp.kd_dokter = d.kd_dokter
            WHERE ni.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
            AND DATE_FORMAT(ni.tanggal, '%Y-%m-01') = '{$tgl_nota}'
            AND 'Ranap' = '{$status_lanjut}'
            {$penjaminCondition}
            AND pp.status = 'Belum Lunas'
            UNION
            SELECT DISTINCT 
                nj.no_rawat, 
                'Ralan' as status_lanjut,
                DATE_FORMAT(nj.tanggal, '%Y-%m-01') as bulan,
                p.no_rkm_medis,
                p.nm_pasien,
                nj.tanggal as tgl_tindakan,
                d.nm_dokter as pj
            FROM nota_jalan nj
            JOIN reg_periksa rp ON nj.no_rawat = rp.no_rawat
            JOIN piutang_pasien pp ON nj.no_rawat = pp.no_rawat
            JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
            LEFT JOIN dokter d ON rp.kd_dokter = d.kd_dokter
            WHERE nj.tanggal BETWEEN '{$tgl1}' AND '{$tgl2}'
            AND DATE_FORMAT(nj.tanggal, '%Y-%m-01') = '{$tgl_nota}'
            AND 'Ralan' = '{$status_lanjut}'
            {$penjaminCondition}
            AND pp.status = 'Belum Lunas'
        )";

        $nipDokterKhusus = [
            '12041999', '0518010327', '518010327', '1802081010970003', '1802081010970000',
            '10104020181', '0817010312', '817010312', '05084010153', '5084010153',
            '0106010608', '106010608', '19960223', '0224010675', '224010675',
            '1802086108980001', '1802086108980000', '1802054204000003', '1802054204000000',
            '09964020055', '9964020055', '88888', '0214010227', '214010227', '0512010199', '512010199', '1204020129'
        ];
        $nipIn = "'" . implode("','", array_unique($nipDokterKhusus)) . "'";

        // 1. DOKTER SAJA
        $sqlDr = "
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, ni.tgl_tindakan, jp.nm_perawatan, d.nm_dokter, '' as nm_petugas, t.material as js, t.bhp, t.tarif_tindakandr as jm_dr, 0 as pr, COALESCE(t.kso, 0) as kso
            FROM rawat_jl_dr t 
            JOIN jns_perawatan jp ON t.kd_jenis_prw = jp.kd_jenis_prw
            JOIN dokter d ON t.kd_dokter = d.kd_dokter
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
            UNION ALL
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, ni.tgl_tindakan, jp.nm_perawatan, d.nm_dokter, '' as nm_petugas, t.material as js, t.bhp, t.tarif_tindakandr as jm_dr, 0 as pr, COALESCE(t.kso, 0) as kso
            FROM rawat_inap_dr t 
            JOIN jns_perawatan_inap jp ON t.kd_jenis_prw = jp.kd_jenis_prw
            JOIN dokter d ON t.kd_dokter = d.kd_dokter
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
        ";
        $detailDr = DB::select($sqlDr);

        // 2. PARAMEDIS
        $sqlPr = "
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, ni.tgl_tindakan, jp.nm_perawatan, '' as nm_dokter, p2.nama as nm_petugas, t.material as js, t.bhp, 0 as jm_dr, t.tarif_tindakanpr as pr, COALESCE(t.kso, 0) as kso
            FROM rawat_jl_pr t 
            JOIN jns_perawatan jp ON t.kd_jenis_prw = jp.kd_jenis_prw
            JOIN petugas p2 ON t.nip = p2.nip
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
            WHERE t.nip NOT IN ($nipIn)
            UNION ALL
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, ni.tgl_tindakan, jp.nm_perawatan, p2.nama as nm_dokter, '' as nm_petugas, t.material as js, t.bhp, t.tarif_tindakanpr as jm_dr, 0 as pr, COALESCE(t.kso, 0) as kso
            FROM rawat_jl_pr t 
            JOIN jns_perawatan jp ON t.kd_jenis_prw = jp.kd_jenis_prw
            JOIN petugas p2 ON t.nip = p2.nip
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
            WHERE t.nip IN ($nipIn)
            UNION ALL
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, ni.tgl_tindakan, jp.nm_perawatan, '' as nm_dokter, p2.nama as nm_petugas, t.material as js, t.bhp, 0 as jm_dr, t.tarif_tindakanpr as pr, COALESCE(t.kso, 0) as kso
            FROM rawat_inap_pr t 
            JOIN jns_perawatan_inap jp ON t.kd_jenis_prw = jp.kd_jenis_prw
            JOIN petugas p2 ON t.nip = p2.nip
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
            WHERE t.nip NOT IN ($nipIn)
            UNION ALL
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, ni.tgl_tindakan, jp.nm_perawatan, p2.nama as nm_dokter, '' as nm_petugas, t.material as js, t.bhp, t.tarif_tindakanpr as jm_dr, 0 as pr, COALESCE(t.kso, 0) as kso
            FROM rawat_inap_pr t 
            JOIN jns_perawatan_inap jp ON t.kd_jenis_prw = jp.kd_jenis_prw
            JOIN petugas p2 ON t.nip = p2.nip
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
            WHERE t.nip IN ($nipIn)
        ";
        $detailPr = DB::select($sqlPr);

        // 3. DRPR
        $sqlDrpr = "
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, ni.tgl_tindakan, jp.nm_perawatan, d.nm_dokter, 
            CASE WHEN t.nip IN ($nipIn) THEN '' ELSE p2.nama END as nm_petugas, 
            t.material as js, t.bhp, (t.tarif_tindakandr + CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, 
            CASE WHEN t.nip IN ($nipIn) THEN 0 ELSE t.tarif_tindakanpr END as pr, 
            COALESCE(t.kso, 0) as kso
            FROM rawat_jl_drpr t 
            JOIN jns_perawatan jp ON t.kd_jenis_prw = jp.kd_jenis_prw
            JOIN dokter d ON t.kd_dokter = d.kd_dokter
            JOIN petugas p2 ON t.nip = p2.nip
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
            UNION ALL
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, ni.tgl_tindakan, jp.nm_perawatan, d.nm_dokter, 
            CASE WHEN t.nip IN ($nipIn) THEN '' ELSE p2.nama END as nm_petugas, 
            t.material as js, t.bhp, (t.tarif_tindakandr + CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, 
            CASE WHEN t.nip IN ($nipIn) THEN 0 ELSE t.tarif_tindakanpr END as pr, 
            COALESCE(t.kso, 0) as kso
            FROM rawat_inap_drpr t 
            JOIN jns_perawatan_inap jp ON t.kd_jenis_prw = jp.kd_jenis_prw
            JOIN dokter d ON t.kd_dokter = d.kd_dokter
            JOIN petugas p2 ON t.nip = p2.nip
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
        ";
        $detailDrpr = DB::select($sqlDrpr);

        // 4. LAB
        $sqlLab = "
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, t.tgl_periksa as tgl_tindakan, jl.nm_perawatan, d.nm_dokter, p2.nama as nm_petugas, t.bagian_rs as js, t.bhp, t.tarif_tindakan_dokter as jm_dr, t.tarif_tindakan_petugas as pr, t.kso
            FROM periksa_lab t 
            JOIN jns_perawatan_lab jl ON t.kd_jenis_prw = jl.kd_jenis_prw
            LEFT JOIN dokter d ON t.kd_dokter = d.kd_dokter
            LEFT JOIN petugas p2 ON t.nip = p2.nip
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
        ";
        $detailLab = DB::select($sqlLab);

        // 5. RO
        $sqlRo = "
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, t.tgl_periksa as tgl_tindakan, jr.nm_perawatan, 
            d.nm_dokter, p2.nama as nm_petugas,
            (t.bagian_rs + CASE WHEN t.dokter_perujuk = 'D0000091' THEN t.tarif_perujuk ELSE 0 END) as js, 
            t.bhp, t.tarif_tindakan_dokter as jm_dr, t.tarif_tindakan_petugas as petugas, (CASE WHEN t.dokter_perujuk = 'D0000091' THEN 0 ELSE t.tarif_perujuk END) as perujuk, t.kso
            FROM periksa_radiologi t 
            JOIN jns_perawatan_radiologi jr ON t.kd_jenis_prw = jr.kd_jenis_prw
            JOIN dokter d ON t.kd_dokter = d.kd_dokter
            JOIN petugas p2 ON t.nip = p2.nip
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
        ";
        $detailRo = DB::select($sqlRo);

        // 6. OK
        $sqlOk = "
            SELECT ni.no_rawat, ni.no_rkm_medis, ni.nm_pasien, t.tgl_operasi as tgl_tindakan, p.nm_perawatan, d.nm_dokter as operator,
                (
                    t.biayaalat + t.biayasewaok + t.akomodasi + t.bagian_rs + t.biayasarpras
                    + (CASE WHEN t.operator1 IN ('-', '') THEN t.biayaoperator1 ELSE 0 END)
                    + (CASE WHEN t.operator2 IN ('-', '') THEN t.biayaoperator2 ELSE 0 END)
                    + (CASE WHEN t.operator3 IN ('-', '') THEN t.biayaoperator3 ELSE 0 END)
                    + (CASE WHEN t.dokter_anak IN ('-', '') THEN t.biayadokter_anak ELSE 0 END)
                    + (CASE WHEN t.dokter_anestesi IN ('-', '') THEN t.biayadokter_anestesi ELSE 0 END)
                    + (CASE WHEN t.dokter_pjanak IN ('-', '') THEN t.biaya_dokter_pjanak ELSE 0 END)
                    + (CASE WHEN t.dokter_umum IN ('-', '') THEN t.biaya_dokter_umum ELSE 0 END)
                    + (CASE WHEN t.asisten_operator1 IN ('-', '') THEN t.biayaasisten_operator1 ELSE 0 END)
                    + (CASE WHEN t.asisten_operator2 IN ('-', '') THEN t.biayaasisten_operator2 ELSE 0 END)
                    + (CASE WHEN t.asisten_operator3 IN ('-', '') THEN t.biayaasisten_operator3 ELSE 0 END)
                    + (CASE WHEN t.asisten_anestesi IN ('-', '') THEN t.biayaasisten_anestesi ELSE 0 END)
                    + (CASE WHEN t.asisten_anestesi2 IN ('-', '') THEN t.biayaasisten_anestesi2 ELSE 0 END)
                    + (CASE WHEN t.bidan IN ('-', '') THEN t.biayabidan ELSE 0 END)
                    + (CASE WHEN t.bidan2 IN ('-', '') THEN t.biayabidan2 ELSE 0 END)
                    + (CASE WHEN t.bidan3 IN ('-', '') THEN t.biayabidan3 ELSE 0 END)
                    + (CASE WHEN t.instrumen IN ('-', '') THEN t.biayainstrumen ELSE 0 END)
                    + (CASE WHEN t.perawaat_resusitas IN ('-', '') THEN t.biayaperawaat_resusitas ELSE 0 END)
                    + (CASE WHEN t.perawat_luar IN ('-', '') THEN t.biayaperawat_luar ELSE 0 END)
                    + (CASE WHEN t.omloop IN ('-', '') THEN t.biaya_omloop ELSE 0 END)
                    + (CASE WHEN t.omloop2 IN ('-', '') THEN t.biaya_omloop2 ELSE 0 END)
                    + (CASE WHEN t.omloop3 IN ('-', '') THEN t.biaya_omloop3 ELSE 0 END)
                    + (CASE WHEN t.omloop4 IN ('-', '') THEN t.biaya_omloop4 ELSE 0 END)
                    + (CASE WHEN t.omloop5 IN ('-', '') THEN t.biaya_omloop5 ELSE 0 END)
                ) as js,
                (
                    (CASE WHEN t.operator1 NOT IN ('-', '') THEN t.biayaoperator1 ELSE 0 END)
                    + (CASE WHEN t.operator2 NOT IN ('-', '') THEN t.biayaoperator2 ELSE 0 END)
                    + (CASE WHEN t.operator3 NOT IN ('-', '') THEN t.biayaoperator3 ELSE 0 END)
                    + (CASE WHEN t.dokter_anak NOT IN ('-', '') THEN t.biayadokter_anak ELSE 0 END)
                    + (CASE WHEN t.dokter_anestesi NOT IN ('-', '') THEN t.biayadokter_anestesi ELSE 0 END)
                    + (CASE WHEN t.dokter_pjanak NOT IN ('-', '') THEN t.biaya_dokter_pjanak ELSE 0 END)
                    + (CASE WHEN t.dokter_umum NOT IN ('-', '') THEN t.biaya_dokter_umum ELSE 0 END)
                    + CASE WHEN t.asisten_operator1 IN ($nipIn) THEN t.biayaasisten_operator1 ELSE 0 END
                    + CASE WHEN t.asisten_operator2 IN ($nipIn) THEN t.biayaasisten_operator2 ELSE 0 END
                    + CASE WHEN t.asisten_operator3 IN ($nipIn) THEN t.biayaasisten_operator3 ELSE 0 END
                    + CASE WHEN t.asisten_anestesi IN ($nipIn) THEN t.biayaasisten_anestesi ELSE 0 END
                    + CASE WHEN t.asisten_anestesi2 IN ($nipIn) THEN t.biayaasisten_anestesi2 ELSE 0 END
                    + CASE WHEN t.bidan IN ($nipIn) THEN t.biayabidan ELSE 0 END
                    + CASE WHEN t.bidan2 IN ($nipIn) THEN t.biayabidan2 ELSE 0 END
                    + CASE WHEN t.bidan3 IN ($nipIn) THEN t.biayabidan3 ELSE 0 END
                    + CASE WHEN t.instrumen IN ($nipIn) THEN t.biayainstrumen ELSE 0 END
                    + CASE WHEN t.perawaat_resusitas IN ($nipIn) THEN t.biayaperawaat_resusitas ELSE 0 END
                    + CASE WHEN t.perawat_luar IN ($nipIn) THEN t.biayaperawat_luar ELSE 0 END
                    + CASE WHEN t.omloop IN ($nipIn) THEN t.biaya_omloop ELSE 0 END
                    + CASE WHEN t.omloop2 IN ($nipIn) THEN t.biaya_omloop2 ELSE 0 END
                    + CASE WHEN t.omloop3 IN ($nipIn) THEN t.biaya_omloop3 ELSE 0 END
                    + CASE WHEN t.omloop4 IN ($nipIn) THEN t.biaya_omloop4 ELSE 0 END
                    + CASE WHEN t.omloop5 IN ($nipIn) THEN t.biaya_omloop5 ELSE 0 END
                ) as jm_dr,
                (
                    CASE WHEN t.asisten_operator1 NOT IN ($nipIn, '-', '') THEN t.biayaasisten_operator1 ELSE 0 END
                    + CASE WHEN t.asisten_operator2 NOT IN ($nipIn, '-', '') THEN t.biayaasisten_operator2 ELSE 0 END
                    + CASE WHEN t.asisten_operator3 NOT IN ($nipIn, '-', '') THEN t.biayaasisten_operator3 ELSE 0 END
                    + CASE WHEN t.asisten_anestesi NOT IN ($nipIn, '-', '') THEN t.biayaasisten_anestesi ELSE 0 END
                    + CASE WHEN t.asisten_anestesi2 NOT IN ($nipIn, '-', '') THEN t.biayaasisten_anestesi2 ELSE 0 END
                    + CASE WHEN t.bidan NOT IN ($nipIn, '-', '') THEN t.biayabidan ELSE 0 END
                    + CASE WHEN t.bidan2 NOT IN ($nipIn, '-', '') THEN t.biayabidan2 ELSE 0 END
                    + CASE WHEN t.bidan3 NOT IN ($nipIn, '-', '') THEN t.biayabidan3 ELSE 0 END
                    + CASE WHEN t.instrumen NOT IN ($nipIn, '-', '') THEN t.biayainstrumen ELSE 0 END
                    + CASE WHEN t.perawaat_resusitas NOT IN ($nipIn, '-', '') THEN t.biayaperawaat_resusitas ELSE 0 END
                    + CASE WHEN t.perawat_luar NOT IN ($nipIn, '-', '') THEN t.biayaperawat_luar ELSE 0 END
                    + CASE WHEN t.omloop NOT IN ($nipIn, '-', '') THEN t.biaya_omloop ELSE 0 END
                    + CASE WHEN t.omloop2 NOT IN ($nipIn, '-', '') THEN t.biaya_omloop2 ELSE 0 END
                    + CASE WHEN t.omloop3 NOT IN ($nipIn, '-', '') THEN t.biaya_omloop3 ELSE 0 END
                    + CASE WHEN t.omloop4 NOT IN ($nipIn, '-', '') THEN t.biaya_omloop4 ELSE 0 END
                    + CASE WHEN t.omloop5 NOT IN ($nipIn, '-', '') THEN t.biaya_omloop5 ELSE 0 END
                ) as jm_pr
            FROM operasi t
            JOIN paket_operasi p ON t.kode_paket = p.kode_paket
            LEFT JOIN dokter d ON t.operator1 = d.kd_dokter
            JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
        ";
        $detailOk = DB::select($sqlOk);

        return view('laporan.rekappendapatanasuransibelumbayar-detail', compact(
            'tgl1', 'tgl2', 'tgl_nota', 'status_lanjut', 'kd_pj',
            'detailDr', 'detailPr', 'detailDrpr', 'detailLab', 'detailRo', 'detailOk'
        ));
    }
}
