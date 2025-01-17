<?php

namespace App\Http\Controllers\RM;

use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Http\Request;

class PasienRanapIgd extends Controller
{

    protected $cacheService;
    public function __construct(CacheService $cacheService)


    {
        $this->cacheService = $cacheService;
    }
    public function PasienRanapIgd(Request $request)

    {
        // $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        // $penjab = $this->cacheService->getPenjab();
        $results = DB::table('reg_periksa')
            ->select(
                'reg_periksa.kd_poli AS poli',
                'reg_periksa.tgl_registrasi AS tanggal_registrasi',
                'reg_periksa.no_rawat AS nomor_rawat',
                'pasien.no_rkm_medis AS nomor_rm',
                'pasien.nm_pasien AS nama_pasien',
                'pemeriksaan_ralan.tgl_perawatan AS tanggal_SOAP',
                'pemeriksaan_ralan.suhu_tubuh',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.tinggi',
                'pemeriksaan_ralan.berat',
                'pemeriksaan_ralan.spo2',
                'pemeriksaan_ralan.gcs',
                'pemeriksaan_ralan.kesadaran',
                'pemeriksaan_ralan.keluhan',
                'pemeriksaan_ralan.pemeriksaan',
                'pemeriksaan_ralan.alergi',
                'pemeriksaan_ralan.lingkar_perut',
                'pemeriksaan_ralan.rtl',
                'pemeriksaan_ralan.penilaian',
                'pemeriksaan_ralan.instruksi',
                'kamar_inap.diagnosa_awal',
                'pemeriksaan_ralan.evaluasi',
                'pegawai.nama'
            )
            ->whereBetween('reg_periksa.tgl_registrasi', [$request->tgl1, $request->tgl2])
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pemeriksaan_ralan', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pegawai', 'pemeriksaan_ralan.nip', '=', 'pegawai.nik')
            ->where('reg_periksa.kd_pj', '=', 'bpj')
            ->where('reg_periksa.kd_poli', '=', 'igdk')
            ->where('reg_periksa.status_lanjut', '=', 'ranap')
            ->orderBy('tanggal_registrasi', 'ASC')
            ->orderBy('nomor_rawat', 'ASC')
            ->paginate(1000); // Ubah get() menjadi paginate()

        // Tambahkan query string ke tautan paginasi
        $results->appends($request->all());

        return  view("rm.pasien-ranap-igd", [
            'results' => $results,
            // 'penjab' => $penjab,
        ]);
    }
}
