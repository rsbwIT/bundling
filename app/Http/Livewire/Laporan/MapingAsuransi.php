<?php

namespace App\Http\Livewire\Laporan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MapingAsuransi extends Component
{
    public $carinomor;
    public  function mount()
    {
        $this->getnamaAsuransi();
    }
    public function render()
    {
        $this->getnamaAsuransi();
        return view('livewire.laporan.maping-asuransi');
    }

    public $getAsuransi;
    public function getnamaAsuransi()
    {
        $cariKode = $this->carinomor;
        $this->getAsuransi = DB::table('penjab')
            ->select(
                'penjab.png_jawab',
                'penjab.kd_pj',
                'bw_maping_asuransi.nama_perusahaan',
                'bw_maping_asuransi.alamat_asuransi',
                'bw_maping_asuransi.kd_surat',
                'tf_rekening_rs',
                'nm_tf_rekening_rs'
            )
            ->leftJoin('bw_maping_asuransi', 'penjab.kd_pj', '=', 'bw_maping_asuransi.kd_pj')
            ->where('penjab.status', '=', '1')
            ->where(function ($query) use ($cariKode) {
                $query->orwhere('penjab.png_jawab', 'LIKE', "%$cariKode%")
                    ->orwhere('penjab.kd_pj', 'LIKE', "%$cariKode%");
            })
            ->get();
    }

    public $nama_perusahaan;
    public $alamat_asuransi;
    public $kd_surat;
    public $tf_rekening_rs;
    public $nm_tf_rekening_rs;
    function updateInsertNomor($keyAsuransi, $kd_pj)
    {
        DB::table('bw_maping_asuransi')->updateOrInsert(
            ['kd_pj' => $kd_pj],
            [
                'nama_perusahaan' => $this->getAsuransi[$keyAsuransi]['nama_perusahaan'],
                'alamat_asuransi' => $this->getAsuransi[$keyAsuransi]['alamat_asuransi'],
                'kd_surat' => $this->getAsuransi[$keyAsuransi]['kd_surat'],
                'tf_rekening_rs' => $this->getAsuransi[$keyAsuransi]['tf_rekening_rs'],
                'nm_tf_rekening_rs' => $this->getAsuransi[$keyAsuransi]['nm_tf_rekening_rs']
            ]
        );
    }
}
