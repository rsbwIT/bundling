<?php

namespace App\Http\Controllers\Rinciankasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MappingPenjaminKasirController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->input('cari', '');
        $filterKategori = $request->input('kategori', '');

        $query = DB::table('penjab')
            ->leftJoin('bw_mapping_penjamin_kasir as mp', 'penjab.kd_pj', '=', 'mp.kd_pj')
            ->select(
                'penjab.kd_pj',
                'penjab.png_jawab',
                'penjab.status',
                DB::raw("COALESCE(mp.kategori, 'Asuransi') as kategori"),
                'mp.nama_akun_piutang',
                'mp.kd_rek_piutang'
            );

        if (!empty($cari)) {
            $query->where(function ($q) use ($cari) {
                $q->where('penjab.kd_pj', 'like', "%{$cari}%")
                  ->orWhere('penjab.png_jawab', 'like', "%{$cari}%")
                  ->orWhere('mp.nama_akun_piutang', 'like', "%{$cari}%");
            });
        }

        if (!empty($filterKategori)) {
            $query->where('mp.kategori', $filterKategori);
        }

        $mappings = $query->orderBy('penjab.png_jawab', 'asc')->paginate(30)->withQueryString();
        $akunPiutangList = DB::table('akun_piutang')->orderBy('nama_bayar', 'asc')->get();

        return view('rinciankasir.mapping-penjamin', compact('mappings', 'akunPiutangList', 'cari', 'filterKategori'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'kd_pj'             => 'required',
            'kategori'          => 'required|in:BPJS Murni,BPJS COB,Asuransi,Umum',
            'nama_akun_piutang' => 'nullable|string'
        ]);

        $kdPj = $request->input('kd_pj');
        $kategori = $request->input('kategori');
        $namaAkun = $request->input('nama_akun_piutang');

        $kdRek = null;
        if (!empty($namaAkun)) {
            $akun = DB::table('akun_piutang')->where('nama_bayar', $namaAkun)->first();
            $kdRek = $akun ? $akun->kd_rek : null;
        }

        DB::table('bw_mapping_penjamin_kasir')->updateOrInsert(
            ['kd_pj' => $kdPj],
            [
                'kategori'          => $kategori,
                'nama_akun_piutang' => $namaAkun,
                'kd_rek_piutang'    => $kdRek,
                'updated_at'        => now()
            ]
        );

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mapping penjamin berhasil diperbarui!']);
        }

        return redirect()->back()->with('success', 'Mapping penjamin berhasil diperbarui!');
    }
}

