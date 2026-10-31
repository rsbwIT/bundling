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
        $mode_cob = $request->input('mode_cob', 'non_cob'); // non_cob atau cob
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));

        $getDetailAsuransi = null;
        if (!empty($kdPenjamin)) {
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
                ->where(function ($query) use ($kdPenjamin) {
                    if (is_array($kdPenjamin)) {
                        $query->whereIn('penjab.kd_pj', $kdPenjamin);
                    } else {
                        $query->where('penjab.kd_pj', $kdPenjamin);
                    }
                })
                ->where('penjab.status', '=', '1')
                ->first();
        }

        $getNomorSurat = null;
        if ($getDetailAsuransi && !empty($getDetailAsuransi->kd_surat)) {
            try {
                $getNomorSurat = NomorInvoice::getAutonumberInvoice($getDetailAsuransi->kd_surat, $status_lanjut);
            } catch (\Throwable $th) {
                $getNomorSurat = null;
            }
        }

        $getPasien = collect();
        if (!empty($kdPenjamin)) {
            $pasienQuery = DB::table('detail_piutang_pasien')
                ->select(
                    'detail_piutang_pasien.no_rawat',
                    'detail_piutang_pasien.nama_bayar',
                    'detail_piutang_pasien.kd_pj as kd_pj_piutang',
                    'detail_piutang_pasien.totalpiutang',
                    'detail_piutang_pasien.sisapiutang',
                    'detail_piutang_pasien.tgltempo',
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.status_lanjut',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.kd_pj as kd_pj_reg',
                    'pasien.nm_pasien',
                    'bw_peserta_asuransi.nomor_kartu',
                    'bw_peserta_asuransi.nomor_klaim'
                )
                ->join('reg_periksa', 'detail_piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('bw_peserta_asuransi', 'pasien.no_rkm_medis', '=', 'bw_peserta_asuransi.no_rkm_medis')
                ->where(function ($query) use ($kdPenjamin) {
                    if (is_array($kdPenjamin)) {
                        $query->whereIn('detail_piutang_pasien.kd_pj', $kdPenjamin)
                              ->orWhereIn('reg_periksa.kd_pj', $kdPenjamin);
                    } else {
                        $query->where('detail_piutang_pasien.kd_pj', $kdPenjamin)
                              ->orWhere('reg_periksa.kd_pj', $kdPenjamin);
                    }
                })
                ->whereBetween('detail_piutang_pasien.tgltempo', [$tanggl1, $tanggl2])
                ->where('reg_periksa.status_lanjut', $status_lanjut);



            // Cek invoice yang sudah dibuat berdasarkan no_rawat dan nama_bayar
            $pasienQuery->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('bw_detail_invoice_asuransi')
                    ->whereColumn('bw_detail_invoice_asuransi.no_rawat', 'detail_piutang_pasien.no_rawat')
                    ->where(function($q) {
                        $q->whereColumn('bw_detail_invoice_asuransi.nama_bayar', 'detail_piutang_pasien.nama_bayar')
                          ->orWhere(function($subQ) {
                              $subQ->whereNull('bw_detail_invoice_asuransi.nama_bayar')
                                   ->whereColumn('bw_detail_invoice_asuransi.kd_pj', 'detail_piutang_pasien.kd_pj');
                          });
                    });
            });

            $getPasien = $pasienQuery->orderBy('reg_periksa.no_rawat')->orderBy('detail_piutang_pasien.nama_bayar')->get();
        }

        $noRawats = $getPasien->pluck('no_rawat')->unique()->toArray();

        // Ambil info semua piutang per pasien untuk mengetahui apakah pasien ini multi-piutang
        $allPiutangPerPasien = DB::table('detail_piutang_pasien')
            ->select('no_rawat', 'kd_pj', 'nama_bayar', 'totalpiutang')
            ->whereIn('no_rawat', $noRawats)
            ->get()
            ->groupBy('no_rawat');

        $tglKeluarDB = DB::table('kamar_inap')->select('no_rawat', 'tgl_keluar', 'jam_keluar')->whereIn('no_rawat', $noRawats)->orderByDesc('tgl_keluar')->orderByDesc('jam_keluar')->get()->groupBy('no_rawat');
        $billings = DB::table('billing')->select('no_rawat', 'nm_perawatan', 'totalbiaya', 'status', 'no')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $getPasien->map(function ($item) use ($allPiutangPerPasien, $tglKeluarDB, $billings) {
            $allPiutang = isset($allPiutangPerPasien[$item->no_rawat]) ? $allPiutangPerPasien[$item->no_rawat] : collect();
            $item->getAllPiutang = $allPiutang;
            $item->is_cob = $allPiutang->count() > 1;

            // Total biaya untuk item baris ini diambil langsung dari piutang baris ini
            $item->biaya_baris = $item->totalpiutang;
            $item->getTotalBiaya = collect([(object)['totalpiutang' => $item->totalpiutang]]);
            
            $kamar = isset($tglKeluarDB[$item->no_rawat]) ? $tglKeluarDB[$item->no_rawat] : collect();
            $item->getTglKeluar = $kamar->take(1)->values();
            $item->tgl_keluar = $kamar->isNotEmpty() ? $kamar->first()->tgl_keluar : null;
            $item->tgl_masuk = $kamar->isNotEmpty() ? $item->tgl_registrasi : null;
            $item->total_biaya = $item->biaya_baris ?? (isset($item->getTotalBiaya) ? $item->getTotalBiaya->sum('totalpiutang') : 0);
            $item->tgl_byr = $item->tgltempo;
            
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
            'mode_cob' => $mode_cob,
            'kdPenjamin' => $kdPenjamin,
            'url' => $url,
            'getNomorSurat' => $getNomorSurat,
            'penjab' => $penjab,
            'getPasien' => $getPasien,
            'getDetailAsuransi' => $getDetailAsuransi,
            'is_preview' => true,
        ]);
    }

    // 2 SIMPAN DAN REDIRECT KE CETAK ======================================================================
    public function simpanNomor(Request $request)
    {
        $selectedItems = $request->input('selected_pasien', []);
        if (empty($selectedItems)) {
            return redirect()->back()->with('gagalSimpanNomor', 'Gagal menyimpan: Silakan pilih minimal 1 tagihan/pasien yang akan dibuatkan invoice.');
        }

        DB::beginTransaction();
        try {
            $count = count($selectedItems);
            $lamiran = $count . ' (' . \App\Services\Keuangan\NomorInvoice::Terbilang($count) . ' ) Lembar';

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
                'mode_cob' => $request->input('mode_cob', 'non_cob'),
                'lamiran' => $lamiran,
            ]);

            $detailData = [];
            foreach ($selectedItems as $val) {
                // val format: "no_rawat|nama_bayar|kd_pj|totalpiutang"
                $parts = explode('|', $val);
                $noRawat = $parts[0];
                $namaBayar = $parts[1] ?? null;
                $kdPjItem = $parts[2] ?? $request->kode_asuransi;
                $totalPiutang = isset($parts[3]) ? (float)$parts[3] : null;

                $detailData[] = [
                    'nomor_tagihan' => $request->nomor_tagihan,
                    'no_rawat' => $noRawat,
                    'kd_pj' => $kdPjItem,
                    'nama_bayar' => $namaBayar,
                    'totalpiutang' => $totalPiutang,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('bw_detail_invoice_asuransi')->insert($detailData);

            DB::commit();
            return redirect()->back()->with('sucsessSimpanNomor', 'Berhasil menyimpan invoice tagihan (' . $request->nomor_tagihan . ') untuk ' . $count . ' tagihan. Tagihan yang dipilih sudah tersimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('gagalSimpanNomor', 'Gagal menyimpan nomor invoice: ' . $th->getMessage());
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
                'bw_invoice_asuransi.mode_cob',
                'bw_invoice_asuransi.lamiran'
            )
            ->where('bw_invoice_asuransi.nomor_tagihan', urldecode($nomor_tagihan))
            ->first();

        if (!$getListInvoice) {
            abort(404, 'Invoice tidak ditemukan');
        }

        $kdPenjamin = array_filter(explode(',', $getListInvoice->cari_kode_asuransi));
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

        // Cek rincian tagihan tersimpan untuk invoice ini
        $detailRows = DB::table('bw_detail_invoice_asuransi')
            ->where('nomor_tagihan', $getListInvoice->nomor_tagihan)
            ->get();

        if ($detailRows->isNotEmpty()) {
            $noRawats = $detailRows->pluck('no_rawat')->unique()->toArray();
            $pasiensMap = DB::table('reg_periksa')
                ->select(
                    'reg_periksa.no_rawat',
                    'pasien.nm_pasien',
                    'reg_periksa.kd_pj',
                    'reg_periksa.umurdaftar',
                    'reg_periksa.sttsumur',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.status_lanjut',
                    'pasien.no_rkm_medis',
                    'bw_peserta_asuransi.nomor_kartu',
                    'bw_peserta_asuransi.nomor_klaim'
                )
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('bw_peserta_asuransi', 'pasien.no_rkm_medis', '=', 'bw_peserta_asuransi.no_rkm_medis')
                ->whereIn('reg_periksa.no_rawat', $noRawats)
                ->get()
                ->keyBy('no_rawat');

            $getPasien = collect();
            foreach ($detailRows as $det) {
                if (isset($pasiensMap[$det->no_rawat])) {
                    $p = clone $pasiensMap[$det->no_rawat];
                    $p->nama_bayar = $det->nama_bayar;
                    $p->kd_pj_piutang = $det->kd_pj;
                    $p->biaya_baris = $det->totalpiutang ?? 0;
                    $p->getTotalBiaya = collect([(object)['totalpiutang' => $p->biaya_baris]]);
                    $p->tgl_byr = $getListInvoice->tgl_cetak;
                    $getPasien->push($p);
                }
            }
        } else {
            // Fallback data lama
            $pasienQuery = DB::table('reg_periksa')
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
                ->join('detail_piutang_pasien', 'detail_piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('bw_peserta_asuransi', 'pasien.no_rkm_medis', '=', 'bw_peserta_asuransi.no_rkm_medis')
                ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->whereBetween('detail_piutang_pasien.tgltempo', [$getListInvoice->tanggl1, $getListInvoice->tanggl2])
                ->where('reg_periksa.status_lanjut', $getListInvoice->status_lanjut);

            if (!empty($kdPenjamin)) {
                $pasienQuery->whereIn('detail_piutang_pasien.kd_pj', $kdPenjamin);
            }

            $getPasien = $pasienQuery->groupBy('reg_periksa.no_rawat')->get();
            $noRawats = $getPasien->pluck('no_rawat')->toArray();
            $modeCobInvoice = $getListInvoice->mode_cob ?? 'non_cob';
            $dpQuery = DB::table('detail_piutang_pasien')->select('no_rawat', 'kd_pj', 'nama_bayar', 'totalpiutang')->whereIn('no_rawat', $noRawats);
            if ($modeCobInvoice === 'non_cob' && !empty($kdPenjamin)) {
                $dpQuery->whereIn('kd_pj', $kdPenjamin);
            } else {
                $dpQuery->where('kd_pj', 'not like', 'BPJ');
            }
            $tbDB = $dpQuery->get()->groupBy('no_rawat');
            $getPasien->map(function ($it) use ($tbDB) {
                $it->getTotalBiaya = isset($tbDB[$it->no_rawat]) ? $tbDB[$it->no_rawat] : collect();
                $it->biaya_baris = $it->getTotalBiaya->sum('totalpiutang');
            });
        }
            
        $noRawats = $getPasien->pluck('no_rawat')->unique()->toArray();
        $diagnosaDB = DB::table('diagnosa_pasien')->select('diagnosa_pasien.no_rawat', 'penyakit.nm_penyakit', 'diagnosa_pasien.kd_penyakit')->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')->whereIn('diagnosa_pasien.no_rawat', $noRawats)->where('diagnosa_pasien.prioritas', '1')->get()->groupBy('no_rawat');
        
        $totalBiaya2DB = DB::table('detail_piutang_pasien')->select('detail_piutang_pasien.no_rawat', 'detail_piutang_pasien.totalpiutang', 'bw_maping_asuransi.nama_perusahaan')->leftJoin('bw_maping_asuransi','detail_piutang_pasien.kd_pj','=','bw_maping_asuransi.kd_pj')->whereIn('detail_piutang_pasien.no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $tglKeluarDB = DB::table('kamar_inap')->select('no_rawat', 'tgl_keluar', 'jam_keluar')->whereIn('no_rawat', $noRawats)->orderByDesc('tgl_keluar')->orderByDesc('jam_keluar')->get()->groupBy('no_rawat');
        $billings = DB::table('billing')->select('no_rawat', 'nm_perawatan', 'totalbiaya', 'status', 'no')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $getPasien->map(function ($item) use ($diagnosaDB, $totalBiaya2DB, $tglKeluarDB, $billings) {
            $item->getDiagnosa = isset($diagnosaDB[$item->no_rawat]) ? $diagnosaDB[$item->no_rawat] : collect();
            $item->getTotalBiaya2 = isset($totalBiaya2DB[$item->no_rawat]) ? $totalBiaya2DB[$item->no_rawat] : collect();
            
            $kamar = isset($tglKeluarDB[$item->no_rawat]) ? $tglKeluarDB[$item->no_rawat] : collect();
            $item->getTglKeluar = $kamar->take(1)->values();
            $item->tgl_keluar = $kamar->isNotEmpty() ? $kamar->first()->tgl_keluar : null;
            $item->tgl_masuk = $kamar->isNotEmpty() ? $item->tgl_registrasi : null;
            $item->total_biaya = $item->biaya_baris ?? (isset($item->getTotalBiaya) ? $item->getTotalBiaya->sum('totalpiutang') : 0);
            
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

    /**
     * Export Excel Satuan untuk 1 Nomor Invoice.
     */
    public function exportExcelSingleInvoice(Request $request, $nomor_tagihan = null)
    {
        $nomor_tagihan = $nomor_tagihan ?: $request->input('nomor_tagihan');
        $nomor_tagihan = urldecode($nomor_tagihan);

        $inv = DB::table('bw_invoice_asuransi')->where('nomor_tagihan', $nomor_tagihan)->first();
        if (!$inv) {
            abort(404, 'Invoice ' . $nomor_tagihan . ' tidak ditemukan');
        }

        $detailRows = DB::table('bw_detail_invoice_asuransi')
            ->where('nomor_tagihan', $inv->nomor_tagihan)
            ->get();

        $rows = [];
        $totalBiaya = 0;

        if ($detailRows->isNotEmpty()) {
            $noRawats = $detailRows->pluck('no_rawat')->unique()->toArray();
            $pasiensMap = DB::table('reg_periksa')
                ->select(
                    'reg_periksa.no_rawat',
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'reg_periksa.tgl_registrasi',
                    'bw_peserta_asuransi.nomor_kartu',
                    'bw_peserta_asuransi.nomor_klaim'
                )
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('bw_peserta_asuransi', 'pasien.no_rkm_medis', '=', 'bw_peserta_asuransi.no_rkm_medis')
                ->whereIn('reg_periksa.no_rawat', $noRawats)
                ->get()
                ->keyBy('no_rawat');

            $billings = DB::table('billing')
                ->select('no_rawat', 'nm_perawatan', 'no')
                ->whereIn('no_rawat', $noRawats)
                ->where('no', 'No.Nota')
                ->get()
                ->groupBy('no_rawat');

            $tglKeluarDB = DB::table('kamar_inap')
                ->select('no_rawat', 'tgl_keluar', 'jam_keluar')
                ->whereIn('no_rawat', $noRawats)
                ->orderByDesc('tgl_keluar')
                ->orderByDesc('jam_keluar')
                ->get()
                ->groupBy('no_rawat');

            foreach ($detailRows as $idx => $det) {
                $p = $pasiensMap[$det->no_rawat] ?? null;
                $biaya = $det->totalpiutang ?? 0;
                $totalBiaya += $biaya;

                $noKwit = '';
                if (isset($billings[$det->no_rawat]) && $billings[$det->no_rawat]->isNotEmpty()) {
                    $rawNota = $billings[$det->no_rawat]->first()->nm_perawatan;
                    $noKwit = substr(str_replace(':', '', $rawNota), -6);
                }

                $tglRawat = '-';
                if ($p) {
                    $tglKeluarItem = null;
                    if (isset($tglKeluarDB[$det->no_rawat]) && $tglKeluarDB[$det->no_rawat]->isNotEmpty()) {
                        $tglKeluarItem = $tglKeluarDB[$det->no_rawat]->first()->tgl_keluar;
                    }
                    $tglMasukStr = $p->tgl_registrasi ? date('d/m/Y', strtotime($p->tgl_registrasi)) : '-';
                    $tglKeluarStr = $tglKeluarItem ? date('d/m/Y', strtotime($tglKeluarItem)) : '-';
                    $tglRawat = ($tglKeluarItem) ? ($tglMasukStr . ' - ' . $tglKeluarStr) : $tglMasukStr;
                }

                $namaPasienDisplay = ($p ? $p->nm_pasien : '-') . ($det->nama_bayar ? ' (' . $det->nama_bayar . ')' : '');

                $rows[] = [
                    'no' => $idx + 1,
                    'nm_pasien' => $namaPasienDisplay,
                    'biaya' => $biaya,
                    'no_kwit' => $noKwit,
                    'nomor_kartu' => $p ? ($p->nomor_kartu ?: '-') : '-',
                    'nomor_klaim' => $p ? ($p->nomor_klaim ?: '-') : '-',
                    'no_rkm_medis' => $p ? $p->no_rkm_medis : '-',
                    'tgl_rawat' => $tglRawat,
                ];
            }
        }

        $cleanNomor = preg_replace('/[^A-Za-z0-9_\-]/', '_', $inv->nomor_tagihan);
        $filename = 'Invoice_' . $cleanNomor . '.xls';

        return response()->streamDownload(function () use ($inv, $rows, $totalBiaya) {
            echo view('laporan.export.excel-single-invoice', [
                'inv' => $inv,
                'rows' => $rows,
                'totalBiaya' => $totalBiaya,
            ])->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Excel Rekap Seluruh Invoice (dengan sheet rekap + lembar rincian per invoice).
     */
    public function exportExcelRekap(Request $request)
    {
        if ($request->filled('nomor_tagihan')) {
            return $this->exportExcelSingleInvoice($request, $request->nomor_tagihan);
        }

        $tgl_cetak1 = $request->input('tgl_cetak1', date('Y-m-d'));
        $tgl_cetak2 = $request->input('tgl_cetak2', date('Y-m-d'));
        $status_lanjut = $request->input('status_lanjut', 'Ranap');
        $carinomor = $request->input('carinomor', '');
        $filter_by = $request->input('filter_by', 'tgl_cetak');

        $query = DB::table('bw_invoice_asuransi')
            ->select(
                'bw_invoice_asuransi.nomor_tagihan',
                'bw_invoice_asuransi.kode_asuransi',
                'bw_invoice_asuransi.nama_asuransi',
                'bw_invoice_asuransi.alamat_asuransi',
                'bw_invoice_asuransi.tanggl1',
                'bw_invoice_asuransi.tanggl2',
                'bw_invoice_asuransi.tgl_cetak',
                'bw_invoice_asuransi.status_lanjut',
                'bw_invoice_asuransi.mode_cob',
                'bw_invoice_asuransi.lamiran',
                'bw_invoice_asuransi.cari_kode_asuransi'
            );

        if ($filter_by === 'periode_rawat') {
            $query->where(function ($q) use ($tgl_cetak1, $tgl_cetak2) {
                $q->whereBetween('bw_invoice_asuransi.tanggl1', [$tgl_cetak1, $tgl_cetak2])
                  ->orWhereBetween('bw_invoice_asuransi.tanggl2', [$tgl_cetak1, $tgl_cetak2])
                  ->orWhere(function ($sub) use ($tgl_cetak1, $tgl_cetak2) {
                      $sub->where('bw_invoice_asuransi.tanggl1', '<=', $tgl_cetak1)
                          ->where('bw_invoice_asuransi.tanggl2', '>=', $tgl_cetak2);
                  });
            });
        } else {
            $query->whereBetween('bw_invoice_asuransi.tgl_cetak', [$tgl_cetak1, $tgl_cetak2]);
        }

        if (!empty($status_lanjut)) {
            $query->where('bw_invoice_asuransi.status_lanjut', $status_lanjut);
        }

        if (!empty($carinomor)) {
            $query->where(function ($q) use ($carinomor) {
                $q->orWhere('bw_invoice_asuransi.kode_asuransi', 'LIKE', "%$carinomor%")
                  ->orWhere('bw_invoice_asuransi.nama_asuransi', 'LIKE', "%$carinomor%")
                  ->orWhere('bw_invoice_asuransi.nomor_tagihan', 'LIKE', "%$carinomor%");
            });
        }

        $invoices = $query->orderBy('bw_invoice_asuransi.nomor_tagihan', 'desc')->get();

        $allNomorTagihan = $invoices->pluck('nomor_tagihan')->toArray();
        $allDetailRows = DB::table('bw_detail_invoice_asuransi')
            ->whereIn('nomor_tagihan', $allNomorTagihan)
            ->get()
            ->groupBy('nomor_tagihan');

        $rekapData = [];
        $rincianPerInvoice = [];

        foreach ($invoices as $idx => $inv) {
            $detailRows = isset($allDetailRows[$inv->nomor_tagihan]) ? $allDetailRows[$inv->nomor_tagihan] : collect();
            $rowsThisInvoice = [];
            $totalBiayaInv = 0;

            if ($detailRows->isNotEmpty()) {
                $noRawats = $detailRows->pluck('no_rawat')->unique()->toArray();
                $pasiensMap = DB::table('reg_periksa')
                    ->select(
                        'reg_periksa.no_rawat',
                        'pasien.no_rkm_medis',
                        'pasien.nm_pasien',
                        'reg_periksa.tgl_registrasi',
                        'bw_peserta_asuransi.nomor_kartu',
                        'bw_peserta_asuransi.nomor_klaim'
                    )
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->leftJoin('bw_peserta_asuransi', 'pasien.no_rkm_medis', '=', 'bw_peserta_asuransi.no_rkm_medis')
                    ->whereIn('reg_periksa.no_rawat', $noRawats)
                    ->get()
                    ->keyBy('no_rawat');

                $billings = DB::table('billing')
                    ->select('no_rawat', 'nm_perawatan', 'no')
                    ->whereIn('no_rawat', $noRawats)
                    ->where('no', 'No.Nota')
                    ->get()
                    ->groupBy('no_rawat');

                $tglKeluarDB = DB::table('kamar_inap')
                    ->select('no_rawat', 'tgl_keluar', 'jam_keluar')
                    ->whereIn('no_rawat', $noRawats)
                    ->orderByDesc('tgl_keluar')
                    ->orderByDesc('jam_keluar')
                    ->get()
                    ->groupBy('no_rawat');

                foreach ($detailRows as $rIdx => $det) {
                    $p = $pasiensMap[$det->no_rawat] ?? null;
                    $biaya = $det->totalpiutang ?? 0;
                    $totalBiayaInv += $biaya;

                    $noKwit = '';
                    if (isset($billings[$det->no_rawat]) && $billings[$det->no_rawat]->isNotEmpty()) {
                        $rawNota = $billings[$det->no_rawat]->first()->nm_perawatan;
                        $noKwit = substr(str_replace(':', '', $rawNota), -6);
                    }

                    $tglRawat = '-';
                    if ($p) {
                        $tglKeluarItem = null;
                        if (isset($tglKeluarDB[$det->no_rawat]) && $tglKeluarDB[$det->no_rawat]->isNotEmpty()) {
                            $tglKeluarItem = $tglKeluarDB[$det->no_rawat]->first()->tgl_keluar;
                        }
                        $tglMasukStr = $p->tgl_registrasi ? date('d/m/Y', strtotime($p->tgl_registrasi)) : '-';
                        $tglKeluarStr = $tglKeluarItem ? date('d/m/Y', strtotime($tglKeluarItem)) : '-';
                        $tglRawat = ($tglKeluarItem) ? ($tglMasukStr . ' - ' . $tglKeluarStr) : $tglMasukStr;
                    }

                    $namaPasienDisplay = ($p ? $p->nm_pasien : '-') . ($det->nama_bayar ? ' (' . $det->nama_bayar . ')' : '');

                    $rowsThisInvoice[] = [
                        'no' => $rIdx + 1,
                        'nm_pasien' => $namaPasienDisplay,
                        'biaya' => $biaya,
                        'no_kwit' => $noKwit,
                        'nomor_kartu' => $p ? ($p->nomor_kartu ?: '-') : '-',
                        'nomor_klaim' => $p ? ($p->nomor_klaim ?: '-') : '-',
                        'no_rkm_medis' => $p ? $p->no_rkm_medis : '-',
                        'tgl_rawat' => $tglRawat,
                    ];
                }
            }

            $rekapData[] = [
                'no' => $idx + 1,
                'nomor_tagihan' => $inv->nomor_tagihan,
                'nama_asuransi' => $inv->nama_asuransi,
                'status_lanjut' => $inv->status_lanjut,
                'mode_cob' => $inv->mode_cob ?? 'non_cob',
                'periode_rawat' => date('d/m/Y', strtotime($inv->tanggl1)) . ' - ' . date('d/m/Y', strtotime($inv->tanggl2)),
                'tgl_cetak' => date('d/m/Y', strtotime($inv->tgl_cetak)),
                'total_pasien' => count($rowsThisInvoice),
                'total_biaya' => $totalBiayaInv,
                'sheet_id' => 'sheet_' . ($idx + 1),
            ];

            $rincianPerInvoice[] = [
                'inv' => $inv,
                'rows' => $rowsThisInvoice,
                'totalBiaya' => $totalBiayaInv,
                'sheet_id' => 'sheet_' . ($idx + 1),
            ];
        }

        $filename = 'Rekap_Invoice_Asuransi_' . date('Ymd_His') . '.xls';

        return response()->streamDownload(function () use ($rekapData, $rincianPerInvoice, $tgl_cetak1, $tgl_cetak2, $status_lanjut, $filter_by) {
            echo view('laporan.export.excel-rekap-invoice', [
                'rekapData' => $rekapData,
                'rincianPerInvoice' => $rincianPerInvoice,
                'tgl_cetak1' => $tgl_cetak1,
                'tgl_cetak2' => $tgl_cetak2,
                'status_lanjut' => $status_lanjut,
                'filter_by' => $filter_by,
            ])->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
