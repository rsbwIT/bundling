<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Listnama extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $tgl1 = $request->input('tgl1', $tanggal ?? date('Y-m-d'));
        $tgl2 = $request->input('tgl2', $tanggal ?? date('Y-m-d'));
        $statusLanjut = $request->input('status_lanjut', '');

        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'pasien.no_ktp',
                'pasien.alamat',
                'reg_periksa.status_lanjut'
            )
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2]);

        if ($statusLanjut) {
            $query->where('reg_periksa.status_lanjut', $statusLanjut);
        }

        $pasien = $query->orderBy('reg_periksa.no_rawat')->get();

        return view('surat.listnama', compact('pasien', 'tgl1', 'tgl2', 'statusLanjut'));
    }

    public function suratKeteranganDokter(Request $request)
    {
        $no_rawat = $request->input('no_rawat');

        if (!$no_rawat) {
            return redirect()->back()->with('error', 'No. Rawat wajib diisi');
        }

        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.umur',
                'pasien.tgl_lahir',
                'pasien.no_ktp',
                'pasien.alamat',
                'dokter.nm_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->first();

        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view()->file(resource_path('views/surat/ket.dokter.blade.php'), compact('data'));
    }

    public function suratKeteranganVaksin(Request $request)
    {
        $no_rawat = $request->input('no_rawat');

        if (!$no_rawat) {
            return redirect()->back()->with('error', 'No. Rawat wajib diisi');
        }

        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.umur',
                'pasien.tgl_lahir',
                'pasien.no_ktp',
                'pasien.alamat',
                'dokter.nm_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->first();

        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view()->file(resource_path('views/surat/ket.vaksin.blade.php'), compact('data'));
    }
}

