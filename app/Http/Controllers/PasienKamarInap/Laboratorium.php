<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Laboratorium extends Controller
{
    /**
     * Menampilkan data pemeriksaan lab dalam rentang tanggal tertentu
     * dengan fitur pencarian nama pasien / No Rawat / No RM
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $search    = $request->input('search', '');

        try {
            $startDate = Carbon::parse($startDate)->toDateString();
            $endDate   = Carbon::parse($endDate)->toDateString();
        } catch (\Exception $e) {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate   = Carbon::now()->endOfMonth()->toDateString();
        }

        $query = DB::table('periksa_lab')
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
            ->whereBetween('periksa_lab.tgl_periksa', [$startDate, $endDate]);

        // Jika ada keyword pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('pasien.nm_pasien', 'LIKE', "%{$search}%")
                  ->orWhere('reg_periksa.no_rawat', 'LIKE', "%{$search}%")
                  ->orWhere('pasien.no_rkm_medis', 'LIKE', "%{$search}%")
                  ->orWhere('jns_perawatan_lab.nm_perawatan', 'LIKE', "%{$search}%"); // tambahkan pencarian berdasarkan nama pemeriksaan
            });
        }

        $data = $query->orderBy('periksa_lab.tgl_periksa', 'asc')->get();

        $totalPasien = $data->count();
        $totalBiaya  = $data->sum('biaya');

        return view('pasienkamarinap.laboratorium', compact(
            'data', 'startDate', 'endDate', 'search', 'totalPasien', 'totalBiaya'
        ));
    }
}
