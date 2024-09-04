<?php

namespace App\Http\Livewire\Test;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test extends Component
{
    public function mount()
    {
        $this->getSeting();
    }
    public function render()
    {
        $this->getSeting();
        return view('livewire.test.test');
    }

    public $getSeting;
    public function getSeting()
    {
        $this->getSeting = DB::connection('db_con2')->table('bw_setting_bundling')
            ->select('bw_setting_bundling.id', 'bw_setting_bundling.nama_berkas', 'bw_setting_bundling.status', 'bw_setting_bundling.urutan')
            ->orderBy('bw_setting_bundling.urutan', 'asc')
            ->get();
    }

    public function updateStatus($id,$value) {
      DB::connection('db_con2')->table('bw_setting_bundling')
        ->where('id', $id)
        ->update(['status' => $value]);
    }

    public function updateOrder($item)
    {
        foreach ($item as $key => $value) {
            DB::connection('db_con2')->table('bw_setting_bundling')
            ->where('id', $value)
            ->update(['urutan' => $key+1]);
        }
    }
}
