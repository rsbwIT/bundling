<?php

namespace App\Http\Livewire\AntrianFarmasi;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class LaporanFarmasi extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $tgl1;            // tanggal awal filter
    public $tgl2;            // tanggal akhir filter
    public $jenisRacik = ''; // filter racik/non-racik

    // Reset pagination saat filter berubah
    public function updatingTgl1() { $this->resetPage(); }
    public function updatingTgl2() { $this->resetPage(); }
    public function updatingJenisRacik() { $this->resetPage(); }

    // Reset semua filter
    public function resetFilter()
    {
        $this->reset(['tgl1', 'tgl2', 'jenisRacik']);
        $this->resetPage();
    }

    public function render()
    {
        $query = DB::table('antrian')
            ->select(
                'tanggal',
                'nomor_antrian',
                'rekam_medik',
                'nama_pasien',
                'created_at',
                'updated_at',
                'racik_non_racik',
                'status',
                'no_rawat',
                'keterangan'
            );

        // Filter tanggal
        if (!empty($this->tgl1) && empty($this->tgl2)) {
            $query->whereDate('tanggal', $this->tgl1);
        } elseif (!empty($this->tgl1) && !empty($this->tgl2)) {
            $query->whereBetween(DB::raw('DATE(tanggal)'), [$this->tgl1, $this->tgl2]);
        } else {
            // default tanggal = hari ini
            $query->whereDate('tanggal', now());
        }

        // Filter racik / non-racik
        if (!empty($this->jenisRacik)) {
            $query->where('racik_non_racik', $this->jenisRacik);
        } else {
            $query->where('keterangan', 'RACIKAN');
        }

        $listData = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('livewire.antrian-farmasi.laporanfarmasi', [
            'listData' => $listData
        ]);
    }
}
