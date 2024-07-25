<?php

namespace App\Http\Livewire\RM;

use Carbon\Carbon;
use Livewire\Component;
use App\Services\BulanRomawi;
use App\Services\Rm\QueryBorlos;
use Illuminate\Support\Facades\DB;

class Bor extends Component
{
    public $year;
    public function mount() {
        $this->year = 2024;
        $this->Bor();
    }
    public function render()
    {
        $this->Bor();
        return view('livewire.r-m.bor');
    }

    public $Bor;
    public function Bor() {
        $Ruangan = DB::table('bw_borlos')
            ->select('bw_borlos.ruangan', 'bw_borlos.jml_bed')
            // ->where('bw_borlos.ruangan', 'ANGGREK')
            ->get();

        $borResults = [];
        for ($month = 1; $month <= 12; $month++) {
            $total_bed_perbulan[$month] = 0;
            $total_hari_rawat[$month] = 0;
        }
        foreach ($Ruangan as $room) {
            $kamar = $room->ruangan;
            $jumlah_tempat_tidur = $room->jml_bed;
            for ($month = 1; $month <= 12; $month++) {
                $start_Date = Carbon::create($this->year, $month, 1)->startOfMonth()->toDateString();
                $end_Date = Carbon::create($this->year, $month, 1)->endOfMonth()->toDateString();
                $jml_hari_dlm_sebulan = Carbon::create($this->year, $month, 1)->daysInMonth;
                $hari_rawat = QueryBorlos::jmlHariRawat($start_Date, $end_Date, $kamar);

                $bor = ($hari_rawat / ($jumlah_tempat_tidur * $jml_hari_dlm_sebulan)) * 100;

                $total_bed_perbulan[$month] += $jumlah_tempat_tidur;
                $total_hari_rawat[$month] += $hari_rawat;

                $borResults[$kamar][BulanRomawi::BulanIndo2(sprintf("%02d",$month))] = [
                    'hari_rawat' => $hari_rawat,
                    'jumlah_tempat_tidur' => $jumlah_tempat_tidur,
                    'jml_hari_dlm_sebulan' => $jml_hari_dlm_sebulan,
                    'bor' => $bor
                ];
            }
        }
        $TotalBor = [];
        foreach ($total_bed_perbulan as $month => $totalBeds) {
            $jml_hari_dlm_sebulan = Carbon::create($this->year, $month, 1)->daysInMonth;
            $hitung_total_bor = ($total_hari_rawat[$month] / ($totalBeds * $jml_hari_dlm_sebulan)) * 100;

            $TotalBor[BulanRomawi::BulanIndo2(sprintf("%02d",$month))] = [
                'hari_rawat' => $total_hari_rawat[$month],
                'jumlah_tempat_tidur' => $totalBeds,
                'jml_hari_dlm_sebulan' => $jml_hari_dlm_sebulan,
                'bor' => $hitung_total_bor
            ];
        }
        $borResults['SEMUA RUANGAN'] = $TotalBor;

        $this->Bor = $borResults;
        $this->emit('chartDataUpdated', $this->Bor);
    }
}
