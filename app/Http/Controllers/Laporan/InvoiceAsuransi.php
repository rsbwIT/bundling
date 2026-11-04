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

        $tglNotaDB = $this->getTanggalNota($noRawats);

        $getPasien->map(function ($item) use ($allPiutangPerPasien, $tglKeluarDB, $billings, $tglNotaDB) {
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
            $item->tgl_byr = $tglNotaDB[$item->no_rawat] ?? $item->tgltempo;
            
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

            // Ambil nominal fallback dari detail_piutang_pasien jika totalpiutang di detail_invoice belum ada
            $fallbackPiutangs = DB::table('detail_piutang_pasien')
                ->select('no_rawat', 'kd_pj', 'nama_bayar', 'totalpiutang')
                ->whereIn('no_rawat', $noRawats)
                ->get()
                ->groupBy('no_rawat');

            $getPasien = collect();
            foreach ($detailRows as $det) {
                if (isset($pasiensMap[$det->no_rawat])) {
                    $p = clone $pasiensMap[$det->no_rawat];
                    $p->nama_bayar = $det->nama_bayar;
                    $p->kd_pj_piutang = $det->kd_pj;

                    $biayaItem = $det->totalpiutang;
                    if ($biayaItem === null || $biayaItem == 0) {
                        $fbList = $fallbackPiutangs[$det->no_rawat] ?? collect();
                        $matchFb = $fbList->first(function ($f) use ($det) {
                            return (!empty($det->kd_pj) && $f->kd_pj == $det->kd_pj)
                                || (!empty($det->nama_bayar) && $f->nama_bayar == $det->nama_bayar);
                        });
                        $biayaItem = $matchFb ? $matchFb->totalpiutang : ($fbList->sum('totalpiutang') ?: 0);
                    }

                    $p->biaya_baris = $biayaItem;
                    $p->total_biaya = $biayaItem;
                    $p->getTotalBiaya = collect([(object)['totalpiutang' => $biayaItem]]);
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
                $it->total_biaya = $it->biaya_baris;
            });
        }
            
        $noRawats = $getPasien->pluck('no_rawat')->unique()->toArray();
        $diagnosaDB = DB::table('diagnosa_pasien')->select('diagnosa_pasien.no_rawat', 'penyakit.nm_penyakit', 'diagnosa_pasien.kd_penyakit')->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')->whereIn('diagnosa_pasien.no_rawat', $noRawats)->where('diagnosa_pasien.prioritas', '1')->get()->groupBy('no_rawat');
        
        $totalBiaya2DB = DB::table('detail_piutang_pasien')->select('detail_piutang_pasien.no_rawat', 'detail_piutang_pasien.totalpiutang', 'bw_maping_asuransi.nama_perusahaan')->leftJoin('bw_maping_asuransi','detail_piutang_pasien.kd_pj','=','bw_maping_asuransi.kd_pj')->whereIn('detail_piutang_pasien.no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $tglKeluarDB = DB::table('kamar_inap')->select('no_rawat', 'tgl_keluar', 'jam_keluar')->whereIn('no_rawat', $noRawats)->orderByDesc('tgl_keluar')->orderByDesc('jam_keluar')->get()->groupBy('no_rawat');
        $billings = DB::table('billing')->select('no_rawat', 'nm_perawatan', 'totalbiaya', 'status', 'no')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $tglNotaDB = $this->getTanggalNota($noRawats);

        $getPasien->map(function ($item) use ($diagnosaDB, $totalBiaya2DB, $tglKeluarDB, $billings, $tglNotaDB) {
            if (!empty($tglNotaDB[$item->no_rawat])) {
                $item->tgl_byr = $tglNotaDB[$item->no_rawat];
            }
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
     * Ambil tanggal cetak nota (nota_inap / nota_jalan) per no_rawat.
     */
    private function getTanggalNota(array $noRawats)
    {
        if (empty($noRawats)) {
            return [];
        }
        $jalan = DB::table('nota_jalan')->whereIn('no_rawat', $noRawats)->pluck('tanggal', 'no_rawat')->toArray();
        $inap = DB::table('nota_inap')->whereIn('no_rawat', $noRawats)->pluck('tanggal', 'no_rawat')->toArray();
        return array_merge($jalan, $inap);
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

            $fallbackPiutangs = DB::table('detail_piutang_pasien')
                ->select('no_rawat', 'kd_pj', 'nama_bayar', 'totalpiutang')
                ->whereIn('no_rawat', $noRawats)
                ->get()
                ->groupBy('no_rawat');

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

            $tglNotaDB = $this->getTanggalNota($noRawats);

            foreach ($detailRows as $idx => $det) {
                $p = $pasiensMap[$det->no_rawat] ?? null;

                $biaya = $det->totalpiutang;
                if ($biaya === null || $biaya == 0) {
                    $fbList = $fallbackPiutangs[$det->no_rawat] ?? collect();
                    $matchFb = $fbList->first(function ($f) use ($det) {
                        return (!empty($det->kd_pj) && $f->kd_pj == $det->kd_pj)
                            || (!empty($det->nama_bayar) && $f->nama_bayar == $det->nama_bayar);
                    });
                    $biaya = $matchFb ? $matchFb->totalpiutang : ($fbList->sum('totalpiutang') ?: 0);
                }

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
                    $tglNotaStr = !empty($tglNotaDB[$det->no_rawat]) ? date('d/m/Y', strtotime($tglNotaDB[$det->no_rawat])) : $tglMasukStr;
                    $tglRawat = ($tglKeluarItem) ? ($tglMasukStr . ' - ' . $tglKeluarStr) : $tglNotaStr;
                }

                $namaPasienDisplay = ($p ? $p->nm_pasien : '-') . ($det->nama_bayar ? ' (' . $det->nama_bayar . ')' : '');

                $rows[] = [
                    'no' => ($idx + 1) . '.',
                    'nama' => $namaPasienDisplay,
                    'jumlah_biaya' => $biaya,
                    'no_kwit' => $noKwit,
                    'no_kartu' => $p ? ($p->nomor_kartu ?: '-') : '-',
                    'no_klaim' => $p ? ($p->nomor_klaim ?: '-') : '-',
                    'rm' => $p ? $p->no_rkm_medis : '-',
                    'tanggal_rawat' => $tglRawat,
                ];
            }
        }

        $xml = $this->buildSingleInvoiceSpreadsheetML($inv, $rows, $totalBiaya);

        $safeName = str_replace(['/', '\\', ' ', ':'], '_', $inv->nomor_tagihan);
        $filename = 'Invoice_' . $safeName . '.xls';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]);
    }

    /**
     * Export Excel Rekap Seluruh Invoice (Sheet 1: Rincian Pasien, Sheet 2: Rekap Invoice).
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

        if (!empty($status_lanjut) && in_array($status_lanjut, ['Ranap', 'Ralan'])) {
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

        $allNoRawats = [];
        foreach ($allDetailRows as $dGroup) {
            foreach ($dGroup as $dItem) {
                $allNoRawats[] = $dItem->no_rawat;
            }
        }
        $allNoRawats = array_unique($allNoRawats);

        $pasiensMap = collect();
        $fallbackPiutangs = collect();
        $billings = collect();
        $tglKeluarDB = collect();

        if (!empty($allNoRawats)) {
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
                ->whereIn('reg_periksa.no_rawat', $allNoRawats)
                ->get()
                ->keyBy('no_rawat');

            $fallbackPiutangs = DB::table('detail_piutang_pasien')
                ->select('no_rawat', 'kd_pj', 'nama_bayar', 'totalpiutang')
                ->whereIn('no_rawat', $allNoRawats)
                ->get()
                ->groupBy('no_rawat');

            $billings = DB::table('billing')
                ->select('no_rawat', 'nm_perawatan', 'no')
                ->whereIn('no_rawat', $allNoRawats)
                ->where('no', 'No.Nota')
                ->get()
                ->groupBy('no_rawat');

            $tglKeluarDB = DB::table('kamar_inap')
                ->select('no_rawat', 'tgl_keluar', 'jam_keluar')
                ->whereIn('no_rawat', $allNoRawats)
                ->orderByDesc('tgl_keluar')
                ->orderByDesc('jam_keluar')
                ->get()
                ->groupBy('no_rawat');

            $tglNotaDB = $this->getTanggalNota($allNoRawats);
        }

        $rekapData = [];
        $detailPasienData = [];

        foreach ($invoices as $idx => $inv) {
            $detailRows = isset($allDetailRows[$inv->nomor_tagihan]) ? $allDetailRows[$inv->nomor_tagihan] : collect();
            $totalBiayaInv = 0;
            $pasienCountInv = 0;

            foreach ($detailRows as $det) {
                $p = $pasiensMap[$det->no_rawat] ?? null;

                $biaya = $det->totalpiutang;
                if ($biaya === null || $biaya == 0) {
                    $fbList = $fallbackPiutangs[$det->no_rawat] ?? collect();
                    $matchFb = $fbList->first(function ($f) use ($det) {
                        return (!empty($det->kd_pj) && $f->kd_pj == $det->kd_pj)
                            || (!empty($det->nama_bayar) && $f->nama_bayar == $det->nama_bayar);
                    });
                    $biaya = $matchFb ? $matchFb->totalpiutang : ($fbList->sum('totalpiutang') ?: 0);
                }

                $totalBiayaInv += $biaya;
                $pasienCountInv++;

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
                    $tglNotaStr = !empty($tglNotaDB[$det->no_rawat]) ? date('d/m/Y', strtotime($tglNotaDB[$det->no_rawat])) : $tglMasukStr;
                    $tglRawat = ($tglKeluarItem) ? ($tglMasukStr . ' - ' . $tglKeluarStr) : $tglNotaStr;
                }

                $namaPasienDisplay = ($p ? $p->nm_pasien : '-') . ($det->nama_bayar ? ' (' . $det->nama_bayar . ')' : '');

                $detailPasienData[] = [
                    'nama' => $namaPasienDisplay,
                    'biaya' => $biaya,
                    'no_kwit' => $noKwit,
                    'nomor_kartu' => $p ? ($p->nomor_kartu ?: '-') : '-',
                    'nomor_klaim' => $p ? ($p->nomor_klaim ?: '-') : '-',
                    'no_rkm_medis' => $p ? $p->no_rkm_medis : '-',
                    'tgl_rawat' => $tglRawat,
                    'nomor_tagihan' => $inv->nomor_tagihan,
                    'nama_asuransi' => $inv->nama_asuransi,
                ];
            }

            $rekapData[] = [
                'tgl_cetak' => $inv->tgl_cetak,
                'nomor_tagihan' => $inv->nomor_tagihan,
                'kode_asuransi' => $inv->kode_asuransi,
                'nama_asuransi' => $inv->nama_asuransi,
                'periode' => date('d/m/Y', strtotime($inv->tanggl1)) . ' s/d ' . date('d/m/Y', strtotime($inv->tanggl2)),
                'status_lanjut' => $inv->status_lanjut,
                'jml_pasien' => $pasienCountInv,
                'total_tagihan' => $totalBiayaInv,
            ];
        }

        $xml = $this->buildRekapSpreadsheetML($rekapData, $detailPasienData, $tgl_cetak1, $tgl_cetak2, $status_lanjut);

        $filename = 'Rekap_Invoice_Asuransi_' . $status_lanjut . '_' . $tgl_cetak1 . '_sd_' . $tgl_cetak2 . '.xls';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]);
    }

    // 6 GENERATE EXCEL SPREADSHEETML UNTUK SINGLE INVOICE =================================
    private function buildSingleInvoiceSpreadsheetML($inv, $rows, $totalBiaya)
    {
        $terbilang = \App\Services\Keuangan\NomorInvoice::Terbilang($totalBiaya);

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n"
              . ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n"
              . ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n"
              . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n"
              . ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

        $xml .= '<Styles>
    <Style ss:ID="Default" ss:Name="Normal">
      <Alignment ss:Vertical="Center"/>
      <Borders/>
      <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#000000"/>
      <Interior/>
      <NumberFormat/>
      <Protection/>
    </Style>
    <Style ss:ID="title">
      <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#0F52BA"/>
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
    </Style>
    <Style ss:ID="subtitle">
      <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#1E293B"/>
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
    </Style>
    <Style ss:ID="metaLabel">
      <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#475569"/>
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
    </Style>
    <Style ss:ID="metaVal">
      <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#0F172A"/>
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
    </Style>
    <Style ss:ID="head">
      <Font ss:FontName="Calibri" ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#0284C7" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
      </Borders>
    </Style>
    <Style ss:ID="headCenter">
      <Font ss:FontName="Calibri" ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#0284C7" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
      </Borders>
    </Style>
    <Style ss:ID="headRight">
      <Font ss:FontName="Calibri" ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#0284C7" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
      </Borders>
    </Style>
    <Style ss:ID="cell">
      <Alignment ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
      </Borders>
    </Style>
    <Style ss:ID="cellCenter">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
      </Borders>
    </Style>
    <Style ss:ID="cellCurrency">
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <NumberFormat ss:Format="&quot;Rp. &quot;#,##0"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
      </Borders>
    </Style>
    <Style ss:ID="totalRow">
      <Font ss:Bold="1" ss:Color="#0F172A"/>
      <Interior ss:Color="#F1F5F9" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94A3B8"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94A3B8"/>
      </Borders>
    </Style>
    <Style ss:ID="totalCurrency">
      <Font ss:Bold="1" ss:Color="#0F172A"/>
      <Interior ss:Color="#F1F5F9" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <NumberFormat ss:Format="&quot;Rp. &quot;#,##0"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94A3B8"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94A3B8"/>
      </Borders>
    </Style>
    <Style ss:ID="terbilangStyle">
      <Font ss:FontName="Calibri" ss:Italic="1" ss:Color="#0F172A"/>
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
    </Style>
  </Styles>' . "\n";

        $xml .= '<Worksheet ss:Name="Tagihan"><Table>' . "\n";
        $xml .= '<Column ss:Width="35"/>'   // No
              . '<Column ss:Width="220"/>'  // Nama
              . '<Column ss:Width="120"/>'  // Jumlah Biaya
              . '<Column ss:Width="90"/>'   // No Kwit
              . '<Column ss:Width="150"/>'  // No Kartu
              . '<Column ss:Width="100"/>'  // No Klaim
              . '<Column ss:Width="80"/>'   // Rm
              . '<Column ss:Width="110"/>'  // Tanggal Rawat
              . "\n";

        $xml .= '<Row><Cell ss:StyleID="title"><Data ss:Type="String">RUMAH SAKIT BUMI WARAS</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">SURAT TAGIHAN BIAYA PERAWATAN DAN PENGOBATAN</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";

        $xml .= '<Row>'
              . '<Cell ss:StyleID="metaLabel"><Data ss:Type="String">Nomor Tagihan</Data></Cell>'
              . '<Cell ss:StyleID="metaVal" ss:MergeAcross="2"><Data ss:Type="String">: ' . $this->xmlEscape($inv->nomor_tagihan) . '</Data></Cell>'
              . '<Cell ss:StyleID="metaLabel"><Data ss:Type="String">Tanggal Cetak</Data></Cell>'
              . '<Cell ss:StyleID="metaVal" ss:MergeAcross="2"><Data ss:Type="String">: ' . date('d/m/Y', strtotime($inv->tgl_cetak)) . '</Data></Cell>'
              . '</Row>' . "\n";

        $xml .= '<Row>'
              . '<Cell ss:StyleID="metaLabel"><Data ss:Type="String">Nama Asuransi</Data></Cell>'
              . '<Cell ss:StyleID="metaVal" ss:MergeAcross="2"><Data ss:Type="String">: ' . $this->xmlEscape($inv->nama_asuransi) . '</Data></Cell>'
              . '<Cell ss:StyleID="metaLabel"><Data ss:Type="String">Status Pelayanan</Data></Cell>'
              . '<Cell ss:StyleID="metaVal" ss:MergeAcross="2"><Data ss:Type="String">: ' . $this->xmlEscape($inv->status_lanjut) . '</Data></Cell>'
              . '</Row>' . "\n";

        $xml .= '<Row>'
              . '<Cell ss:StyleID="metaLabel"><Data ss:Type="String">Periode Rawat</Data></Cell>'
              . '<Cell ss:StyleID="metaVal" ss:MergeAcross="2"><Data ss:Type="String">: ' . date('d/m/Y', strtotime($inv->tanggl1)) . ' s/d ' . date('d/m/Y', strtotime($inv->tanggl2)) . '</Data></Cell>'
              . '<Cell ss:StyleID="metaLabel"><Data ss:Type="String">Lampiran</Data></Cell>'
              . '<Cell ss:StyleID="metaVal" ss:MergeAcross="2"><Data ss:Type="String">: ' . $this->xmlEscape($inv->lamiran) . '</Data></Cell>'
              . '</Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";

        // Kolom persis permintaan: No., Nama, Jumlah Biaya, No Kwit, No Kartu, No Klaim, Rm, Tanggal Rawat
        $xml .= '<Row>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">No.</Data></Cell>'
              . '<Cell ss:StyleID="head"><Data ss:Type="String">Nama</Data></Cell>'
              . '<Cell ss:StyleID="headRight"><Data ss:Type="String">Jumlah Biaya</Data></Cell>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">No Kwit</Data></Cell>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">No Kartu</Data></Cell>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">No Klaim</Data></Cell>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">Rm</Data></Cell>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">Tanggal Rawat</Data></Cell>'
              . '</Row>' . "\n";

        foreach ($rows as $r) {
            $xml .= '<Row>'
                  . $this->xmlCell($r['no'], 'cellCenter')
                  . $this->xmlCell($r['nama'], 'cell')
                  . $this->xmlCell($r['jumlah_biaya'], 'cellCurrency', 'Number')
                  . $this->xmlCell($r['no_kwit'], 'cellCenter')
                  . $this->xmlCell($r['no_kartu'], 'cellCenter')
                  . $this->xmlCell($r['no_klaim'], 'cellCenter')
                  . $this->xmlCell($r['rm'], 'cellCenter')
                  . $this->xmlCell($r['tanggal_rawat'], 'cellCenter')
                  . '</Row>' . "\n";
        }

        // Total Row
        $xml .= '<Row>'
              . '<Cell ss:StyleID="totalRow" ss:MergeAcross="1"><Data ss:Type="String">Total</Data></Cell>'
              . $this->xmlCell($totalBiaya, 'totalCurrency', 'Number')
              . '<Cell ss:StyleID="totalRow" ss:MergeAcross="4"><Data ss:Type="String"></Data></Cell>'
              . '</Row>' . "\n";

        // Terbilang Row
        $xml .= '<Row>'
              . '<Cell ss:StyleID="terbilangStyle" ss:MergeAcross="7"><Data ss:Type="String">Terbilang : ' . trim($terbilang) . ' rupiah.</Data></Cell>'
              . '</Row>' . "\n";

        $xml .= '</Table></Worksheet>' . "\n";
        $xml .= '</Workbook>';

        return $xml;
    }

    // 7 GENERATE MULTI-SHEET REKAP SPREADSHEETML (SHEET 1: RINCIAN PASIEN, SHEET 2: REKAP INVOICE) =================
    private function buildRekapSpreadsheetML($rekapData, $detailPasienData, $tgl_cetak1, $tgl_cetak2, $status_lanjut)
    {
        $periode = date('d/m/Y', strtotime($tgl_cetak1)) . ' s/d ' . date('d/m/Y', strtotime($tgl_cetak2));

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
              . 'xmlns:o="urn:schemas-microsoft-com:office:office" '
              . 'xmlns:x="urn:schemas-microsoft-com:office:excel" '
              . 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";

        $xml .= '<Styles>
    <Style ss:ID="Default" ss:Name="Normal">
      <Alignment ss:Vertical="Center"/>
      <Borders/>
      <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#000000"/>
      <Interior/>
      <NumberFormat/>
      <Protection/>
    </Style>
    <Style ss:ID="title"><Font ss:Bold="1" ss:Size="14" ss:Color="#0B4F6C"/><Alignment ss:Vertical="Center"/></Style>
    <Style ss:ID="subtitle"><Font ss:Italic="1" ss:Size="10" ss:Color="#4B5563"/><Alignment ss:Vertical="Center"/></Style>
    <Style ss:ID="head">
      <Font ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#0284C7" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
      </Borders>
    </Style>
    <Style ss:ID="headCenter">
      <Font ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#0284C7" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
      </Borders>
    </Style>
    <Style ss:ID="headRight">
      <Font ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#0284C7" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
      </Borders>
    </Style>
    <Style ss:ID="cell">
      <Alignment ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
      </Borders>
    </Style>
    <Style ss:ID="cellCenter">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
      </Borders>
    </Style>
    <Style ss:ID="cellNumber">
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <NumberFormat ss:Format="#,##0"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
      </Borders>
    </Style>
    <Style ss:ID="cellCurrency">
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <NumberFormat ss:Format="&quot;Rp&quot;\ #,##0"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
      </Borders>
    </Style>
    <Style ss:ID="totalRow">
      <Font ss:Bold="1" ss:Color="#0F172A"/>
      <Interior ss:Color="#E2E8F0" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94A3B8"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94A3B8"/>
      </Borders>
    </Style>
    <Style ss:ID="totalNumber">
      <Font ss:Bold="1" ss:Color="#0F172A"/>
      <Interior ss:Color="#E2E8F0" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <NumberFormat ss:Format="#,##0"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94A3B8"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94A3B8"/>
      </Borders>
    </Style>
    <Style ss:ID="totalCurrency">
      <Font ss:Bold="1" ss:Color="#0F172A"/>
      <Interior ss:Color="#E2E8F0" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <NumberFormat ss:Format="&quot;Rp&quot;\ #,##0"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94A3B8"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94A3B8"/>
      </Borders>
    </Style>
  </Styles>' . "\n";

        // SHEET 1: RINCIAN PASIEN (PERSIS KOLOM: No, Nama, Jumlah Biaya, No Kwit, No Kartu, No Klaim, Rm, Tanggal Rawat)
        $xml .= '<Worksheet ss:Name="Rincian Pasien"><Table>' . "\n";
        $xml .= '<Column ss:Width="40"/>'   // No.
              . '<Column ss:Width="230"/>'  // Nama
              . '<Column ss:Width="130"/>'  // Jumlah Biaya
              . '<Column ss:Width="95"/>'   // No Kwit
              . '<Column ss:Width="160"/>'  // No Kartu
              . '<Column ss:Width="100"/>'  // No Klaim
              . '<Column ss:Width="80"/>'   // Rm
              . '<Column ss:Width="120"/>'  // Tanggal Rawat
              . '<Column ss:Width="200"/>'  // Nomor Tagihan
              . '<Column ss:Width="240"/>'  // Nama Asuransi
              . "\n";

        $xml .= '<Row><Cell ss:StyleID="title"><Data ss:Type="String">RINCIAN DATA PASIEN INVOICE ASURANSI</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">' . $this->xmlEscape("Periode: {$periode} | Pelayanan: {$status_lanjut} | Total: " . count($detailPasienData) . " Pasien") . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";

        $xml .= '<Row>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">No.</Data></Cell>'
              . '<Cell ss:StyleID="head"><Data ss:Type="String">Nama</Data></Cell>'
              . '<Cell ss:StyleID="headRight"><Data ss:Type="String">Jumlah Biaya</Data></Cell>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">No Kwit</Data></Cell>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">No Kartu</Data></Cell>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">No Klaim</Data></Cell>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">Rm</Data></Cell>'
              . '<Cell ss:StyleID="headCenter"><Data ss:Type="String">Tanggal Rawat</Data></Cell>'
              . '<Cell ss:StyleID="head"><Data ss:Type="String">Nomor Tagihan</Data></Cell>'
              . '<Cell ss:StyleID="head"><Data ss:Type="String">Nama Asuransi</Data></Cell>'
              . '</Row>' . "\n";

        $totalBiayaDetail = 0;
        foreach ($detailPasienData as $idx => $d) {
            $totalBiayaDetail += $d['biaya'];

            $xml .= '<Row>'
                  . $this->xmlCell(($idx + 1) . '.', 'cellCenter')
                  . $this->xmlCell($d['nama'], 'cell')
                  . $this->xmlCell($d['biaya'], 'cellCurrency', 'Number')
                  . $this->xmlCell($d['no_kwit'], 'cellCenter')
                  . $this->xmlCell($d['nomor_kartu'], 'cellCenter')
                  . $this->xmlCell($d['nomor_klaim'], 'cellCenter')
                  . $this->xmlCell($d['no_rkm_medis'], 'cellCenter')
                  . $this->xmlCell($d['tgl_rawat'], 'cellCenter')
                  . $this->xmlCell($d['nomor_tagihan'], 'cell')
                  . $this->xmlCell($d['nama_asuransi'], 'cell')
                  . '</Row>' . "\n";
        }

        // Summary row
        $xml .= '<Row>'
              . '<Cell ss:StyleID="totalRow" ss:MergeAcross="1"><Data ss:Type="String">Total (' . count($detailPasienData) . ' Pasien)</Data></Cell>'
              . $this->xmlCell($totalBiayaDetail, 'totalCurrency', 'Number')
              . '<Cell ss:StyleID="totalRow" ss:MergeAcross="6"><Data ss:Type="String"></Data></Cell>'
              . '</Row>' . "\n";

        $xml .= '</Table></Worksheet>' . "\n";

        // SHEET 2: REKAP INVOICE
        $xml .= '<Worksheet ss:Name="Rekap Invoice"><Table>' . "\n";
        $xml .= '<Column ss:Width="35"/>'   // No
              . '<Column ss:Width="85"/>'   // Tanggal Cetak
              . '<Column ss:Width="190"/>'  // Nomor Tagihan
              . '<Column ss:Width="70"/>'   // Kode Asuransi
              . '<Column ss:Width="260"/>'  // Nama Asuransi
              . '<Column ss:Width="160"/>'  // Periode Tagihan
              . '<Column ss:Width="75"/>'   // Status
              . '<Column ss:Width="85"/>'   // Pasien/Lembar
              . '<Column ss:Width="130"/>'  // Total Tagihan
              . "\n";

        $xml .= '<Row><Cell ss:StyleID="title"><Data ss:Type="String">RUMAH SAKIT BUMI WARAS - REKAP INVOICE ASURANSI</Data></Cell></Row>' . "\n";
        $xml .= '<Row><Cell ss:StyleID="subtitle"><Data ss:Type="String">' . $this->xmlEscape("Periode: {$periode} | Pelayanan: {$status_lanjut} | Total Invoice: " . count($rekapData)) . '</Data></Cell></Row>' . "\n";
        $xml .= '<Row></Row>' . "\n";

        $headers1 = ['No.', 'Tgl Cetak', 'Nomor Tagihan', 'Kode Asuransi', 'Nama Asuransi / Perusahaan', 'Periode Rawat (Close Billing)', 'Pelayanan', 'Jml Pasien', 'Total Tagihan (Rp)'];
        $xml .= '<Row>';
        foreach ($headers1 as $h) {
            $xml .= '<Cell ss:StyleID="head"><Data ss:Type="String">' . $this->xmlEscape($h) . '</Data></Cell>';
        }
        $xml .= '</Row>' . "\n";

        $totalLembarAll = 0;
        $totalBiayaAll = 0;
        foreach ($rekapData as $idx => $r) {
            $totalLembarAll += $r['jml_pasien'];
            $totalBiayaAll += $r['total_tagihan'];

            $xml .= '<Row>'
                  . $this->xmlCell($idx + 1, 'cellCenter', 'Number')
                  . $this->xmlCell(date('d/m/Y', strtotime($r['tgl_cetak'])), 'cellCenter')
                  . $this->xmlCell($r['nomor_tagihan'], 'cell')
                  . $this->xmlCell($r['kode_asuransi'], 'cellCenter')
                  . $this->xmlCell($r['nama_asuransi'], 'cell')
                  . $this->xmlCell($r['periode'], 'cellCenter')
                  . $this->xmlCell($r['status_lanjut'], 'cellCenter')
                  . $this->xmlCell($r['jml_pasien'], 'cellNumber', 'Number')
                  . $this->xmlCell($r['total_tagihan'], 'cellCurrency', 'Number')
                  . '</Row>' . "\n";
        }

        // Summary row
        $xml .= '<Row>'
              . '<Cell ss:StyleID="totalRow" ss:MergeAcross="6"><Data ss:Type="String">TOTAL KESELURUHAN (' . count($rekapData) . ' INVOICE)</Data></Cell>'
              . $this->xmlCell($totalLembarAll, 'totalNumber', 'Number')
              . $this->xmlCell($totalBiayaAll, 'totalCurrency', 'Number')
              . '</Row>' . "\n";

        $xml .= '</Table></Worksheet>' . "\n";
        $xml .= '</Workbook>';

        return $xml;
    }

    private function xmlEscape($v)
    {
        return htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function xmlCell($value, $style = 'cell', $type = 'String')
    {
        return '<Cell ss:StyleID="' . $style . '"><Data ss:Type="' . $type . '">' . $this->xmlEscape($value) . '</Data></Cell>';
    }
}
