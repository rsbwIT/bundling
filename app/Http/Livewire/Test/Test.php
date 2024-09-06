<?php

namespace App\Http\Livewire\Test;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\Bpjs\ReferensiBPJS;

class Test extends Component
{
    public function mount()
    {
        $this->diagnosa = 'Cari Diagnosa';
        $this->getSeting();
        $this->getDataDiagnosa();
    }
    public function render()
    {
        $this->getDataDiagnosa();
        $this->getSeting();
        return view('livewire.test.test');
    }

    // Search Dropdown ===============================================================
    public $diagnosa;
    public function setDiagnosa($diagnosa)
    {
        $this->diagnosa = $diagnosa;
        $this->cariDiagnosa = '';
    }
    public $cariDiagnosa;
    public $getDataDiagnosa;
    public function getDataDiagnosa()
    {
        $getdiagnosa = new ReferensiBPJS;
        if ($this->cariDiagnosa) {
            try {
                $data = json_decode($getdiagnosa->getDiagnosa($this->cariDiagnosa));
                $this->getDataDiagnosa = $data->response->diagnosa ?? [];
            } catch (\Throwable $th) {
                $this->getDataDiagnosa = [];
            }
        } else {
            $this->getDataDiagnosa = [];
        }
    }
    // Search Dropdown ===============================================================

    function submitButton() {
        dd($this->diagnosa);
    }

    // final drag and drop ===============================================================
    public $getSeting;
    public function getSeting()
    {
        $this->getSeting = DB::table('bw_setting_bundling')
            ->select('bw_setting_bundling.id', 'bw_setting_bundling.nama_berkas', 'bw_setting_bundling.status', 'bw_setting_bundling.urutan')
            ->orderBy('bw_setting_bundling.urutan', 'asc')
            ->get();
    }

    public function updateStatus($id, $value)
    {
        DB::table('bw_setting_bundling')
            ->where('id', $id)
            ->update(['status' => $value]);
    }

    public function updateOrder($item)
    {
        foreach ($item as $key => $value) {
            DB::table('bw_setting_bundling')
                ->where('id', $value)
                ->update(['urutan' => $key + 1]);
        }
    }
}
