<?php

namespace App\Http\Livewire\Regperiksa;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Listpasien extends Component
{
    public $tanggal1;
    public $tanggal2;
    public $status_lanjut;
    public $status_pulang;
    public $carinomor;
    public function mount()
    {
        $this->tanggal1 = date('Y-m-d');
        $this->tanggal2 = date('Y-m-d');
        $this->status_lanjut = 'Ralan';
        $this->status_pulang = 'blm_pulang';
        $this->getListPasien();
    }
    public function render()
    {
        $this->getListPasien();
        return view('livewire.regperiksa.listpasien');
    }
    function Ralan()
    {
        $cariKode = $this->carinomor;
        return DB::table('reg_periksa')
            ->select(
                'pasien.nm_pasien',
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'dokter.nm_dokter',
                DB::raw('COALESCE(bridging_sep.no_sep, "-") as no_sep')
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi', [$this->tanggal1, $this->tanggal2])
            ->where(function ($query) use ($cariKode) {
                $query->orwhere('reg_periksa.no_rkm_medis', 'LIKE', "%$cariKode%")
                    ->orwhere('pasien.nm_pasien', 'LIKE', "%$cariKode%")
                    ->orwhere('bridging_sep.no_sep', 'LIKE', "%$cariKode%");
            })
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->get();
    }
    function Ranap()
    {
        $cariKode = $this->carinomor;
        $sts_pulang = $this->status_pulang;
        return DB::table('reg_periksa')
            ->select(
                'pasien.nm_pasien',
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'dokter.nm_dokter',
                DB::raw('COALESCE(bridging_sep.no_sep, "-") as no_sep'),
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.stts_pulang'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where(function ($query) use ($sts_pulang) {
                if ($sts_pulang == 'blm_pulang') {
                    $query->where('kamar_inap.stts_pulang', '-');
                } elseif ($sts_pulang == 'tgl_masuk') {
                    $query->whereBetween('kamar_inap.tgl_masuk', [$this->tanggal1, $this->tanggal2]);
                } elseif ($sts_pulang == 'tgl_keluar') {
                    $query->whereBetween('kamar_inap.tgl_keluar', [$this->tanggal1, $this->tanggal2]);
                }
            })
            ->where(function ($query) use ($cariKode) {

                $query->orwhere('reg_periksa.no_rkm_medis', 'LIKE', "%$cariKode%")
                    ->orwhere('pasien.nm_pasien', 'LIKE', "%$cariKode%")
                    ->orwhere('bridging_sep.no_sep', 'LIKE', "%$cariKode%");
            })
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->groupBy('kamar_inap.no_rawat')
            ->get();
    }
    public $getPasien;
    // 1 Get Pasien ==================================================================================
    function getListPasien()
    {
        if ($this->status_lanjut == 'Ralan') {
            $this->getPasien = $this->Ralan();
        } else {
            $this->getPasien = $this->Ranap();
        }
    }
}
