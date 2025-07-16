<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SirsBridgingController extends Controller
{
    public function kirimRawatInap()
    {
        // Konfigurasi Header SIRS
        $kodeRs   = '0801R002'; // Ganti dengan kode RS kamu
        $password = 'RSbw_2025'; // Ganti dengan password asli
        $md5pass  = md5($password);
        $timestamp = now()->format('Y-m-d H:i:s');

        // Ambil data dari sistem internal
        $dataPasien = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select(
                'kamar_inap.no_rawat',
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.diagnosa_awal',
                'kamar_inap.kd_kamar',
                'reg_periksa.kd_dokter'
            )
            ->limit(10)
            ->get();

        foreach ($dataPasien as $pasien) {
            $payload = [
                'no_rawat'      => $pasien->no_rawat,
                'tgl_masuk'     => $pasien->tgl_masuk,
                'tgl_pulang'    => $pasien->tgl_keluar ?? '',
                'diagnosa_awal' => $pasien->diagnosa_awal,
                'kd_kamar'      => $pasien->kd_kamar,
                'dokter'        => $pasien->kd_dokter,
                'status_pulang' => $pasien->tgl_keluar ? 'Pulang' : 'Masih Dirawat',
            ];

            $response = Http::withHeaders([
                'X-rs-id'     => $kodeRs,
                'X-pass'      => $md5pass,
                'X-Timestamp' => $timestamp,
            ])->post('https://api.sirs.kemkes.go.id/bridging/rawat-inap', $payload);

            // Simpan log
            DB::table('log_kirim_sirs_rawat_inap')->insert([
                'no_rawat'         => $pasien->no_rawat,
                'response_code'    => $response->status(),
                'response_message' => $response->body(),
                'status_kirim'     => $response->successful() ? 'BERHASIL' : 'GAGAL',
                'tgl_kirim'        => now(),
            ]);
        }

        return response()->json(['status' => 'Pengiriman selesai']);
    }
}
