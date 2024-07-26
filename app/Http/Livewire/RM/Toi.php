<?php

namespace App\Http\Livewire\RM;

use Carbon\Carbon;
use Livewire\Component;
use App\Services\BulanRomawi;
use App\Services\Rm\QueryBorlos;
use Illuminate\Support\Facades\DB;

class Toi extends Component
{
    public $year;
    public function mount() {
        $this->year = 2024;
        $this->Toi();
    }
    public function render()
    {
        $this->Toi();
        return view('livewire.r-m.toi');
    }

    public $Toi;
    public function Toi()
    {
        $Ruangan = DB::table('bw_borlos')
            ->select('bw_borlos.ruangan', 'bw_borlos.jml_bed')
            ->get();
        $toiResults = [];
        for ($month = 1; $month <= 12; $month++) {
            $total_hari_rawat[$month] = 0;
            $total_pasien_keluar[$month] = 0;
            $total_jumlah_tempat_tidur[$month] = 0;
        }
        foreach ($Ruangan as $room) {
            for ($month = 1; $month <= 12; $month++) {
                $kamar = $room->ruangan;
                $jumlah_tempat_tidur = $room->jml_bed;
                $start_Date = Carbon::create($this->year, $month, 1)->startOfMonth()->toDateString();
                $end_Date = Carbon::create($this->year, $month, 1)->endOfMonth()->toDateString();
                $jml_hari_dlm_sebulan = Carbon::create($this->year, $month, 1)->daysInMonth;
                $hari_rawat = QueryBorlos::jmlHariRawat($start_Date, $end_Date, $kamar);
                $pasien_keluar = QueryBorlos::pasienKeluar($start_Date, $end_Date, $kamar);

                // $toi = $pasien_keluar > 0 ? ((($jumlah_tempat_tidur * $jml_hari_dlm_sebulan) - $hari_rawat) / $pasien_keluar) : 0;
                $toi = $pasien_keluar > 0 ? ((($jumlah_tempat_tidur * $jml_hari_dlm_sebulan) - $hari_rawat) / $pasien_keluar) : 0;

                $toiResults[$kamar][BulanRomawi::BulanIndo2(sprintf("%02d",$month))] = [
                    'pasien_keluar' => $pasien_keluar,
                    'jumlah_tempat_tidur' => $jumlah_tempat_tidur,
                    'jml_hari_dlm_sebulan' => $jml_hari_dlm_sebulan,
                    'hari_rawat' => $hari_rawat,
                    'toi' => $toi,
                ];

                $total_hari_rawat[$month] += $hari_rawat;
                $total_pasien_keluar[$month] += $pasien_keluar;
                $total_jumlah_tempat_tidur[$month] += $jumlah_tempat_tidur;
            }
        }
        $TotalToi = [];
        foreach ($total_hari_rawat as $month => $total_hari_rawat) {
            $jml_hari_dlm_sebulan = Carbon::create($this->year, $month, 1)->daysInMonth;
            $toi = $total_pasien_keluar[$month] > 0 ? ((($total_jumlah_tempat_tidur[$month] * $jml_hari_dlm_sebulan) - $total_hari_rawat) / $total_pasien_keluar[$month]) : 0;
            $TotalToi[BulanRomawi::BulanIndo2(sprintf("%02d", $month))] = [
                'total_pasien_keluar' => $total_pasien_keluar[$month],
                'total_jumlah_tempat_tidur' => $total_jumlah_tempat_tidur[$month],
                'jml_hari_dlm_sebulan' => $jml_hari_dlm_sebulan,
                'total_hari_rawat' => $total_hari_rawat,
                'toi' => $toi
            ];
        }
        $toiResults['SEMUA RUANGAN'] = $TotalToi;
        $this->Toi = $toiResults;
        $this->emit('chartDataToiUpdated', $this->Toi);
    }
}
