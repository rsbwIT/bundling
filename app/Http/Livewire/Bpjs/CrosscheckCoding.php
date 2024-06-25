<?php

namespace App\Http\Livewire\Bpjs;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CrosscheckCoding extends Component
{
    public $tanggal1;
    public $tanggal2;
    public $statusLanjut;
    public $penjamin;
    public function mount()
    {
        $this->tanggal1 = date('Y-m-d');
        $this->tanggal2 = date('Y-m-d');
        $this->statusLanjut = 'Ralan';
        $this->penjamin = 'Semua';
        $this->getListPasienRalan();
    }
    public function render()
    {
        $this->getListPasienRalan();
        return view('livewire.bpjs.crosscheck-coding');
    }

    public $carinomor;
    public $getPasien;
    // 1 Get Pasien Ralan ==================================================================================
    function getListPasienRalan()
    {
        $cariKode = $this->carinomor;
        $this->getPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'poliklinik.nm_poli',
                'pasien.nm_pasien',
                'resume_pasien.diagnosa_utama as ralan_diagnosa_utama',
                'resume_pasien_ranap.diagnosa_utama as ranap_diagnosa_utama',
                'penjab.png_jawab'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('resume_pasien', 'resume_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('resume_pasien_ranap', 'resume_pasien_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('kamar_inap','kamar_inap.no_rawat','=','reg_periksa.no_rawat')
            ->where(function ($query) use ($cariKode) {
                $query->orwhere('reg_periksa.no_rkm_medis', 'LIKE', "%$cariKode%")
                ->orwhere('pasien.nm_pasien', 'LIKE', "%$cariKode%")
                ->orwhere('reg_periksa.no_rawat', 'LIKE', "%$cariKode%");
            })
            ->where(function ($query) {
                if ($this->penjamin == "Bpjs") {
                    $query->where('penjab.png_jawab', 'Bpjs');
                } else if ($this->penjamin == 'NonBpjs') {
                    $query->where('penjab.png_jawab', '!=', 'Bpjs');
                }
            })
            ->where(function ($query) {
                if ($this->statusLanjut == "Ralan") {
                    $query->whereBetween('reg_periksa.tgl_registrasi', [$this->tanggal1, $this->tanggal2]);
                } else {
                    $query->whereBetween('kamar_inap.tgl_keluar', [$this->tanggal1, $this->tanggal2]);
                }
            })

            ->where('reg_periksa.status_lanjut', $this->statusLanjut)
            ->where('reg_periksa.stts', 'Sudah')
            ->orderBy('reg_periksa.no_rawat','asc')
            ->get();
                $this->getPasien->map(function ($item) {
                    $item->getDiagnosa = DB::table('diagnosa_pasien')
                        ->select('diagnosa_pasien.no_rawat')
                        ->where('diagnosa_pasien.no_rawat', $item->no_rawat)
                        ->get();
                });
                $this->getPasien->map(function ($item) {
                    $item->getCoding = DB::connection('db_con2')->table('bw_crosscheck_coding')
                    ->select('bw_crosscheck_coding.no_rawat')
                    ->where('bw_crosscheck_coding.no_rawat', $item->no_rawat)
                    ->get();
                });
    }
    function simpanPekerjaan($no_rawat, $tgl) {
        DB::connection('db_con2')->table('bw_crosscheck_coding')->insert([
            'no_rawat' => $no_rawat,
            'tanggal_pengerjaan' => $tgl,
            'coder' => session('auth.id_user'),
        ]);
    }
}
