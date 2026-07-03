<?php

namespace App\Http\Livewire\InfoKamar;

use Livewire\Component;
use App\Services\BulanRomawi;
use Illuminate\Support\Facades\DB;
use App\Services\Bpjs\ReferensiBPJS;

class SettingKamar extends Component
{
    public $statusMessage;
    public $syncingBedId = null;
    public $syncingBedLabel = null;

    protected $referensi;
    public function __construct()
    {
        $this->referensi = new ReferensiBPJS;
    }

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
                    ->select('bw_display_bad.kamar', 'bw_display_bad.kelas', 'bw_display_bad.kelas', 'bw_display_bad.nm_ruangan_bpjs')
                    ->where('bw_display_bad.ruangan', $item->ruangan)
                    ->groupBy('bw_display_bad.kamar')
                    ->get();
                $item->getKamar->map(function ($item) {
                    $item->getBed = DB::table('bw_display_bad')
                        ->select(
                            'bw_display_bad.id',
                            'bw_display_bad.ruangan',
                            'bw_display_bad.kamar',
                            'bw_display_bad.bad',
                            'bw_display_bad.status',
                            'bw_display_bad.kelas',
                            'bw_display_bad.kd_kelas_bpjs',
                            'bw_display_bad.nm_ruangan_bpjs'
                        )
                        ->where('bw_display_bad.kamar', $item->kamar)
                        ->get();
                });
            });
        } catch (\Throwable $th) {
        }
    }

    // public function actionIsi($status, $id, $kd_kelas_bpjs, $nm_ruangan_bpjs)
    // {
    //     date_default_timezone_set('Asia/Jakarta');
    //     if ($status == '1') {
    //         $updateStatus = '0';
    //     } else {
    //         $updateStatus = '1';
    //     }
    //     DB::table('bw_display_bad')
    //         ->where('id', $id)
    //         ->update(['status' => $updateStatus]);
    //     DB::table('bw_display_bad')
    //         ->where('nm_ruangan_bpjs', $nm_ruangan_bpjs)
    //         ->where('kd_kelas_bpjs', $kd_kelas_bpjs)
    //         ->update(['times_update' => now()]);

    //     $this->UpdateKamarMJKN($kd_kelas_bpjs, $nm_ruangan_bpjs);
    // }

    public function actionIsi($status, $id, $kd_kelas_bpjs, $nm_ruangan_bpjs)
    {
        date_default_timezone_set('Asia/Jakarta');

        $updateStatus = $this->getNextStatus($status);

        DB::table('bw_display_bad')->where('id', $id)->update(['status' => $updateStatus]);
        DB::table('bw_display_bad')->update(['times_update' => now()]);

        $this->syncingBedId = $id;
        $this->syncingBedLabel = 'Mengirim ke MJKN...';
        $this->getKamar();
        $this->getRuang();
        $this->statusMessage = 'Status kamar berubah di aplikasi lokal.';
        $this->emit('$refresh');

        $this->syncToMjkn($kd_kelas_bpjs, $nm_ruangan_bpjs);
    }

    public function syncToMjkn($kd_kelas_bpjs, $nm_ruangan_bpjs)
    {
        try {
            $this->UpdateKamarMJKN($kd_kelas_bpjs, $nm_ruangan_bpjs);

            if (!empty($this->respone)) {
                $this->statusMessage = 'Status kamar berubah di aplikasi lokal dan dikirim ke MJKN.';
            } else {
                $this->statusMessage = 'Status kamar berubah di aplikasi lokal, tetapi sinkronisasi MJKN belum memberi respons.';
            }
        } catch (\Throwable $th) {
            $this->respone = ['error' => $th->getMessage()];
            $this->statusMessage = 'Status kamar berubah di aplikasi lokal, sinkronisasi MJKN gagal.';
        } finally {
            $this->syncingBedId = null;
            $this->syncingBedLabel = null;
        }
    }

    public function getNextStatus($status)
    {
        return match ($status) {
            '0' => '1',
            '1' => '2',
            '2' => '0',
            default => '0',
        };
    }

    // TAMBAH KAMAR
    public $input_bed = [];
    public function addInput()
    {
        if (count($this->input_bed) < 6) {
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

    // UpdateKamar MJKN
    public $respone;
    public function UpdateKamarMJKN($kd_kelas_bpjs, $nm_ruangan_bpjs)
    {
        try {
            $udapteKamar = DB::table('bw_display_bad')
                ->select(
                    'bw_display_bad.ruangan',
                    'bw_display_bad.nm_ruangan_bpjs',
                    'bw_display_bad.kd_ruang',
                    'bw_display_bad.kd_kelas_bpjs',
                    DB::raw('COUNT(bw_display_bad.status) AS kapasitas'),
                    DB::raw('COUNT(CASE WHEN bw_display_bad.status = 0 THEN 0 END) AS tersedia'),
                    DB::raw('COUNT(CASE WHEN bw_display_bad.status = 0 THEN 0 END) AS tersedia_wanita'),
                    DB::raw('COUNT(CASE WHEN bw_display_bad.status = 0 THEN 0 END) AS tersedia_pria_wanita')
                )
                ->where('bw_display_bad.kd_kelas_bpjs', $kd_kelas_bpjs)
                ->where('bw_display_bad.nm_ruangan_bpjs', $nm_ruangan_bpjs)
                ->groupBy('bw_display_bad.kd_kelas_bpjs')
                ->first();

            if (!$udapteKamar) {
                $this->respone = ['error' => 'Data kamar tidak ditemukan untuk sinkronisasi MJKN.'];
                return;
            }

            $data = [
                'kodekelas' => $udapteKamar->kd_kelas_bpjs,
                'koderuang' => $udapteKamar->kd_ruang,
                'namaruang' => 'R ' . $udapteKamar->nm_ruangan_bpjs,
                'kapasitas' => $udapteKamar->kapasitas,
                'tersedia' => $udapteKamar->tersedia,
                'tersediapria' => 0,
                'tersediawanita' => 0,
                'tersediapriawanita' => $udapteKamar->tersedia,
            ];

            $response = $this->referensi->updateRuangan(json_encode($data));
            $decoded = json_decode($response);

            $this->respone = is_object($decoded) && isset($decoded->metadata)
                ? (array) $decoded->metadata
                : ['response' => $response];
        } catch (\Throwable $th) {
            $this->respone = ['error' => $th->getMessage()];
            throw $th;
        }
    }
}
