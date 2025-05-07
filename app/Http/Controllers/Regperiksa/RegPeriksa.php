<?php

namespace App\Http\Controllers\RegPeriksa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegPeriksa extends Controller
{
    // Menampilkan daftar reg_periksa, dengan filter jika ada pencarian
    public function regperiksa(Request $request)
    {
        $data = collect(); // kosongkan default data

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
                ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                ->get();
        }

        return view("regperiksa.regperiksa", [
            'results' => $data,
            'no_rkm_medis' => $request->no_rkm_medis // agar bisa tampilkan nilai input sebelumnya
        ]);
    }

    // Update status pasien
    public function updateStatus(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'status' => 'required'
        ]);

        // Mengupdate status pada tabel reg_periksa
        DB::table('reg_periksa')
            ->where('no_rawat', $request->no_rawat)
            ->update(['stts' => $request->status]);

        // Kembalikan response JSON
        return response()->json(['success' => true]);
    }
}
