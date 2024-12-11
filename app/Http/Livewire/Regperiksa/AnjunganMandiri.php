<?php

namespace App\Http\Livewire\Regperiksa;

use Carbon\Carbon;
use Livewire\Component;
use App\Services\TrackerSQL;
use App\Services\CacheService;
use App\Services\DayListService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Services\Regperiksa\FuntionKhanza;

class AnjunganMandiri extends Component
{
    public function mount()
    {
        $this->showItem = 1;
        $this->getPoli();
        $this->getKeluarga();
        $this->getPenjab();
    }

    public function render()
    {
        $this->getPoli();
        $this->getKeluarga();
        $this->getPenjab();
        return view('livewire.regperiksa.anjungan-mandiri');
    }

    public $getPenjab;
    function getPenjab()
    {
        $penjab = new CacheService;
        $this->getPenjab = $penjab->getPenjab();
    }

    public $getKeluarga;
    function getKeluarga()
    {
        $this->getKeluarga = DB::table('pasien')
            ->select('pasien.keluarga')
            ->groupBy('pasien.keluarga')
            ->get();
    }

    public $showItem;
    function ResertShow($no)
    {
        $this->showItem = $no;
        $this->getPoli();
        $this->getDokter();
        $this->getpasien();
        $this->getRegistrasi = [];
    }

    // GET PASIEN ===================================================================================
    public $getpasien;
    function setPasien()
    {
        $pasienBelum = FuntionKhanza::cekRegistrasiBelum($this->cariKode);
        $pasien = FuntionKhanza::cekRegistrasi($this->cariKode);
        $this->getpasien();
        if ($pasienBelum >= 1) {
            $this->showItem = 1;
            session()->flash('pasienBelum', 'Terdapat perawatan yang belum terselesaikan');
        } elseif ($pasien >= 1) {
            $this->showItem = 1;
            session()->flash('sudahada', 'Sudah Terdaftar Hari Ini.');
        } elseif ($this->getpasien->isNotEmpty()) {
            $this->showItem = 2;
        } else {
            $this->showItem = 1;
            session()->flash('message', 'Data tidak ditemukan.');
        }
    }
    public $cariKode;

    function getpasien()
    {
        $cariKode = $this->cariKode;
        if (!empty($cariKode)) {
            $this->getpasien = DB::table('pasien')
                ->select(
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.no_ktp',
                    'pasien.jk',
                    'pasien.umur',
                    'pasien.namakeluarga',
                    'pasien.kd_pj',
                    'pasien.keluarga',
                    'pasien.alamat',
                    'pasien.tgl_daftar'
                )
                ->where(function ($query) use ($cariKode) {
                    $query->orWhere('pasien.no_rkm_medis', '=', $cariKode)
                        ->orWhere('pasien.no_ktp', '=', $cariKode)
                        ->orWhere('pasien.no_peserta', '=', $cariKode);
                })
                ->limit(1)
                ->get();
        } else {
            $this->getpasien = collect();
        }
    }

    // GET POLI
    public $getPoli;
    function getPoli()
    {

        $this->getDokter = $this->getDokter;
        $this->getPoli = DB::table('poliklinik')
            ->select('poliklinik.kd_poli', 'poliklinik.nm_poli', 'poliklinik.registrasi', 'poliklinik.registrasilama')
            ->join('jadwal', 'poliklinik.kd_poli', '=', 'jadwal.kd_poli')
            ->where('jadwal.hari_kerja', '=', DayListService::hariKhanza(date('l')))
            ->groupBy('poliklinik.kd_poli')
            ->orderBy('poliklinik.nm_poli', 'asc')
            ->get();
    }

    // GET DOKTER ===================================================================================
    public $kdPoli;
    public $nm_poli;
    function setDokter($kdPoli, $nm_poli)
    {
        $this->getpasien();
        $this->kdPoli = $kdPoli;
        $this->nm_poli = $nm_poli;
        $this->getDokter();
        $this->showItem = 3;
    }

    public $getDokter;
    function getDokter()
    {
        $kdPoli =  $this->kdPoli;
        $nm_poli  = $this->nm_poli;
        try {
            $this->getDokter = DB::table('dokter')
                ->select(
                    'dokter.kd_dokter',
                    'dokter.nm_dokter',
                    'jadwal.kuota',
                    'jadwal.jam_mulai',
                    'jadwal.jam_selesai',
                    'list_dokter.foto',
                    DB::raw('IFNULL(list_dokter.kuota_tambahan, 0) as kuota_tambahan'),
                    DB::raw('IFNULL(list_dokter.kuota_tambahan, 0) + jadwal.kuota as total_kuota')
                )
                ->join('jadwal', 'dokter.kd_dokter', '=', 'jadwal.kd_dokter')
                ->leftJoin('list_dokter', 'dokter.kd_dokter', '=', 'list_dokter.kd_dokter')
                ->where('jadwal.hari_kerja', '=', DayListService::hariKhanza(date('l')))
                ->where('jadwal.kd_poli', '=', $kdPoli)
                ->groupBy('dokter.kd_dokter')
                ->orderBy('list_dokter.nama_dokter', 'asc')
                ->get()
                ->map(function ($item) use ($kdPoli, $nm_poli) {
                    $item->kd_poli = $kdPoli;
                    $item->nm_poli = $nm_poli;
                    $nextNoReg = DB::table('reg_periksa')
                        ->where('kd_poli', $kdPoli)
                        ->where('kd_dokter', $item->kd_dokter)
                        ->where('tgl_registrasi', Carbon::now()->format('Y-m-d'))
                        ->count();
                    $item->terdaftar = is_null($nextNoReg) ? 1 : $nextNoReg;
                    return $item;
                });
        } catch (\Throwable $th) {
            $this->getDokter = [];
        }
    }


    // GET REGISTRASI ===================================================================================
    public $registrasi;
    public $namakeluarga;
    public $keluarga;
    public $alamat;
    public $stts_daftar;
    public $penjab;
    function pilihDokter($kd_dokter, $kdPoli, $nm_dokter, $nm_poli)
    {
        // JIKA INGIN NOMOR REG BERDASARKAN PENJAMIN
        // $this->kd_dokter = $kd_dokter;
        // $this->kdPoli = $kdPoli;
        // $this->nm_dokter = $nm_dokter;
        // $this->nm_poli = $nm_poli;
        $this->getDokter();
        $this->stts_daftar = $this->getpasien[0]['namakeluarga'] == Carbon::now()->format('Y-m-d') ? "Baru" : "Lama";
        $this->namakeluarga = $this->getpasien[0]['namakeluarga'];
        $this->keluarga = $this->getpasien[0]['keluarga'];
        $this->alamat = $this->getpasien[0]['alamat'];
        $this->penjab = $this->getpasien[0]['kd_pj'];
        // $this->penjab = $this->penjab; // JIKA INGIN NOMOR REG BERDASARKAN PENJAMIN
        try {
            $data = [
                // 'no_reg' => FuntionKhanza::noRegistrasi($kd_dokter, $kdPoli, $this->penjab), // JIKA INGIN NOMOR REG BERDASARKAN PENJAMIN
                'no_reg' => FuntionKhanza::noRegistrasi($kd_dokter, $kdPoli),
                'no_rawat' => FuntionKhanza::noRawat(),
                'tgl_registrasi' => Carbon::now()->format('Y-m-d'),
                'jam_reg' => Carbon::now()->format('H:i:s'),
                'kd_dokter' => $kd_dokter,
                'no_rkm_medis' => $this->getpasien[0]['no_rkm_medis'],
                'kd_poli' => $kdPoli,
                'nm_dokter' => $nm_dokter,
                'nm_poli' => $nm_poli,
                'p_jawab' => $this->getpasien[0]['namakeluarga'],
            ];
            $this->registrasi = $data;
            $this->showItem = 4;
        } catch (\Throwable $th) {
            $this->registrasi = null;
        }
    }
    // JIKA INGIN NOMOR REG BERDASARKAN PENJAMIN
    // public $kd_dokter;
    // public $nm_dokter;
    // public function updatedPenjab()
    // {
    //     $this->pilihDokter($this->kd_dokter, $this->kdPoli, $this->nm_dokter, $this->nm_poli);
    // }


    public $getRegistrasi;
    public function Registrasi()
    {
        try {
            $data = [
                // 'no_reg' => FuntionKhanza::noRegistrasi($this->registrasi['kd_dokter'], $this->registrasi['kd_poli'], $this->penjab), // JIKA INGIN NOMOR REG BERDASARKAN PENJAMIN
                'no_reg' => FuntionKhanza::noRegistrasi($this->registrasi['kd_dokter'], $this->registrasi['kd_poli']),
                'no_rawat' => FuntionKhanza::noRawat(),
                'tgl_registrasi' => $this->registrasi['tgl_registrasi'],
                'jam_reg' => Carbon::now()->format('H:i:s'),
                'kd_dokter' => $this->registrasi['kd_dokter'],
                'no_rkm_medis' => $this->registrasi['no_rkm_medis'],
                'kd_poli' => $this->registrasi['kd_poli'],
                'p_jawab' => $this->namakeluarga,
                'almt_pj' => $this->alamat,
                'hubunganpj' => $this->keluarga,
                'biaya_reg' => (int)FuntionKhanza::getBiayaReg($this->registrasi['kd_poli'], $this->stts_daftar),
                'stts' => 'Belum',
                'stts_daftar' => $this->stts_daftar,
                'status_lanjut' => 'Ralan',
                'kd_pj' => $this->penjab,
                'umurdaftar' => FuntionKhanza::umurPasien($this->registrasi['no_rkm_medis'])['umur'],
                'sttsumur' => FuntionKhanza::umurPasien($this->registrasi['no_rkm_medis'])['sttsumur'],
                'status_bayar' => 'Belum Bayar',
                'status_poli' => FuntionKhanza::getStatuspoli($this->registrasi['no_rkm_medis'], $this->registrasi['kd_poli']),
            ];
            $pasien = FuntionKhanza::cekRegistrasi($this->cariKode);
            $pasienBelum = FuntionKhanza::cekRegistrasiBelum($this->cariKode);
            if ($pasien >= 1 || $pasienBelum >= 1) {
                session()->flash('gagalRegistrasi', 'Sudah Ada');
            } else {
                DB::table('reg_periksa')->insert($data);
                $this->getRegistrasi = Crypt::encryptString($data['no_rawat']);
                FuntionKhanza::TrackerSimpan('reg_periksa (Unit APM)', $data);
                session()->flash('messageRegistrasi', 'Berhasil');
            }
        } catch (\Throwable $th) {
            session()->flash('gagalRegistrasi', 'Gagal');
        }
    }
}
