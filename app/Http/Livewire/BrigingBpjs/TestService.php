<?php

namespace App\Http\Livewire\BrigingBpjs;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\Bpjs\ReferensiBPJS;

class TestService extends Component
{
    protected $ReferensiBpjs;
    public function __construct()
    {
        $this->ReferensiBpjs = new ReferensiBPJS;
    }
    public $date;
    public $time;
    public function mount()
    {
        $this->date = date('Y-m-d');
        $this->time = date('H:i:s');
    }
    public function render()
    {
        return view('livewire.briging-bpjs.test-service');
    }
    public $kode_booking;
    public $taskid;
    public $waktu;
    public $menit;
    public $getCekin;
    public function Cekin()
    {
        try {
            date_default_timezone_set('Asia/Jakarta');
            $data = array_map('trim', explode(',', $this->kode_booking));
            foreach ($data as $key => $item) {
                $jam = date('H:i:s', strtotime($this->time . " +" . ($key * $this->menit) . " minutes"));
                $timestamp_sec = strtotime($this->date . $jam);
                $this->waktu = $timestamp_sec * 1000;
                $jayParsedAry = [
                    'kodebooking' => $item,
                    'taskid' => $this->taskid,
                    'waktu' => $this->waktu,
                ];
                $data = json_decode($this->ReferensiBpjs->cekinBPJS(json_encode($jayParsedAry)));
                $data->metadata->kodebooking = $item;
                $response[] = [$data->metadata];

                // DB::connection('db_con2')->table('bw_test_cekin')->insert([
                //     'kode_booking' => $item,
                //     'task_id' => $this->taskid,
                //     'jam' => $this->waktu,
                //     'timestamp_sec' => $jam,
                // ]);

            }
            $this->getCekin = $response;
        } catch (\Throwable $th) {
        }
    }

    public function TestKoneksi()
    {
        $p =[
            'hari' => '8',
            'buka' => "09:00",
            'tutup' => "13:00"
        ];
        $jayParsedAry = [
            'kodepoli' => "JAN",
            'kodesubspesialis' => "JAN",
            'kodedokter' => 415168,
            'jadwal' => [
                $p
            ]
        ];
        $data = json_decode($this->ReferensiBpjs->updateJadwalHfisDokter(json_encode($jayParsedAry)));
        dd($data);
    }
}
