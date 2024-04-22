<?php

namespace App\Http\Livewire\AntrianPendaftaran;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Services\DayListService;
use Illuminate\Support\Facades\DB;

class DisplayAntrian extends Component
{
    public $getLoket;
    public $kdpendaftaran;

    public function mount(Request $request)
    {
        $this->kdpendaftaran = $request->KdPendaftaran;
        $this->loadData();
    }

    public function render()
    {
        $this->loadData();
        return view('livewire.antrian-pendaftaran.display-antrian');
    }

    public function loadData()
    {
        try {
            $dayList = DayListService::getDayList();
            $hari = $dayList[date('l')];
            $this->getLoket = DB::table('loket')
                ->select('kd_loket', 'nama_loket', 'kd_pendaftaran')
                    ->where('kd_pendaftaran','=', $this->kdpendaftaran)
                    ->get();
                $this->getLoket->map(function ($item) use ($hari) {
                    $item->getPasien = DB::table('reg_periksa')
                        ->select('pasien.nm_pasien',
                            'reg_periksa.no_rawat',
                            'reg_periksa.no_reg',
                            'bw_jadwal_dokter.jam_mulai',
                            'bw_jadwal_dokter.hari_kerja',
                            'reg_periksa.kd_dokter',
                            'list_dokter.nama_dokter')
                        ->join('list_dokter','reg_periksa.kd_dokter','=','list_dokter.kd_dokter')
                        ->join('loket','list_dokter.kd_loket','=','loket.kd_loket')
                        ->join('bw_jadwal_dokter','list_dokter.kd_dokter','=','bw_jadwal_dokter.kd_dokter')
                        ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
                        ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
                        ->where('loket.kd_loket', $item->kd_loket)
                        ->where('bw_jadwal_dokter.hari_kerja', '=', $hari)
                        ->where('reg_periksa.kd_pj','=','BPJ')
                        ->whereNotExists(function ($query) {
                            $query->from('log_antrian_loket')
                                ->whereRaw('reg_periksa.no_rawat = log_antrian_loket.no_rawat');
                        })
                        ->orderBy('bw_jadwal_dokter.jam_mulai','asc')
                        ->orderBy('reg_periksa.no_reg','asc')
                        ->orderBy('reg_periksa.jam_reg','asc')
                        ->take(1)
                        ->get();
                });
        } catch (\Throwable $th) {
            $this->getLoket = [];
        }
    }
}
