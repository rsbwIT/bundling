<?php

namespace App\Http\Livewire\AntrianFarmasi;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class Farmasi extends Component
{
    public Collection $antrians;

    public ?string $lastRacik = null;
    public ?string $lastNonRacik = null;

    public function mount()
    {
        $this->antrians = collect();
        $this->loadAntrians();
    }

    // Method untuk load data antrian terbaru
    public function loadAntrians()
    {
        $this->antrians = DB::table('antrian')
            ->select(['nomor_antrian', 'nama_pasien', 'keterangan', 'status'])
            ->whereIn('status', ['antrian', 'dipanggil'])
            ->where(function ($query) {
                $query->where('keterangan', 'like', '%racikan%')
                      ->orWhere('keterangan', 'like', '%non racik%');
            })
            ->orderBy('nomor_antrian', 'asc')
            ->get();

        // Cari antrian racik & non racik pertama
        $antrianRacik = $this->antrians->first(fn($item) => stripos($item->keterangan ?? '', 'racikan') !== false);
        $antrianNonRacik = $this->antrians->first(fn($item) => stripos($item->keterangan ?? '', 'non racik') !== false);

        // Trigger event suara jika antrian racik baru
        if ($antrianRacik && $antrianRacik->nomor_antrian !== $this->lastRacik) {
            $this->dispatchBrowserEvent('panggil-antrian', [
                'jenis' => 'racik',
                'nomor' => $antrianRacik->nomor_antrian,
                'nama' => $antrianRacik->nama_pasien,
            ]);
            $this->lastRacik = $antrianRacik->nomor_antrian;
        }

        // Trigger event suara jika antrian non racik baru
        if ($antrianNonRacik && $antrianNonRacik->nomor_antrian !== $this->lastNonRacik) {
            $this->dispatchBrowserEvent('panggil-antrian', [
                'jenis' => 'nonracik',
                'nomor' => $antrianNonRacik->nomor_antrian,
                'nama' => $antrianNonRacik->nama_pasien,
            ]);
            $this->lastNonRacik = $antrianNonRacik->nomor_antrian;
        }
    }

    // Method yang dipanggil polling Livewire setiap 3 detik
    public function poll()
    {
        $this->loadAntrians();
    }

    public function render()
    {
        $antrianRacik = $this->antrians->first(fn($item) => stripos($item->keterangan ?? '', 'racikan') !== false);
        $antrianNonRacik = $this->antrians->first(fn($item) => stripos($item->keterangan ?? '', 'non racik') !== false);

        return view('livewire.antrian-farmasi.farmasi', compact('antrianRacik', 'antrianNonRacik'));
    }
}
