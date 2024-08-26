<?php

namespace App\Http\Livewire\Bpjs;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeCariCasemix extends Component
{
    public $cariNorawat = '';
    public $getPasien;
    public function mount(Request $request)
    {
        $this->cariNorawat = $request->get('cariNorawat', '');
        $this->getPasien();
    }
    public function render()
    {
        $this->getPasien();
        return view('livewire.bpjs.home-cari-casemix');
    }
    public function getPasien()
    {
        $this->getPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'pasien.nm_pasien',
                'pasien.jk',
                'bridging_sep.no_sep',
                'bridging_sep.jnspelayanan'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where(function ($query) {
                $query->orWhere('reg_periksa.no_rawat', '=', $this->cariNorawat)
                    ->orWhere('reg_periksa.no_rkm_medis', '=', $this->cariNorawat)
                    ->orWhere('bridging_sep.no_sep', '=', $this->cariNorawat);
            })
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->get();
    }
}
