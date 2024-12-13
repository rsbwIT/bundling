<?php

namespace App\Http\Livewire\AntrianPoli;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Http\Request;
use App\Events\PanggilPoliEvent;
use App\Services\DayListService;
use Illuminate\Support\Facades\DB;

class Panggilpoli extends Component
{
    public $kd_ruang_poli;
    public $kd_display;
    public function mount(Request $request)
    {
        $this->kd_ruang_poli = $request->kd_ruang_poli;
        $this->kd_display = $request->kd_display;
        $this->getPasien();
    }
    public function render()
    {
        $this->getPasien();
        return view('livewire.antrian-poli.panggilpoli');
    }
    public $getPasien;
    public function getPasien()
    {
        $dayList = DayListService::getDayList();
        $hari = $dayList[date('l')];
        $this->getPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_reg',
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_dokter',
                'reg_periksa.kd_pj',
                'jadwal.hari_kerja',
                'jadwal.jam_mulai',
                'bw_ruangpoli_dokter.kd_ruang_poli',
                'bw_ruangpoli_dokter.nama_dokter',
                'pasien.nm_pasien',
                'bw_log_antrian_poli.status',
                'penjab.png_jawab',
                'poliklinik.nm_poli'
            )
            ->leftJoin('bw_log_antrian_poli', 'bw_log_antrian_poli.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('bw_ruangpoli_dokter', 'reg_periksa.kd_dokter', '=', 'bw_ruangpoli_dokter.kd_dokter')
            ->join('jadwal', 'bw_ruangpoli_dokter.kd_dokter', '=', 'jadwal.kd_dokter')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab','reg_periksa.kd_pj','=','penjab.kd_pj')
            ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
            ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
            ->where('jadwal.hari_kerja', $hari)
            ->where('bw_ruangpoli_dokter.kd_ruang_poli', $this->kd_ruang_poli)
            ->orderBy('jadwal.jam_mulai', 'asc')
            ->orderBy('reg_periksa.no_reg', 'asc')
            ->orderBy('reg_periksa.jam_reg', 'asc')
            ->get();
    }
    public function handleLog($kd_dokter, $no_rawat, $kdLoket, $type)
    {
        $status = ($type == 'ada') ? '0' : '1';
        DB::table('bw_log_antrian_poli')->updateOrInsert(
            ['no_rawat' => $no_rawat],
            ['kd_ruang_poli' => $kdLoket, 'status' => $status]
        );
    }
    public function resetLog($no_rawat)
    {
        DB::table('bw_log_antrian_poli')
            ->where('no_rawat', $no_rawat)
            ->delete();
    }

    public function panggilLog($nm_pasien, $kd_ruang_poli, $nm_poli , $no_reg, $kd_display)
    {

        PanggilPoliEvent::dispatch($nm_pasien, $kd_ruang_poli, $nm_poli , $no_reg, $kd_display);

    }
}
