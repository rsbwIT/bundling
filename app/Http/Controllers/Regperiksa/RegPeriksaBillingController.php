<?php

namespace App\Http\Controllers\Regperiksa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        // Update status di tabel reg_periksa
        DB::table('reg_periksa')
            ->where('no_rawat', $request->no_rawat)
            ->update(['stts' => $request->status]);

        // Tambahkan log perubahan status
        $this->logStatusUpdate($request);

        return response()->json(['success' => true]);
    }

    /**
     * Mencatat log perubahan status ke tabel bw_tracker_log_reg.
     */
    protected function logStatusUpdate(Request $request)
    {
        $user = Auth::user(); // Ambil user yang sedang login
        if (!$user) return;

        // Ambil nama user dari tabel `user`
        $namaUser = DB::table('user')
            ->where('id_user', $user->id)  // Asumsi id_user adalah kolom yang cocok dengan user id
            ->value('nama_user'); // Ambil nama user

        // Simpan log perubahan status ke dalam tabel bw_tracker_log_reg
        DB::table('bw_tracker_log_reg')->insert([
            'id_user'    => $user->id,  // ID User yang login
            'nama_user'  => $namaUser ?? $user->name, // Nama user atau fallback ke nama dari Auth
            'tanggal'    => now(), // Tanggal dan waktu sekarang
            'status'     => $request->status, // Status yang diperbarui
        ]);
    }
}
