<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BayarPiutangKhanza extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function BayarPiutangKhanza(Request $request)
    {
        $penjab       = $this->cacheService->getPenjab();
        $url          = '/bayar-piutang-khanza';
        $cariNomor    = $request->cariNomor;
        $tgl1         = $request->tgl1 ?? now()->format('Y-m-d');
        $tgl2         = $request->tgl2 ?? now()->format('Y-m-d');
        $kdPenjamin   = $request->kdPenjamin ? explode(',', $request->kdPenjamin) : [];

        // 🔍 Query utama (filter)
        $query = DB::table('bayar_piutang')
            ->select(
                'bayar_piutang.tgl_bayar',
                'bayar_piutang.no_rkm_medis',
                'pasien.nm_pasien',
                'bayar_piutang.besar_cicilan',
                'bayar_piutang.catatan',
                'bayar_piutang.no_rawat',
                'bayar_piutang.kd_rek',
                'bayar_piutang.kd_rek_kontra',
                'bayar_piutang.diskon_piutang',
                'bayar_piutang.kd_rek_diskon_piutang',
                'bayar_piutang.tidak_terbayar',
                'bayar_piutang.kd_rek_tidak_terbayar',
                'penjab.kd_pj',
                'penjab.png_jawab'
            )
            ->join('pasien', 'bayar_piutang.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('reg_periksa', 'bayar_piutang.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereBetween('bayar_piutang.tgl_bayar', [$tgl1, $tgl2])
            ->when($kdPenjamin, function ($q) use ($kdPenjamin) {
                $q->whereIn('penjab.kd_pj', $kdPenjamin);
            })
            ->when($cariNomor, function ($q) use ($cariNomor) {
                $q->where(function ($sub) use ($cariNomor) {
                    $sub->where('bayar_piutang.no_rawat', 'like', "%{$cariNomor}%")
                        ->orWhere('bayar_piutang.no_rkm_medis', 'like', "%{$cariNomor}%")
                        ->orWhere('pasien.nm_pasien', 'like', "%{$cariNomor}%");
                });
            });

        // 🔹 Data untuk tabel (paginate)
        $bayarPiutang = (clone $query)
            ->orderBy('bayar_piutang.tgl_bayar', 'asc')
            ->orderBy('bayar_piutang.no_rkm_medis', 'asc')
            ->paginate(1000);

        // 🔹 Koleksi data yang ditampilkan di halaman ini
        $displayedCollection = $bayarPiutang->getCollection();

        // ✅ Total baris (sesuai nomor urut tabel yang tampil)
        $totalBarisDisplayed = $displayedCollection->count();

        // ✅ Total pasien unik di halaman ini
        $totalPasienDisplayed = $displayedCollection->pluck('no_rkm_medis')->filter()->unique()->count();

        // 🔹 Data summary (ambil semua data sesuai filter — tanpa paginate)
        $allData = (clone $query)->get();

        $totalPasienAll       = $allData->pluck('no_rkm_medis')->filter()->unique()->count();
        $totalCicilan         = $allData->sum('besar_cicilan');
        $totalDiskon          = $allData->sum('diskon_piutang');
        $totalTidakTerbayar   = $allData->sum('tidak_terbayar');
        $totalKeseluruhan     = $totalCicilan + $totalDiskon + $totalTidakTerbayar;

        // 🔹 Tambahkan info nota per item (untuk setiap baris pada paginator)
        $bayarPiutang->getCollection()->transform(function ($item) {
            $item->getNomorNota = DB::table('billing')
                ->select('nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('no', '=', 'No.Nota')
                ->get();
            return $item;
        });

        return view('laporan.bayarPiutangKhanza', [
            'penjab'               => $penjab,
            'url'                  => $url,
            'bayarPiutang'         => $bayarPiutang,

            // 🔹 Total sesuai tabel (baris ditampilkan)
            'totalBaris'           => $totalBarisDisplayed,

            // 🔹 Total pasien unik di halaman sekarang
            'totalPasien'          => $totalPasienDisplayed,

            // 🔹 Total pasien unik seluruh data (tanpa paginate)
            'totalPasienAll'       => $totalPasienAll,

            'totalCicilan'         => $totalCicilan,
            'totalDiskon'          => $totalDiskon,
            'totalTidakTerbayar'   => $totalTidakTerbayar,
            'totalKeseluruhan'     => $totalKeseluruhan,
        ]);
    }
}
