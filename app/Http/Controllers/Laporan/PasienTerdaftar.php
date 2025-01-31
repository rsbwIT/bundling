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
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $getPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_poli',
                'pasien.nm_pasien',
                'reg_periksa.status_lanjut',
                'poliklinik.nm_poli'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggl1, $tanggl2])
            ->where(function($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->orderBy('reg_periksa.jam_reg', 'asc')
            ->get();
        $getPasien->map(function ($item) use($tanggl1, $tanggl2){
            $item->getPasienUmum = DB::table('reg_periksa')
                ->select('reg_periksa.no_rawat')
                ->where('reg_periksa.no_rawat', '=', $item->no_rawat)
                ->where('reg_periksa.kd_pj', '=', 'UMU')
                ->get();
            $item->getPasienBpjs = DB::table('reg_periksa')
                ->select('reg_periksa.no_rawat')
                ->where('reg_periksa.no_rawat', '=', $item->no_rawat)
                ->where('reg_periksa.kd_pj', '=', 'BPJ')
                ->get();
            $item->getPasienAsuransi = DB::table('reg_periksa')
                ->select('reg_periksa.no_rawat')
                ->where('reg_periksa.no_rawat', '=', $item->no_rawat)
                ->whereNotIn('reg_periksa.kd_pj', ['UMU', 'BPJ'])
                ->get();
            $item->getPiutangPasien = DB::table('piutang_pasien')
                ->select('piutang_pasien.no_rawat')
                ->where('piutang_pasien.no_rawat', '=', $item->no_rawat)
                ->get();
            $item->getBilling = DB::table('billing')
                ->select('nm_perawatan')
                ->where('no_rawat', $item->no_rawat)
                ->where('no', '=', 'No.Nota')
                // ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
                ->get();
            $item->getPasienBatal = DB::table('reg_periksa')
                ->select('reg_periksa.no_rawat')
                ->where('reg_periksa.no_rawat', '=', $item->no_rawat)
                ->where('reg_periksa.stts', '=', 'Batal')
                ->get();
            $item->getPasienOpname= DB::table('kamar_inap')
                ->select('kamar_inap.no_rawat')
                ->where('kamar_inap.no_rawat', '=', $item->no_rawat)
                ->get();
        });

        return view('laporan.pasien-terdaftar', [
            'getPasien' => $getPasien,
        ]);
    }
}
