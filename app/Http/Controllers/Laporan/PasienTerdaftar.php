<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PasienTerdaftar extends Controller
{
    public function PasienTerdaftar(Request $request)
    {
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');
        $getPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'pasien.nm_pasien',
                'reg_periksa.status_lanjut',
                'poliklinik.nm_poli'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggl1, $tanggl2])
            ->where(function ($query) use ($cariNomor) {
                if (!empty($cariNomor)) {
                    $query->where(function($q) use ($cariNomor) {
                $q->orWhere('reg_periksa.no_rawat', 'like', $cariNomor . '%');
                $q->orWhere('reg_periksa.no_rkm_medis', 'like', $cariNomor . '%');
                $q->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                                });
                }
            })
            ->orderBy('reg_periksa.jam_reg', 'asc')
            ->get();
            
        $noRawats = $getPasien->pluck('no_rawat')->toArray();
        $piutangPasien = DB::table('piutang_pasien')->select('no_rawat')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $billing = DB::table('billing')->select('no_rawat', 'nm_perawatan')->where('no', 'No.Nota')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $kamarInap = DB::table('kamar_inap')->select('no_rawat')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $getPasien->map(function ($item) use($piutangPasien, $billing, $kamarInap) {
            $obj = new \stdClass();
            $obj->no_rawat = $item->no_rawat;
            $itemArr = collect([$obj]);
            $emptyArr = collect();

            $item->getPasienUmum = ($item->kd_pj == 'UMU') ? $itemArr : $emptyArr;
            $item->getPasienBpjs = ($item->kd_pj == 'BPJ') ? $itemArr : $emptyArr;
            $item->getPasienAsuransi = (!in_array($item->kd_pj, ['UMU', 'BPJ'])) ? $itemArr : $emptyArr;
            $item->getPasienBatal = ($item->stts == 'Batal') ? $itemArr : $emptyArr;
            
            $item->getPiutangPasien = isset($piutangPasien[$item->no_rawat]) ? $piutangPasien[$item->no_rawat] : collect();
            $item->getBilling = isset($billing[$item->no_rawat]) ? $billing[$item->no_rawat] : collect();
            $item->getPasienOpname = isset($kamarInap[$item->no_rawat]) ? $kamarInap[$item->no_rawat] : collect();
            return $item;
        });

        return view('laporan.pasien-terdaftar', [
            'getPasien' => $getPasien,
        ]);
    }
}
