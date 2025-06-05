<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Http\Request;

class RawatInap extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function RawatInap(Request $request)
    {
        $query = DB::table('reg_periksa')
    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
    ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
    ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
    ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
    ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
    ->join('dpjp_ranap', 'reg_periksa.no_rawat', '=', 'dpjp_ranap.no_rawat')
    ->join('dokter', 'dpjp_ranap.kd_dokter', '=', 'dokter.kd_dokter')
    ->leftJoin('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat') // tetap LEFT JOIN
    ->select([
        'reg_periksa.no_rawat',
        'pasien.no_rkm_medis',
        'pasien.nm_pasien',
        'pasien.alamat',
        'reg_periksa.p_jawab',
        'reg_periksa.hubunganpj',
        'penjab.png_jawab',
        DB::raw("CONCAT(kamar.kd_kamar, '  ', bangsal.nm_bangsal) as kamar_bangsal"),
        'kamar_inap.trf_kamar',
        'kamar_inap.diagnosa_awal',
        'kamar_inap.diagnosa_akhir',
        'kamar_inap.tgl_masuk',
        'kamar_inap.jam_masuk',
        'kamar_inap.tgl_keluar',
        'kamar_inap.jam_keluar',
        'kamar_inap.ttl_biaya',
        'kamar_inap.stts_pulang',
        'kamar_inap.lama',
        'dokter.nm_dokter',
        'reg_periksa.status_bayar',
        'pasien.agama',
        'reg_periksa.kd_pj',
        'bridging_sep.klsrawat',
        'kamar.kelas',
        DB::raw("
            CASE
                WHEN bridging_sep.jnspelayanan = '1' THEN
                    CASE
                        WHEN bridging_sep.klsrawat = '1' AND kamar.kelas = 'Kelas 1' THEN 'hijau'
                        WHEN bridging_sep.klsrawat = '2' AND kamar.kelas = 'Kelas 2' THEN 'hijau'
                        WHEN bridging_sep.klsrawat = '3' AND kamar.kelas = 'Kelas 3' THEN 'hijau'
                        WHEN bridging_sep.klsrawat = 'VIP' AND kamar.kelas = 'VIP' THEN 'hijau'
                        WHEN bridging_sep.klsrawat = 'VVIP' AND kamar.kelas = 'VVIP' THEN 'hijau'
                        ELSE 'orange'
                    END
                ELSE 'hijau'
            END as warna_kelas
        ")
    ])
    ->groupBy('reg_periksa.no_rawat');


        // Default filter: tampilkan pasien belum pulang saat awal
        if (
            !$request->has('belum_pulang') &&
            !$request->has('tgl_masuk') &&
            !$request->has('tgl_pulang')
        ) {
            $query->where('kamar_inap.stts_pulang', '-');
        }

        // Jika ada filter explicit, pakai sesuai filter request
        if ($request->has('belum_pulang')) {
            $query->where('kamar_inap.stts_pulang', '-');
        }

        if ($request->has('tgl_masuk')) {
            $query->whereBetween('kamar_inap.tgl_masuk', [$request->tgl1, $request->tgl2]);
        }

        if ($request->has('tgl_pulang')) {
            $query->whereBetween('kamar_inap.tgl_keluar', [$request->tgl1, $request->tgl2]);
        }

        $results = $query->get();

        return view('pasienkamarinap.rawat-inap', [
            'results' => $results,
        ]);
    }
}
