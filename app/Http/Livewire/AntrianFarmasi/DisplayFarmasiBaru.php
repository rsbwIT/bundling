<?php

namespace App\Http\Livewire\AntrianFarmasi;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class DisplayFarmasiBaru extends Component
{
    public $tanggal;
    public $antrians;

    public function mount()
    {
        $this->tanggal = now()->toDateString();
        $this->loadData();
    }

    public function loadData()
    {
        $this->antrians = DB::table('antrian')
            ->select('nomor_antrian', 'nama_pasien', 'keterangan', 'status')
            ->whereDate('tanggal', $this->tanggal)
            ->whereIn('keterangan', ['RACIKAN', 'NON RACIK'])
            ->get();
    }

    public function render()
    {
        return view('livewire.antrian-farmasi.displayfarmasibaru', [
            'antrians' => $this->antrians
        ]);
    }
}