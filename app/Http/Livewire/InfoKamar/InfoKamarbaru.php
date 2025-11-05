<?php

namespace App\Http\Livewire\InfoKamar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class InfoKamarbaru extends Component
{
    public $ruanganList = [];
    public $currentIndex = 0;
    public $ruangan = [];
    public $namaRuangan = '';

    protected $listeners = ['next-room' => 'nextRoom'];

    public function mount()
    {
        $this->loadData();
        $this->setCurrentRoom();
    }

    public function render()
    {
        return view('livewire.info-kamar.info-kamarbaru');
    }

    public function nextRoom()
    {
        $this->currentIndex = ($this->currentIndex + 1) % count($this->ruanganList);
        $this->setCurrentRoom();
    }

    public function loadData()
    {
        $this->ruanganList = DB::table('bw_display_bad')
            ->select('ruangan')
            ->groupBy('ruangan')
            ->pluck('ruangan')
            ->toArray();
    }

    public function setCurrentRoom()
    {
        $ruang = $this->ruanganList[$this->currentIndex] ?? null;
        if (!$ruang) return;

        $this->namaRuangan = $ruang;

        $kamar = DB::table('bw_display_bad')
            ->select('kamar', 'kelas')
            ->where('ruangan', $ruang)
            ->groupBy('kamar')
            ->get();

        $result = [
            'total_bad' => 0,
            'total_isi' => DB::table('bw_display_bad')->where('ruangan', $ruang)->where('status', 1)->count(),
            'total_kosong' => DB::table('bw_display_bad')->where('ruangan', $ruang)->where('status', 0)->count(),
            'kamar' => []
        ];

        foreach ($kamar as $km) {

            $beds = DB::table('bw_display_bad')
                ->select('bad', 'bad as no_bed', 'status') // ✅ FIX: tambah 'bad'
                ->where('kamar', $km->kamar)
                ->where('ruangan', $ruang)
                ->orderBy('bad')
                ->get();

            $result['kamar'][$km->kamar] = [
                'kelas' => $km->kelas,
                'jumlah_isi' => $beds->where('status', 1)->count(),
                'jumlah_kosong' => $beds->where('status', 0)->count(),
                'beds' => $beds
            ];

            $result['total_bad'] += count($beds);
        }

        $this->ruangan = $result;
    }
}
