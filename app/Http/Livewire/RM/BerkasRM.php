<?php

namespace App\Http\Livewire\RM;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class BerkasRM extends Component
{
    public $status_lanjut = '';
    public $jenis_berkas = '';
    public $cari_nomor = '';
    public $tgl1;
    public $tgl2;
    public function mount()
    {
        $this->tgl1 = now()->format('Y-m-d');
        $this->tgl2 = now()->format('Y-m-d');
    }
    public function render()
    {
        switch ($this->jenis_berkas) {
            case 'RESUMEDLL':
                break;
            case 'INACBG':
                break;
            case 'SCAN':
                $getBerkasPasien = DB::table('bw_file_casemix_scan')
                    ->select('bw_file_casemix_scan.file', 'reg_periksa.no_rawat', 'pasien.nm_pasien', 'pasien.no_peserta', 'reg_periksa.tgl_registrasi', 'pasien.no_rkm_medis')
                    ->join('reg_periksa', 'bw_file_casemix_scan.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->whereBetween('reg_periksa.tgl_registrasi', [$this->tgl1, $this->tgl2])
                    ->when($this->status_lanjut, function ($query) {
                        return $query->where('reg_periksa.status_lanjut', $this->status_lanjut);
                        $query->orwhere('reg_periksa.no_rkm_medis', 'LIKE', "%$cariKode%")
                            ->orwhere('pasien.nm_pasien', 'LIKE', "%$cariKode%")
                            ->orwhere('reg_periksa.no_rawat', 'LIKE', "%$cariKode%");
                    })
                    ->get();
                break;
            case 'HASIL':
                break;
            default:
                break;
        }

        return view('livewire.r-m.berkas-r-m', [
            'getBerkasPasien' => $getBerkasPasien,
        ]);
    }
}
