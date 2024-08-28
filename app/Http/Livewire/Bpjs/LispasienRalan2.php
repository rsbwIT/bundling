<?php

namespace App\Http\Livewire\Bpjs;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\PrintPdfService;
use App\Services\GabungPdfService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class LispasienRalan2 extends Component
{
    public $tanggal1;
    public $tanggal2;
    public $penjamnin;
    public function mount()
    {
        $this->tanggal1 = date('Y-m-d');
        $this->tanggal2 = date('Y-m-d');
        $this->getListPasienRalan();
    }
    public function render()
    {
        $this->getListPasienRalan();
        return view('livewire.bpjs.lispasien-ralan2');
    }

    public $carinomor;
    public $getPasien;
    // 1 Get Pasien Ralan ==================================================================================
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
                'poliklinik.nm_poli',
                'bw_file_casemix_hasil.file'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('bw_file_casemix_hasil', 'bw_file_casemix_hasil.no_rawat', '=', 'reg_periksa.no_rawat')
            ->whereBetween('reg_periksa.tgl_registrasi', [$this->tanggal1, $this->tanggal2])
            ->where(function ($query) use ($cariKode) {
                if ($cariKode) {
                    $query->orwhere('reg_periksa.no_rkm_medis', '=', "$cariKode")
                        ->orwhere('pasien.nm_pasien', '=', "$cariKode")
                        ->orwhere('bridging_sep.no_sep', '=', "$cariKode");
                }
            })
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->get();
    }

    // 2 PROSES UPLOAD ==================================================================================
    // A
    public $keyModal;
    public $no_rawat;
    public $no_rkm_medis;
    public $nm_pasien;
    use WithFileUploads;
    public function SetmodalInacbg($key)
    {
        $this->keyModal = $key;
        $this->no_rawat = $this->getPasien[$key]['no_rawat'];
        $this->no_rkm_medis = $this->getPasien[$key]['no_rkm_medis'];
        $this->nm_pasien = $this->getPasien[$key]['nm_pasien'];
    }
    public $upload_file_inacbg = [];
    public function UploadInacbg($key, $no_rawat, $no_rkm_medis)
    {
        try {
            $no_rawatSTR = str_replace('/', '', $no_rawat);
            $file_name = 'INACBG' . '-' . $no_rawatSTR . '.' . $this->upload_file_inacbg[$key]->getClientOriginalExtension();

            $this->upload_file_inacbg[$key]->storeAs('file_inacbg',  $file_name, 'public');
            $livewire_tmp_file = 'livewire-tmp/' . $this->upload_file_inacbg[$key]->getFileName();
            Storage::delete($livewire_tmp_file);
            $cekBerkas = DB::table('bw_file_casemix_inacbg')->where('no_rawat', $no_rawat)
                ->exists();
            if (!$cekBerkas) {
                DB::table('bw_file_casemix_inacbg')->insert([
                    'no_rkm_medis' => $no_rkm_medis,
                    'no_rawat' => $no_rawat,
                    'file' => $file_name,
                ]);
            }
            session()->flash('successSaveINACBG', 'Berhasil Mengupload File Inacbg');
        } catch (\Throwable $th) {
            session()->flash('errorBundling', 'Gagal!! Upload File Inacbg');
        }
    }
    // B
    public function SetmodalScan($key)
    {
        $this->keyModal = $key;
        $this->no_rawat = $this->getPasien[$key]['no_rawat'];
        $this->no_rkm_medis = $this->getPasien[$key]['no_rkm_medis'];
        $this->nm_pasien = $this->getPasien[$key]['nm_pasien'];
    }
    public $upload_file_scan = [];
    public function UploadScan($key, $no_rawat, $no_rkm_medis)
    {
        $no_rawatSTR = str_replace('/', '', $no_rawat);
        $file_name = 'SCAN' . '-' . $no_rawatSTR . '.' . $this->upload_file_scan[$key]->getClientOriginalExtension();

        $this->upload_file_scan[$key]->storeAs('file_scan',  $file_name, 'public');
        $livewire_tmp_file = 'livewire-tmp/' . $this->upload_file_scan[$key]->getFileName();
        Storage::delete($livewire_tmp_file);
        try {
            $cekBerkas = DB::table('bw_file_casemix_scan')->where('no_rawat', $no_rawat)
                ->exists();
            if (!$cekBerkas) {
                DB::table('bw_file_casemix_scan')->insert([
                    'no_rkm_medis' => $no_rkm_medis,
                    'no_rawat' => $no_rawat,
                    'file' => $file_name,
                ]);
            }
            session()->flash('successSaveINACBG', 'Berhasil Mengupload File Scan');
        } catch (\Throwable $th) {
            session()->flash('errorBundling', 'Gagal!! Upload File Scan');
        }
    }

    // 3 PROSES SIMPAN KHANZA ==================================================================================
    public function SimpanKhanza($no_rawat, $no_sep)
    {
        try {
            PrintPdfService::printPdf($no_rawat, $no_sep);
            Session::flash('successSaveINACBG', 'Berhasil Menyimpan File Khanza');
        } catch (\Throwable $th) {
            session()->flash('errorBundling', 'Gagal!! Menyimpan File Khanza');
        }
    }

    // 4 PROSES GABUNG BERKAS ==================================================================================
    public  function GabungBerkas($no_rawat, $no_rkm_medis)
    {
        try {
            GabungPdfService::printPdf($no_rawat, $no_rkm_medis);
            session()->flash('successSaveINACBG', 'Berhasil Menggabungkan Berkas');
        } catch (\Throwable $th) {
            session()->flash('errorBundling', 'Gagal!! Cek Kelengkapan Berkas Inacbg / Scan / Khanza');
        }
    }
}
