<?php

namespace App\Http\Controllers\RegPeriksa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegPeriksaBillingController extends Controller
{
    /**
     * Menampilkan daftar reg_periksa berdasarkan no_rkm_medis (jika diisi).
     */
    public function regperiksabilling(Request $request)
    {
        $data = collect(); // Default kosong

        if ($request->filled('no_rkm_medis')) {
            $data = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.status_lanjut',
                    'reg_periksa.stts',
                    'reg_periksa.status_bayar',
                    'dokter.nm_dokter',
                    'poliklinik.nm_poli'
                )
                ->where('reg_periksa.no_rkm_medis', $request->no_rkm_medis)
                ->orderByDesc('reg_periksa.tgl_registrasi')
                ->get();
        }

        return view('regperiksa.regperiksabilling', [
            'results' => $data,
            'no_rkm_medis' => $request->no_rkm_medis
        ]);
    }

    /**
     * Mengupdate status pasien berdasarkan no_rawat.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required|string',
            'status' => 'required|string'
        ]);

        DB::table('reg_periksa')
            ->where('no_rawat', $request->no_rawat)
            ->update(['stts' => $request->status]);

        return response()->json(['success' => true]);
    }
}
