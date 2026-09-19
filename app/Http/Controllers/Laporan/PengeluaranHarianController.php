<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MappingSuplierNonMedis;

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
            ->selectRaw('COALESCE(mapping_suplier_non_medis.kategori, "Belum Dipetakan") as kategori, SUM(bayar_pemesanan_non_medis.besar_bayar) as total')
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
            DB::raw('COALESCE(mapping_suplier_non_medis.kategori, "Belum Dipetakan") as kategori')
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
                ->selectRaw('COALESCE(mapping_suplier_non_medis.kategori, "Belum Dipetakan") as kategori, SUM(bayar_pemesanan_non_medis.besar_bayar) as total')
                ->groupBy('kategori')
                ->get()
                ->keyBy('kategori');
        } else {
            $dataSebelumnya = collect();
        }

        $allCategories = collect($dataSekarang->keys()->merge($dataSebelumnya->keys())->unique()->sort()->values());

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

            if (stripos($kategori, 'investasi') !== false || stripos($kategori, 'pembelian alat') !== false) {
                $totalInvestasiSekarang += $valSekarang;
                $totalInvestasiSebelumnya += $valSebelumnya;
            } else {
                $totalBebanSekarang += $valSekarang;
                $totalBebanSebelumnya += $valSebelumnya;
            }
        }

        $setting = DB::table('setting')->first();

        return view('laporan.pengeluaranHarian', compact(
            'bulan', 'tahunSekarang', 'tahunSebelumnya', 'reportData',
            'totalBebanSekarang', 'totalBebanSebelumnya', 
            'totalInvestasiSekarang', 'totalInvestasiSebelumnya',
            'filter_type', 'tglAwal', 'tglAkhir', 'showPrevYear', 'dataDetail', 'setting'
        ));
    }

    public function mapping()
    {
        $mappings = MappingSuplierNonMedis::orderBy('nama_suplier')->get();
        return view('laporan.mappingSuplier', compact('mappings'));
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
                    }
                }
            }
            return redirect()->back()->with('success', 'Data mapping berhasil diimport.');
        }

        return redirect()->back()->with('error', 'Data tidak boleh kosong.');
    }
}
