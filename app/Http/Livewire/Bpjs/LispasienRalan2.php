<?php

namespace App\Http\Livewire\Bpjs;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\PrintPdfService;
use App\Services\GabungPdfService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


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
                'bw_file_casemix_hasil.file',
                DB::raw('CASE WHEN resume_pasien.no_rawat IS NOT NULL THEN 1 ELSE 0 END as sudah_resume'),
                DB::raw('CASE WHEN data_triase_igd.no_rawat IS NOT NULL THEN 1 ELSE 0 END as sudah_triase'),
                DB::raw('CASE WHEN pemeriksaan_ralan.no_rawat IS NOT NULL THEN 1 ELSE 0 END as sudah_pemeriksaan'),
                DB::raw('CASE WHEN pasien_mati.no_rkm_medis IS NOT NULL THEN 1 ELSE 0 END as sudah_mati')
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('bw_file_casemix_hasil', 'bw_file_casemix_hasil.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('resume_pasien', 'resume_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('data_triase_igd', 'data_triase_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('pemeriksaan_ralan', 'pemeriksaan_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('pasien_mati', 'pasien_mati.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->whereBetween('reg_periksa.tgl_registrasi', [$this->tanggal1, $this->tanggal2])
            ->where(function ($query) use ($cariKode) {
                if ($cariKode) {
                    $query->orwhere('reg_periksa.no_rkm_medis', '=', "$cariKode")
                        ->orwhere('pasien.nm_pasien', '=', "$cariKode")
                        ->orwhere('bridging_sep.no_sep', '=', "$cariKode");
                }
            })
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->distinct() // Menghindari data ganda
            ->groupBy('reg_periksa.no_rkm_medis', 'reg_periksa.no_rawat')
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
    // public function UploadInacbg($key, $no_rawat, $no_rkm_medis)
    // {
    //     try {
    //         $no_rawatSTR = str_replace('/', '', $no_rawat);

    //         $file_name = 'INACBG' . '-' . $no_rawatSTR . '.' . $this->upload_file_inacbg[$key]->getClientOriginalExtension();

    //         $this->upload_file_inacbg[$key]->storeAs('file_inacbg',  $file_name, 'public');
    //         $livewire_tmp_file = 'livewire-tmp/' . $this->upload_file_inacbg[$key]->getFileName();
    //         Storage::delete($livewire_tmp_file);
    //         $cekBerkas = DB::table('bw_file_casemix_inacbg')->where('no_rawat', $no_rawat)
    //             ->exists();
    //         if (!$cekBerkas) {
    //             DB::table('bw_file_casemix_inacbg')->insert([
    //                 'no_rkm_medis' => $no_rkm_medis,
    //                 'no_rawat' => $no_rawat,
    //                 'file' => $file_name,
    //             ]);
    //         }
    //         session()->flash('successSaveINACBG', 'Berhasil Mengupload File Inacbg');
    //     } catch (\Throwable $th) {
    //         session()->flash('errorBundling', 'Gagal!! Upload File Inacbg');
    //     }
    // }


    public function UploadInacbg($key, $no_rawat, $no_rkm_medis)
    {
        try {
            if (!isset($this->upload_file_inacbg[$key]) || !$this->upload_file_inacbg[$key]->isValid()) {
                session()->flash('errorBundling', 'Gagal!! File tidak ditemukan atau tidak valid.');
                return;
            }

            $no_rawatSTR = str_replace('/', '', $no_rawat);
            $extension   = $this->upload_file_inacbg[$key]->getClientOriginalExtension();
            $file_name   = 'INACBG-' . $no_rawatSTR . '.' . $extension;

            // hapus lama jika ada
            $cekBerkas = DB::table('bw_file_casemix_inacbg')->where('no_rawat', $no_rawat)->first();
            if ($cekBerkas && $cekBerkas->file) {
                $oldPath = 'file_inacbg/' . $cekBerkas->file;
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Simpan file baru
            $this->upload_file_inacbg[$key]->storeAs('file_inacbg', $file_name, 'public');

            // Simpan ke DB
            DB::table('bw_file_casemix_inacbg')->updateOrInsert(
                ['no_rawat' => $no_rawat],
                [
                    'no_rkm_medis' => $no_rkm_medis,
                    'file'         => $file_name,
                ]
            );

            // generate URL file
            $urlFile = asset('storage/file_inacbg/' . $file_name);

            // kasih ke session / emit ke Livewire untuk dibuka langsung
            session()->flash('successSaveINACBG', 'Berhasil mengupload file INACBG');
            $this->dispatchBrowserEvent('open-inacbg-pdf', ['url' => $urlFile]);

            // 🔹 Tutup modal otomatis
            $this->dispatchBrowserEvent('close-modal', ['modal' => 'UploadInacbg']);

            // Reset input file
            $this->upload_file_inacbg[$key] = null;
        } catch (\Throwable $th) {
            // log dihapus
            session()->flash('errorBundling', 'Gagal!! Upload file INACBG: ' . $th->getMessage());
        }
    }




    //     } catch (\Throwable $th) {
    //         // Catat error sebagai warning (kuning)
    //         \Log::warning('Upload file INACBG bermasalah', [
    //             'error' => $th->getMessage(),
    //             'line'  => $th->getLine(),
    //             'file'  => $th->getFile(),
    //         ]);

    //         session()->flash('errorBundling', 'Gagal!! Upload file INACBG: ' . $th->getMessage());
    //     }
    //}


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
        // CEK apakah file-nya ada
        $file = $this->upload_file_scan[$key] ?? null;
        if (!$file || !$file->isValid()) {
            session()->flash('errorBundling', 'Gagal!! File tidak ditemukan atau tidak valid!');
            return;
        }

        try {
            $no_rawatSTR = str_replace('/', '', $no_rawat);

            $file_name = 'SCAN-' . $no_rawatSTR . '.' . $file->getClientOriginalExtension();
            $file->storeAs('file_scan', $file_name, 'public');

            // Hapus file temp Livewire kalau ada
            if (Storage::exists('livewire-tmp/' . $file->getFileName())) {
                Storage::delete('livewire-tmp/' . $file->getFileName());
            }

            // Cek apakah sudah ada file untuk no_rawat
            $cekBerkas = DB::table('bw_file_casemix_scan')->where('no_rawat', $no_rawat)->first();

            if ($cekBerkas) {
                // Jika ada, update file baru
                DB::table('bw_file_casemix_scan')
                    ->where('no_rawat', $no_rawat)
                    ->update([
                        'file' => $file_name,
                    ]);
            } else {
                // Jika belum ada, insert baru
                DB::table('bw_file_casemix_scan')->insert([
                    'no_rkm_medis' => $no_rkm_medis,
                    'no_rawat'     => $no_rawat,
                    'file'         => $file_name,
                ]);
            }

            // Flash message sukses
            session()->flash('successSaveINACBG', 'Berhasil Mengupload File Scan');

            // 🔹 Tutup modal otomatis
            $this->dispatchBrowserEvent('close-modal', ['modal' => 'UploadScan']);

            // 🔹 Reset input file
            $this->upload_file_scan[$key] = null;
        } catch (\Throwable $th) {
            session()->flash('errorBundling', 'Gagal!! Upload file Scan: ' . $th->getMessage());
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
