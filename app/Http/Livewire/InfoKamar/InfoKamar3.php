<?php

namespace App\Http\Livewire\InfoKamar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class InfoKamar3 extends Component
{
    public $kd_kelas_bpjs  = [];
    public function mount()
    {
        $this->loadData();
        $this->getKelas();
    }
    public function render()
    {
        $this->getKelas();
        $this->loadData();
        return view('livewire.info-kamar.info-kamar3');
    }
    public $getRuangan;

    public $getKelas;
    public function getKelas() {
        $this->getKelas = DB::table('bw_display_bad')
        ->select('bw_display_bad.kd_kelas_bpjs')
        ->groupBy('bw_display_bad.kd_kelas_bpjs')
        ->get();
    }

    public function pilih($ruangan_id)
    {
        if (in_array($ruangan_id, $this->kd_kelas_bpjs)) {
            $this->kd_kelas_bpjs = array_diff($this->kd_kelas_bpjs, [$ruangan_id]);
        } else {
            $this->kd_kelas_bpjs[] = $ruangan_id;
        }

        $this->loadData();
    }
    public function resetPaginate()
    {
        $this->kd_kelas_bpjs = [];
    }
    public function loadData()
    {
        $kd_kelas_bpjs = $this->kd_kelas_bpjs;
        try {
            $allKamars = DB::table('bw_display_bad')
                ->select(
                    'kd_kelas_bpjs',
                    'nm_ruangan_bpjs',
                    'times_update',
                    DB::raw('COUNT(status) AS kapasitas'),
                    DB::raw('COUNT(CASE WHEN status = 0 THEN 0 END) AS tersedia'),
                    DB::raw("'0' AS tersedia_wanita"),
                    DB::raw("'0' AS tersedia_pria"),
                    DB::raw('COUNT(CASE WHEN status = 0 THEN 0 END) AS tersedia_pria_wanita')
                )
                ->where(function ($query) use ($kd_kelas_bpjs) {
                    if ($kd_kelas_bpjs) {
                        $query->whereIn('kd_kelas_bpjs', $kd_kelas_bpjs);
                    }
                })
                ->groupBy('kd_kelas_bpjs', 'nm_ruangan_bpjs')
                ->get();
            
            $groupedByKelas = $allKamars->groupBy('kd_kelas_bpjs');
            
            $this->getRuangan = $groupedByKelas->map(function ($items, $kelas) {
                $item = (object)[];
                $item->kd_kelas_bpjs = $kelas;
                $item->getkamar = $items;
                return $item;
            })->values();
        } catch (\Throwable $th) {
        }
    }
}
