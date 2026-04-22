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
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Registrasi')
            ->where('p.status', 'lunas')
            // ->where('r.kd_pj', 'BPJ') // filter BPJS
            ->where('r.status_lanjut', 'Ranap') // filter Ranap
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

        $registerRalanBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->where('b.status', 'Registrasi')
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ralan') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
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
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Obat', 'Obat']) // filter Ralan & Ranap Dokter
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
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

        $RanapReturObatBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Retur Obat', 'Retur Obat']) // filter Ralan & Ranap Dokter
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
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
            // ->where('r.kd_pj', 'UMU')
            ->whereBetween('b.tgl_byr', [$tgl_bpjs1, $tgl_bpjs2])
            ->sum(DB::raw('ROUND(b.totalbiaya * 1.11, 0)'));

        $RanapLaboratBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Laborat', 'Laborat']) // filter Ralan & Ranap Dokter
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
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

        $RanapRadiologiBpjs = DB::table('billing as b')
            ->join('piutang_pasien as p', 'b.no_rawat', '=', 'p.no_rawat')
            ->join('bayar_piutang as bp', 'b.no_rawat', '=', 'bp.no_rawat')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Radiologi', 'Radiologi']) // filter Ralan & Ranap Dokter
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

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
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Kamar', 'Kamar']) // filter Ralan & Ranap Dokter
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
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
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat') // join reg_periksa
            ->select(
                'b.no_rawat',
                'bp.tgl_bayar',
                'b.status',
                'b.totalbiaya'
            )
            ->whereIn('b.status', ['Potongan', 'Potongan']) // filter Ralan & Ranap Dokter
            ->where('p.status', 'lunas')
            ->where('r.status_lanjut', 'Ranap') // filter Ralan
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
            ->get();

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
            ->whereBetween(DB::raw('DATE(bp.tgl_bayar)'), [$tgl_bpjs1, $tgl_bpjs2])
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


        return view('laporan.rekapPendapatanBulanan', compact(
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
            'grandRalan'
        ));
    }
}
