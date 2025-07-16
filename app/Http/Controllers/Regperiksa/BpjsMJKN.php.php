<?php

namespace App\Http\Controllers\Regperiksa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Helpers\BpjsHelper;

class BpjsMJKN extends Controller
{
    public function kirimAntreanBPJS()
    {
        // Ambil data berdasarkan pasien & dokter
        $data = DB::table('reg_periksa')
            ->leftJoin('maping_poli_bpjs', 'maping_poli_bpjs.kd_poli_rs', '=', 'reg_periksa.kd_poli')
            ->leftJoin('maping_dokter_dpjpvclaim', 'reg_periksa.kd_dokter', '=', 'maping_dokter_dpjpvclaim.kd_dokter')
            ->leftJoin('bridging_sep', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
            ->leftJoin('bridging_surat_kontrol_bpjs', 'bridging_sep.no_sep', '=', 'bridging_surat_kontrol_bpjs.no_sep')
            ->leftJoin('jadwal', 'maping_dokter_dpjpvclaim.kd_dokter', '=', 'jadwal.kd_dokter')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('reg_periksa.no_rkm_medis', '262964') // ← bisa dijadikan parameter
            ->where('bridging_surat_kontrol_bpjs.nm_dokter_bpjs', 'dr. Lydia Theresia Tampubolon, M.Ked(PD), Sp.PD')
            ->where('jadwal.hari_kerja', 'senin')
            ->select(
                'maping_dokter_dpjpvclaim.kd_dokter_bpjs',
                'maping_poli_bpjs.kd_poli_bpjs',
                'bridging_surat_kontrol_bpjs.no_surat',
                'bridging_surat_kontrol_bpjs.tgl_rencana',
                DB::raw("CONCAT(jadwal.jam_mulai, '-', jadwal.jam_selesai) AS jam_praktek"),
                'pasien.no_peserta',
                'pasien.no_ktp',
                'pasien.no_tlp',
                'pasien.no_rkm_medis'
            )
            ->first();

        if (!$data) {
            return response()->json([
                'status' => 404,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $token = BpjsHelper::getToken();
        if (!$token) {
            return response()->json([
                'status' => 500,
                'message' => 'Gagal mengambil token BPJS'
            ]);
        }

        $body = [
            'nomorkartu'     => $data->no_peserta,
            'nik'            => $data->no_ktp,
            'nohp'           => $data->no_tlp,
            'kodepoli'       => $data->kd_poli_bpjs,
            'norm'           => $data->no_rkm_medis,
            'tanggalperiksa' => $data->tgl_rencana,
            'kodedokter'     => $data->kd_dokter_bpjs,
            'jampraktek'     => $data->jam_praktek,
            'jeniskunjungan' => '1', // ← ubah jika kontrol (2)
            'nomorreferensi' => $data->no_surat
        ];

        $response = Http::withHeaders([
            'x-token'      => $token,
            'x-username'   => env('BPJS_USERNAME'),
            'Content-Type' => 'application/json'
        ])->post(env('BPJS_BASEURL') . '/ambilantrean', $body);

        return response()->json([
            'status' => $response->status(),
            'response' => $response->json()
        ]);
    }
}
