<?php

namespace App\Http\Controllers\DetailTindakanBulanan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PeriksaLabPABulanan extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $action = '/periksalabpabulanan';

        $penjab  = $this->cacheService->getPenjab();
        $petugas = $this->cacheService->getPetugas();
        $dokter  = $this->cacheService->getDokter();

        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;

        // JANGAN TAMPILKAN DATA SAAT PERTAMA BUKA
        if (!$request->filled('tgl1') || !$request->filled('tgl2')) {
            return view('detail-tindakan-bulanan.periksalabpabulanan', [
                'action'  => $action,
                'penjab'  => $penjab,
                'petugas' => $petugas,
                'dokter'  => $dokter,
                'data'    => collect()
            ]);
        }

        $kdPenjamin = $request->kdPenjamin
            ? explode(',', $request->kdPenjamin)
            : [];

        $kdPetugas = $request->kdPetugas
            ? explode(',', $request->kdPetugas)
            : [];

        $status = $request->statusLunas ?? '';
        $cari   = $request->cariNomor;

        // BASE QUERY
        $query = DB::table('periksa_lab')
            ->select(
                'periksa_lab.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'periksa_lab.kd_jenis_prw',
                'jns_perawatan_lab.nm_perawatan',
                'periksa_lab.kd_dokter as kd_dokter_lab',
                'dokter_lab.nm_dokter as nm_dokter_lab',
                'periksa_lab.dokter_perujuk as kd_dokter_perujuk',
                'dokter_perujuk.nm_dokter as nm_dokter_perujuk',
                'periksa_lab.tgl_periksa',
                'periksa_lab.jam',
                'penjab.png_jawab',
                'piutang_pasien.status as status_lunas',
                'periksa_lab.bagian_rs',
                'periksa_lab.bhp',
                'periksa_lab.tarif_perujuk',
                'periksa_lab.tarif_tindakan_dokter',
                'periksa_lab.tarif_tindakan_petugas',
                'periksa_lab.kso',
                'periksa_lab.menejemen',
                'periksa_lab.biaya'
            )
            ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('dokter as dokter_lab', 'periksa_lab.kd_dokter', '=', 'dokter_lab.kd_dokter')
            ->leftJoin('dokter as dokter_perujuk', 'periksa_lab.dokter_perujuk', '=', 'dokter_perujuk.kd_dokter')
            ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'periksa_lab.no_rawat')
            ->leftJoin('bayar_piutang', 'bayar_piutang.no_rawat', '=', 'periksa_lab.no_rawat')
            ->where('periksa_lab.kategori', 'PA');
        // FILTER PENJAMIN
        if (!empty($kdPenjamin)) {
            $query->whereIn('penjab.kd_pj', $kdPenjamin);
        }
        // FILTER PETUGAS
        if (!empty($kdPetugas)) {
            $query->whereIn('periksa_lab.kd_dokter', $kdPetugas);
        }
        // FILTER STATUS
        if ($status === "Lunas") {

            $query->whereBetween('bayar_piutang.tgl_bayar', [$tgl1, $tgl2])
                  ->where('piutang_pasien.status', 'Lunas');

        } elseif ($status === "Belum Lunas") {

            $query->whereBetween('piutang_pasien.tgl_piutang', [$tgl1, $tgl2])
                  ->where('piutang_pasien.status', 'Belum Lunas');

        } else {
            // Semua
            $query->whereBetween('periksa_lab.tgl_periksa', [$tgl1, $tgl2]);
        }
        // FILTER PENCARIAN
        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('reg_periksa.no_rawat', 'like', "%$cari%")
                  ->orWhere('reg_periksa.no_rkm_medis', 'like', "%$cari%")
                  ->orWhere('pasien.nm_pasien', 'like', "%$cari%");
            });
        }
        // GROUP & ORDER
        $data = $query->groupBy(
                        'periksa_lab.no_rawat',
                        'periksa_lab.kd_jenis_prw',
                        'periksa_lab.tgl_periksa',
                        'periksa_lab.jam',
                        'periksa_lab.biaya'
                    )
                    ->orderBy('periksa_lab.tgl_periksa', 'desc')
                    ->get();

        return view('detail-tindakan-bulanan.periksalabpabulanan', compact(
            'action',
            'penjab',
            'petugas',
            'dokter',
            'data'
        ));
    }
}