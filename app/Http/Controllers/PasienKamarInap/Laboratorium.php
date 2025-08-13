<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Laboratorium extends Controller
{
    /**
     * Menampilkan data pemeriksaan lab NDF dalam rentang tanggal tertentu
     */
    public function index(Request $request)
    {
        // Ambil filter tanggal dari request, default bulan berjalan
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Query data pemeriksaan lab NDF
        $data = DB::table('periksa_lab')
            ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select(
                'reg_periksa.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'periksa_lab.tgl_periksa',
                'jns_perawatan_lab.nm_perawatan',
                'periksa_lab.biaya'
            )
            ->whereBetween('periksa_lab.tgl_periksa', [$startDate, $endDate])
            ->where('jns_perawatan_lab.nm_perawatan', 'LIKE', '%NDF%')
            ->orderBy('periksa_lab.tgl_periksa')
            ->get();

        return view('pasienkamarinap.laboratorium', [
            'data'      => $data,
            'startDate' => $startDate,
            'endDate'   => $endDate
        ]);
    }
}
