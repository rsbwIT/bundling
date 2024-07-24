<?php

namespace App\Http\Livewire\RM;

use Carbon\Carbon;
use Livewire\Component;
use App\Services\BulanRomawi;
use Illuminate\Support\Facades\DB;

class Bor extends Component
{
    public $year;
    public function mount() {
        $this->year = 2024;
        $this->Bor();
        $this->emit('initialChartData', $this->BOR);
    }
    public function render()
    {
        $this->Bor();
        $this->emit('initialChartData', $this->BOR);
        return view('livewire.r-m.bor');
    }

    public $BOR;
    public function Bor() {
        $Ruangan = DB::table('bw_borlos')
            ->select('bw_borlos.ruangan', 'bw_borlos.jml_bed')
            // ->where('bw_borlos.ruangan', 'ANGGREK')
            ->get();

        $borResults = [];
        for ($month = 1; $month <= 12; $month++) {
            $total_bed_perbulan[$month] = 0;
            $total_hari_perbulan[$month] = 0;
        }
        foreach ($Ruangan as $room) {
            $kamar = $room->ruangan;
            $jumlah_tempat_tidur = $room->jml_bed;
            for ($month = 1; $month <= 12; $month++) {
                $start_Date = Carbon::create($this->year, $month, 1)->startOfMonth()->toDateString();
                $end_Date = Carbon::create($this->year, $month, 1)->endOfMonth()->toDateString();
                $total_hari = DB::table('reg_periksa')
                    ->select(DB::raw('SUM(DATEDIFF(kamar_inap.tgl_keluar, kamar_inap.tgl_masuk)) as total_jumlah_hari'))
                    ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                    ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->whereBetween('reg_periksa.tgl_registrasi', [$start_Date, $end_Date])
                    ->where('bangsal.nm_bangsal', 'like', '%' . $kamar . '%')
                    ->first();
                $jumlah_hari = (int) ($total_hari->total_jumlah_hari ?? 0);
                $periode_hari = Carbon::create($this->year, $month, 1)->daysInMonth;

                $bor = ($jumlah_hari / ($jumlah_tempat_tidur * $periode_hari)) * 100;

                $borResults[$kamar][BulanRomawi::BulanIndo2(sprintf("%02d",$month))] = [
                    'jumlah_hari' => $jumlah_hari,
                    'jumlah_tempat_tidur' => $jumlah_tempat_tidur,
                    'periode_hari' => $periode_hari,
                    'bor' => $bor
                ];
                $total_bed_perbulan[$month] += $jumlah_tempat_tidur;
                $total_hari_perbulan[$month] += $jumlah_hari;
            }
        }
        $TotalBor = [];
        foreach ($total_bed_perbulan as $month => $totalBeds) {
            $periode_hari = Carbon::create($this->year, $month, 1)->daysInMonth;
            $hitung_total_bor = ($total_hari_perbulan[$month] / ($totalBeds * $periode_hari)) * 100;

            $TotalBor[BulanRomawi::BulanIndo2(sprintf("%02d",$month))] = [
                'jumlah_hari' => $total_hari_perbulan[$month],
                'jumlah_tempat_tidur' => $totalBeds,
                'periode_hari' => $periode_hari,
                'bor' => $hitung_total_bor
            ];
        }
        $borResults['SEMUA RUANGAN'] = $TotalBor;

        $this->BOR = $borResults;
        $this->emit('chartDataUpdated', $this->BOR);
    }
}
