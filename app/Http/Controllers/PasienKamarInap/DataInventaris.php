<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataInventaris extends Controller
{
    public function index(Request $request)
{
    $query = DB::table('inventaris')
        ->join('inventaris_barang', 'inventaris.kode_barang', '=', 'inventaris_barang.kode_barang')
        ->join('inventaris_ruang', 'inventaris.id_ruang', '=', 'inventaris_ruang.id_ruang')
        ->select(
            'inventaris.no_inventaris',
            'inventaris_barang.nama_barang',
            'inventaris_ruang.nama_ruang',
            'inventaris.tgl_pengadaan'
        );

    if ($request->filled('cari')) {
        $search = $request->cari;
        $query->where(function ($q) use ($search) {
            $q->where('inventaris.no_inventaris', 'like', "%$search%")
              ->orWhere('inventaris_barang.nama_barang', 'like', "%$search%")
              ->orWhere('inventaris_ruang.nama_ruang', 'like', "%$search%");
        });
    }

    $dataInventaris = $query->get();

    return view('pasienkamarinap.inventarisbarang', compact('dataInventaris'));
}
}
