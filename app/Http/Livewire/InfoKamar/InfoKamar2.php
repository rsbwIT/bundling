<?php

namespace App\Http\Livewire\InfoKamar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class InfoKamar2 extends Component
{
    public $getKamar;
    public function mount() {
        $this->getKamat();
    }
    public function render()
    {
        $this->getKamat();
        return view('livewire.info-kamar.info-kamar2');
    }

    function getKamat() {
        $this->getKamar = DB::table('bw_display_bad')
            ->select('kelas')
            ->selectRaw('COUNT(CASE WHEN status = 1 THEN 1 END) AS total_status_1')
            ->selectRaw('COUNT(CASE WHEN status = 0 THEN 1 END) AS total_status_0')
            ->groupBy('kelas')
            ->get();

    }
}
