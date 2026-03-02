<?php

namespace App\Http\Controllers\DetailTindakanUmum;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PeriksaLabPAUmum extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $action = '/periksalabpaumum';

        $penjab  = $this->cacheService->getPenjab();
        $petugas = $this->cacheService->getPetugas();
        $dokter  = $this->cacheService->getDokter();

        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $kdPetugas  = $request->kdPetugas ? explode(',', $request->kdPetugas) : [];
        $cari = $request->cariNomor;

        /*
        ==================================================
        JIKA BELUM ADA FILTER → JANGAN TAMPILKAN DATA
        ==================================================
        */
        if (!$tgl1 && !$tgl2 && !$cari && empty($kdPetugas)) {
            $data = collect();
            return view('detail-tindakan-umum.periksalabpaumum', compact(
                'action',
                'penjab',
                'petugas',
                'dokter',
                'data'
            ));
        }

        /*
        ==================================================
        QUERY UTAMA
        ==================================================
        */
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

            ->where('periksa_lab.kategori', 'PA')
            ->where('reg_periksa.kd_pj', 'UMU'); // KHUSUS PASIEN UMUM

        /*
        ==================================================
        FILTER TANGGAL
        ==================================================
        */
        if ($tgl1 && $tgl2) {
            $query->whereBetween('periksa_lab.tgl_periksa', [$tgl1, $tgl2]);
        }

        /*
        ==================================================
        FILTER DOKTER
        ==================================================
        */
        if (!empty($kdPetugas)) {
            $query->whereIn('periksa_lab.kd_dokter', $kdPetugas);
        }

        /*
        ==================================================
        FILTER PENCARIAN
        ==================================================
        */
        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('reg_periksa.no_rawat', 'like', "%$cari%")
                  ->orWhere('reg_periksa.no_rkm_medis', 'like', "%$cari%")
                  ->orWhere('pasien.nm_pasien', 'like', "%$cari%");
            });
        }

        /*
        ==================================================
        GROUPING & ORDER
        ==================================================
        */
        $data = $query->groupBy(
                    'periksa_lab.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'periksa_lab.kd_jenis_prw',
                    'jns_perawatan_lab.nm_perawatan',
                    'periksa_lab.kd_dokter',
                    'dokter_lab.nm_dokter',
                    'periksa_lab.dokter_perujuk',
                    'dokter_perujuk.nm_dokter',
                    'periksa_lab.tgl_periksa',
                    'periksa_lab.jam',
                    'penjab.png_jawab',
                    'periksa_lab.bagian_rs',
                    'periksa_lab.bhp',
                    'periksa_lab.tarif_perujuk',
                    'periksa_lab.tarif_tindakan_dokter',
                    'periksa_lab.tarif_tindakan_petugas',
                    'periksa_lab.kso',
                    'periksa_lab.menejemen',
                    'periksa_lab.biaya'
                )
                ->orderBy('periksa_lab.tgl_periksa', 'desc')
                ->get();

        return view('detail-tindakan-umum.periksalabpaumum', compact(
            'action',
            'penjab',
            'petugas',
            'dokter',
            'data'
        ));
    }
}