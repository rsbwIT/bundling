<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RekapPendapatanBulanan extends Controller
{
    public function index(Request $request)
    {

        // ====================== FILTER ======================

        $tgl1 = $request->tgl1 ?? date('Y-m-01');
        $tgl2 = $request->tgl2 ?? date('Y-m-d');
        $cariNomor = $request->cariNomor;
        $stsLanjut = $request->stsLanjut;
        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];
        $multi_tanggal_ralan = request('multi_tanggal_ralan');
        $multi_tanggal_ralan = $multi_tanggal_ralan
            ? array_map('trim', explode(',', $multi_tanggal_ralan))
            : [];


        // ====================== DATA PASIEN UMUM ======================

        $bayarUmum = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.status_lanjut',
                'billing.tgl_byr'
            )
            ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('reg_periksa.kd_pj', 'UMU')
            ->whereBetween('billing.tgl_byr', [$tgl1, $tgl2])
            ->where('billing.no', 'No.Nota')
            ->when($stsLanjut, fn($q) => $q->where('reg_periksa.status_lanjut', $stsLanjut))
            ->when(
                $cariNomor,
                fn($q) =>
                $q->where(function ($x) use ($cariNomor) {
                    $x->where('reg_periksa.no_rawat', 'like', "%$cariNomor%")
                        ->orWhere('reg_periksa.no_rkm_medis', 'like', "%$cariNomor%");
                })
            )
            ->groupBy('reg_periksa.no_rawat')
            ->get();


        // ====================== AMBIL DETAIL BILLING ======================

        foreach ($bayarUmum as $item) {

            $billing = DB::table('billing')
                ->select('status', 'totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->get();

            $item->getRegistrasi = $billing->where('status', 'Registrasi');

            // ================= DOKTER =================

            $item->getRalanDokter = $billing->where('status', 'Ralan Dokter');

            $item->getRanapDokter = $billing->whereIn('status', [
                'Ranap Dokter',
                'Ralan Dokter'
            ]);

            // ================= PARAMEDIS =================

            $item->getRalanParamedis = $billing->where('status', 'Ralan Paramedis');

            $item->getRanapParamedis = $billing->whereIn('status', [
                'Ranap Paramedis',
                'Ralan Paramedis'
            ]);

            // ================= DR PARAMEDIS =================

            $item->getRalanDrParamedis = $billing->where('status', 'Ralan Dokter Paramedis');

            $item->getRanapDrParamedis = $billing->whereIn('status', [
                'Ranap Dokter Paramedis',
                'Ralan Dokter Paramedis'
            ]);

            // ================= LAINNYA =================

            $item->getObat = $billing->where('status', 'Obat');
            $item->getReturObat = $billing->where('status', 'Retur Obat');
            $item->getLaborat = $billing->where('status', 'Laborat');
            $item->getRadiologi = $billing->where('status', 'Radiologi');
            $item->getKamarInap = $billing->where('status', 'Kamar');
            $item->getPotongan = $billing->where('status', 'Potongan');
            $item->getOperasi = $billing->where('status', 'Operasi');
        }


        // ====================== INISIALISASI ======================

        $registerRanap = $registerRalan = 0;
        $jmDokterRanap = $jmDokterRalan = 0;
        // $paramedisRanap = $paramedisRalan = 0;
        $paramedisRanap = -DB::table('billing as b')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->where('b.nm_perawatan', 'like', '%Paket Hemodialisa%')
            ->where('b.status', 'Ralan Paramedis')
            ->where('r.status_lanjut', 'Ranap')
            ->where('r.kd_pj', 'UMU')
            ->whereBetween('b.tgl_byr', [$tgl1, $tgl2])
            ->sum(DB::raw("
        CASE 
            WHEN b.status = 'Ralan Paramedis' 
            THEN ROUND(b.totalbiaya * 1.11, 0)
            ELSE 0
        END
    "));


        $paramedisRalan = -DB::table('billing as b')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->where('b.nm_perawatan', 'like', '%Paket Hemodialisa%')
            ->where('b.status', 'Ralan Paramedis')
            ->where('r.status_lanjut', 'Ralan')
            ->where('r.kd_pj', 'UMU')
            ->whereBetween('b.tgl_byr', [$tgl1, $tgl2])
            ->sum(DB::raw("
        CASE 
            WHEN b.status = 'Ralan Paramedis' 
            THEN ROUND(b.totalbiaya * 1.11, 0)
            ELSE 0
        END
    "));


        $drparamedisRanap = $drparamedisRalan = 0;
        $obatRanap = $obatRalan = 0;
        $returRanap = $returRalan = 0;
        $labRanap = $labRalan = 0;
        $radiologiRanap = $radiologiRalan = 0;
        $kamarRanap = $kamarRalan = 0;
        $operasiRanap = $operasiRalan = 0;
        $potonganRanap = $potonganRalan = 0;

        //bpjs registrasi
        $tgl_bpjs1 = $request->tgl_bpjs1 ?? $tgl1;
        $tgl_bpjs2 = $request->tgl_bpjs2 ?? $tgl2;


        $registerRanapBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Registrasi')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap')
            ->when(!empty($multi_tanggal_ranap), function ($q) use ($multi_tanggal_ranap) {
                $q->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
            })
            ->get();

        $registerRalanBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Registrasi')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan')
            ->where('r.kd_pj', 'BPJ') // ← ini filter BPJS
            ->when(!empty($multi_tanggal_ralan), function ($q) use ($multi_tanggal_ralan) {
                $q->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ralan);
            })
            ->get();


        $RalanDokterBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'

            )
            ->where('b.status', 'Ralan Dokter')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $RanapDokterBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Ralan Dokter', 'Ranap Dokter']) // filter Ralan & Ranap Dokter
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        // $TotalJasaSarana = DB::selectOne("
        //     SELECT 
        //         COALESCE(SUM(x.jasa_sarana),0) AS total_jasa_sarana
        //     FROM (

        //         SELECT SUM(rjpr.material) AS jasa_sarana
        //         FROM rawat_jl_pr rjpr
        //         WHERE EXISTS (
        //             SELECT 1 FROM bayar_piutang bp 
        //             WHERE bp.no_rawat = rjpr.no_rawat
        //             AND DATE(bp.tgl_bayar) BETWEEN ? AND ?
        //         )

        //         UNION ALL

        //         SELECT SUM(rjdrpr.material)
        //         FROM rawat_jl_drpr rjdrpr
        //         WHERE EXISTS (
        //             SELECT 1 FROM bayar_piutang bp 
        //             WHERE bp.no_rawat = rjdrpr.no_rawat
        //             AND DATE(bp.tgl_bayar) BETWEEN ? AND ?
        //         )

        //         UNION ALL

        //         SELECT SUM(rjdr.material)
        //         FROM rawat_jl_dr rjdr
        //         WHERE EXISTS (
        //             SELECT 1 FROM bayar_piutang bp 
        //             WHERE bp.no_rawat = rjdr.no_rawat
        //             AND DATE(bp.tgl_bayar) BETWEEN ? AND ?
        //         )

        //         UNION ALL

        //         SELECT SUM(ridr.material)
        //         FROM rawat_inap_dr ridr
        //         WHERE EXISTS (
        //             SELECT 1 FROM bayar_piutang bp 
        //             WHERE bp.no_rawat = ridr.no_rawat
        //             AND DATE(bp.tgl_bayar) BETWEEN ? AND ?
        //         )

        //         UNION ALL

        //         SELECT SUM(ridrpr.material)
        //         FROM rawat_inap_drpr ridrpr
        //         WHERE EXISTS (
        //             SELECT 1 FROM bayar_piutang bp 
        //             WHERE bp.no_rawat = ridrpr.no_rawat
        //             AND DATE(bp.tgl_bayar) BETWEEN ? AND ?
        //         )

        //         UNION ALL

        //         SELECT SUM(ripr.material)
        //         FROM rawat_inap_pr ripr
        //         WHERE EXISTS (
        //             SELECT 1 FROM bayar_piutang bp 
        //             WHERE bp.no_rawat = ripr.no_rawat
        //             AND DATE(bp.tgl_bayar) BETWEEN ? AND ?
        //         )

        //     ) x
        // ", [
        //     $tgl_bpjs1,
        //     $tgl_bpjs2,
        //     $tgl_bpjs1,
        //     $tgl_bpjs2,
        //     $tgl_bpjs1,
        //     $tgl_bpjs2,
        //     $tgl_bpjs1,
        //     $tgl_bpjs2,
        //     $tgl_bpjs1,
        //     $tgl_bpjs2,
        //     $tgl_bpjs1,
        //     $tgl_bpjs2,
        // ]);

        // $TotalJasaSarana = $TotalJasaSarana->total_jasa_sarana ?? 0;

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        if (empty($multi_tanggal_ranap)) {
            $TotalJasaSarana = 0;
        } else {

            $placeholders = implode(',', array_fill(0, count($multi_tanggal_ranap), '?'));

            $TotalJasaSarana = DB::selectOne("
        SELECT 
            COALESCE(SUM(x.jasa_sarana),0) AS total_jasa_sarana
        FROM (

            SELECT SUM(rjpr.material) AS jasa_sarana
            FROM rawat_jl_pr rjpr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = rjpr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT SUM(rjdrpr.material)
            FROM rawat_jl_drpr rjdrpr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = rjdrpr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT SUM(rjdr.material)
            FROM rawat_jl_dr rjdr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = rjdr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT SUM(ridr.material)
            FROM rawat_inap_dr ridr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = ridr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT SUM(ridrpr.material)
            FROM rawat_inap_drpr ridrpr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = ridrpr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT SUM(ripr.material)
            FROM rawat_inap_pr ripr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = ripr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

        ) x
    ", array_merge(
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap
            ));

            $TotalJasaSarana = $TotalJasaSarana->total_jasa_sarana ?? 0;
        }

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        if (empty($multi_tanggal_ranap)) {
            $TotalBHP = 0;
        } else {

            $placeholders = implode(',', array_fill(0, count($multi_tanggal_ranap), '?'));

            $TotalBHP = DB::selectOne("
        SELECT 
            COALESCE(SUM(x.bhp),0) AS total_bhp
        FROM (

            SELECT SUM(rjpr.bhp) AS bhp
            FROM rawat_jl_pr rjpr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = rjpr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT SUM(rjdrpr.bhp)
            FROM rawat_jl_drpr rjdrpr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = rjdrpr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT SUM(rjdr.bhp)
            FROM rawat_jl_dr rjdr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = rjdr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT SUM(ridr.bhp)
            FROM rawat_inap_dr ridr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = ridr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT SUM(ridrpr.bhp)
            FROM rawat_inap_drpr ridrpr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = ridrpr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT SUM(ripr.bhp)
            FROM rawat_inap_pr ripr
            WHERE EXISTS (
                SELECT 1 FROM bayar_piutang bp 
                WHERE bp.no_rawat = ripr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

        ) x
    ", array_merge(
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap
            ));

            $TotalBHP = $TotalBHP->total_bhp ?? 0;
        }

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        if (empty($multi_tanggal_ranap)) {
            $TotalJMDokter = 0;
        } else {

            $placeholders = implode(',', array_fill(0, count($multi_tanggal_ranap), '?'));

            $TotalJMDokter = DB::selectOne("
        SELECT 
            COALESCE(dokter.total_dokter,0) 
            + COALESCE(paramedis.total_paramedis,0) AS total_jm_dokter
        FROM

        (
            SELECT 
                SUM(tarif) AS total_dokter
            FROM (
                SELECT rjdr.tarif_tindakandr AS tarif
                FROM rawat_jl_dr rjdr
                WHERE EXISTS (
                    SELECT 1 FROM bayar_piutang bp 
                    WHERE bp.no_rawat = rjdr.no_rawat
                    AND DATE(bp.tgl_bayar) IN ($placeholders)
                )

                UNION ALL

                SELECT rjdrpr.tarif_tindakandr
                FROM rawat_jl_drpr rjdrpr
                WHERE EXISTS (
                    SELECT 1 FROM bayar_piutang bp 
                    WHERE bp.no_rawat = rjdrpr.no_rawat
                    AND DATE(bp.tgl_bayar) IN ($placeholders)
                )

                UNION ALL

                SELECT ridr.tarif_tindakandr
                FROM rawat_inap_dr ridr
                WHERE EXISTS (
                    SELECT 1 FROM bayar_piutang bp 
                    WHERE bp.no_rawat = ridr.no_rawat
                    AND DATE(bp.tgl_bayar) IN ($placeholders)
                )

                UNION ALL

                SELECT ridrpr.tarif_tindakandr
                FROM rawat_inap_drpr ridrpr
                WHERE EXISTS (
                    SELECT 1 FROM bayar_piutang bp 
                    WHERE bp.no_rawat = ridrpr.no_rawat
                    AND DATE(bp.tgl_bayar) IN ($placeholders)
                )
            ) t
        ) dokter,

        (
            SELECT 
                SUM(tarif) AS total_paramedis
            FROM (
                SELECT rjdrpr.tarif_tindakanpr AS tarif
                FROM rawat_jl_drpr rjdrpr
                INNER JOIN petugas pr ON pr.nip = rjdrpr.nip
                WHERE rjdrpr.tarif_tindakanpr > 0
                AND pr.nama IN (
                    'Nusae Qolbi',
                    '(Fis) Agung Rangga Dinata',
                    '(FIS) Aini Rosmaniar Bakri',
                    '(FIS) Ultha Apriza',
                    '(NS) Kuspratiknyo',
                    '(GZ) Nyiayu Farahnaz',
                    '(GZ) Vega Aurellia Putri',
                    '(TWS) Rahma Idhanani',
                    '(FIS) Andri Oktavian',
                    '(FIS) Revi Restiana'
                )
                AND EXISTS (
                    SELECT 1 FROM bayar_piutang bp 
                    WHERE bp.no_rawat = rjdrpr.no_rawat
                    AND DATE(bp.tgl_bayar) IN ($placeholders)
                )

                UNION ALL

                SELECT rjpr.tarif_tindakanpr
                FROM rawat_jl_pr rjpr
                INNER JOIN petugas pr ON pr.nip = rjpr.nip
                WHERE rjpr.tarif_tindakanpr > 0
                AND pr.nama IN (
                    'Nusae Qolbi',
                    '(Fis) Agung Rangga Dinata',
                    '(FIS) Aini Rosmaniar Bakri',
                    '(FIS) Ultha Apriza',
                    '(NS) Kuspratiknyo',
                    '(GZ) Nyiayu Farahnaz',
                    '(GZ) Vega Aurellia Putri',
                    '(TWS) Rahma Idhanani',
                    '(FIS) Andri Oktavian',
                    '(FIS) Revi Restiana'
                )
                AND EXISTS (
                    SELECT 1 FROM bayar_piutang bp 
                    WHERE bp.no_rawat = rjpr.no_rawat
                    AND DATE(bp.tgl_bayar) IN ($placeholders)
                )

                UNION ALL

                SELECT ripr.tarif_tindakanpr
                FROM rawat_inap_pr ripr
                INNER JOIN petugas pr ON pr.nip = ripr.nip
                WHERE ripr.tarif_tindakanpr > 0
                AND pr.nama IN (
                    'Nusae Qolbi',
                    '(Fis) Agung Rangga Dinata',
                    '(FIS) Aini Rosmaniar Bakri',
                    '(FIS) Ultha Apriza',
                    '(NS) Kuspratiknyo',
                    '(GZ) Nyiayu Farahnaz',
                    '(GZ) Vega Aurellia Putri',
                    '(TWS) Rahma Idhanani',
                    '(FIS) Andri Oktavian',
                    '(FIS) Revi Restiana'
                )
                AND EXISTS (
                    SELECT 1 FROM bayar_piutang bp 
                    WHERE bp.no_rawat = ripr.no_rawat
                    AND DATE(bp.tgl_bayar) IN ($placeholders)
                )
            ) p
        ) paramedis
    ", array_merge(
                // dokter (4x)
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,

                // paramedis (3x)
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap
            ));

            $TotalJMDokter = $TotalJMDokter->total_jm_dokter ?? 0;
        }

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        if (empty($multi_tanggal_ranap)) {
            $totalParamedis = 0;
        } else {

            $placeholders = implode(',', array_fill(0, count($multi_tanggal_ranap), '?'));

            $totalParamedis = DB::selectOne("
        SELECT 
            COALESCE(SUM(x.jm_paramedis),0) AS total_paramedis
        FROM (

            SELECT rjdrpr.tarif_tindakanpr AS jm_paramedis
            FROM rawat_jl_drpr rjdrpr
            INNER JOIN petugas pr ON pr.nip = rjdrpr.nip
            WHERE rjdrpr.tarif_tindakanpr > 0
            AND pr.nama NOT IN (
                'Nusae Qolbi',
                '(Fis) Agung Rangga Dinata',
                '(FIS) Aini Rosmaniar Bakri',
                '(FIS) Ultha Apriza',
                '(NS) Kuspratiknyo',
                '(GZ) Nyiayu Farahnaz',
                '(GZ) Vega Aurellia Putri',
                '(TWS) Rahma Idhanani',
                '(FIS) Andri Oktavian',
                '(FIS) Revi Restiana'
            )
            AND EXISTS (
                SELECT 1 
                FROM bayar_piutang bp 
                WHERE bp.no_rawat = rjdrpr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT rjpr.tarif_tindakanpr
            FROM rawat_jl_pr rjpr
            INNER JOIN petugas pr ON pr.nip = rjpr.nip
            WHERE rjpr.tarif_tindakanpr > 0
            AND pr.nama NOT IN (
                'Nusae Qolbi',
                '(Fis) Agung Rangga Dinata',
                '(FIS) Aini Rosmaniar Bakri',
                '(FIS) Ultha Apriza',
                '(NS) Kuspratiknyo',
                '(GZ) Nyiayu Farahnaz',
                '(GZ) Vega Aurellia Putri',
                '(TWS) Rahma Idhanani',
                '(FIS) Andri Oktavian',
                '(FIS) Revi Restiana'
            )
            AND EXISTS (
                SELECT 1 
                FROM bayar_piutang bp 
                WHERE bp.no_rawat = rjpr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            SELECT ripr.tarif_tindakanpr
            FROM rawat_inap_pr ripr
            INNER JOIN petugas pr ON pr.nip = ripr.nip
            WHERE ripr.tarif_tindakanpr > 0
            AND pr.nama NOT IN (
                'Nusae Qolbi',
                '(Fis) Agung Rangga Dinata',
                '(FIS) Aini Rosmaniar Bakri',
                '(FIS) Ultha Apriza',
                '(NS) Kuspratiknyo',
                '(GZ) Nyiayu Farahnaz',
                '(GZ) Vega Aurellia Putri',
                '(TWS) Rahma Idhanani',
                '(FIS) Andri Oktavian',
                '(FIS) Revi Restiana'
            )
            AND EXISTS (
                SELECT 1 
                FROM bayar_piutang bp 
                WHERE bp.no_rawat = ripr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

        ) x
    ", array_merge(
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap
            ));

            $totalParamedis = $totalParamedis->total_paramedis ?? 0;
        }

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        if (empty($multi_tanggal_ranap)) {
            $totalKsoPR = 0;
        } else {

            $placeholders = implode(',', array_fill(0, count($multi_tanggal_ranap), '?'));

            $ksoPR = DB::selectOne("
        SELECT SUM(kso) as total_kso_pr
        FROM (

            -- RAWAT JALAN PR
            SELECT rjpr.kso
            FROM rawat_jl_pr rjpr
            JOIN jns_perawatan jp 
                ON jp.kd_jenis_prw = rjpr.kd_jenis_prw
            WHERE rjpr.kso > 0
            AND jp.nm_perawatan IN (
                'Sewa CPAP Perhari',
                'Sewa Syringe Pump',
                'Sewa Alat OK Mata (Katarak Non Phaco)',
                'Sewa Alat OK Mata (Pterigium)',
                'Sewa Alat OK Mata (Tumor Palpebra)'
            )
            AND EXISTS (
                SELECT 1 FROM bayar_piutang bp
                WHERE bp.no_rawat = rjpr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            -- RAWAT JALAN DRPR
            SELECT rjdrpr.kso
            FROM rawat_jl_drpr rjdrpr
            JOIN jns_perawatan jp 
                ON jp.kd_jenis_prw = rjdrpr.kd_jenis_prw
            WHERE rjdrpr.kso > 0
            AND jp.nm_perawatan IN (
                'Sewa CPAP Perhari',
                'Sewa Syringe Pump',
                'Sewa Alat OK Mata (Katarak Non Phaco)',
                'Sewa Alat OK Mata (Pterigium)',
                'Sewa Alat OK Mata (Tumor Palpebra)'
            )
            AND EXISTS (
                SELECT 1 FROM bayar_piutang bp
                WHERE bp.no_rawat = rjdrpr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

            UNION ALL

            -- RAWAT INAP PR
            SELECT ripr.kso
            FROM rawat_inap_pr ripr
            JOIN jns_perawatan_inap jp 
                ON jp.kd_jenis_prw = ripr.kd_jenis_prw
            WHERE ripr.kso > 0
            AND jp.nm_perawatan IN (
                'Sewa CPAP Perhari',
                'Sewa Syringe Pump',
                'Sewa Alat OK Mata (Katarak Non Phaco)',
                'Sewa Alat OK Mata (Pterigium)',
                'Sewa Alat OK Mata (Tumor Palpebra)'
            )
            AND EXISTS (
                SELECT 1 FROM bayar_piutang bp
                WHERE bp.no_rawat = ripr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

        ) t
    ", array_merge(
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap
            ));

            $totalKsoPR = $ksoPR->total_kso_pr ?? 0;
        }


        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        if (empty($multi_tanggal_ranap)) {
            $totalKsoDR = 0;
        } else {

            $placeholders = implode(',', array_fill(0, count($multi_tanggal_ranap), '?'));

            $ksoDR = DB::selectOne("
        SELECT SUM(kso) as total_kso_dr
        FROM (

            -- RAWAT INAP DR
            SELECT ridr.kso
            FROM rawat_inap_dr ridr
            JOIN jns_perawatan_inap jp 
                ON jp.kd_jenis_prw = ridr.kd_jenis_prw
            WHERE ridr.kso > 0
            AND (
                jp.nm_perawatan LIKE 'Sewa Alat%'
                OR jp.nm_perawatan LIKE 'Alat Orthopedi%'
                OR jp.nm_perawatan LIKE 'Alat + Sewa%'
            )
            AND jp.nm_perawatan NOT LIKE '%USG%'
            AND EXISTS (
                SELECT 1 FROM bayar_piutang bp
                WHERE bp.no_rawat = ridr.no_rawat
                AND DATE(bp.tgl_bayar) IN ($placeholders)
            )

        ) t
    ", $multi_tanggal_ranap);

            $totalKsoDR = $ksoDR->total_kso_dr ?? 0;
        }

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        if (empty($multi_tanggal_ranap)) {
            $totalAmbulanceValue = 0;
        } else {

            $placeholders = implode(',', array_fill(0, count($multi_tanggal_ranap), '?'));

            $totalAmbulance = DB::selectOne("
        SELECT SUM(kso) as total_ambulance
        FROM (

            SELECT DISTINCT no_rawat, kso
            FROM (

                -- RAWAT INAP
                SELECT 
                    ripr.no_rawat,
                    ripr.kso
                FROM rawat_inap_pr ripr
                JOIN jns_perawatan_inap jp_inap
                    ON jp_inap.kd_jenis_prw = ripr.kd_jenis_prw
                WHERE ripr.kso > 0
                AND LOWER(jp_inap.nm_perawatan) LIKE '%ambulance%'
                AND EXISTS (
                    SELECT 1 
                    FROM bayar_piutang bp
                    WHERE bp.no_rawat = ripr.no_rawat
                    AND DATE(bp.tgl_bayar) IN ($placeholders)
                )

                UNION ALL

                -- RAWAT JALAN PR
                SELECT 
                    rjpr.no_rawat,
                    rjpr.kso
                FROM rawat_jl_pr rjpr
                JOIN jns_perawatan jp_jl
                    ON jp_jl.kd_jenis_prw = rjpr.kd_jenis_prw
                WHERE rjpr.kso > 0
                AND LOWER(jp_jl.nm_perawatan) LIKE '%ambulance%'
                AND EXISTS (
                    SELECT 1 
                    FROM bayar_piutang bp
                    WHERE bp.no_rawat = rjpr.no_rawat
                    AND DATE(bp.tgl_bayar) IN ($placeholders)
                )

                UNION ALL

                -- RAWAT JALAN DRPR
                SELECT 
                    rjdrpr.no_rawat,
                    rjdrpr.kso
                FROM rawat_jl_drpr rjdrpr
                JOIN jns_perawatan jp_drpr
                    ON jp_drpr.kd_jenis_prw = rjdrpr.kd_jenis_prw
                WHERE rjdrpr.kso > 0
                AND LOWER(jp_drpr.nm_perawatan) LIKE '%ambulance%'
                AND EXISTS (
                    SELECT 1 
                    FROM bayar_piutang bp
                    WHERE bp.no_rawat = rjdrpr.no_rawat
                    AND DATE(bp.tgl_bayar) IN ($placeholders)
                )

            ) x

        ) t
    ", array_merge(
                $multi_tanggal_ranap,
                $multi_tanggal_ranap,
                $multi_tanggal_ranap
            ));

            $totalAmbulanceValue = $totalAmbulance->total_ambulance ?? 0;
        }

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        if (empty($multi_tanggal_ranap)) {
            $totalResepPulangValue = 0;
        } else {

            $placeholders = implode(',', array_fill(0, count($multi_tanggal_ranap), '?'));

            $totalResepPulang = DB::selectOne("
        SELECT 
            SUM(billing.totalbiaya) AS total_resep_pulang
        FROM billing
        JOIN reg_periksa 
            ON billing.no_rawat = reg_periksa.no_rawat
        WHERE billing.status = 'resep pulang'
        AND EXISTS (
            SELECT 1 
            FROM bayar_piutang bp
            WHERE bp.no_rawat = billing.no_rawat
            AND DATE(bp.tgl_bayar) IN ($placeholders)
        )
    ", $multi_tanggal_ranap);

            $totalResepPulangValue = $totalResepPulang->total_resep_pulang ?? 0;
        }

        $RalanParamedisBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Ralan Paramedis')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $RanapParamedisBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Ralan Paramedis', 'Ranap Paramedis']) // filter Ralan & Ranap Dokter
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $RalanDokterParamedisBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Ralan Dokter Paramedis')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $RanapDokterParamedisBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Ralan Dokter Paramedis', 'Ranap Dokter Paramedis']) // filter Ralan & Ranap Dokter
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $RalanObatBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Obat')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $RanapObatBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Obat')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap')
            ->when(!empty($multi_tanggal_ranap), function ($q) use ($multi_tanggal_ranap) {
                $q->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
            })
            ->get();

        $RalanReturObatBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Retur Obat')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        $RanapReturObatBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Retur Obat', 'Retur Obat'])
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap')
            ->when(!empty($multi_tanggal_ranap), function ($q) use ($multi_tanggal_ranap) {
                $q->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
            })
            ->get();

        $totalHDRalanBpjs = DB::table('billing as b')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->where(function ($q) {
                $q->where('b.nm_perawatan', 'like', '%HEMODIALISA SET LENGKAP (B) (ALKES HABIS PAKAI)%')
                    ->orWhere('b.nm_perawatan', 'like', '%Paket Hemodialisa%');
            })
            ->where(function ($q) {
                $q->where('b.status', 'Obat')
                    ->orWhere('b.status', 'Ralan Paramedis');
            })
            ->where('r.status_lanjut', 'Ralan')
            // ->where('r.kd_pj', 'UMU')
            ->whereBetween('b.tgl_byr', [$tgl_bpjs1, $tgl_bpjs2])
            ->sum(DB::raw('ROUND(b.totalbiaya * 1.11, 0)'));

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        $totalHDRanapBpjs = DB::table('billing as b')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->where(function ($q) {
                $q->where('b.nm_perawatan', 'like', '%HEMODIALISA SET LENGKAP (B) (ALKES HABIS PAKAI)%')
                    ->orWhere('b.nm_perawatan', 'like', '%Paket Hemodialisa%');
            })
            ->where(function ($q) {
                $q->where('b.status', 'Obat')
                    ->orWhere('b.status', 'Ralan Paramedis');
            })
            ->where('r.status_lanjut', 'Ranap')
            ->when(!empty($multi_tanggal_ranap), function ($q) use ($multi_tanggal_ranap) {
                $q->whereIn(DB::raw('DATE(b.tgl_byr)'), $multi_tanggal_ranap);
            })
            ->sum(DB::raw('ROUND(b.totalbiaya * 1.11, 0)'));

        $RanapLaboratBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Laborat')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap')
            ->when(!empty($multi_tanggal_ranap), function ($q) use ($multi_tanggal_ranap) {
                $q->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
            })
            ->get();

        $RalanLaboratBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Laborat')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        $RanapRadiologiBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Radiologi')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap')
            ->when(!empty($multi_tanggal_ranap), function ($q) use ($multi_tanggal_ranap) {
                $q->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
            })
            ->get();

        $multi_tanggal_ranap = request('multi_tanggal_ranap');
        $multi_tanggal_ranap = $multi_tanggal_ranap
            ? array_map('trim', explode(',', $multi_tanggal_ranap))
            : [];

        /* ===================== RADIologi ===================== */

        $RanapRadiologiBpjsJS1 = DB::table('periksa_radiologi as pr')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'pr.no_rawat')
            ->when(!empty($multi_tanggal_ranap), function ($main) use ($multi_tanggal_ranap) {
                $main->whereExists(function ($q) use ($multi_tanggal_ranap) {
                    $q->select(DB::raw(1))
                        ->from('bayar_piutang as bp')
                        ->whereColumn('bp.no_rawat', 'pr.no_rawat')
                        ->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
                });
            })
            ->sum('pr.bagian_rs');

        $RanapRadiologiBpjsBHP1 = DB::table('periksa_radiologi as pr')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'pr.no_rawat')
            ->when(!empty($multi_tanggal_ranap), function ($main) use ($multi_tanggal_ranap) {
                $main->whereExists(function ($q) use ($multi_tanggal_ranap) {
                    $q->select(DB::raw(1))
                        ->from('bayar_piutang as bp')
                        ->whereColumn('bp.no_rawat', 'pr.no_rawat')
                        ->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
                });
            })
            ->sum('pr.bhp');

        $RanapRadiologiJmDokterPj = DB::table('periksa_radiologi as pr')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'pr.no_rawat')
            ->when(!empty($multi_tanggal_ranap), function ($main) use ($multi_tanggal_ranap) {
                $main->whereExists(function ($q) use ($multi_tanggal_ranap) {
                    $q->select(DB::raw(1))
                        ->from('bayar_piutang as bp')
                        ->whereColumn('bp.no_rawat', 'pr.no_rawat')
                        ->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
                });
            })
            ->sum('pr.tarif_tindakan_dokter');

        $RanapRadiologiJmPetugas = DB::table('periksa_radiologi as pr')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'pr.no_rawat')
            ->when(!empty($multi_tanggal_ranap), function ($main) use ($multi_tanggal_ranap) {
                $main->whereExists(function ($q) use ($multi_tanggal_ranap) {
                    $q->select(DB::raw(1))
                        ->from('bayar_piutang as bp')
                        ->whereColumn('bp.no_rawat', 'pr.no_rawat')
                        ->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
                });
            })
            ->sum('pr.tarif_tindakan_petugas');

        $RanapRadiologiPerujuk = DB::table('periksa_radiologi as pr')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'pr.no_rawat')
            ->when(!empty($multi_tanggal_ranap), function ($main) use ($multi_tanggal_ranap) {
                $main->whereExists(function ($q) use ($multi_tanggal_ranap) {
                    $q->select(DB::raw(1))
                        ->from('bayar_piutang as bp')
                        ->whereColumn('bp.no_rawat', 'pr.no_rawat')
                        ->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
                });
            })
            ->sum('pr.tarif_perujuk');

        /* ===================== BILLING ===================== */

        $totalBilling = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->where('b.status', 'Radiologi')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap')
            ->when(!empty($multi_tanggal_ranap), function ($q) use ($multi_tanggal_ranap) {
                $q->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
            })
            ->sum('b.totalbiaya');

        /* ===================== HITUNGAN ===================== */

        $totalPengurang =
            $RanapRadiologiBpjsJS1 +
            $RanapRadiologiBpjsBHP1 +
            $RanapRadiologiJmDokterPj +
            $RanapRadiologiJmPetugas +
            $RanapRadiologiPerujuk;

        $hasilAkhir = ($totalBilling ?? 0)
            - ($totalPengurang ?? 0)
            + ($RanapRadiologiBpjsJS1 ?? 0);

        /* ===================== EXCESS ===================== */

        $ExsesBPJS = DB::table('detail_nota_inap as dni')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'dni.no_rawat')
            ->when(!empty($multi_tanggal_ranap), function ($main) use ($multi_tanggal_ranap) {
                $main->whereExists(function ($q) use ($multi_tanggal_ranap) {
                    $q->select(DB::raw(1))
                        ->from('bayar_piutang as bp')
                        ->whereColumn('bp.no_rawat', 'dni.no_rawat')
                        ->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
                });
            })
            ->sum('dni.besar_bayar');

        $RalanRadiologiBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Radiologi')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $RanapKamarBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Kamar')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap')
            ->when(!empty($multi_tanggal_ranap), function ($q) use ($multi_tanggal_ranap) {
                $q->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
            })
            ->get();

        $RalanKamarBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Kamar')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $RanapOperasiBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Operasi', 'Operasi']) // filter Ralan & Ranap Dokter
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $RalanOperasiBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Operasi')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $RanapPotonganBpjs = DB::table('billing as b')
    ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
    ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
    ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
    ->select(
        'b.no_rawat',
        'bp.tgl_bayar',
        'b.status',
        'b.totalbiaya'
    )
    ->where('b.status', 'Potongan')
    ->where('p.status', 'lunas')
    ->where('r.status_lanjut', 'Ranap')
    ->when(!empty($multi_tanggal_ranap), function ($q) use ($multi_tanggal_ranap) {
        $q->where(function ($q2) use ($multi_tanggal_ranap) {
            foreach ($multi_tanggal_ranap as $tgl) {
                $q2->orWhereBetween('bp.tgl_bayar', [
                    $tgl . ' 00:00:00',
                    $tgl . ' 23:59:59'
                ]);
            }
        });
    })
    ->get();

        $sub = DB::table('detail_piutang_pasien as dpp')
            ->join('reg_periksa as rp', 'dpp.no_rawat', '=', 'rp.no_rawat')
            ->join('bayar_piutang as bp', function ($join) use ($multi_tanggal_ranap) {
                $join->on('bp.no_rawat', '=', 'rp.no_rawat');

                if (!empty($multi_tanggal_ranap)) {
                    $join->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
                }
            })
            ->select(
                'rp.no_rawat',
                DB::raw('SUM(dpp.totalpiutang) as totalpiutang')
            )
            ->where('dpp.kd_pj', '<>', 'BPJS')
            ->where('dpp.nama_bayar', 'NOT LIKE', '%BPJS%')
            ->groupBy('rp.no_rawat');

        $cobranapbpjs = DB::table(DB::raw("({$sub->toSql()}) as x"))
            ->mergeBindings($sub)
            ->sum('x.totalpiutang');

        $RalanPotonganBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Potongan')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();


        $RalanCOBBpjs = DB::table('bayar_piutang as bp')
            ->join('piutang_pasien as pp', 'bp.no_rkm_medis', '=', 'pp.no_rkm_medis')
            ->join('detail_piutang_pasien as dpp', 'bp.no_rawat', '=', 'dpp.no_rawat')
            ->select(
                'bp.tgl_bayar',
                'bp.besar_cicilan',
                'pp.no_rkm_medis',
                'dpp.nama_bayar',
                'dpp.kd_pj',
                'dpp.no_rawat',
                'bp.catatan'
            )
            ->when(!empty($multi_tanggal_ranap), function ($q) use ($multi_tanggal_ranap) {
                $q->whereIn(DB::raw('DATE(bp.tgl_bayar)'), $multi_tanggal_ranap);
            })
            ->where('bp.catatan', 'diverifikasi oleh 20101987.A')
            ->groupBy('dpp.no_rawat');

        $totalCicilan = DB::table(DB::raw("({$RalanCOBBpjs->toSql()}) as t"))
            ->mergeBindings($RalanCOBBpjs)
            ->selectRaw('SUM(t.besar_cicilan) as total_cicilan')
            ->value('total_cicilan');

        // dd($totalCicilan);
        // ====================== REKAP PASIEN UMUM ======================

        foreach ($bayarUmum as $item) {

            if ($item->status_lanjut == 'Ranap') {

                $registerRanap += $item->getRegistrasi->sum('totalbiaya');
                $jmDokterRanap += $item->getRanapDokter->sum('totalbiaya');
                $paramedisRanap += $item->getRanapParamedis->sum('totalbiaya');
                $drparamedisRanap += $item->getRanapDrParamedis->sum('totalbiaya');
                $obatRanap += $item->getObat->sum('totalbiaya');
                $returRanap += $item->getReturObat->sum('totalbiaya');
                $labRanap += $item->getLaborat->sum('totalbiaya');
                $radiologiRanap += $item->getRadiologi->sum('totalbiaya');
                $kamarRanap += $item->getKamarInap->sum('totalbiaya');
                $operasiRanap += $item->getOperasi->sum('totalbiaya');
                $potonganRanap += $item->getPotongan->sum('totalbiaya');
            } else {

                $registerRalan += $item->getRegistrasi->sum('totalbiaya');
                $jmDokterRalan += $item->getRalanDokter->sum('totalbiaya');
                $paramedisRalan += $item->getRalanParamedis->sum('totalbiaya');
                $drparamedisRalan += $item->getRalanDrParamedis->sum('totalbiaya');
                $obatRalan += $item->getObat->sum('totalbiaya');
                $returRalan += $item->getReturObat->sum('totalbiaya');
                $labRalan += $item->getLaborat->sum('totalbiaya');
                $radiologiRalan += $item->getRadiologi->sum('totalbiaya');
                $kamarRalan += $item->getKamarInap->sum('totalbiaya');
                $operasiRalan += $item->getOperasi->sum('totalbiaya');
                $potonganRalan += $item->getPotongan->sum('totalbiaya');
            }
        }


        // ====================== TAMBAHAN NON UMUM ======================

        $tambahaanNonUmumRalan = DB::table('nota_jalan as nj')
            ->join('detail_nota_jalan as d', 'nj.no_rawat', '=', 'd.no_rawat')
            ->join('reg_periksa as r', 'nj.no_rawat', '=', 'r.no_rawat')
            ->whereBetween('r.tgl_registrasi', [$tgl1, $tgl2])
            ->where('r.kd_pj', '<>', 'UMU')
            ->where('d.nama_bayar', 'Tambahan')
            ->sum('d.besar_bayar');

        $tambahaanNonUmumRanap = DB::table('nota_inap as ni')
            ->join('detail_nota_inap as di', 'ni.no_rawat', '=', 'di.no_rawat')
            ->join('reg_periksa as r', 'ni.no_rawat', '=', 'r.no_rawat')
            ->whereBetween('r.tgl_registrasi', [$tgl1, $tgl2])
            ->where('r.kd_pj', '<>', 'UMU')
            ->where('di.nama_bayar', 'Tambahan')
            ->sum('di.besar_bayar');


        // ====================== TOTAL PEMAKAIAN HD ======================

        // $totalHDRanap = DB::table('billing as b')
        //     ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
        //     ->where('b.nm_perawatan', 'like', '%HEMODIALISA SET TANPA AV FISTULA%')
        //     ->where('b.status', 'Obat')
        //     ->where('r.status_lanjut', 'Ranap')
        //     ->where('r.kd_pj', 'UMU')
        //     ->whereBetween('b.tgl_byr', [$tgl1, $tgl2])
        //     ->sum(DB::raw('ROUND(b.totalbiaya * 1.11, 0)'));

        // $totalHDRalan = DB::table('billing as b')
        //     ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
        //     ->where('b.nm_perawatan', 'like', '%HEMODIALISA SET TANPA AV FISTULA%')
        //     ->where('b.status', 'Obat')
        //     ->where('r.status_lanjut', 'Ralan')
        //     ->where('r.kd_pj', 'UMU')
        //     ->whereBetween('b.tgl_byr', [$tgl1, $tgl2])
        //     ->sum(DB::raw('ROUND(b.totalbiaya * 1.11, 0)'));

        // // KURANGI DARI OBAT
        // $obatRalan = $obatRalan - $totalHDRalan;
        // $obatRanap = $obatRanap - $totalHDRanap;

        $totalHDRanap = DB::table('billing as b')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->where(function ($q) {
                $q->where('b.nm_perawatan', 'like', '%HEMODIALISA SET TANPA AV FISTULA%')
                    ->orWhere('b.nm_perawatan', 'like', '%Paket Hemodialisa%');
            })
            ->where(function ($q) {
                $q->where('b.status', 'Obat')
                    ->orWhere('b.status', 'Ralan Paramedis');
            })
            ->where('r.status_lanjut', 'Ranap')
            ->where('r.kd_pj', 'UMU')
            ->whereBetween('b.tgl_byr', [$tgl1, $tgl2])
            ->sum(DB::raw('ROUND(b.totalbiaya * 1.11, 0)'));


        $totalHDRalan = DB::table('billing as b')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->where(function ($q) {
                $q->where('b.nm_perawatan', 'like', '%HEMODIALISA SET TANPA AV FISTULA%')
                    ->orWhere('b.nm_perawatan', 'like', '%Paket Hemodialisa%');
            })
            ->where(function ($q) {
                $q->where('b.status', 'Obat')
                    ->orWhere('b.status', 'Ralan Paramedis');
            })
            ->where('r.status_lanjut', 'Ralan')
            ->where('r.kd_pj', 'UMU')
            ->whereBetween('b.tgl_byr', [$tgl1, $tgl2])
            ->sum(DB::raw('ROUND(b.totalbiaya * 1.11, 0)'));


        // KURANGI DARI OBAT
        // $obatRalan = $obatRalan - $totalHDRalan;
        // $obatRanap = $obatRanap - $totalHDRanap;



        // ====================== PEMBAYARAN PJ ======================

        $totalPJ = DB::table('tagihan_sadewa')
            ->where('no_nota', 'like', 'PJ%')
            ->whereDate('tgl_bayar', '>=', $tgl1)
            ->whereDate('tgl_bayar', '<=', $tgl2)
            ->sum('jumlah_bayar');


        // ====================== EKSES ======================

        $totalEksesRanap = DB::table('piutang_pasien as pp')
            ->leftJoin('detail_nota_inap as dni', 'pp.no_rawat', '=', 'dni.no_rawat')
            ->join('reg_periksa as rp', 'pp.no_rawat', '=', 'rp.no_rawat')
            ->where('rp.status_lanjut', 'Ranap')
            ->whereBetween('pp.tgl_piutang', [$tgl1, $tgl2])
            ->sum('dni.besar_bayar');

        $totalEksesRalan = DB::table('piutang_pasien as pp')
            ->leftJoin('detail_nota_jalan as dnj', 'pp.no_rawat', '=', 'dnj.no_rawat')
            ->join('reg_periksa as rp', 'pp.no_rawat', '=', 'rp.no_rawat')
            ->where('rp.status_lanjut', 'Ralan')
            ->whereBetween('pp.tgl_piutang', [$tgl1, $tgl2])
            ->sum('dnj.besar_bayar');


        // ====================== GRAND TOTAL ======================

        $grandRanap =
            $registerRanap +
            $jmDokterRanap +
            $paramedisRanap +
            $drparamedisRanap +
            $obatRanap +
            $returRanap +
            $labRanap +
            $radiologiRanap +
            $kamarRanap +
            $operasiRanap +
            $tambahaanNonUmumRanap +
            $totalEksesRanap +
            $potonganRanap +
            $totalHDRanap;

        // $grandRalan =
        //     $registerRalan +
        //     $jmDokterRalan +
        //     $paramedisRalan +
        //     $drparamedisRalan +
        //     $obatRalan +
        //     $returRalan +
        //     $labRalan +
        //     $radiologiRalan +
        //     $kamarRalan +
        //     $operasiRalan +
        //     $tambahaanNonUmumRalan +
        //     $totalPJ +
        //     $totalEksesRalan +
        //     $potonganRalan +
        //     $totalHDRalan;

        $grandRalan =
            $registerRalan +
            $jmDokterRalan +
            $paramedisRalan +
            $drparamedisRalan +
            $obatRalan +
            $returRalan +
            $labRalan +
            $radiologiRalan +
            $kamarRalan +
            $operasiRalan +
            $tambahaanNonUmumRalan +
            $totalPJ +
            $totalEksesRalan +
            $potonganRalan;


        return view('laporan.rekappendapatanbulanan', compact(
            'tgl1',
            'tgl2',
            'registerRanap',
            'registerRanapBpjs',
            'registerRalanBpjs',
            'RalanDokterBpjs',
            'RanapDokterBpjs',
            'RalanParamedisBpjs',
            'RanapParamedisBpjs',
            'RanapDokterParamedisBpjs',
            'RalanDokterParamedisBpjs',
            'totalCicilan',
            'registerRalan',
            'jmDokterRanap',
            'jmDokterRalan',
            'paramedisRanap',
            'paramedisRalan',
            'drparamedisRanap',
            'drparamedisRalan',
            'obatRanap',
            'obatRalan',
            'RanapPotonganBpjs',
            'RalanPotonganBpjs',
            'RanapReturObatBpjs',
            'RalanReturObatBpjs',
            'RanapObatBpjs',
            'RalanObatBpjs',
            'returRanap',
            'returRalan',
            'labRanap',
            'labRalan',
            'RalanOperasiBpjs',
            'RanapOperasiBpjs',
            'RanapKamarBpjs',
            'RalanKamarBpjs',
            'RalanRadiologiBpjs',
            'RanapRadiologiBpjs',
            'RalanLaboratBpjs',
            'RanapLaboratBpjs',
            'RalanCOBBpjs',
            'radiologiRanap',
            'radiologiRalan',
            'kamarRanap',
            'kamarRalan',
            'operasiRanap',
            'operasiRalan',
            'potonganRanap',
            'potonganRalan',
            'tambahaanNonUmumRanap',
            'tambahaanNonUmumRalan',
            'totalPJ',
            'totalEksesRanap',
            'totalEksesRalan',
            'totalHDRanap',
            'totalHDRalan',
            'totalHDRanapBpjs',
            'totalHDRalanBpjs',
            'grandRanap',
            'grandRalan',
            'TotalJasaSarana',
            'TotalBHP',
            'TotalJMDokter',
            'totalParamedis',
            'ksoPR',
            'totalKsoDR',
            'totalAmbulanceValue',
            'totalResepPulangValue',
            'RanapRadiologiBpjsJS1',
            'RanapRadiologiBpjsBHP1',
            'RanapRadiologiJmDokterPj',
            'RanapRadiologiJmPetugas',
            'RanapRadiologiPerujuk',
            'totalPengurang',
            'hasilAkhir',
            'ExsesBPJS',
            'cobranapbpjs'
            // 'hasilAkhirJS'
        ));
    }
}
