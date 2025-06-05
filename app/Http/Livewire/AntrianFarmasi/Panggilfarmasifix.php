<?php

namespace App\Http\Livewire\AntrianFarmasi;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;

class Panggilfarmasifix extends Component
{
    public $antrians;
    public $lastClickedRawat = null;
    public $lastClickedAction = null;
    public $filterRacik = null;
    public $filterKeterangan = null;

    protected $listeners = ['refreshAntrian' => 'loadAntrians'];

    public function mount()
    {
        $this->loadAntrians();
    }

    public function loadAntrians()
    {
        $query = DB::table('antrian')
            ->select(
                'nomor_antrian',
                'rekam_medik',
                'nama_pasien',
                'keterangan',
                'status',
                'tanggal',
                'racik_non_racik',
                'no_rawat'
            )
            ->whereDate('tanggal', Carbon::today());

        if ($this->filterRacik === 'racik') {
            $query->where('racik_non_racik', 'like', '%racik%');
        } elseif ($this->filterRacik === 'non_racik') {
            $query->where('racik_non_racik', 'like', '%non racik%');
        }

        if (!empty($this->filterKeterangan)) {
            $query->where('keterangan', 'like', '%' . $this->filterKeterangan . '%');
        }

        $this->antrians = $query->orderBy('nomor_antrian', 'asc')->get();
    }

    public function panggil($noRawat)
    {
        $antrian = DB::table('antrian')->where('no_rawat', $noRawat)->first();

        if ($antrian) {
            DB::table('antrian')
                ->where('no_rawat', $noRawat)
                ->update(['status' => 'dipanggil']);

            $this->lastClickedRawat = $noRawat;
            $this->lastClickedAction = 'Panggil';

            $namaPasien = $antrian->keterangan ?: $antrian->nama_pasien;

            $this->dispatchBrowserEvent('speakQueue', [
                'nomorAntrian' => $antrian->nomor_antrian,
                'namaPasien' => $namaPasien,
            ]);

            $this->loadAntrians();
        }
    }

    public function markAda($noRawat)
    {
        DB::table('antrian')
            ->where('no_rawat', $noRawat)
            ->update(['status' => 'selesai']);

        $this->lastClickedRawat = $noRawat;
        $this->lastClickedAction = 'Ada';

        $this->loadAntrians();
    }

    public function markTidakAda($noRawat)
    {
        DB::table('antrian')
            ->where('no_rawat', $noRawat)
            ->update(['status' => 'tidak ada']);

        $this->lastClickedRawat = $noRawat;
        $this->lastClickedAction = 'Tidak Ada';

        $this->loadAntrians();
    }

    public function ulangiPanggil($nomorAntrian)
    {
        $antrian = $this->antrians->firstWhere('nomor_antrian', $nomorAntrian);

        if ($antrian) {
            $this->panggil($antrian->no_rawat);
            $this->lastClickedRawat = $antrian->no_rawat;
            $this->lastClickedAction = 'Ulang';
        }
    }

    public function setFilterRacik($filter)
    {
        $this->filterRacik = $filter;
        $this->loadAntrians();
    }

    public function setFilterKeterangan($filter)
    {
        $this->filterKeterangan = $filter;
        $this->loadAntrians();
    }

    public function render()
    {
        return view('livewire.antrian-farmasi.panggilfarmasifix', [
            'antrians' => $this->antrians,
            'lastClickedRawat' => $this->lastClickedRawat,
            'lastClickedAction' => $this->lastClickedAction,
        ]);
    }
}
