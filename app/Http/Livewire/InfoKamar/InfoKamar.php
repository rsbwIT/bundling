<?php

namespace App\Http\Livewire\InfoKamar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class InfoKamar extends Component
{
    public function mount()
    {
        $this->loadData();
    }
    public function render()
    {
        $this->loadData();
        return view('livewire.info-kamar.info-kamar');
    }
    public $getRuangan;
    public function loadData()
    {
        try {
            $allBeds = DB::table('bw_display_bad')->get();
            $groupedByRuangan = $allBeds->groupBy('ruangan');
            
            $this->getRuangan = $groupedByRuangan->map(function ($items, $ruanganName) {
                $item = (object)[];
                $item->id = $items->first()->id;
                $item->ruangan = $ruanganName;
                
                $item->getKamarIsi = $items->where('status', '1')->count();
                $item->getKamarKosong = $items->where('status', '0')->count();
                
                $groupedByKamar = $items->groupBy('kamar');
                $item->getKamar = $groupedByKamar->map(function ($bedItems, $kamarName) use ($ruanganName) {
                    $kamarItem = (object)[];
                    $kamarItem->kamar = $kamarName;
                    $kamarItem->kelas = $bedItems->first()->kelas;
                    $kamarItem->ruangan = $ruanganName;
                    $kamarItem->getBed = $bedItems;
                    return $kamarItem;
                })->values();
                
                return $item;
            })->values();
        } catch (\Throwable $th) {
        }
    }


}
