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

        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');
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
            ->where(function ($query) use ( $kdPenjamin) {
                if ($kdPenjamin) {
                    $query->whereIn('detail_piutang_pasien.kd_pj', $kdPenjamin);
                }
            })
            ->whereBetween('piutang_pasien.tgltempo', [$tanggl1, $tanggl2])
            ->where('reg_periksa.status_lanjut', $status_lanjut)
            ->groupBy('reg_periksa.no_rawat')
            ->get();
            
        $noRawats = $getPasien->pluck('no_rawat')->toArray();
        $totalBiayaDB = DB::table('detail_piutang_pasien')->select('no_rawat', 'totalpiutang')->where('kd_pj', 'not like', 'BPJ')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $tglKeluarDB = DB::table('kamar_inap')->select('no_rawat', 'tgl_keluar', 'jam_keluar')->whereIn('no_rawat', $noRawats)->orderByDesc('tgl_keluar')->orderByDesc('jam_keluar')->get()->groupBy('no_rawat');
        $billings = DB::table('billing')->select('no_rawat', 'nm_perawatan', 'totalbiaya', 'status', 'no')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $getPasien->map(function ($item) use ($totalBiayaDB, $tglKeluarDB, $billings) {
            $item->getTotalBiaya = isset($totalBiayaDB[$item->no_rawat]) ? $totalBiayaDB[$item->no_rawat] : collect();
            
            $kamar = isset($tglKeluarDB[$item->no_rawat]) ? $tglKeluarDB[$item->no_rawat] : collect();
            $item->getTglKeluar = $kamar->take(1)->values();
            
            $itemBillings = isset($billings[$item->no_rawat]) ? $billings[$item->no_rawat] : collect();
            
            $item->getNomorNota = $itemBillings->where('no', 'No.Nota')->values();
            $item->getRegistrasi = $itemBillings->where('status', 'Registrasi')->values();
            $item->getRalanDokter = $itemBillings->where('status', 'Ralan Dokter')->values();
            $item->getRalanDrParamedis = $itemBillings->where('status', 'Ralan Dokter Paramedis')->values();
            $item->getRalanParamedis = $itemBillings->where('status', 'Ralan Paramedis')->values();
            $item->getRanapDokter = $itemBillings->where('status', 'Ranap Dokter')->values();
            $item->getRanapDrParamedis = $itemBillings->where('status', 'Ranap Dokter Paramedis')->values();
            $item->getRanapParamedis = $itemBillings->where('status', 'Ranap Paramedis')->values();
            $item->getOprasi = $itemBillings->where('status', 'Operasi')->values();
            $item->getLaborat = $itemBillings->where('status', 'Laborat')->values();
            $item->getRadiologi = $itemBillings->where('status', 'Radiologi')->values();
            $item->getKamarInap = $itemBillings->where('status', 'Kamar')->values();
            $item->getObat = $itemBillings->where('status', 'Obat')->values();
            $item->getReturObat = $itemBillings->where('status', 'Retur Obat')->values();
            $item->getTambahan = $itemBillings->where('status', 'Tambahan')->values();
        });

        return view('laporan.invoiceAsuransi', [
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
            ->where(function ($query) use ( $kdPenjamin) {
                if ($kdPenjamin) {
                    $query->whereIn('detail_piutang_pasien.kd_pj', $kdPenjamin);
                }
            })
            ->whereBetween('piutang_pasien.tgltempo', [$getListInvoice->tanggl1, $getListInvoice->tanggl2])
            ->where('reg_periksa.status_lanjut', $getListInvoice->status_lanjut)
            ->groupBy('reg_periksa.no_rawat')
            ->get();
            
        $noRawats = $getPasien->pluck('no_rawat')->toArray();
        $diagnosaDB = DB::table('diagnosa_pasien')->select('diagnosa_pasien.no_rawat', 'penyakit.nm_penyakit', 'diagnosa_pasien.kd_penyakit')->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')->whereIn('diagnosa_pasien.no_rawat', $noRawats)->where('diagnosa_pasien.prioritas', '1')->get()->groupBy('no_rawat');
        $totalBiayaDB = DB::table('detail_piutang_pasien')->select('no_rawat', 'totalpiutang')->where('kd_pj', 'not like', 'BPJ')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $totalBiaya2DB = DB::table('detail_piutang_pasien')->select('detail_piutang_pasien.no_rawat', 'detail_piutang_pasien.totalpiutang', 'bw_maping_asuransi.nama_perusahaan')->leftJoin('bw_maping_asuransi','detail_piutang_pasien.kd_pj','=','bw_maping_asuransi.kd_pj')->whereIn('detail_piutang_pasien.no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $tglKeluarDB = DB::table('kamar_inap')->select('no_rawat', 'tgl_keluar', 'jam_keluar')->whereIn('no_rawat', $noRawats)->orderByDesc('tgl_keluar')->orderByDesc('jam_keluar')->get()->groupBy('no_rawat');
        $billings = DB::table('billing')->select('no_rawat', 'nm_perawatan', 'totalbiaya', 'status', 'no')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $getPasien->map(function ($item) use ($diagnosaDB, $totalBiayaDB, $totalBiaya2DB, $tglKeluarDB, $billings) {
            $item->getDiagnosa = isset($diagnosaDB[$item->no_rawat]) ? $diagnosaDB[$item->no_rawat] : collect();
            $item->getTotalBiaya = isset($totalBiayaDB[$item->no_rawat]) ? $totalBiayaDB[$item->no_rawat] : collect();
            $item->getTotalBiaya2 = isset($totalBiaya2DB[$item->no_rawat]) ? $totalBiaya2DB[$item->no_rawat] : collect();
            
            $kamar = isset($tglKeluarDB[$item->no_rawat]) ? $tglKeluarDB[$item->no_rawat] : collect();
            $item->getTglKeluar = $kamar->take(1)->values();
            
            $itemBillings = isset($billings[$item->no_rawat]) ? $billings[$item->no_rawat] : collect();
            
            $item->getNomorNota = $itemBillings->where('no', 'No.Nota')->values();
            $item->getRegistrasi = $itemBillings->where('status', 'Registrasi')->values();
            $item->getRalanDokter = $itemBillings->where('status', 'Ralan Dokter')->values();
            $item->getRalanDrParamedis = $itemBillings->where('status', 'Ralan Dokter Paramedis')->values();
            $item->getRalanParamedis = $itemBillings->where('status', 'Ralan Paramedis')->values();
            $item->getRanapDokter = $itemBillings->where('status', 'Ranap Dokter')->values();
            $item->getRanapDrParamedis = $itemBillings->where('status', 'Ranap Dokter Paramedis')->values();
            $item->getRanapParamedis = $itemBillings->where('status', 'Ranap Paramedis')->values();
            $item->getOprasi = $itemBillings->where('status', 'Operasi')->values();
            $item->getLaborat = $itemBillings->where('status', 'Laborat')->values();
            $item->getRadiologi = $itemBillings->where('status', 'Radiologi')->values();
            $item->getKamarInap = $itemBillings->where('status', 'Kamar')->values();
            $item->getObat = $itemBillings->where('status', 'Obat')->values();
            $item->getReturObat = $itemBillings->where('status', 'Retur Obat')->values();
            $item->getTambahan = $itemBillings->where('status', 'Tambahan')->values();
        });

        return view('laporan.cetak.cetakinvoiceAsuransi', [
            'getDetailAsuransi' => $getDetailAsuransi,
            'getListInvoice' => $getListInvoice,
            'getPasien' => $getPasien,
            'template' => $template,
        ]);
    }
}
