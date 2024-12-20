<?php

namespace App\Http\Livewire\BrigingBpjs;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\Bpjs\ReferensiBPJS;

class UpdateJadwalDokter extends Component
{
    protected $ReferensiBpjs;
    public function __construct()
    {
        $this->ReferensiBpjs = new ReferensiBPJS;
    }
    public $times = [];
    public $hari;
    public function mount()
    {
        $this->tanggal = date('Y-m-d');
    }

    public function render()
    {
        $this->getDokter();
        $this->getPoli();
        return view('livewire.briging-bpjs.update-jadwal-dokter');
    }

    public function addInput()
    {
        if (count($this->times) < 3) {
            $this->times[] = [
                'buka' => '',
                'tutup' => ''
            ];
        }
    }

    public function deleteInput($index)
    {
        unset($this->times[$index]);
        $this->times = array_values($this->times);
    }

    public $getDokter;
    public function getDokter()
    {
        $this->getDokter = DB::table('maping_dokter_dpjpvclaim')
            ->select('maping_dokter_dpjpvclaim.kd_dokter', 'maping_dokter_dpjpvclaim.kd_dokter_bpjs', 'maping_dokter_dpjpvclaim.nm_dokter_bpjs')
            ->get();
    }
    public $getPoli;
    public function getPoli()
    {
        $this->getPoli = DB::table('maping_poli_bpjs')
            ->select('maping_poli_bpjs.kd_poli_rs', 'maping_poli_bpjs.kd_poli_bpjs', 'maping_poli_bpjs.nm_poli_bpjs')
            ->get();
    }

    public $dokter;
    public $poli;
    public $response;
    public function UpdateJadwal()
    {
        $this->times = array_map(function($time) {
            return array_merge(
                ['hari' => $this->hari],
                $time
            );
        }, $this->times);
        $jayParsedAry = [
            'kodepoli' => $this->poli,
            'kodesubspesialis' => $this->poli,
            'kodedokter' => $this->dokter,
            'jadwal' => $this->times
        ];
        dd(json_encode($jayParsedAry));

        // $data = json_decode($this->ReferensiBpjs->updateJadwalHfisDokter(json_encode($jayParsedAry)));
        // $this->response = [$data->metadata];
        // SENIN tgl 27	ANA - ANAK 	dr. Soelistyowati,Sp.A 	40 	08:00-12:00 // di rubah pada hari selasa jam 13:45
    }
    public function getDataTable($kodedokter, $kodepoli) {
        $this->dokter = $kodedokter;
        $this->poli = $kodepoli;
    }

    public $poliParam;
    public $tanggal;
    public $getJadwaldr;
    public function getJadwaldr()
    {
        try {
            $data = json_decode($this->ReferensiBpjs->getJadwalHfisDokter($this->poliParam, $this->tanggal));
            $this->getJadwaldr = $data->response;
            // dd($data);
        } catch (\Throwable $th) {
            $this->getJadwaldr = null;
        }
    }
}
