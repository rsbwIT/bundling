<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Keuangan\NomorInvoice;

class InvoiceAsuransi extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function InvoiceAsuransi(Request $request)
    {
        $penjab = $this->cacheService->getPenjab();
        $url = 'invoice-asuransi';

        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $status_lanjut = $request->status_lanjut;
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        $lamiran = $request->lampiran;

        $getDetailAsuransi = DB::table('penjab')
            ->select('penjab.kd_pj', 'penjab.png_jawab', 'penjab.nama_perusahaan', 'penjab.alamat_asuransi', 'penjab.no_telp', 'penjab.status')
            ->where('penjab.kd_pj', $kdPenjamin)
            ->where('penjab.status', '=', '1')
            ->first();

        try {
            $kodeSts = $status_lanjut == "Ranap" ? "KEURI" : "KEURJ";
            $getNomorSurat = NomorInvoice::getAutonumberInvoice($getDetailAsuransi->kd_pj, $kodeSts);
        } catch (\Throwable $th) {
            $getNomorSurat = [];
        }

        $getPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'pasien.nm_pasien',
                'reg_periksa.kd_pj',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.status_lanjut',
                DB::raw('SUM(billing.totalbiaya) AS total_biaya'),
                'billing.tgl_byr',
                'kamar_inap.tgl_keluar',
                'kamar_inap.tgl_masuk',
                'pasien.no_rkm_medis'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('reg_periksa.kd_pj', '=', 'A09')
            ->whereBetween('reg_periksa.tgl_registrasi', ['2024-06-01', '2024-06-11'])
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->groupBy('reg_periksa.no_rawat')
            ->get();

        return view('laporan.invoiceAsuransi', [
            'lamiran' => $lamiran,
            'url' => $url,
            'penjab' => $penjab,
            'getDetailAsuransi' => $getDetailAsuransi,
            'getNomorSurat' => $getNomorSurat,
            'getPasien' => $getPasien,
        ]);
    }
}
