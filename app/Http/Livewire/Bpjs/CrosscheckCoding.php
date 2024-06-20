<?php

namespace App\Http\Livewire\Bpjs;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CrosscheckCoding extends Component
{
    public $tanggal1;
    public $tanggal2;
    public $statusLanjut;
    public function mount()
    {
        $this->tanggal1 = date('Y-m-d');
        $this->tanggal2 = date('Y-m-d');
        $this->statusLanjut = 'Ralan';
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
        ->select('reg_periksa.no_rkm_medis',
            'reg_periksa.no_rawat',
            'poliklinik.nm_poli',
            'pasien.nm_pasien',
            'resume_pasien.diagnosa_utama as ralan_diagnosa_utama',
            'resume_pasien_ranap.diagnosa_utama as ranap_diagnosa_utama'
        )
        ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
        ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
        ->leftJoin('resume_pasien','resume_pasien.no_rawat','=','reg_periksa.no_rawat')
        ->leftJoin('resume_pasien_ranap','resume_pasien_ranap.no_rawat','=','reg_periksa.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi', [$this->tanggal1, $this->tanggal2])
            ->where(function ($query) use ($cariKode) {
                $query->orwhere('reg_periksa.no_rkm_medis', 'LIKE', "%$cariKode%")
                    ->orwhere('pasien.nm_pasien', 'LIKE', "%$cariKode%")
                    ->orwhere('reg_periksa.no_rawat', 'LIKE', "%$cariKode%");
            })
            ->where('reg_periksa.status_lanjut', $this->statusLanjut)
            ->get();
    }
}
