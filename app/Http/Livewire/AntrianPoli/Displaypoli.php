<?php

namespace App\Http\Livewire\AntrianPoli;

use Livewire\Component;
use App\Services\ValueENV;
use Illuminate\Http\Request;
use App\Services\DayListService;
use Illuminate\Support\Facades\DB;

class Displaypoli extends Component
{
    public $kd_display;
    public $pusher_key;
    public function mount(Request $request)
    {
        $this->kd_display = $request->kd_display;
        $this->getDisplay();
        $this->pusher_key = ValueENV::getPusherKey();
    }
    public function render()
    {
        $this->getDisplay();
        return view('livewire.antrian-poli.displaypoli');
    }
    public $getPoli;
    public $getPasien;
    // KIRI = 0
    // KANAN = 1
    public function getDisplay() {
        $dayList = DayListService::getDayList();
        $hari = $dayList[date('l')];
        $this->getPoli = DB::table('bw_ruang_poli')
            ->select('bw_ruang_poli.kd_ruang_poli',
                'bw_ruang_poli.nama_ruang_poli',
                'bw_ruang_poli.kd_display',
                'bw_ruang_poli.posisi_display_poli')
                ->where('bw_ruang_poli.kd_display', $this->kd_display)
                ->orderBy('bw_ruang_poli.posisi_display_poli','asc')
                ->get();
                $this->getPoli->map(function ($item) use ($hari) {
                    $item->getPasien = DB::table('reg_periksa')
                    ->select('reg_periksa.no_reg',
                        'reg_periksa.no_rawat',
                        'bw_ruangpoli_dokter.nama_dokter',
                        'jadwal.hari_kerja',
                        'jadwal.jam_mulai',
                        'bw_ruangpoli_dokter.kd_ruang_poli',
                        'pasien.nm_pasien',
                        'reg_periksa.kd_pj')
                    ->join('bw_ruangpoli_dokter','reg_periksa.kd_dokter','=','bw_ruangpoli_dokter.kd_dokter')
                    ->join('jadwal','bw_ruangpoli_dokter.kd_dokter','=','jadwal.kd_dokter')
                    ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
                    ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
                    ->where('jadwal.hari_kerja', $hari)
                    ->where('bw_ruangpoli_dokter.kd_ruang_poli', $item->kd_ruang_poli)
                    ->whereNotExists(function ($query) {
                        $query->from('bw_log_antrian_poli')
                            ->whereRaw('reg_periksa.no_rawat = bw_log_antrian_poli.no_rawat');
                    })
                    ->orderBy('jadwal.jam_mulai','asc')
                    ->orderBy('reg_periksa.no_reg','asc')
                    ->orderBy('reg_periksa.jam_reg','asc')
                    ->take(1)
                    ->get();
                });
    }
}
