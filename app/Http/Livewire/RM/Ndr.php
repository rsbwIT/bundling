<?php

namespace App\Http\Livewire\RM;

use Carbon\Carbon;
use Livewire\Component;
use App\Services\BulanRomawi;
use App\Services\Rm\QueryBorlos;
use Illuminate\Support\Facades\DB;

class Ndr extends Component
{
    public $year;
    public function mount() {
        $this->year = 2024;
        $this->Ndr();
    }
    public function render()
    {
        $this->Ndr();
        return view('livewire.r-m.ndr');
    }
    public $Ndr;
    public function Ndr() {
        $Ruangan = DB::table('bw_borlos')
            ->select('bw_borlos.ruangan', 'bw_borlos.jml_bed')
            ->get();
        $ndrResult = [];
        for ($month = 1; $month <= 12; $month++) {
            $total_pasien_mati_48jam[$month] = 0;
            $total_pasien_keluar[$month] = 0;
        }
        foreach ($Ruangan as $room) {
            for ($month = 1; $month <= 12; $month++) {
                $kamar = $room->ruangan;
                $start_Date = Carbon::create($this->year, $month, 1)->startOfMonth()->toDateString();
                $end_Date = Carbon::create($this->year, $month, 1)->endOfMonth()->toDateString();
                $pasien_keluar = QueryBorlos::pasienKeluar($start_Date, $end_Date, $kamar);
                $pasien_mati_48jam = QueryBorlos::pasienMati48jam($start_Date, $end_Date, $kamar);

                $ndr = $pasien_keluar > 0 ? ($pasien_mati_48jam / $pasien_keluar) * 1000 : 0;
                $ndrResult[$kamar][BulanRomawi::BulanIndo2(sprintf("%02d", $month))] = [
                    'pasien_keluar' => $pasien_keluar,
                    'pasien_mati_48jam' => $pasien_mati_48jam,
                    'ndr' => $ndr,
                ];

                $total_pasien_mati_48jam[$month] += $pasien_mati_48jam;
                $total_pasien_keluar[$month] += $pasien_keluar;
            }
        }

        $TotalNdr = [];
        foreach ($total_pasien_mati_48jam as $month => $total_pasien_mati_48jam) {
            $ndr = $total_pasien_keluar[$month] > 0 ? ($total_pasien_mati_48jam / $total_pasien_keluar[$month]) * 1000 : 0;

            $TotalNdr[BulanRomawi::BulanIndo2(sprintf("%02d", $month))] = [
                'total_pasien_keluar' => $total_pasien_keluar[$month],
                'total_pasien_mati_48jam' => $total_pasien_mati_48jam,
                'ndr' => $ndr,
            ];
        }
        $ndrResult['SEMUA RUANGAN'] = $TotalNdr;
        $this->Ndr = $ndrResult;
        $this->emit('chartDataNdrUpdated', $this->Ndr);
    }
}
