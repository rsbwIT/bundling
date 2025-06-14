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
            ->leftJoin('dpjp_ranap', 'reg_periksa.no_rawat', '=', 'dpjp_ranap.no_rawat')
            ->leftJoin('dokter', 'dpjp_ranap.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
            ->select([
                'reg_periksa.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.alamat',
                'reg_periksa.p_jawab',
                'reg_periksa.hubunganpj',
                'penjab.png_jawab',
                DB::raw("CONCAT(kamar.kd_kamar, '  ', bangsal.nm_bangsal) AS kamar_bangsal"),
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
                'bridging_sep.klsnaik',

                // Keterangan naik kelas
                DB::raw("CASE bridging_sep.klsnaik
                WHEN '1' THEN 'VVIP'
                WHEN '2' THEN 'VIP'
                WHEN '3' THEN 'Kelas 1'
                WHEN '4' THEN 'Kelas 2'
                WHEN '5' THEN 'Kelas 3'
                WHEN '6' THEN 'ICCU'
                WHEN '7' THEN 'ICU'
                WHEN '8' THEN 'Di Atas Kelas 1'
                ELSE 'Tidak Ada'
            END AS keterangan_klsnaik"),

                // Warna naik kelas
                DB::raw("CASE
                WHEN bridging_sep.klsnaik IS NULL
                    OR bridging_sep.klsnaik NOT IN ('1','2','3','4','5','6','7','8') THEN 'kuning'
                ELSE 'hijau'
            END AS warna_klsnaik"),

                // Kelas kamar
                'kamar.kelas',

                // Warna kelas sesuai hak rawat
                DB::raw("CASE
                WHEN bridging_sep.jnspelayanan = '1' THEN
                    CASE
                        WHEN bridging_sep.klsrawat = '1' AND kamar.kelas = 'Kelas 1' THEN 'hijau'
                        WHEN bridging_sep.klsrawat = '2' AND kamar.kelas = 'Kelas 2' THEN 'hijau'
                        WHEN bridging_sep.klsrawat = '3' AND kamar.kelas = 'Kelas 3' THEN 'hijau'
                        WHEN bridging_sep.klsrawat = 'VIP' AND kamar.kelas = 'VIP' THEN 'hijau'
                        WHEN bridging_sep.klsrawat = 'VVIP' AND kamar.kelas = 'VVIP' THEN 'hijau'
                        ELSE 'orange'
                    END
                ELSE NULL
            END AS warna_kelas"),
            ])
            ->where('reg_periksa.status_lanjut', 'RANAP')
            // ->where('kamar_inap.tgl_keluar', '0000-00-00')
            ->groupBy('reg_periksa.no_rawat');

        // Filter berdasarkan request
        if ($request->has('kelas_filter')) {
            $query->where('kamar.kelas', $request->kelas_filter);
        }

        if (!$request->has('belum_pulang') && !$request->has('tgl_masuk') && !$request->has('tgl_pulang')) {
            $query->where('kamar_inap.stts_pulang', '-');
        }

        if ($request->has('belum_pulang')) {
            $query->where('kamar_inap.stts_pulang', '-');
        }

        if ($request->has('tgl_masuk')) {
            $query->whereBetween('kamar_inap.tgl_masuk', [$request->tgl1, $request->tgl2]);
        }

        if ($request->has('tgl_pulang')) {
            $query->whereBetween('kamar_inap.tgl_keluar', [$request->tgl1, $request->tgl2]);
        }

        // Tampilkan semua, baik SEP maupun non-SEP
        $query->where(function ($q) {
            $q->where('bridging_sep.jnspelayanan', '1')
                ->orWhereNull('bridging_sep.no_rawat');
        });

        $results = $query->get();

        // tgl pilang

        if ($request->has('tgl_pulang')) {
            $query->whereBetween('kamar_inap.tgl_keluar', [$request->tgl1, $request->tgl2]);
        }


        // Filter warna jika ada
        if ($request->filled('filter_warna')) {
            $results = $results->filter(function ($item) use ($request) {
                $warna = 'putih';

                if (empty($item->klsrawat) && empty($item->warna_kelas)) {
                    $warna = 'putih';
                } elseif ($item->warna_kelas === 'hijau') {
                    $warna = 'hijau';
                } elseif ($item->keterangan_klsnaik === 'Tidak Ada') {
                    $warna = 'kuning';
                } else {
                    $warna = 'merah';
                }

                return $warna === $request->filter_warna;
            });
        }

        return view('pasienkamarinap.rawat-inap', [
            'results' => $results,
        ]);
    }
}
