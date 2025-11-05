<?php

namespace App\Http\Livewire\AntrianFarmasi;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanFarmasi extends Component
{
    public $tgl1;
    public $tgl2;
    public $search = '';
    public $labels = [];
    public $values = [];
    public $listData;

    public function mount()
    {
        $this->resetFilter();
    }

    public function resetFilter()
    {
        $this->tgl1 = Carbon::today()->toDateString();
        $this->tgl2 = Carbon::today()->toDateString();
        $this->search = '';

        $this->loadData();
    }

    public function loadData()
    {
        if (!$this->tgl1 || !$this->tgl2) {
            $this->listData = collect();
            $this->labels = [];
            $this->values = [];
            return;
        }

        // Format tanggal lengkap
        $tgl1 = Carbon::parse($this->tgl1)->startOfDay()->toDateTimeString();
        $tgl2 = Carbon::parse($this->tgl2)->endOfDay()->toDateTimeString();

        // Statistik status
        $statistik = DB::table('antrian as a')
            ->join('reg_periksa as r', 'r.no_rawat', '=', 'a.no_rawat')
            ->join('pasien as p', 'p.no_rkm_medis', '=', 'r.no_rkm_medis')
            ->select(
                DB::raw("COALESCE(a.status,'Tidak Ada') as status"),
                DB::raw("COUNT(*) as total")
            )
            ->whereBetween('r.tgl_registrasi', [$tgl1, $tgl2])
            ->when($this->search, function($q){
                $q->where(function($x){
                    $x->where('r.no_rawat','like',"%{$this->search}%")
                      ->orWhere('r.no_rkm_medis','like',"%{$this->search}%")
                      ->orWhere('p.nm_pasien','like',"%{$this->search}%");
                });
            })
            ->groupBy('a.status')
            ->get();

        $this->labels = $statistik->pluck('status')->toArray();
        $this->values = $statistik->pluck('total')->toArray();

        // Data pasien
        $query = DB::table('antrian as a')
            ->join('reg_periksa as r', 'r.no_rawat', '=', 'a.no_rawat')
            ->join('pasien as p', 'p.no_rkm_medis', '=', 'r.no_rkm_medis')
            ->select(
                'a.no_rawat',
                'p.no_rkm_medis',
                'p.nm_pasien',
                'r.tgl_registrasi',
                'a.created_at as masuk',
                'a.updated_at as selesai',
                DB::raw("COALESCE(a.status,'Tidak Ada') as status"),
                'a.keterangan',
                DB::raw("TIMESTAMPDIFF(MINUTE, a.created_at, a.updated_at) as durasi_menit")
            )
            ->whereBetween('r.tgl_registrasi', [$tgl1, $tgl2])
            ->when($this->search, function($q){
                $q->where(function($x){
                    $x->where('p.nm_pasien','like',"%{$this->search}%")
                      ->orWhere('p.no_rkm_medis','like',"%{$this->search}%")
                      ->orWhere('a.no_rawat','like',"%{$this->search}%");
                });
            });

        $this->listData = $query->orderBy('r.tgl_registrasi','desc')->get();

        // Emit chart update
        $this->emit('refreshChart', [
            'labels' => $this->labels,
            'values' => $this->values
        ]);
    }

    public function render()
    {
        return view('livewire.antrian-farmasi.laporanfarmasi');
    }
}
