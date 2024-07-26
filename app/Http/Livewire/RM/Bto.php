<?php

namespace App\Http\Livewire\RM;

use Carbon\Carbon;
use Livewire\Component;
use App\Services\BulanRomawi;
use App\Services\Rm\QueryBorlos;
use Illuminate\Support\Facades\DB;

class Bto extends Component
{
    public $year;
    public function mount() {
        $this->year = 2024;
        $this->Bto();
    }
    public function render()
    {
        $this->Bto();
        return view('livewire.r-m.bto');
    }
    public $Bto;
    public function Bto() {
        $Ruangan = DB::table('bw_borlos')
        ->select('bw_borlos.ruangan', 'bw_borlos.jml_bed')
        ->get();
        $btoResults = [];
        for ($month = 1; $month <= 12; $month++) {
            $total_pasien_keluar[$month] = 0;
            $total_jumlah_tempat_tidur[$month] = 0;
        }
        foreach ($Ruangan as $room) {
            for ($month = 1; $month <= 12; $month++) {
                $kamar = $room->ruangan;
                $jumlah_tempat_tidur = $room->jml_bed;
                $start_Date = Carbon::create($this->year, $month, 1)->startOfMonth()->toDateString();
                $end_Date = Carbon::create($this->year, $month, 1)->endOfMonth()->toDateString();
                $pasien_keluar = QueryBorlos::pasienKeluar($start_Date, $end_Date, $kamar);

                $bto = $pasien_keluar > 0 ? $pasien_keluar / $jumlah_tempat_tidur: 0;
                $btoResults[$kamar][BulanRomawi::BulanIndo2(sprintf("%02d",$month))] = [
                    'jumlah_tempat_tidur' => $jumlah_tempat_tidur,
                    'pasien_keluar' => $pasien_keluar,
                    'bto' => $bto,
                ];
                $total_pasien_keluar[$month] += $pasien_keluar;
                $total_jumlah_tempat_tidur[$month] += $jumlah_tempat_tidur;
            }
        }
        $TotalBto = [];
        foreach ($total_jumlah_tempat_tidur as $month => $jumlah_tempat_tidur) {
            $bto = $total_pasien_keluar[$month] > 0 ? $total_pasien_keluar[$month] / $total_jumlah_tempat_tidur[$month]: 0;
            $TotalBto[BulanRomawi::BulanIndo2(sprintf("%02d", $month))] = [
                'total_pasien_keluar' => $total_pasien_keluar[$month],
                'total_jumlah_tempat_tidur' => $total_jumlah_tempat_tidur[$month],
                'bto' => $bto,
            ];
        }
        $btoResults['SEMUA RUANGAN'] = $TotalBto;
        $this->Bto = $btoResults;
        $this->emit('chartDataBtoUpdated', $this->Bto);
    }
}
