<?php

namespace App\Http\Livewire\BrigingBpjs;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\Bpjs\ReferensiBPJS;
use Illuminate\Support\Facades\Session;

class Icare extends Component
{

    protected $referensi;
    public function __construct()
    {
        $this->referensi = new ReferensiBPJS;
    }
    public $tanggal1;
    public $tanggal2;
    public $penjamnin;
    public function mount()
    {
        $this->tanggal1 = date('Y-m-d');
        $this->tanggal2 = date('Y-m-d');
        $this->getPasien();
    }
    public function render()
    {
        $this->getPasien();
        return view('livewire.briging-bpjs.icare');
    }

    public $carinomor;
    public $getPasien;
    function getPasien()
    {
        $cariKode = $this->carinomor;
        $this->getPasien = DB::table('reg_periksa')
            ->select(
                'pasien.nm_pasien',
                'dokter.nm_dokter',
                'reg_periksa.no_rawat',
                'poliklinik.nm_poli',
                'maping_dokter_dpjpvclaim.kd_dokter_bpjs',
                'pasien.no_ktp'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('maping_dokter_dpjpvclaim', 'maping_dokter_dpjpvclaim.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->whereBetween('reg_periksa.tgl_registrasi', [$this->tanggal1, $this->tanggal2])
            ->where(function ($query) use ($cariKode) {
                $query->orwhere('reg_periksa.no_rkm_medis', 'LIKE', "%$cariKode%")
                    ->orwhere('pasien.nm_pasien', 'LIKE', "%$cariKode%")
                    ->orwhere('pasien.no_ktp', 'LIKE', "%$cariKode%")
                    ->orwhere('reg_periksa.no_rawat', 'LIKE', "%$cariKode%");
            })
            ->get();
    }
    public $getriwayat;
    function riwayatIcare($no_ktp, $kd_dokter_bpjs, $key)
    {
        $data = [
            'param' => (string) $no_ktp,
            'kodedokter' => (int) $kd_dokter_bpjs,
        ];
        $data = json_decode($this->referensi->validateICARE(json_encode($data)), true);
        $this->getriwayat = $data;
        if (isset($this->getriwayat['metaData']) && $this->getriwayat['metaData']['code'] == 200) {
            Session::flash('sucsessGetUrl' . $key, $this->getriwayat['response']['url']);
        }else{
            Session::flash('failedGetUrl' . $key, 'gagal');
        }
    }
}
