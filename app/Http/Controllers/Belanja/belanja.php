<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use PDF;

class Belanja extends Controller
{
    private const STATUS_AKTIF        = 'AKTIF';
    private const STATUS_NONAKTIF     = 'NONAKTIF';
    private const STATUS_BARANG_AKTIF = '1';
    private const NAMA_MCU            = 'YELLOW CAP SST WITH GEL 4 ML';

    private const CACHE_TTL_MASTER = 3600; // 1 jam
    private const CACHE_TTL_REPORT = 600;  // 10 menit

    /**
     * Base query untuk tabel databarang + join golongan/kategori/diskon.
     */
    private function queryBarang()
    {
        return DB::table('databarang')
            ->leftJoin('golongan_barang', 'databarang.kode_golongan', '=', 'golongan_barang.kode')
            ->leftJoin('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
            ->leftJoin(DB::raw('(SELECT kode_brng, MAX(dis) as dis, MAX(besardis) as besardis FROM detailpesan GROUP BY kode_brng) as dp'), 'databarang.kode_brng', '=', 'dp.kode_brng')
            ->select(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.h_beli',
                'databarang.ralan',
                'databarang.dasar',
                'databarang.kode_sat',
                'golongan_barang.nama as golongan_nama',
                'kategori_barang.nama as kategori_nama',
                'dp.dis as dis',
                'dp.besardis as besardis'
            );
    }

    private function sanitizeDate(mixed $value, string $fallback): string
    {
        if (!is_string($value)) {
            return $fallback;
        }
        $value = trim($value);
        if ($value === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $fallback;
        }
        return $value;
    }

    public function index(Request $request): \Illuminate\View\View
    {
        $tanggal_awal  = $this->sanitizeDate($request->tanggal_awal, date('Y-m-d'));
        $tanggal_akhir = $this->sanitizeDate($request->tanggal_akhir, date('Y-m-d'));
        if ($tanggal_awal > $tanggal_akhir) {
            [$tanggal_awal, $tanggal_akhir] = [$tanggal_akhir, $tanggal_awal];
        }
        $tanggalSebelumnya = date('Y-m-d', strtotime($tanggal_awal . ' -1 day'));

        $supplierFilter = is_string($request->supplier) && $request->supplier !== ''
            ? $request->supplier
            : null;
        $filterHarga = is_string($request->filter_harga) ? $request->filter_harga : null;
        $filterType  = is_string($request->filter_type) ? $request->filter_type : null;

        // Data master (di-cache karena jarang berubah)
        $bangsal          = $this->getBangsalCached();
        $nonaktif_bangsal = $this->getNonaktifBangsalCached();
        $supplierList     = $this->getSupplierListCached();
        $selectedBangsal  = $bangsal->whereNotIn('kd_bangsal', $nonaktif_bangsal)->values();

        // Data report (di-cache per-range tanggal)
        $barang                  = $this->getBarang();
        $stok_lokasi             = $this->getStokLokasiCached();
        $total_pengeluaran       = $this->getTotalPengeluaranCached($tanggal_awal, $tanggal_akhir);
        $riwayat_stok_sebelumnya = $this->getRiwayatStokCached($tanggalSebelumnya);
        $data_batch_beli_map     = $this->getDataBatchBeliCached($tanggal_awal, $tanggal_akhir, $supplierFilter);
        $bangsal_kd_list         = $selectedBangsal->pluck('kd_bangsal')->all();

        // Bangun baris laporan + map stok per-bangsal dalam satu pass
        $built   = $this->buildRows(
            $barang,
            $stok_lokasi,
            $bangsal_kd_list,
            $total_pengeluaran,
            $riwayat_stok_sebelumnya,
            $data_batch_beli_map
        );
        $rows    = $built['rows'];
        $stokPerBangsalMap = $built['stok_per_bangsal'];

        $hanyaKebutuhan = $request->hanya_kebutuhan == '1';
        if ($hanyaKebutuhan) {
            $rows = array_filter($rows, fn($r) => $r['kebutuhan'] > 0);
        }

        // Hitung ringkasan (top/low + grand total) dalam satu pass
        $summary = $this->computeSummary($rows);

        // Terapkan sorting sesuai filter (default: nilai_belanja desc)
        $rows = $this->applySort($rows, $filterHarga, $filterType);

        $filterLabelMap = [
            'pengeluaran_terbanyak' => 'Pengeluaran Terbanyak',
            'pengeluaran_terdikit'  => 'Pengeluaran Terdikit',
            'stok_terbanyak'        => 'Stok Terbanyak',
            'stok_terdikit'         => 'Stok Terdikit',
            'nilai_terbanyak'       => 'Nilai Belanja Terbanyak',
            'nilai_terendah'        => 'Nilai Belanja Terdikit',
            'kebutuhan_terbanyak'   => 'Rencana Pembelian Terbanyak',
            'kebutuhan_terdikit'    => 'Rencana Pembelian Terdikit',
        ];

        return view('belanja.belanja', compact(
            'tanggal_awal',
            'tanggal_akhir',
            'tanggalSebelumnya',
            'bangsal',
            'nonaktif_bangsal',
            'selectedBangsal',
            'supplierList',
            'rows',
            'stokPerBangsalMap',
            'summary',
            'filterType',
            'filterLabelMap'
        ));
    }

    public function cetakPdf(Request $request)
    {
        $tanggal_awal  = $this->sanitizeDate($request->tanggal_awal, date('Y-m-d'));
        $tanggal_akhir = $this->sanitizeDate($request->tanggal_akhir, date('Y-m-d'));
        if ($tanggal_awal > $tanggal_akhir) {
            [$tanggal_awal, $tanggal_akhir] = [$tanggal_akhir, $tanggal_awal];
        }
        $tanggalSebelumnya = date('Y-m-d', strtotime($tanggal_awal . ' -1 day'));

        $supplierFilter = is_string($request->supplier) && $request->supplier !== ''
            ? $request->supplier
            : null;
        $filterHarga = is_string($request->filter_harga) ? $request->filter_harga : null;
        $filterType  = is_string($request->filter_type) ? $request->filter_type : null;

        // Data master (di-cache karena jarang berubah)
        $bangsal          = $this->getBangsalCached();
        $nonaktif_bangsal = $this->getNonaktifBangsalCached();
        $supplierList     = $this->getSupplierListCached();
        $selectedBangsal  = $bangsal->whereNotIn('kd_bangsal', $nonaktif_bangsal)->values();

        // Data report (di-cache per-range tanggal)
        $barang                  = $this->getBarang();
        $stok_lokasi             = $this->getStokLokasiCached();
        $total_pengeluaran       = $this->getTotalPengeluaranCached($tanggal_awal, $tanggal_akhir);
        $riwayat_stok_sebelumnya = $this->getRiwayatStokCached($tanggalSebelumnya);
        $data_batch_beli_map     = $this->getDataBatchBeliCached($tanggal_awal, $tanggal_akhir, $supplierFilter);
        $bangsal_kd_list         = $selectedBangsal->pluck('kd_bangsal')->all();

        // Bangun baris laporan + map stok per-bangsal dalam satu pass
        $built   = $this->buildRows(
            $barang,
            $stok_lokasi,
            $bangsal_kd_list,
            $total_pengeluaran,
            $riwayat_stok_sebelumnya,
            $data_batch_beli_map
        );
        $rows    = $built['rows'];
        $stokPerBangsalMap = $built['stok_per_bangsal'];

        $hanyaKebutuhan = $request->hanya_kebutuhan == '1';
        if ($hanyaKebutuhan) {
            $rows = array_filter($rows, fn($r) => $r['kebutuhan'] > 0);
        }

        // Hitung ringkasan (top/low + grand total) dalam satu pass
        $summary = $this->computeSummary($rows);

        // Terapkan sorting sesuai filter (default: nilai_belanja desc)
        $rows = $this->applySort($rows, $filterHarga, $filterType);

        $getSetting = DB::table('setting')->first();

        $pdf = PDF::loadView('belanja.pdf', compact(
            'tanggal_awal',
            'tanggal_akhir',
            'selectedBangsal',
            'rows',
            'stokPerBangsalMap',
            'summary',
            'filterType',
            'getSetting'
        ));

        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('Laporan_Belanja_' . $tanggal_awal . '_sampai_' . $tanggal_akhir . '.pdf');
    }

    public function toggleBangsal(Request $request): \Illuminate\Http\JsonResponse
    {
        $status = $request->boolean('status');
        $kd     = $request->input('kd_bangsal');

        if (!is_string($kd) || $kd === '') {
            return response()->json(['success' => false, 'message' => 'kd_bangsal wajib diisi'], 422);
        }

        DB::table('nonaktif_bangsal')->updateOrInsert(
            ['kd_bangsal' => $kd],
            ['keterangan' => $status ? self::STATUS_AKTIF : self::STATUS_NONAKTIF]
        );

        // Invalidate cache master karena status gudang berubah
        Cache::forget('belanja.bangsal');
        Cache::forget('belanja.nonaktif_bangsal');

        return response()->json(['success' => true]);
    }

    /* ============================================================
     | MASTER DATA HELPERS (cached)
     |============================================================ */

    private function getBangsalCached(): Collection
    {
        return Cache::remember('belanja.bangsal', self::CACHE_TTL_MASTER, function () {
            return DB::table('gudangbarang as g')
                ->leftJoin('bangsal as b', 'b.kd_bangsal', '=', 'g.kd_bangsal')
                ->select('g.kd_bangsal', DB::raw('COALESCE(b.nm_bangsal, g.kd_bangsal) as nm_bangsal'))
                ->distinct()
                ->orderBy('g.kd_bangsal')
                ->get();
        });
    }

    private function getNonaktifBangsalCached(): array
    {
        return Cache::remember('belanja.nonaktif_bangsal', self::CACHE_TTL_MASTER, function () {
            return DB::table('nonaktif_bangsal')
                ->where('keterangan', self::STATUS_NONAKTIF)
                ->pluck('kd_bangsal')
                ->toArray();
        });
    }

    private function getSupplierListCached(): Collection
    {
        return Cache::remember('belanja.supplier_list', self::CACHE_TTL_MASTER, function () {
            return DB::table('datasuplier')
                ->select('kode_suplier', 'nama_suplier')
                ->orderBy('nama_suplier')
                ->get();
        });
    }

    private function getStokLokasiCached(): Collection
    {
        return Cache::remember('belanja.stok_lokasi', self::CACHE_TTL_MASTER, function () {
            return DB::table('gudangbarang')
                ->select('kode_brng', 'kd_bangsal', 'stok')
                ->get()
                ->groupBy('kode_brng');
        });
    }

    /* ============================================================
     | REPORT HELPERS (cached per parameter)
     |============================================================ */

    private function getBarang(): Collection
    {
        return $this->queryBarang()
            ->where('databarang.status', self::STATUS_BARANG_AKTIF)
            ->get()
            ->keyBy('kode_brng');
    }

    private function getTotalPengeluaranCached(string $tglAwal, string $tglAkhir): Collection
    {
        $key = "belanja.pengeluaran.{$tglAwal}.{$tglAkhir}";

        return Cache::remember($key, self::CACHE_TTL_REPORT, function () use ($tglAwal, $tglAkhir) {
            $sub = DB::table(function ($q) use ($tglAwal, $tglAkhir) {
                $q->select(
                    'detail_pengeluaran_obat_bhp.kode_brng',
                    DB::raw('SUM(detail_pengeluaran_obat_bhp.jumlah) as jumlah')
                )
                    ->from('pengeluaran_obat_bhp')
                    ->join('detail_pengeluaran_obat_bhp', 'detail_pengeluaran_obat_bhp.no_keluar', '=', 'pengeluaran_obat_bhp.no_keluar')
                    ->whereBetween('pengeluaran_obat_bhp.tanggal', [$tglAwal, $tglAkhir])
                    ->groupBy('detail_pengeluaran_obat_bhp.kode_brng')
                    ->unionAll(
                        DB::table('detail_pemberian_obat')
                            ->select('kode_brng', DB::raw('SUM(jml) as jumlah'))
                            ->whereBetween('tgl_perawatan', [$tglAwal, $tglAkhir])
                            ->groupBy('kode_brng')
                    )
                    ->unionAll(
                        DB::table('resep_pulang')
                            ->select('kode_brng', DB::raw('SUM(jml_barang) as jumlah'))
                            ->whereBetween('tanggal', [$tglAwal, $tglAkhir])
                            ->groupBy('kode_brng')
                    )
                    ->unionAll(
                        DB::table('penjualan')
                            ->join('detailjual', 'detailjual.nota_jual', '=', 'penjualan.nota_jual')
                            ->select('detailjual.kode_brng', DB::raw('SUM(detailjual.jumlah) as jumlah'))
                            ->whereBetween('penjualan.tgl_jual', [$tglAwal, $tglAkhir])
                            ->groupBy('detailjual.kode_brng')
                    );
            }, 'x')
                ->select('kode_brng', DB::raw('SUM(jumlah) as total_pengeluaran'))
                ->groupBy('kode_brng');

            return $sub->pluck('total_pengeluaran', 'kode_brng');
        });
    }

    private function getRiwayatStokCached(string $tanggal): Collection
    {
        $key = "belanja.riwayat_stok.{$tanggal}";

        return Cache::remember($key, self::CACHE_TTL_REPORT, function () use ($tanggal) {
            return DB::table('riwayat_barang_medis')
                ->select('kode_brng', 'stok_akhir', 'tanggal')
                ->whereDate('tanggal', $tanggal)
                ->get()
                ->groupBy('kode_brng')
                ->map(fn($items) => (float) ($items->first()->stok_akhir ?? 0));
        });
    }

    private function getDataBatchBeliCached(string $tglAwal, string $tglAkhir, ?string $supplier): Collection
    {
        $supplier = $supplier ?? 'all';
        $key = "belanja.batch_beli.{$tglAwal}.{$tglAkhir}.{$supplier}";

        return Cache::remember($key, self::CACHE_TTL_REPORT, function () use ($tglAwal, $tglAkhir, $supplier) {
            $rows = DB::table('data_batch')
                ->join('detailpesan', 'data_batch.no_batch', '=', 'detailpesan.no_batch')
                ->join('pemesanan', 'detailpesan.no_faktur', '=', 'pemesanan.no_faktur')
                ->join('datasuplier', 'pemesanan.kode_suplier', '=', 'datasuplier.kode_suplier')
                ->select(
                    'data_batch.kode_brng as kode_brng',
                    'detailpesan.no_faktur as no_faktur',
                    'datasuplier.kode_suplier as kode_suplier',
                    'datasuplier.nama_suplier as nama_suplier',
                    'data_batch.no_batch as no_batch',
                    'data_batch.tgl_beli as tgl_beli',
                    DB::raw('SUM(detailpesan.jumlah) as total_jumlahbeli')
                )
                ->whereBetween('pemesanan.tgl_pesan', [$tglAwal, $tglAkhir])
                ->whereBetween('data_batch.tgl_beli', [$tglAwal, $tglAkhir])
                ->when($supplier !== 'all', fn($q) => $q->where('datasuplier.kode_suplier', $supplier))
                ->groupBy(
                    'data_batch.kode_brng',
                    'detailpesan.no_faktur',
                    'datasuplier.kode_suplier',
                    'datasuplier.nama_suplier',
                    'data_batch.no_batch',
                    'data_batch.tgl_beli'
                )
                ->get();

            return $rows->groupBy('kode_brng')->map(fn($items) => [
                'total_jumlahbeli' => $items->sum('total_jumlahbeli'),
                'supplier' => $items->map(fn($item) => [
                    'no_faktur'    => $item->no_faktur,
                    'kode_suplier' => $item->kode_suplier,
                    'nama_suplier' => $item->nama_suplier,
                    'no_batch'     => $item->no_batch,
                    'tgl_beli'     => $item->tgl_beli,
                    'jumlah'       => $item->total_jumlahbeli,
                ])->values(),
            ]);
        });
    }

    /* ============================================================
     | ROW BUILDER + SUMMARY (single pass)
     |============================================================ */

    /**
     * Bangun seluruh baris laporan + map stok per-bangsal dalam satu pass.
     *
     * @return array{rows: array, stok_per_bangsal: array}
     */
    private function buildRows(
        Collection $barang,
        Collection $stokLokasi,
        array $bangsalKdList,
        Collection $totalPengeluaran,
        Collection $riwayatStok,
        Collection $batchBeliMap
    ): array {
        $rows = [];
        $stokPerBangsalMap = [];

        // Index batchBeliMap: kode_brng => total_jumlahbeli & supplier_names
        $batchBeliTotal = [];
        $batchBeliSupplierNames = [];
        foreach ($batchBeliMap as $kode => $info) {
            $batchBeliTotal[$kode] = $info['total_jumlahbeli'] ?? 0;
            $batchBeliSupplierNames[$kode] = collect($info['supplier'] ?? [])
                ->pluck('nama_suplier')
                ->filter()
                ->unique()
                ->implode(', ');
        }

        foreach ($barang as $kode => $item) {
            $stokBarang = $stokLokasi[$kode] ?? collect();

            // Stok per bangsal: bangun map dalam satu pass
            $stokPerBangsal = [];
            $stok = 0;
            foreach ($stokBarang as $s) {
                if (in_array($s->kd_bangsal, $bangsalKdList, true)) {
                    $stokPerBangsal[$s->kd_bangsal] = (int) $s->stok;
                    $stok += (int) $s->stok;
                }
            }
            foreach ($bangsalKdList as $kd) {
                $stokPerBangsal[$kd] = $stokPerBangsal[$kd] ?? 0;
            }
            $stokPerBangsalMap[$kode] = $stokPerBangsal;

            $keluar    = (int) ($totalPengeluaran[$kode] ?? 0);
            $kebutuhan = max($keluar - $stok, 0);
            $nilai     = $kebutuhan * (float) $item->h_beli;
            $stokPrev  = (float) ($riwayatStok[$kode] ?? 0);

            $rows[] = [
                'kode_brng'       => $kode,
                'nama_brng'       => $item->nama_brng,
                'kategori_nama'   => $item->kategori_nama ?? null,
                'golongan_nama'   => $item->golongan_nama ?? null,
                'kode_sat'        => $item->kode_sat,
                'harga_beli'      => (float) $item->h_beli,
                'ralan'           => (float) $item->ralan,
                'dis'             => $item->dis ?? null,
                'besardis'        => $item->besardis ?? null,
                'stok'            => $stok,
                'stok_sebelumnya' => $stokPrev,
                'pengeluaran'     => $keluar,
                'kebutuhan'       => $kebutuhan,
                'nilai_belanja'   => $nilai,
                'jumlah_beli'     => (int) ($batchBeliTotal[$kode] ?? 0),
                'supplier_names'  => $batchBeliSupplierNames[$kode] ?? '',
            ];
        }

        return ['rows' => $rows, 'stok_per_bangsal' => $stokPerBangsalMap];
    }

    /**
     * Hitung top/low + grand total dalam satu pass.
     */
    private function computeSummary(array $rows): array
    {
        $grandStok = $grandStokSebelumnya = $grandKeluar = $grandKebutuhan = 0;
        $topPengeluaran = $lowPengeluaran = $topStok = $lowStok = null;

        foreach ($rows as $r) {
            $grandStok           += $r['stok'];
            $grandStokSebelumnya += $r['stok_sebelumnya'];
            $grandKeluar         += $r['pengeluaran'];
            $grandKebutuhan      += $r['kebutuhan'];

            if ($topPengeluaran === null || $r['pengeluaran'] > $topPengeluaran['pengeluaran']) {
                $topPengeluaran = $r;
            }
            if ($lowPengeluaran === null || ($r['pengeluaran'] > 0 && $r['pengeluaran'] < $lowPengeluaran['pengeluaran'])) {
                $lowPengeluaran = $r;
            }
            if ($topStok === null || $r['stok'] > $topStok['stok']) {
                $topStok = $r;
            }
            if ($lowStok === null || $r['stok'] < $lowStok['stok']) {
                $lowStok = $r;
            }
        }

        return [
            'grand_stok'             => $grandStok,
            'grand_stok_sebelumnya'  => $grandStokSebelumnya,
            'grand_keluar'           => $grandKeluar,
            'grand_kebutuhan'        => $grandKebutuhan,
            'top_pengeluaran'        => $topPengeluaran,
            'low_pengeluaran'        => $lowPengeluaran,
            'top_stok'               => $topStok,
            'low_stok'               => $lowStok,
        ];
    }

    /**
     * Terapkan sorting sesuai filter. Default: nilai_belanja desc.
     */
    private function applySort(array $rows, ?string $filterHarga, ?string $filterType): array
    {
        $field = 'nilai_belanja';
        $dir   = -1;

        if ($filterHarga === 'termahal') {
            $field = 'harga_beli';
            $dir   = -1;
        } elseif ($filterHarga === 'termurah') {
            $field = 'harga_beli';
            $dir   = 1;
        } elseif ($filterType) {
            $map = [
                'pengeluaran_terbanyak' => ['pengeluaran', -1],
                'pengeluaran_terdikit'  => ['pengeluaran', 1],
                'stok_terbanyak'        => ['stok', -1],
                'stok_terdikit'         => ['stok', 1],
                'nilai_terbanyak'       => ['nilai_belanja', -1],
                'nilai_terendah'        => ['nilai_belanja', 1],
                'kebutuhan_terbanyak'   => ['kebutuhan', -1],
                'kebutuhan_terdikit'    => ['kebutuhan', 1],
            ];
            if (isset($map[$filterType])) {
                [$field, $dir] = $map[$filterType];
            }
        }

        usort($rows, function ($a, $b) use ($field, $dir) {
            $av = $a[$field] ?? 0;
            $bv = $b[$field] ?? 0;
            return ($av <=> $bv) * $dir;
        });

        return $rows;
    }
    public function getPengeluaranDetail(Request $request): \Illuminate\Http\JsonResponse
    {
        $kode_brng = $request->kode_brng;
        $tglAwal = $this->sanitizeDate($request->tanggal_awal, date('Y-m-d'));
        $tglAkhir = $this->sanitizeDate($request->tanggal_akhir, date('Y-m-d'));

        if (!$kode_brng) {
            return response()->json([]);
        }

        $pengeluaran = DB::table('pengeluaran_obat_bhp')
            ->join('detail_pengeluaran_obat_bhp', 'detail_pengeluaran_obat_bhp.no_keluar', '=', 'pengeluaran_obat_bhp.no_keluar')
            ->join('bangsal', 'pengeluaran_obat_bhp.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->select(
                'pengeluaran_obat_bhp.tanggal as tanggal',
                'pengeluaran_obat_bhp.no_keluar as no_rawat',
                'bangsal.nm_bangsal', 
                DB::raw("'Pengeluaran BHP' as tujuan"),
                DB::raw("'-' as nm_pasien"),
                DB::raw('SUM(detail_pengeluaran_obat_bhp.jumlah) as jumlah')
            )
            ->whereBetween('pengeluaran_obat_bhp.tanggal', [$tglAwal, $tglAkhir])
            ->where('detail_pengeluaran_obat_bhp.kode_brng', $kode_brng)
            ->groupBy('pengeluaran_obat_bhp.tanggal', 'pengeluaran_obat_bhp.no_keluar', 'bangsal.nm_bangsal');

        $pemberian = DB::table('detail_pemberian_obat')
            ->join('bangsal', 'detail_pemberian_obat.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('reg_periksa', 'detail_pemberian_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select(
                'detail_pemberian_obat.tgl_perawatan as tanggal',
                'detail_pemberian_obat.no_rawat',
                'bangsal.nm_bangsal', 
                DB::raw("'Pemberian Obat' as tujuan"),
                'pasien.nm_pasien',
                DB::raw('SUM(detail_pemberian_obat.jml) as jumlah')
            )
            ->whereBetween('detail_pemberian_obat.tgl_perawatan', [$tglAwal, $tglAkhir])
            ->where('detail_pemberian_obat.kode_brng', $kode_brng)
            ->groupBy('detail_pemberian_obat.tgl_perawatan', 'detail_pemberian_obat.no_rawat', 'bangsal.nm_bangsal', 'pasien.nm_pasien');

        $resep = DB::table('resep_pulang')
            ->join('bangsal', 'resep_pulang.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('reg_periksa', 'resep_pulang.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select(
                'resep_pulang.tanggal as tanggal',
                'resep_pulang.no_rawat',
                'bangsal.nm_bangsal', 
                DB::raw("'Resep Pulang' as tujuan"),
                'pasien.nm_pasien',
                DB::raw('SUM(resep_pulang.jml_barang) as jumlah')
            )
            ->whereBetween('resep_pulang.tanggal', [$tglAwal, $tglAkhir])
            ->where('resep_pulang.kode_brng', $kode_brng)
            ->groupBy('resep_pulang.tanggal', 'resep_pulang.no_rawat', 'bangsal.nm_bangsal', 'pasien.nm_pasien');

        $penjualan = DB::table('penjualan')
            ->join('detailjual', 'detailjual.nota_jual', '=', 'penjualan.nota_jual')
            ->join('bangsal', 'penjualan.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->select(
                'penjualan.tgl_jual as tanggal',
                'penjualan.nota_jual as no_rawat',
                'bangsal.nm_bangsal', 
                DB::raw("'Penjualan Bebas' as tujuan"),
                DB::raw("IFNULL(penjualan.nm_pasien, 'Pasien Umum') as nm_pasien"),
                DB::raw('SUM(detailjual.jumlah) as jumlah')
            )
            ->whereBetween('penjualan.tgl_jual', [$tglAwal, $tglAkhir])
            ->where('detailjual.kode_brng', $kode_brng)
            ->groupBy('penjualan.tgl_jual', 'penjualan.nota_jual', 'bangsal.nm_bangsal', 'penjualan.nm_pasien');

        $union = $pengeluaran
            ->unionAll($pemberian)
            ->unionAll($resep)
            ->unionAll($penjualan);

        $results = DB::table(DB::raw("({$union->toSql()}) as sub"))
            ->mergeBindings($union)
            ->select('tanggal', 'no_rawat', 'nm_bangsal', 'nm_pasien', 'tujuan', DB::raw('SUM(jumlah) as total_jumlah'))
            ->groupBy('tanggal', 'no_rawat', 'nm_bangsal', 'nm_pasien', 'tujuan')
            ->orderByDesc('tanggal')
            ->orderByDesc('total_jumlah')
            ->get();

        return response()->json($results);
    }
}
