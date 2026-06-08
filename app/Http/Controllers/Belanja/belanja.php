<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Belanja extends Controller
{
    public function index(Request $request)
    {
        $tanggal_awal  = $request->tanggal_awal ?? date('Y-m-d');
        $tanggal_akhir = $request->tanggal_akhir ?? date('Y-m-d');

        /*
        |-----------------------
        | DATA BARANG
        |-----------------------
        */
        $barang = DB::table('databarang')
            ->select('kode_brng', 'nama_brng', 'h_beli', 'kode_sat')
            ->get()
            ->keyBy('kode_brng');

        /*
        |-----------------------
        | BANGSAL (SUDAH JOIN NAMA)
        |-----------------------
        */
        $bangsal = DB::table('gudangbarang as g')
            ->leftJoin('bangsal as b', 'b.kd_bangsal', '=', 'g.kd_bangsal')
            ->select('g.kd_bangsal', DB::raw('COALESCE(b.nm_bangsal, g.kd_bangsal) as nm_bangsal'))
            ->distinct()
            ->orderBy('g.kd_bangsal')
            ->get();

        /*
        |-----------------------
        | NONAKTIF
        |-----------------------
        */
        $nonaktif_bangsal = DB::table('nonaktif_bangsal')
            ->where('keterangan', 'NONAKTIF')
            ->pluck('kd_bangsal')
            ->toArray();

        /*
        |-----------------------
        | STOK
        |-----------------------
        */
        $stok_lokasi = DB::table('gudangbarang')
            ->select('kode_brng', 'kd_bangsal', 'stok')
            ->get()
            ->groupBy('kode_brng');

        /*
        |-----------------------
        | PENGELUARAN
        |-----------------------
        */
        $total_pengeluaran = DB::table(function ($query) use ($tanggal_awal, $tanggal_akhir) {

            $query->select(
                    'detail_pengeluaran_obat_bhp.kode_brng',
                    DB::raw('SUM(detail_pengeluaran_obat_bhp.jumlah) as jumlah')
                )
                ->from('pengeluaran_obat_bhp')
                ->join('detail_pengeluaran_obat_bhp', 'detail_pengeluaran_obat_bhp.no_keluar', '=', 'pengeluaran_obat_bhp.no_keluar')
                ->whereBetween('pengeluaran_obat_bhp.tanggal', [$tanggal_awal, $tanggal_akhir])
                ->groupBy('detail_pengeluaran_obat_bhp.kode_brng')

                ->unionAll(
                    DB::table('detail_pemberian_obat')
                        ->select('kode_brng', DB::raw('SUM(jml) as jumlah'))
                        ->whereBetween('tgl_perawatan', [$tanggal_awal, $tanggal_akhir])
                        ->groupBy('kode_brng')
                )

                ->unionAll(
                    DB::table('resep_pulang')
                        ->select('kode_brng', DB::raw('SUM(jml_barang) as jumlah'))
                        ->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
                        ->groupBy('kode_brng')
                )

                ->unionAll(
                    DB::table('penjualan')
                        ->join('detailjual', 'detailjual.nota_jual', '=', 'penjualan.nota_jual')
                        ->select('detailjual.kode_brng', DB::raw('SUM(detailjual.jumlah) as jumlah'))
                        ->whereBetween('penjualan.tgl_jual', [$tanggal_awal, $tanggal_akhir])
                        ->groupBy('detailjual.kode_brng')
                );

        }, 'x')
        ->select('kode_brng', DB::raw('SUM(jumlah) as total_pengeluaran'))
        ->groupBy('kode_brng')
        ->pluck('total_pengeluaran', 'kode_brng');

        return view('belanja.belanja', compact(
            'tanggal_awal',
            'tanggal_akhir',
            'barang',
            'bangsal',
            'stok_lokasi',
            'total_pengeluaran',
            'nonaktif_bangsal'
        ));
    }

    /*
    |-----------------------
    | TOGGLE STATUS
    |-----------------------
    */
    public function toggleBangsal(Request $request)
    {
        DB::table('nonaktif_bangsal')->updateOrInsert(
            ['kd_bangsal' => $request->kd_bangsal],
            ['keterangan' => $request->status ? 'AKTIF' : 'NONAKTIF']
        );

        return response()->json(['success' => true]);
    }
}