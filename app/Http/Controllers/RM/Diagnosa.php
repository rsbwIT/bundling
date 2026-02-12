<?php
namespace App\Http\Controllers\RM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Diagnosa extends Controller
{
    public function index(Request $request)
    {
        $keyword   = $request->keyword;
        $tgl_awal  = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;

        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('diagnosa_pasien as dp', 'reg_periksa.no_rawat', '=', 'dp.no_rawat')
            ->join('penyakit as p', 'dp.kd_penyakit', '=', 'p.kd_penyakit')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'pasien.nm_pasien',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.almt_pj',
                'pasien.jk',
                'dp.kd_penyakit',
                'p.nm_penyakit',
                'reg_periksa.status_lanjut'
            )
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('dp.kd_penyakit', 'like', "%$keyword%")
                        ->orWhere('p.nm_penyakit', 'like', "%$keyword%");
                });
            })
            ->when($tgl_awal, function ($q) use ($tgl_awal) {
                $q->whereDate('reg_periksa.tgl_registrasi', '>=', $tgl_awal);
            })
            ->when($tgl_akhir, function ($q) use ($tgl_akhir) {
                $q->whereDate('reg_periksa.tgl_registrasi', '<=', $tgl_akhir);
            })
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->get();

        return view('rm.diagnosa', compact('data'));
    }
}
