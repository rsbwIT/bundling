<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Http\Request;

class InfoKamarInap extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function InfoKamarInap(Request $request)
    {
        $urutanBangsal = [
            'RANG',
            'ANINH',
            'RG2',
            'G2INH',
            'RHCU',
            'RG3','B0167',
            'INHG3',
            'RG5',
            'RICU2',
            'RICU1',
            'RISNG',
            'RISG2',
            'RISG3',
            'KNR',
            'RKTLG',
            'RMLT',
            'MRK',
            'RNICU',
            'RNUR',
            'RPERI',
            'RPENA'
        ];

        $data = DB::table('bangsal')
            ->join('kamar', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->leftJoin('kamar_inap', function ($join) {
                $join->on('kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
                    ->where('kamar_inap.tgl_keluar', '=', '0000-00-00');
            })
            ->leftJoin('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select(
                'bangsal.kd_bangsal',
                'bangsal.nm_bangsal',
                'kamar.kd_kamar',
                'kamar.kelas',
                'bangsal.status as status_bangsal',
                'kamar.status as status_kamar',
                'kamar.statusdata',
                'kamar_inap.no_rawat',
                'kamar_inap.tgl_keluar',
                'kamar_inap.tgl_masuk',
                'kamar_inap.diagnosa_awal',
                'pasien.nm_pasien',
                'pasien.jk',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur'
            )
            ->where('bangsal.status', '1')
            ->where('kamar.statusdata', '1')
            ->get()
            ->map(function ($item) {
                $item->status = $item->no_rawat ? 'Terisi' : 'Kosong';
                return $item;
            })
            ->sortBy([['nm_bangsal', 'asc'], ['kelas', 'asc']]) // bisa dihapus jika tidak dibutuhkan
            ->groupBy('kd_bangsal')
            ->sortBy(function ($items, $kd_bangsal) use ($urutanBangsal) {
                $index = array_search($kd_bangsal, $urutanBangsal);
                return $index !== false ? $index : 9999; // letakkan yang tidak terdaftar di akhir
            });

        return view('pasienkamarinap.infokamarinap', [
            'results' => $data
        ]);
    }
}
