<?php

namespace App\Http\Livewire\Farmasi;

use Livewire\Component;
use App\Services\PrintPdfService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BundlingResepobat2 extends Component
{

    public $tanggal1;
    public $tanggal2;
    public $status_lanjut;
    public function mount()
    {
        $this->tanggal1 = date('Y-m-d');
        $this->tanggal2 = date('Y-m-d');
        $this->status_lanjut = 'Ralan';
        $this->getListPasienRalan();
    }

    public function render()
    {
        $this->getListPasienRalan();
        return view('livewire.farmasi.bundling-resepobat2');
    }

    public $carinomor;
    public $getPasien;
    function getListPasienRalan()
    {
        $cariKode = $this->carinomor;
        $this->getPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.status_bayar',
                DB::raw('COALESCE(bridging_sep.no_sep, "-") as no_sep'),
                'pasien.nm_pasien',
                'bridging_sep.tglsep',
                'poliklinik.nm_poli'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi', [$this->tanggal1, $this->tanggal2])
            ->where(function ($query) use ($cariKode) {
                $query->orwhere('reg_periksa.no_rkm_medis', 'LIKE', "%$cariKode%")
                    ->orwhere('pasien.nm_pasien', 'LIKE', "%$cariKode%")
                    ->orwhere('reg_periksa.no_rawat', 'LIKE', "%$cariKode%")
                    ->orwhere('bridging_sep.no_sep', 'LIKE', "%$cariKode%");
            })
            ->where('reg_periksa.status_lanjut', $this->status_lanjut)
            ->get();
            $this->getPasien->map(function ($item) {
                $item->fileFarmasi = DB::table('file_farmasi')
                    ->select('file_farmasi.file')
                    ->where('file_farmasi.jenis_berkas', '=', 'HASIL-FARMASI2')
                    ->where('file_farmasi.no_rawat',$item->no_rawat)
                    ->first();
            });
    }

    function SimpanResep($no_rawat, $no_sep)
    {
        try {
            PrintPdfService::printPdfResep($no_rawat, $no_sep);
        } catch (\Throwable $th) {
            session()->flash('errorBundling', 'Gagal!! Menyimpan File Khanza');
        }
    }
}
