<?php

namespace App\Http\Controllers\Fisioterapi\Print;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PrintController extends Controller
{
    public function print($no_rkm_medis, $lembar)
    {
        // Ambil semua kunjungan fisioterapi
        $data = DB::table('fisioterapi_kunjungan as fk')
            ->join('fisioterapi_form as ff', function ($join) {
                $join->on('fk.no_rkm_medis', '=', 'ff.no_rkm_medis')
                     ->on('fk.lembar', '=', 'ff.lembar');
            })
            ->join('pasien as p', 'fk.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->select(
                'p.nm_pasien',
                'fk.no_rawat',
                'fk.no_rkm_medis',
                'fk.kunjungan',
                'fk.program',
                'fk.tanggal',
                'fk.ttd_pasien',
                'fk.ttd_dokter',
                'fk.ttd_terapis',
                'fk.lembar',
                'ff.diagnosa',
                'ff.ft',
                'ff.st'
            )
            ->where('fk.no_rkm_medis', $no_rkm_medis)
            ->where('fk.lembar', $lembar)
            ->orderBy('fk.kunjungan', 'ASC')
            ->get();

        if ($data->isEmpty()) {
            abort(404, "Data tidak ditemukan");
        }

        $first = $data->first();

        // Ambil setting rumah sakit
        $setting = DB::table('setting')->first();

        // Ambil dokter penanggung jawab dari reg_periksa
        $dokterPJ = DB::table('reg_periksa')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('reg_periksa.no_rawat', $first->no_rawat)
            ->select('dokter.nm_dokter')
            ->first();

        // Tanggal kunjungan pertama
        $tanggalPertama = $data->first()->tanggal;

        return view('fisioterapi.print.print', [
            'data' => $data,
            'first' => $first,
            'setting' => $setting,
            'dokterPJ' => $dokterPJ,
            'tanggalPertama' => $tanggalPertama
        ]);
    }
}
