<?php

namespace App\Http\Controllers\Belanja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class belanja extends Controller
{
    public function index(Request $request)
    {
        $tanggal_awal =
            $request->tanggal_awal ??
            date('Y-m-d');

        $tanggal_akhir =
            $request->tanggal_akhir ??
            date('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | Data Barang
        |--------------------------------------------------------------------------
        */
        $barang = DB::table('databarang')
            ->select(
                'kode_brng',
                'nama_brng',
                'h_beli',
                'kode_sat'
            )
            ->get()
            ->keyBy('kode_brng');

        /*
        |--------------------------------------------------------------------------
        | Semua Bangsal
        |--------------------------------------------------------------------------
        */
        $bangsal = DB::table('gudangbarang')
            ->select('kd_bangsal')
            ->distinct()
            ->orderBy('kd_bangsal')
            ->pluck('kd_bangsal');

        /*
        |--------------------------------------------------------------------------
        | Stok Lokasi
        |--------------------------------------------------------------------------
        */
        $stok_lokasi = DB::table('gudangbarang')
            ->select(
                'kode_brng',
                'kd_bangsal',
                'stok'
            )
            ->get()
            ->groupBy('kode_brng');

        /*
        |--------------------------------------------------------------------------
        | Total Pengeluaran
        |--------------------------------------------------------------------------
        */
        $total_pengeluaran = DB::table(function ($query) use (
            $tanggal_awal,
            $tanggal_akhir
        ) {

            /*
            |--------------------------------------------------------------------------
            | Pengeluaran Obat BHP
            |--------------------------------------------------------------------------
            */
            $query->select(
                    'detail_pengeluaran_obat_bhp.kode_brng',
                    DB::raw('SUM(detail_pengeluaran_obat_bhp.jumlah) as jumlah')
                )
                ->from('pengeluaran_obat_bhp')
                ->join(
                    'detail_pengeluaran_obat_bhp',
                    'detail_pengeluaran_obat_bhp.no_keluar',
                    '=',
                    'pengeluaran_obat_bhp.no_keluar'
                )
                ->whereBetween(
                    'pengeluaran_obat_bhp.tanggal',
                    [$tanggal_awal, $tanggal_akhir]
                )
                ->groupBy(
                    'detail_pengeluaran_obat_bhp.kode_brng'
                )

            /*
            |--------------------------------------------------------------------------
            | UNION ALL
            |--------------------------------------------------------------------------
            */
                ->unionAll(

                    DB::table('detail_pemberian_obat')
                        ->select(
                            'kode_brng',
                            DB::raw('SUM(jml) as jumlah')
                        )
                        ->whereBetween(
                            'tgl_perawatan',
                            [$tanggal_awal, $tanggal_akhir]
                        )
                        ->groupBy('kode_brng')

                )

                ->unionAll(

                    DB::table('resep_pulang')
                        ->select(
                            'kode_brng',
                            DB::raw('SUM(jml_barang) as jumlah')
                        )
                        ->whereBetween(
                            'tanggal',
                            [$tanggal_awal, $tanggal_akhir]
                        )
                        ->groupBy('kode_brng')

                )

                ->unionAll(

                    DB::table('penjualan')
                        ->join(
                            'detailjual',
                            'detailjual.nota_jual',
                            '=',
                            'penjualan.nota_jual'
                        )
                        ->select(
                            'detailjual.kode_brng',
                            DB::raw('SUM(detailjual.jumlah) as jumlah')
                        )
                        ->whereBetween(
                            'penjualan.tgl_jual',
                            [$tanggal_awal, $tanggal_akhir]
                        )
                        ->groupBy('detailjual.kode_brng')

                );

        }, 'x')
        ->select(
            'kode_brng',
            DB::raw('SUM(jumlah) as total_pengeluaran')
        )
        ->groupBy('kode_brng')
        ->pluck(
            'total_pengeluaran',
            'kode_brng'
        );

        return view(
            'belanja.belanja',
            compact(
                'tanggal_awal',
                'tanggal_akhir',
                'barang',
                'bangsal',
                'stok_lokasi',
                'total_pengeluaran'
            )
        );
    }
}