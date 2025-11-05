<?php

namespace App\Http\Livewire\InfoKamar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class InfoKamarbaru extends Component
{
    public $getRuangan = [];

    protected $listeners = ['refreshData' => 'loadData']; // untuk manual refresh jika diperlukan

    public function mount()
    {
        $this->loadData();
    }

    public function render()
    {
        // Auto-refresh setiap render
        return view('livewire.info-kamar.info-kamarbaru');
    }

    public function loadData()
    {
        try {
            $allBeds = DB::table('bw_display_bad')
                ->select('id', 'ruangan', 'kamar', 'bad', 'status', 'kelas')
                ->get();

            $this->getRuangan = $allBeds
                ->groupBy('ruangan')
                ->map(function ($beds, $ruangan) {
                    $kamarGrouped = $beds->groupBy('kamar')->map(function ($kamarBeds, $kamar) {
                        return [
                            'kelas' => $kamarBeds->first()->kelas,
                            'beds'  => $kamarBeds,
                            'jumlah_isi' => $kamarBeds->where('status', '1')->count(),
                            'jumlah_kosong' => $kamarBeds->where('status', '0')->count(),
                        ];
                    });

                    return [
                        'total_bed' => $beds->count(),
                        'total_isi' => $beds->where('status', '1')->count(),
                        'total_kosong' => $beds->where('status', '0')->count(),
                        'kamar' => $kamarGrouped,
                    ];
                });
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());
            $this->getRuangan = collect();
        }
    }
}
