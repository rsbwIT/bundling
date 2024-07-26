<?php

namespace App\Http\Livewire\RM;

use Carbon\Carbon;
use Livewire\Component;
use App\Services\BulanRomawi;
use App\Services\Rm\QueryBorlos;
use Illuminate\Support\Facades\DB;

class Gdr extends Component
{
    public $year;
    public function mount()
    {
        $this->year = 2024;
        $this->Gdr();
    }
    public function render()
    {
        $this->Gdr();
        return view('livewire.r-m.gdr');
    }

    public $Gdr;
    public function Gdr()
    {
        $Ruangan = DB::table('bw_borlos')
            ->select('bw_borlos.ruangan', 'bw_borlos.jml_bed')
            ->get();
        $gdrResult = [];
        for ($month = 1; $month <= 12; $month++) {
            $total_pasien_mati[$month] = 0;
            $total_pasien_keluar[$month] = 0;
        }
        foreach ($Ruangan as $room) {
            for ($month = 1; $month <= 12; $month++) {
                $kamar = $room->ruangan;
                $start_Date = Carbon::create($this->year, $month, 1)->startOfMonth()->toDateString();
                $end_Date = Carbon::create($this->year, $month, 1)->endOfMonth()->toDateString();
                $pasien_keluar = QueryBorlos::pasienKeluar($start_Date, $end_Date, $kamar);
                $pasien_mati = QueryBorlos::pasienMati($start_Date, $end_Date, $kamar);

                $gdr = $pasien_keluar > 0 ? ($pasien_mati / $pasien_keluar) * 1000 : 0;
                $gdrResult[$kamar][BulanRomawi::BulanIndo2(sprintf("%02d", $month))] = [
                    'pasien_keluar' => $pasien_keluar,
                    'pasien_mati' => $pasien_mati,
                    'gdr' => $gdr,
                ];

                $total_pasien_mati[$month] += $pasien_mati;
                $total_pasien_keluar[$month] += $pasien_keluar;
            }
        }

        $TotalGdr = [];
        foreach ($total_pasien_mati as $month => $total_pasien_mati) {
            $gdr = $total_pasien_keluar[$month] > 0 ? ($total_pasien_mati / $total_pasien_keluar[$month]) * 1000 : 0;

            $TotalGdr[BulanRomawi::BulanIndo2(sprintf("%02d", $month))] = [
                'total_pasien_keluar' => $total_pasien_keluar[$month],
                'total_pasien_mati' => $total_pasien_mati,
                'gdr' => $gdr,
            ];
        }
        $gdrResult['SEMUA RUANGAN'] = $TotalGdr;
        $this->Gdr = $gdrResult;
        $this->emit('chartDataGdrUpdated', $this->Gdr);
    }
}
