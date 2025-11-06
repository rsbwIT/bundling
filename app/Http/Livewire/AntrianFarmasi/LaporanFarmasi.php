<?php

namespace App\Http\Livewire\AntrianFarmasi;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class LaporanFarmasi extends Component
{
    public $tgl1, $tgl2, $search = '';
    public $listData;

    public function mount()
    {
        // Awalnya kosong agar tidak langsung menampilkan semua data
        $this->listData = collect();
    }

    public function loadData()
    {
        // Validasi tanggal
        if (empty($this->tgl1) || empty($this->tgl2)) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'warning',
                'message' => 'Silakan pilih rentang tanggal terlebih dahulu.'
            ]);
            return;
        }

        try {
            // Query ambil data dari tabel antrian
            $this->listData = DB::table('antrian')
                ->select(
                    'tanggal',
                    'nomor_antrian',
                    'rekam_medik',
                    'nama_pasien',
                    'status',
                    'keterangan',
                    'no_rawat',
                    'created_at',
                    'updated_at',
                    'racik_non_racik'
                )
                ->whereBetween(DB::raw('DATE(tanggal)'), [$this->tgl1, $this->tgl2])
                ->when($this->search, function ($query) {
                    $search = trim($this->search);
                    $query->where(function ($q) use ($search) {
                        $q->where('nama_pasien', 'like', "%{$search}%")
                            ->orWhere('rekam_medik', 'like', "%{$search}%")
                            ->orWhere('no_rawat', 'like', "%{$search}%")
                            ->orWhere('nomor_antrian', 'like', "%{$search}%");
                    });
                })
                ->orderBy('tanggal', 'desc')
                ->get(); // hasil berupa object collection

            // Jika tidak ada data
            if ($this->listData->isEmpty()) {
                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'info',
                    'message' => 'Tidak ada data ditemukan untuk rentang tanggal tersebut.'
                ]);
            }
        } catch (\Exception $e) {
            // Tangani error SQL
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
            $this->listData = collect();
        }
    }

    public function resetFilter()
    {
        $this->reset(['tgl1', 'tgl2', 'search']);
        $this->listData = collect();
    }

    public function render()
    {
        return view('livewire.antrian-farmasi.laporanfarmasi');
    }
}
