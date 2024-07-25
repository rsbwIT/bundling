<?php

namespace App\Http\Livewire\RM;

use Carbon\Carbon;
use Livewire\Component;
use App\Services\BulanRomawi;
use App\Services\Rm\QueryBorlos;
use Illuminate\Support\Facades\DB;

class Los extends Component
{

    public $year;
    public function mount()
    {
        $this->year = 2024;
        $this->Los();
    }
    public function render()
    {
        $this->Los();
        return view('livewire.r-m.los');
    }

    public $Los;
    public  function Los()
    {
        $Ruangan = DB::table('bw_borlos')
            ->select('bw_borlos.ruangan')
            ->get();
        $losResult = [];
        for ($month = 1; $month <= 12; $month++) {
            $total_lama_dirawat[$month] = 0;
            $total_pasien_keluar[$month] = 0;
        }
        foreach ($Ruangan as $room) {
            $kamar = $room->ruangan;
            for ($month = 1; $month <= 12; $month++) {
                $start_Date = Carbon::create($this->year, $month, 1)->startOfMonth()->toDateString();
                $end_Date = Carbon::create($this->year, $month, 1)->endOfMonth()->toDateString();
                $jumlah_lama_dirawat = QueryBorlos::lamaDiRawat($start_Date, $end_Date, $kamar);
                $pasien_keluar = QueryBorlos::pasienKeluar($start_Date, $end_Date, $kamar);

                $los = $pasien_keluar > 0 ? $jumlah_lama_dirawat / $pasien_keluar : 0;

                $losResult[$kamar][BulanRomawi::BulanIndo2(sprintf("%02d", $month))] = [
                    'lama_dirawat' => $jumlah_lama_dirawat,
                    'pasien_keluar' => $pasien_keluar,
                    'los' => $los
                ];
                $total_lama_dirawat[$month] += $jumlah_lama_dirawat;
                $total_pasien_keluar[$month] += $pasien_keluar;
            }
        }
        $TotalBor = [];
        foreach ($total_lama_dirawat as $month => $total_lamarawat) {
            $totalPasienKeluar = $total_pasien_keluar[$month];
            $hitung_total_los = $totalPasienKeluar > 0 ? $total_lamarawat / $totalPasienKeluar : 0;
            $TotalBor[BulanRomawi::BulanIndo2(sprintf("%02d", $month))] = [
                'lama_dirawat' => $total_lamarawat,
                'pasien_keluar' => $totalPasienKeluar,
                'los' => $hitung_total_los
            ];
        }
        $losResult['SEMUA RUANGAN'] = $TotalBor;
        $this->Los = $losResult;
        $this->emit('updateChartLos', $this->Los);
    }
}
