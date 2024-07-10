<?php

namespace App\Http\Livewire\Laporan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class InvoceAsuransi extends Component
{
    public function mount() {
        $this->getPasien();
    }
    public function render()
    {
        $this->getPasien();
        return view('livewire.laporan.invoce-asuransi');
    }
    public $getPasien;
    public $carinomor;
    function getPasien()
    {
        $cariKode = $this->carinomor;
        $this->getPasien = collect();
        if ($cariKode) {
            $this->getPasien = DB::table('pasien')
                ->select(
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.nm_ibu',
                    'pasien.tgl_daftar',
                    'pasien.no_tlp',
                    'bw_peserta_asuransi.nomor_kartu',
                    'bw_peserta_asuransi.nomor_klaim'
                )
                ->leftJoin('bw_peserta_asuransi','pasien.no_rkm_medis','=','bw_peserta_asuransi.no_rkm_medis')
                ->where(function ($query) use ($cariKode) {
                    $query->orwhere('pasien.no_rkm_medis', 'LIKE', "%$cariKode%")
                        ->orwhere('pasien.nm_pasien', 'LIKE', "%$cariKode%")
                        ->orwhere('pasien.no_tlp', 'LIKE', "%$cariKode%");
                })
                ->get();
        }
    }

    public $nomor_kartu;
    public $nomor_klaim;
    public $no_tlp;
    function updateInsertNomor($keyInvoice, $no_rkm_medis)  {
        try {
            DB::table('bw_peserta_asuransi')->updateOrInsert(
                ['no_rkm_medis' => $no_rkm_medis],
                ['nomor_kartu' => $this->getPasien[$keyInvoice]['nomor_kartu'], 'nomor_klaim' => $this->getPasien[$keyInvoice]['nomor_klaim']]
            );
            Session::flash('sucsess' . $no_rkm_medis, 'Berhasil');
        } catch (\Throwable $th) {
            Session::flash('gagal' . $no_rkm_medis, 'gagal');
        }
    }
}
