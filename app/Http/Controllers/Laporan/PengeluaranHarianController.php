<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MappingSuplierNonMedis;
use App\Models\MasterKategoriNonMedis;

class PengeluaranHarianController extends Controller
{
    public function index(Request $request)
    {
        $filter_type = $request->input('filter_type', 'bulan');
        $bulan = $request->input('bulan', date('m'));
        $tahunSekarang = $request->input('tahun', date('Y'));
        $tglAwal = $request->input('tgl_awal', date('Y-m-d'));
        $tglAkhir = $request->input('tgl_akhir', date('Y-m-d'));

        $tahunSebelumnya = $tahunSekarang - 1;
        $tglAwalSebelumnya = date('Y-m-d', strtotime('-1 year', strtotime($tglAwal)));
        $tglAkhirSebelumnya = date('Y-m-d', strtotime('-1 year', strtotime($tglAkhir)));

        $showPrevYear = $request->has('show_prev_year');

        $querySekarang = DB::table('bayar_pemesanan_non_medis')
            ->join('ipsrspemesanan', 'bayar_pemesanan_non_medis.no_faktur', '=', 'ipsrspemesanan.no_faktur')
            ->join('ipsrssuplier', 'ipsrspemesanan.kode_suplier', '=', 'ipsrssuplier.kode_suplier')
            ->leftJoin('mapping_suplier_non_medis', DB::raw('TRIM(ipsrssuplier.nama_suplier)'), '=', DB::raw('TRIM(mapping_suplier_non_medis.nama_suplier)'));

        if ($filter_type == 'tanggal') {
            $querySekarang->whereBetween('bayar_pemesanan_non_medis.tgl_bayar', [$tglAwal, $tglAkhir]);
            $tahunSekarang = date('Y', strtotime($tglAkhir));
            $tahunSebelumnya = date('Y', strtotime($tglAkhirSebelumnya));
        } else {
            $querySekarang->whereMonth('bayar_pemesanan_non_medis.tgl_bayar', $bulan)
                          ->whereYear('bayar_pemesanan_non_medis.tgl_bayar', $tahunSekarang);
        }

        $queryDetail = clone $querySekarang;

        $dataSekarang = $querySekarang
            ->selectRaw('COALESCE(NULLIF(TRIM(mapping_suplier_non_medis.kategori), ""), "Belum Dipetakan") as kategori, SUM(bayar_pemesanan_non_medis.besar_bayar) as total')
            ->groupBy('kategori')
            ->get()
            ->keyBy('kategori');

        $dataDetail = $queryDetail->select(
            'ipsrspemesanan.no_faktur', 
            'bayar_pemesanan_non_medis.tgl_bayar', 
            'bayar_pemesanan_non_medis.besar_bayar', 
            'ipsrssuplier.nama_suplier', 
            'bayar_pemesanan_non_medis.keterangan', 
            'bayar_pemesanan_non_medis.nama_bayar', 
            'bayar_pemesanan_non_medis.no_bukti',
            DB::raw('COALESCE(NULLIF(TRIM(mapping_suplier_non_medis.kategori), ""), "Belum Dipetakan") as kategori')
        )->orderBy('bayar_pemesanan_non_medis.tgl_bayar', 'asc')->get();

        if ($showPrevYear) {
            $querySebelumnya = DB::table('bayar_pemesanan_non_medis')
                ->join('ipsrspemesanan', 'bayar_pemesanan_non_medis.no_faktur', '=', 'ipsrspemesanan.no_faktur')
                ->join('ipsrssuplier', 'ipsrspemesanan.kode_suplier', '=', 'ipsrssuplier.kode_suplier')
                ->leftJoin('mapping_suplier_non_medis', DB::raw('TRIM(ipsrssuplier.nama_suplier)'), '=', DB::raw('TRIM(mapping_suplier_non_medis.nama_suplier)'));

            if ($filter_type == 'tanggal') {
                $querySebelumnya->whereBetween('bayar_pemesanan_non_medis.tgl_bayar', [$tglAwalSebelumnya, $tglAkhirSebelumnya]);
            } else {
                $querySebelumnya->whereMonth('bayar_pemesanan_non_medis.tgl_bayar', $bulan)
                                ->whereYear('bayar_pemesanan_non_medis.tgl_bayar', $tahunSebelumnya);
            }

            $dataSebelumnya = $querySebelumnya
                ->selectRaw('COALESCE(NULLIF(TRIM(mapping_suplier_non_medis.kategori), ""), "Belum Dipetakan") as kategori, SUM(bayar_pemesanan_non_medis.besar_bayar) as total')
                ->groupBy('kategori')
                ->get()
                ->keyBy('kategori');
        } else {
            $dataSebelumnya = collect();
        }

        $allCategories = collect($dataSekarang->keys()->merge($dataSebelumnya->keys())->unique()->sort()->values());

        // Ambil mapping jenis (BEBAN vs INVESTASI) dari MasterKategoriNonMedis
        $masterKategoriMap = MasterKategoriNonMedis::pluck('jenis', 'nama_kategori')->toArray();

        $totalBebanSekarang = 0;
        $totalBebanSebelumnya = 0;
        $totalInvestasiSekarang = 0;
        $totalInvestasiSebelumnya = 0;

        $reportData = [];
        foreach($allCategories as $kategori) {
            $valSekarang = $dataSekarang->has($kategori) ? (float) $dataSekarang[$kategori]->total : 0;
            $valSebelumnya = $dataSebelumnya->has($kategori) ? (float) $dataSebelumnya[$kategori]->total : 0;

            $reportData[] = [
                'kategori' => $kategori,
                'jumlah_sebelumnya' => $valSebelumnya,
                'jumlah_sekarang' => $valSekarang,
                'grandtotal' => $valSebelumnya + $valSekarang
            ];

            // Tentukan jenis berdasarkan Master Kategori atau fallback
            $jenisKategori = $masterKategoriMap[$kategori] ?? null;
            if (!$jenisKategori) {
                $isInvestasi = (stripos($kategori, 'investasi') !== false || stripos($kategori, 'pembelian alat') !== false);
                $jenisKategori = $isInvestasi ? 'INVESTASI' : 'BEBAN';
            }

            if ($jenisKategori === 'INVESTASI') {
                $totalInvestasiSekarang += $valSekarang;
                $totalInvestasiSebelumnya += $valSebelumnya;
            } else {
                $totalBebanSekarang += $valSekarang;
                $totalBebanSebelumnya += $valSebelumnya;
            }
        }

        $setting = DB::table('setting')->first();
        $masterKategori = MasterKategoriNonMedis::orderBy('nama_kategori')->get();

        return view('laporan.pengeluaranHarian', compact(
            'bulan', 'tahunSekarang', 'tahunSebelumnya', 'reportData',
            'totalBebanSekarang', 'totalBebanSebelumnya', 
            'totalInvestasiSekarang', 'totalInvestasiSebelumnya',
            'filter_type', 'tglAwal', 'tglAkhir', 'showPrevYear', 'dataDetail', 'setting',
            'masterKategori'
        ));
    }

    public function mapping()
    {
        $mappings = MappingSuplierNonMedis::orderBy('nama_suplier')->get();
        $masterKategori = MasterKategoriNonMedis::orderBy('nama_kategori')->get();
        
        // Ambil daftar supplier dari ipsrssuplier untuk dropdown autocomplete jika ada supplier baru
        $allSupliers = DB::table('ipsrssuplier')->select('nama_suplier')->distinct()->orderBy('nama_suplier')->pluck('nama_suplier');

        return view('laporan.mappingSuplier', compact('mappings', 'masterKategori', 'allSupliers'));
    }

    public function simpanMapping(Request $request)
    {
        $rawData = $request->input('raw_data');
        if($rawData) {
            $lines = explode("\n", str_replace("\r", "", $rawData));
            foreach($lines as $line) {
                $parts = explode("\t", $line);
                if(count($parts) >= 2) {
                    $nama_suplier = trim($parts[0]);
                    $kategori = trim($parts[1]);
                    
                    if(!empty($nama_suplier) && !empty($kategori)) {
                        MappingSuplierNonMedis::updateOrCreate(
                            ['nama_suplier' => $nama_suplier],
                            ['kategori' => $kategori]
                        );

                        // Otomatis masukkan ke MasterKategori jika belum terdaftar
                        MasterKategoriNonMedis::firstOrCreate(
                            ['nama_kategori' => $kategori],
                            ['jenis' => (stripos($kategori, 'investasi') !== false || stripos($kategori, 'pembelian alat') !== false) ? 'INVESTASI' : 'BEBAN']
                        );
                    }
                }
            }
            return redirect()->back()->with('success', 'Data mapping berhasil diimport.');
        }

        return redirect()->back()->with('error', 'Data tidak boleh kosong.');
    }

    // CRUD Master Kategori
    public function storeMasterKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'jenis' => 'required|in:BEBAN,INVESTASI'
        ]);

        $nama = trim($request->input('nama_kategori'));
        $jenis = $request->input('jenis');

        MasterKategoriNonMedis::updateOrCreate(
            ['nama_kategori' => $nama],
            ['jenis' => $jenis]
        );

        return redirect()->back()->with('success', "Kategori '$nama' berhasil disimpan.");
    }

    public function updateMasterKategori(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'jenis' => 'required|in:BEBAN,INVESTASI'
        ]);

        $kat = MasterKategoriNonMedis::findOrFail($id);
        $oldNama = $kat->nama_kategori;
        $newNama = trim($request->input('nama_kategori'));

        $kat->update([
            'nama_kategori' => $newNama,
            'jenis' => $request->input('jenis')
        ]);

        // Update kategori di mapping_suplier_non_medis jika namanya berubah
        if ($oldNama !== $newNama) {
            MappingSuplierNonMedis::where('kategori', $oldNama)->update(['kategori' => $newNama]);
        }

        return redirect()->back()->with('success', "Kategori '$newNama' berhasil diperbarui.");
    }

    public function destroyMasterKategori($id)
    {
        $kat = MasterKategoriNonMedis::findOrFail($id);
        $nama = $kat->nama_kategori;
        $kat->delete();

        return redirect()->back()->with('success', "Kategori '$nama' berhasil dihapus dari master.");
    }

    // Update Mapping Supplier per baris (baik dari menu mapping maupun modal detail)
    public function updateMappingSuplier(Request $request)
    {
        $namaSuplier = trim($request->input('nama_suplier'));
        $kategori = trim($request->input('kategori'));

        if (empty($namaSuplier)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Nama supplier tidak boleh kosong'], 422);
            }
            return redirect()->back()->with('error', 'Nama supplier tidak boleh kosong.');
        }

        if (empty($kategori)) {
            // Hapus mapping jika dikosongkan
            MappingSuplierNonMedis::where('nama_suplier', $namaSuplier)->delete();
        } else {
            MappingSuplierNonMedis::updateOrCreate(
                ['nama_suplier' => $namaSuplier],
                ['kategori' => $kategori]
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'nama_suplier' => $namaSuplier,
                'kategori' => $kategori ?: 'Belum Dipetakan',
                'message' => "Mapping untuk supplier '$namaSuplier' berhasil diperbarui!"
            ]);
        }

        return redirect()->back()->with('success', "Mapping untuk supplier '$namaSuplier' berhasil disimpan.");
    }

    public function destroyMappingSuplier($id)
    {
        $map = MappingSuplierNonMedis::findOrFail($id);
        $nama = $map->nama_suplier;
        $map->delete();

        return redirect()->back()->with('success', "Mapping supplier '$nama' berhasil dihapus.");
    }

    // Endpoint AJAX untuk ubah kategori langsung dari popup Detail Pembayaran
    public function updateKategoriDetail(Request $request)
    {
        return $this->updateMappingSuplier($request);
    }
}
