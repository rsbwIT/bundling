<?php

namespace App\Http\Livewire\Keperawatan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PengawasKeperawatan extends Component
{

    protected $listeners = ['render'];

    public $kodejnslb;
    public $tanggal;
    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->cariPasien();
        $this->cariNamaKegiatan();
        $this->cariKewenanganKhusus();
        $this->getLookBook();
        $this->kodejnslb =  $this->kodejnslb;
    }
    public function render()
    {
        $this->cariPasien();
        $this->cariNamaKegiatan();
        $this->cariKewenanganKhusus();
        $this->getLookBook();
        $this->kodejnslb =  $this->kodejnslb;
        return view('livewire.keperawatan.pengawas-keperawatan');
    }

    // Get Pasien
    public $cari_nama_rm;
    public $getPasien;
    public $getLogbookPasien;
    public $getLogbookPasienKHusus;
    public function cariPasien()
    {
        $querypasien = $this->cari_nama_rm;
        $this->getPasien = DB::table('pasien')
            ->select('pasien.no_rkm_medis', 'pasien.nm_pasien', 'pasien.jk',  'pasien.no_tlp', 'pasien.tgl_lahir')
            ->where('pasien.no_rkm_medis', $this->cari_nama_rm)
            ->get();
        $this->getLogbookPasien = DB::table('bw_logbook_keperawatan')
            ->select(
                'bw_logbook_keperawatan.id_logbook',
                'bw_logbook_keperawatan.kd_kegiatan',
                'bw_logbook_keperawatan.user',
                'bw_logbook_keperawatan.no_rkm_medis',
                'bw_logbook_keperawatan.mandiri',
                'bw_logbook_keperawatan.supervisi',
                'bw_logbook_keperawatan.tanggal',
                'bw_nm_kegiatan_keperawatan.nama_kegiatan'
            )
            ->join('bw_nm_kegiatan_keperawatan', 'bw_logbook_keperawatan.kd_kegiatan', '=', 'bw_nm_kegiatan_keperawatan.kd_kegiatan')
            ->where('bw_logbook_keperawatan.user', session('auth.id_user'))
            ->where('bw_logbook_keperawatan.tanggal', $this->tanggal)
            ->where('bw_logbook_keperawatan.no_rkm_medis', $this->getPasien->pluck('no_rkm_medis')->first())
            ->get();
        $this->getLogbookPasienKHusus = DB::table('bw_logbook_keperawatan_kewenangankhusus')
            ->select(
                'bw_logbook_keperawatan_kewenangankhusus.id_kewenangankhusus',
                'bw_logbook_keperawatan_kewenangankhusus.kd_kewenangan',
                'bw_logbook_keperawatan_kewenangankhusus.user',
                'bw_logbook_keperawatan_kewenangankhusus.no_rkm_medis',
                'bw_logbook_keperawatan_kewenangankhusus.mandiri',
                'bw_logbook_keperawatan_kewenangankhusus.supervisi',
                'bw_logbook_keperawatan_kewenangankhusus.tanggal',
                'bw_kewenangankhusus_keperawatan.nama_kewenangan'
            )
            ->join('bw_kewenangankhusus_keperawatan', 'bw_logbook_keperawatan_kewenangankhusus.kd_kewenangan', '=', 'bw_kewenangankhusus_keperawatan.kd_kewenangan')
            ->where('bw_logbook_keperawatan_kewenangankhusus.user', session('auth.id_user'))
            ->where('bw_logbook_keperawatan_kewenangankhusus.tanggal', $this->tanggal)
            ->where('bw_logbook_keperawatan_kewenangankhusus.no_rkm_medis', $this->getPasien->pluck('no_rkm_medis')->first())
            ->get();
    }

    // Get Jenis LookBook
    public $getLookBook;
    public function getLookBook()
    {
        $this->getLookBook = DB::table('bw_jenis_lookbook')
            ->select('kd_jesni_lb', 'nama_jenis_lb')
            ->get();
    }

    // Get Nama Kegiatan Dasar
    public $mandiri = [];
    public $dibawahsupervisi = [];
    public $getKegiatan;
    public $cari_kode_kegiatan;
    public function cariNamaKegiatan()
    {
        $cariKode = $this->cari_kode_kegiatan;
        $this->getKegiatan = DB::table('bw_nm_kegiatan_keperawatan')
            ->select('bw_nm_kegiatan_keperawatan.kd_kegiatan', 'bw_nm_kegiatan_keperawatan.nama_kegiatan', 'default_mandiri', 'default_supervisi')
            ->where('kd_jesni_lb', $this->kodejnslb)
            ->where(function ($query) use ($cariKode) {
                $query->orwhere('kd_kegiatan', 'LIKE', "%$cariKode%")
                    ->orWhere('nama_kegiatan', 'LIKE', "%$cariKode%");
            })
            ->get();
        foreach ($this->getKegiatan as $key => $kegiatan) {
            $this->mandiri[$key] = $kegiatan->default_mandiri == 'false' ? false : true;
            $this->dibawahsupervisi[$key] = $kegiatan->default_supervisi == 'false' ? false : true;
        }
    }

    // Simpan Kegiatan Dasar
    public function simpanKegiatan($key, $kd_kegiatan, $user, $no_rkm_medis)
    {
        DB::table('bw_logbook_keperawatan')->insert([
            'kd_kegiatan' => $kd_kegiatan,
            'user' => $user,
            'no_rkm_medis' => $no_rkm_medis,
            'mandiri' => $this->mandiri[$key],
            'supervisi' => $this->dibawahsupervisi[$key],
            'tanggal' => $this->tanggal,
        ]);
        Session::flash('sucsess' . $key, 'Berhasil');
    }

    // Hapus Kegiatan Dasar
    public function HapusKegiatan($id_logbook)
    {
        try {
            DB::table('bw_logbook_keperawatan')
                ->where('bw_logbook_keperawatan.id_logbook', $id_logbook)
                ->delete();
        } catch (\Throwable $th) {
        }
    }

    // Get Nama Kegiatan Kewewnangan Khusus
    public $kw_mandiri = [];
    public $kw_dibawahsupervisi = [];
    public $getKewenanganKhusus;
    public $cari_kode_kewenagankhusus;
    public function cariKewenanganKhusus()
    {
        $cariKode = $this->cari_kode_kewenagankhusus;
        $this->getKewenanganKhusus = DB::table('bw_kewenangankhusus_keperawatan')
            ->select('bw_kewenangankhusus_keperawatan.kd_kewenangan', 'bw_kewenangankhusus_keperawatan.nama_kewenangan', 'default_mandiri', 'default_supervisi')
            ->where('kd_jesni_lb', $this->kodejnslb)
            ->where(function ($query) use ($cariKode) {
                $query->orwhere('kd_kewenangan', 'LIKE', "%$cariKode%")
                    ->orWhere('nama_kewenangan', 'LIKE', "%$cariKode%");
            })
            ->get();
        foreach ($this->getKewenanganKhusus as $key => $kegiatan) {
            $this->kw_mandiri[$key] = $kegiatan->default_mandiri == 'false' ? false : true;
            $this->kw_dibawahsupervisi[$key] = $kegiatan->default_supervisi == 'false' ? false : true;
        }
    }

    public function simpanKewenangan($key, $kd_kewenangan, $user, $no_rkm_medis)
    {
        DB::table('bw_logbook_keperawatan_kewenangankhusus')->insert([
            'kd_kewenangan' => $kd_kewenangan,
            'user' => $user,
            'no_rkm_medis' => $no_rkm_medis,
            'mandiri' => $this->kw_mandiri[$key],
            'supervisi' => $this->kw_dibawahsupervisi[$key],
            'tanggal' => $this->tanggal,
        ]);
        Session::flash('sucsess2' . $key, 'Berhasil');
    }
    // Hapus Kegiatan Dasar
    public function HapusKegiatanKhusus($id_kewenangankhusus)
    {
        try {
            DB::table('bw_logbook_keperawatan_kewenangankhusus')
                ->where('bw_logbook_keperawatan_kewenangankhusus.id_kewenangankhusus', $id_kewenangankhusus)
                ->delete();
        } catch (\Throwable $th) {
        }
    }
}
