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
    // BUAT NOMOR ======================================================================
    function InvoiceAsuransi(Request $request)
    {
        $penjab = $this->cacheService->getPenjab();
        $url = 'invoice-asuransi';

        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $tgl_cetak = $request->tgl_cetak;
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
                'piutang_pasien.sisapiutang AS total_biaya',
                'piutang_pasien.tgltempo AS tgl_byr',
                'kamar_inap.tgl_keluar',
                'kamar_inap.tgl_masuk',
                'pasien.no_rkm_medis'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('reg_periksa.kd_pj', $kdPenjamin)
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggl1, $tanggl2])
            ->where('reg_periksa.status_lanjut', $status_lanjut)
            ->groupBy('reg_periksa.no_rawat')
            ->get();
        $getPasien->map(function ($item) {
            $item->getNomorNota = DB::table('billing')
                ->select('nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('no', '=', 'No.Nota')
                ->get();
        });

        $getListInvoice = DB::connection('db_con2')->table('bw_invoice_asuransi')
            ->select('bw_invoice_asuransi.nomor_tagihan', 'bw_invoice_asuransi.kode_asuransi', 'bw_invoice_asuransi.nama_asuransi', 'bw_invoice_asuransi.alamat_asuransi', 'bw_invoice_asuransi.tanggl1', 'bw_invoice_asuransi.tanggl2', 'bw_invoice_asuransi.tgl_cetak', 'bw_invoice_asuransi.status_lanjut', 'bw_invoice_asuransi.lamiran')
            ->where('bw_invoice_asuransi.kode_asuransi', $kdPenjamin)
            ->where('bw_invoice_asuransi.status_lanjut', $status_lanjut)
            ->orderBy('bw_invoice_asuransi.nomor_tagihan','desc')
            ->get();

        return view('laporan.invoiceAsuransi', [
            'getListInvoice' => $getListInvoice,
            'lamiran' => $lamiran,
            'tanggl1' => $tanggl1,
            'tanggl2' => $tanggl2,
            'tgl_cetak' => $tgl_cetak,
            'status_lanjut' => $status_lanjut,
            'url' => $url,
            'penjab' => $penjab,
            'getDetailAsuransi' => $getDetailAsuransi,
            'getNomorSurat' => $getNomorSurat,
            'getPasien' => $getPasien,
        ]);
    }

    // 2 SIMPAN DAN REDIRECT KE CETAK ======================================================================
    public function simpanNomor(Request $request)
    {
        DB::connection('db_con2')->table('bw_invoice_asuransi')->insert([
            'nomor_tagihan' => $request->nomor_tagihan,
            'kode_asuransi' => $request->kode_asuransi,
            'nama_asuransi' => $request->nama_asuransi,
            'alamat_asuransi' => $request->alamat_asuransi,
            'tanggl1' => $request->tanggl1,
            'tanggl2' => $request->tanggl2,
            'tgl_cetak' => $request->tgl_cetak,
            'status_lanjut' => $request->status_lanjut,
            'lamiran' => $request->lamiran,
        ]);
        return redirect()->back();
    }

    // 3 CETAK ======================================================================
    public function cetakInvoice(Request $request)
    {


        $getListInvoice = DB::connection('db_con2')->table('bw_invoice_asuransi')
            ->select(
                'bw_invoice_asuransi.nomor_tagihan',
                'bw_invoice_asuransi.kode_asuransi',
                'bw_invoice_asuransi.nama_asuransi',
                'bw_invoice_asuransi.alamat_asuransi',
                'bw_invoice_asuransi.tanggl1',
                'bw_invoice_asuransi.tanggl2',
                'bw_invoice_asuransi.tgl_cetak',
                'bw_invoice_asuransi.status_lanjut',
                'bw_invoice_asuransi.lamiran'
            )
            ->where('bw_invoice_asuransi.nomor_tagihan', $request->nomor_tagihan)
            ->first();

        $getDetailAsuransi = DB::table('penjab')
            ->select('penjab.kd_pj', 'penjab.png_jawab', 'penjab.nama_perusahaan', 'penjab.alamat_asuransi', 'penjab.no_telp', 'penjab.status')
            ->where('penjab.kd_pj', $getListInvoice->kode_asuransi)
            ->where('penjab.status', '=', '1')
            ->first();

        $getPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'pasien.nm_pasien',
                'reg_periksa.kd_pj',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.status_lanjut',
                'piutang_pasien.sisapiutang AS total_biaya',
                'piutang_pasien.tgltempo AS tgl_byr',
                'kamar_inap.tgl_keluar',
                'kamar_inap.tgl_masuk',
                'pasien.no_rkm_medis'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('reg_periksa.kd_pj', $getListInvoice->kode_asuransi)
            ->whereBetween('reg_periksa.tgl_registrasi', [$getListInvoice->tanggl1, $getListInvoice->tanggl2])
            ->where('reg_periksa.status_lanjut', $getListInvoice->status_lanjut)
            ->groupBy('reg_periksa.no_rawat')
            ->get();
        $getPasien->map(function ($item) {
            $item->getNomorNota = DB::table('billing')
                ->select('nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('no', '=', 'No.Nota')
                ->get();
        });

        return view('laporan.cetak.cetakinvoiceAsuransi', [
            'getDetailAsuransi' => $getDetailAsuransi,
            'getListInvoice' => $getListInvoice,
            'getPasien' => $getPasien,
        ]);
    }
}
