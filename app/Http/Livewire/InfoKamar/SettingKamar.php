<?php

namespace App\Http\Livewire\InfoKamar;

use Livewire\Component;
use App\Services\BulanRomawi;
use Illuminate\Support\Facades\DB;

class SettingKamar extends Component
{
    public $select_kamar;
    public function mount()
    {
        $this->select_kamar = 'Anggrek';
        $this->input_kelas = 'Kelas 1';
        $this->getKamar();
        $this->getRuang();
    }
    public function render()
    {
        $this->getKamar();
        $this->getRuang();
        return view('livewire.info-kamar.setting-kamar');
    }

    public $getRuang;
    public function getRuang()
    {
        $this->getRuang = DB::table('bw_display_bad')
            ->select('bw_display_bad.ruangan')
            ->groupBy('bw_display_bad.ruangan')
            ->get();
    }


    public $getRuangan;
    public function getKamar()
    {
        try {
            $this->getRuangan = DB::table('bw_display_bad')
                ->select('bw_display_bad.id', 'bw_display_bad.ruangan')
                ->where('bw_display_bad.ruangan', $this->select_kamar)
                ->groupBy('bw_display_bad.ruangan')
                ->get();
            $this->getRuangan->map(function ($item) {
                $item->getKamar = DB::table('bw_display_bad')
                    ->select('bw_display_bad.kamar', 'bw_display_bad.kelas', 'bw_display_bad.kelas')
                    ->where('bw_display_bad.ruangan', $item->ruangan)
                    ->groupBy('bw_display_bad.kamar')
                    ->get();
                $item->getKamar->map(function ($item) {
                    $item->getBed = DB::table('bw_display_bad')
                        ->select('bw_display_bad.id', 'bw_display_bad.ruangan', 'bw_display_bad.kamar', 'bw_display_bad.bad', 'bw_display_bad.status', 'bw_display_bad.kelas')
                        ->where('bw_display_bad.kamar', $item->kamar)
                        ->get();
                });
            });
        } catch (\Throwable $th) {
        }
    }

    public function actionIsi($status, $id)
    {
        if ($status == '1') {
            $updateStatus = '0';
        } else {
            $updateStatus = '1';
        }
        DB::table('bw_display_bad')
            ->where('id', $id)
            ->update(['status' => $updateStatus]);
    }

    // TAMBAH KAMAR
    public $input_bed = [];
    public function addInput()
    {
        if (count($this->input_bed) < 5) {
            $this->input_bed[] = count($this->input_bed);
        }
    }
    public function deleteInput($index)
    {
        unset($this->input_bed[$index]);
        $this->input_bed = array_values($this->input_bed);
    }

    public $input_kamar;
    public $input_kelas;
    public function tambahBed()
    {
        $lastId = DB::table('bw_display_bad')->orderBy('id', 'desc')->value('id');
        $lastNumber = $lastId ? intval(substr($lastId, 3)) : 0;
        foreach ($this->input_bed as $key => $value) {
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            $newId = 'DIS' . $newNumber;
            $tets = [
                'id' => $newId,
                'ruangan' => $this->select_kamar,
                'kamar' => $this->input_kamar,
                'bad' => BulanRomawi::angkaToAbjad($value),
                'kelas' => $this->input_kelas,
                'status' => '0',
            ];
            DB::table('bw_display_bad')->insert($tets);
            $lastNumber++;
        }
    }
}
