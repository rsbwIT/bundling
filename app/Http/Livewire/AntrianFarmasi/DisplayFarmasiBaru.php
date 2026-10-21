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
        $now = now();
        $this->antrians = DB::table('antrian')
            ->select('nomor_antrian', 'nama_pasien', 'keterangan', 'status', 'created_at', 'updated_at')
            ->whereDate('tanggal', $this->tanggal)
            ->whereIn('keterangan', ['RACIKAN', 'NON RACIK'])
            ->get()
            ->map(function ($item) use ($now) {
                $createdAt = $item->created_at ? \Carbon\Carbon::parse($item->created_at) : null;
                $updatedAt = $item->updated_at ? \Carbon\Carbon::parse($item->updated_at) : null;

                $item->jam_ambil = $createdAt ? $createdAt->format('H:i') : '-';

                if ($createdAt) {
                    if ($item->status === 'DIPANGGIL' && $updatedAt && $updatedAt->greaterThan($createdAt)) {
                        $menit = $createdAt->diffInMinutes($updatedAt);
                    } else {
                        $menit = $createdAt->diffInMinutes($now);
                    }

                    if ($menit < 60) {
                        $item->lama_tunggu = $menit . ' Menit';
                        $item->lama_tunggu_singkat = $menit . ' mnt';
                    } else {
                        $jam = floor($menit / 60);
                        $sisa = $menit % 60;
                        $item->lama_tunggu = $jam . ' Jam ' . ($sisa > 0 ? $sisa . ' Menit' : '');
                        $item->lama_tunggu_singkat = $jam . 'j ' . $sisa . 'm';
                    }
                    $item->menit_tunggu = $menit;
                } else {
                    $item->lama_tunggu = '-';
                    $item->lama_tunggu_singkat = '-';
                    $item->menit_tunggu = 0;
                }

                return $item;
            });
    }

    public function render()
    {
        return view('livewire.antrian-farmasi.displayfarmasibaru', [
            'antrians' => $this->antrians
        ]);
    }
}