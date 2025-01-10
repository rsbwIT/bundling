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

        $getDetailAsuransi = DB::table('penjab')
            ->select(
                'penjab.kd_pj',
                'penjab.png_jawab',
                'penjab.no_telp',
                'penjab.status',
                'bw_maping_asuransi.nama_perusahaan',
                'bw_maping_asuransi.alamat_asuransi',
                'bw_maping_asuransi.kd_surat',
                'tf_rekening_rs',
                'nm_tf_rekening_rs'
            )
            ->leftJoin('bw_maping_asuransi', 'penjab.kd_pj', '=', 'bw_maping_asuransi.kd_pj')
            ->where('penjab.kd_pj', $kdPenjamin)
            ->where('penjab.status', '=', '1')
            ->first();

        try {

            $getNomorSurat = NomorInvoice::getAutonumberInvoice($getDetailAsuransi->kd_surat, $status_lanjut);
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
                'pasien.no_rkm_medis',
                'bw_peserta_asuransi.nomor_kartu',
                'bw_peserta_asuransi.nomor_klaim'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('detail_piutang_pasien', 'detail_piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat') // Kode Baru
            ->leftJoin('bw_peserta_asuransi', 'pasien.no_rkm_medis', '=', 'bw_peserta_asuransi.no_rkm_medis')
            ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            // KODE LAMA
            // ->where(function ($query) use ( $kdPenjamin) {
            //     if ($kdPenjamin) {
            //         $query->whereIn('reg_periksa.kd_pj', $kdPenjamin);
            //     }
            // })
            // KDOE BARU
            ->where(function ($query) use ( $kdPenjamin) {
                if ($kdPenjamin) {
                    $query->whereIn('detail_piutang_pasien.kd_pj', $kdPenjamin);
                }
            })
            ->whereBetween('piutang_pasien.tgltempo', [$tanggl1, $tanggl2])
            ->where('reg_periksa.status_lanjut', $status_lanjut)
            ->groupBy('reg_periksa.no_rawat')
            ->get();
        $getPasien->map(function ($item) {
            // getTotalBiaya
            $item->getTotalBiaya = DB::table('detail_piutang_pasien')
                ->select('totalpiutang')
                ->where('no_rawat', $item->no_rawat)
                ->where('kd_pj', 'not like', 'BPJ')
                ->get();
            // TANGGAL KELUAR
            $item->getTglKeluar = DB::table('kamar_inap')
                ->select('tgl_keluar')
                ->where('no_rawat', $item->no_rawat)
                ->orderByDesc('tgl_keluar')
                ->orderByDesc('jam_keluar')
                ->limit(1)
                ->get();
            // NOMOR NOTA
            $item->getNomorNota = DB::table('billing')
                ->select('nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('no', '=', 'No.Nota')
                ->get();
             // DETAIL TINDAKAN=====================================
            // REGISTRASI
            $item->getRegistrasi = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Registrasi')
                ->get();
            // RALAN DOKTER / 1 Paket Tindakan
            $item->getRalanDokter = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ralan Dokter')
                ->get();
            // RALAN DOKTER PARAMEDIS / 2 Paket Tindakan
            $item->getRalanDrParamedis = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ralan Dokter Paramedis')
                ->get();
            // RALAN PARAMEDIS / 3 Paket Tindakan
            $item->getRalanParamedis = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ralan Paramedis')
                ->get();
            // RANAP DOKTER / 4 Paket Tindakan
            $item->getRanapDokter = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ranap Dokter')
                ->get();
            // RANAP DOKTER PARAMEDIS / 5 Paket Tindakan
            $item->getRanapDrParamedis = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ranap Dokter Paramedis')
                ->get();
            // RANAP PARAMEDIS / 6 Ranap Paramedis
            $item->getRanapParamedis = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ranap Paramedis')
                ->get();
            // OPRASI
            $item->getOprasi = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Operasi')
                ->get();
            // LABORAT
            $item->getLaborat = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Laborat')
                ->get();
            // RADIOLOGI
            $item->getRadiologi = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Radiologi')
                ->get();
            // KAMAR INAP
            $item->getKamarInap = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Kamar')
                ->get();
            // Obat+Emb+Tsl / OBAT
            $item->getObat = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Obat')
                ->get();
            // Retur Obat
            $item->getReturObat = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Retur Obat')
                ->get();
            // TAMBAHAN
            $item->getTambahan = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Tambahan')
                ->get();
        });

        // $getListInvoice = DB::table('bw_invoice_asuransi')
        //     ->select('bw_invoice_asuransi.nomor_tagihan', 'bw_invoice_asuransi.kode_asuransi', 'bw_invoice_asuransi.nama_asuransi', 'bw_invoice_asuransi.alamat_asuransi', 'bw_invoice_asuransi.tanggl1', 'bw_invoice_asuransi.tanggl2', 'bw_invoice_asuransi.tgl_cetak', 'bw_invoice_asuransi.status_lanjut', 'bw_invoice_asuransi.lamiran')
        //     ->where('bw_invoice_asuransi.kode_asuransi', $kdPenjamin)
        //     ->where('bw_invoice_asuransi.status_lanjut', $status_lanjut)
        //     ->orderBy('bw_invoice_asuransi.nomor_tagihan', 'desc')
        //     ->get();

        return view('laporan.invoiceAsuransi', [
            // 'getListInvoice' => $getListInvoice,
            'tanggl1' => $tanggl1,
            'tanggl2' => $tanggl2,
            'tgl_cetak' => $tgl_cetak,
            'status_lanjut' => $status_lanjut,
            'kdPenjamin' => $kdPenjamin,
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
        try {
            DB::table('bw_invoice_asuransi')->insert([
                'nomor_tagihan' => $request->nomor_tagihan,
                'kode_asuransi' => $request->kode_asuransi,
                'cari_kode_asuransi' => $request->cari_kode_asuransi,
                'nama_asuransi' => $request->nama_asuransi,
                'alamat_asuransi' => $request->alamat_asuransi,
                'tanggl1' => $request->tanggl1,
                'tanggl2' => $request->tanggl2,
                'tgl_cetak' => $request->tgl_cetak,
                'status_lanjut' => $request->status_lanjut,
                'lamiran' => $request->lamiran,
            ]);
            return redirect()->back()->with('sucsessSimpanNomor', 'Berhasil menyimpan template tagihan, silahkan cetak tagian');
        } catch (\Throwable $th) {
            return redirect()->back()->with('gagalSimpanNomor', 'Gagal menyimpan template tagihan, cek kembali data anda');
        }
    }

    // 3 CETAK ======================================================================
    public function cetakInvoice(Request $request, $nomor_tagihan, $template)
    {
        $getListInvoice = DB::table('bw_invoice_asuransi')
            ->select(
                'bw_invoice_asuransi.nomor_tagihan',
                'bw_invoice_asuransi.kode_asuransi',
                'bw_invoice_asuransi.cari_kode_asuransi',
                'bw_invoice_asuransi.nama_asuransi',
                'bw_invoice_asuransi.alamat_asuransi',
                'bw_invoice_asuransi.tanggl1',
                'bw_invoice_asuransi.tanggl2',
                'bw_invoice_asuransi.tgl_cetak',
                'bw_invoice_asuransi.status_lanjut',
                'bw_invoice_asuransi.lamiran'
            )
            ->where('bw_invoice_asuransi.nomor_tagihan', urldecode($nomor_tagihan))
            ->first();
        $kdPenjamin = explode(',',$getListInvoice->cari_kode_asuransi);
        $getDetailAsuransi = DB::table('penjab')
            ->select(
                'penjab.kd_pj',
                'penjab.png_jawab',
                'penjab.no_telp',
                'penjab.status',
                'bw_maping_asuransi.nama_perusahaan',
                'bw_maping_asuransi.alamat_asuransi',
                'tf_rekening_rs',
                'nm_tf_rekening_rs'
            )
            ->leftJoin('bw_maping_asuransi', 'penjab.kd_pj', '=', 'bw_maping_asuransi.kd_pj')
            ->where('penjab.kd_pj', $getListInvoice->kode_asuransi)
            ->where('penjab.status', '=', '1')
            ->first();

        $getPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'pasien.nm_pasien',
                'reg_periksa.kd_pj',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.status_lanjut',
                'piutang_pasien.sisapiutang AS total_biaya',
                'piutang_pasien.tgltempo AS tgl_byr',
                'kamar_inap.tgl_keluar',
                'kamar_inap.tgl_masuk',
                'pasien.no_rkm_medis',
                'bw_peserta_asuransi.nomor_kartu',
                'bw_peserta_asuransi.nomor_klaim'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('detail_piutang_pasien', 'detail_piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat') // Kode Baru
            ->leftJoin('bw_peserta_asuransi', 'pasien.no_rkm_medis', '=', 'bw_peserta_asuransi.no_rkm_medis')
            ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            // ->where(function ($query) use ( $kdPenjamin) {
            //     if ($kdPenjamin) {
            //         $query->whereIn('reg_periksa.kd_pj', $kdPenjamin);
            //     }
            // })
            ->where(function ($query) use ( $kdPenjamin) {
                if ($kdPenjamin) {
                    $query->whereIn('detail_piutang_pasien.kd_pj', $kdPenjamin);
                }
            })
            ->whereBetween('piutang_pasien.tgltempo', [$getListInvoice->tanggl1, $getListInvoice->tanggl2])
            ->where('reg_periksa.status_lanjut', $getListInvoice->status_lanjut)
            ->groupBy('reg_periksa.no_rawat')
            ->get();
        $getPasien->map(function ($item) {
            // Get Diagnosa
            $item->getDiagnosa = DB::table('diagnosa_pasien')
                ->select('penyakit.nm_penyakit', 'diagnosa_pasien.kd_penyakit')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $item->no_rawat)
                ->where('diagnosa_pasien.prioritas', '1')
                ->get();
            // getTotalBiaya
            $item->getTotalBiaya = DB::table('detail_piutang_pasien')
                ->select('totalpiutang')
                ->where('no_rawat', $item->no_rawat)
                ->where('kd_pj', 'not like', 'BPJ')
                ->get();
            // getTotalBiaya2
            $item->getTotalBiaya2 = DB::table('detail_piutang_pasien')
                ->select('totalpiutang', 'bw_maping_asuransi.nama_perusahaan')
                ->where('no_rawat', $item->no_rawat)
                ->leftJoin('bw_maping_asuransi','detail_piutang_pasien.kd_pj','=','bw_maping_asuransi.kd_pj')
                ->get();
            // TANGGAL KELUAR
            $item->getTglKeluar = DB::table('kamar_inap')
                ->select('tgl_keluar')
                ->where('no_rawat', $item->no_rawat)
                ->orderByDesc('tgl_keluar')
                ->orderByDesc('jam_keluar')
                ->limit(1)
                ->get();
            // NOMOR NOTA
            $item->getNomorNota = DB::table('billing')
                ->select('nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('no', '=', 'No.Nota')
                ->get();
            // DETAIL TINDAKAN=====================================
            // REGISTRASI
            $item->getRegistrasi = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Registrasi')
                ->get();
            // RALAN DOKTER / 1 Paket Tindakan
            $item->getRalanDokter = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ralan Dokter')
                ->get();
            // RALAN DOKTER PARAMEDIS / 2 Paket Tindakan
            $item->getRalanDrParamedis = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ralan Dokter Paramedis')
                ->get();
            // RALAN PARAMEDIS / 3 Paket Tindakan
            $item->getRalanParamedis = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ralan Paramedis')
                ->get();
            // RANAP DOKTER / 4 Paket Tindakan
            $item->getRanapDokter = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ranap Dokter')
                ->get();
            // RANAP DOKTER PARAMEDIS / 5 Paket Tindakan
            $item->getRanapDrParamedis = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ranap Dokter Paramedis')
                ->get();
            // RANAP PARAMEDIS / 6 Ranap Paramedis
            $item->getRanapParamedis = DB::table('billing')
                ->select('totalbiaya', 'nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ranap Paramedis')
                ->get();
            // OPRASI
            $item->getOprasi = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Operasi')
                ->get();
            // LABORAT
            $item->getLaborat = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Laborat')
                ->get();
            // RADIOLOGI
            $item->getRadiologi = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Radiologi')
                ->get();
            // KAMAR INAP
            $item->getKamarInap = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Kamar')
                ->get();
            // Obat+Emb+Tsl / OBAT
            $item->getObat = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Obat')
                ->get();
            // Retur Obat
            $item->getReturObat = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Retur Obat')
                ->get();
            // TAMBAHAN
            $item->getTambahan = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Tambahan')
                ->get();
        });

        return view('laporan.cetak.cetakinvoiceAsuransi', [
            'getDetailAsuransi' => $getDetailAsuransi,
            'getListInvoice' => $getListInvoice,
            'getPasien' => $getPasien,
            'template' => $template,
        ]);
    }
}
