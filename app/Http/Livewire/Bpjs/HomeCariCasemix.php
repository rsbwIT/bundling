<?php

namespace App\Http\Livewire\Bpjs;

use GuzzleHttp\Client;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeCariCasemix extends Component
{
    public $cariNorawat = '';
    public $getPasien = [];
    public function mount(Request $request)
    {
        $this->cariNorawat = $request->get('cariNorawat');
        if (!empty($this->cariNorawat)) {
            $this->getPasien();
        }
    }
    public function render()
    {
        return view('livewire.bpjs.home-cari-casemix');
    }
    public function updatedCariNorawat()
    {
        $this->getPasien();
    }
    // public function getPasien()
    // {
    // $client = new Client();
    // $response = $client->request('GET', 'http://localhost:8001/casemix/1');
    // $data = json_decode($response->getBody()->getContents())->data;
    // // dd($data);
    // $this->getPasien = $data;



    // if ($this->cariNorawat) {
    //     $this->getPasien = DB::table('reg_periksa')
    //         ->select(
    //             'reg_periksa.no_rawat',
    //             'reg_periksa.no_rkm_medis',
    //             'reg_periksa.tgl_registrasi',
    //             'pasien.nm_pasien',
    //             'pasien.jk',
    //             DB::raw('COALESCE(bridging_sep.no_sep, "-") as no_sep'),
    //             DB::raw('COALESCE(bridging_sep.jnspelayanan, "-") as jnspelayanan'),
    //             'reg_periksa.status_lanjut'
    //         )
    //         ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
    //         ->leftJoin('bridging_sep', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
    //         ->where(function ($query) {
    //             $query->orWhere('reg_periksa.no_rawat', '=', $this->cariNorawat)
    //                 ->orWhere('reg_periksa.no_rkm_medis', '=', $this->cariNorawat)
    //                 ->orWhere('bridging_sep.no_sep', '=', $this->cariNorawat);
    //         })
    //         ->orderBy('reg_periksa.tgl_registrasi', 'desc')
    //         ->get();

    public function getPasien()
{
    if ($this->cariNorawat) {
        $this->getPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'pasien.nm_pasien',
                'pasien.jk',
                DB::raw("COALESCE(bridging_ranap.no_sep, bridging_ralan.no_sep, '-') AS no_sep"),
                DB::raw("
                    CASE
                        WHEN reg_periksa.status_lanjut = 'Ranap' THEN '1'
                        WHEN reg_periksa.status_lanjut = 'Ralan' THEN '2'
                        ELSE '-'
                    END AS jnspelayanan
                "),
                'reg_periksa.status_lanjut'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin(DB::raw("
                (SELECT bridging_sep.no_rawat, bridging_sep.no_sep
                 FROM bridging_sep
                 WHERE bridging_sep.jnspelayanan = '1'
                 AND bridging_sep.no_sep > 2
                 AND bridging_sep.no_sep = (
                     SELECT MIN(bridging_sep_inner.no_sep)
                     FROM bridging_sep bridging_sep_inner
                     WHERE bridging_sep_inner.jnspelayanan = '1'
                     AND bridging_sep_inner.no_rawat = bridging_sep.no_rawat
                     AND bridging_sep_inner.no_sep > 2
                 )
                ) AS bridging_ranap
            "), 'bridging_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin(DB::raw("
                (SELECT bridging_sep.no_rawat, bridging_sep.no_sep
                 FROM bridging_sep
                 WHERE bridging_sep.jnspelayanan = '2'
                 AND bridging_sep.no_sep > 2
                 AND bridging_sep.no_sep = (
                     SELECT MIN(bridging_sep_inner.no_sep)
                     FROM bridging_sep bridging_sep_inner
                     WHERE bridging_sep_inner.jnspelayanan = '2'
                     AND bridging_sep_inner.no_rawat = bridging_sep.no_rawat
                     AND bridging_sep_inner.no_sep > 2
                 )
                ) AS bridging_ralan
            "), 'bridging_ralan.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where(function ($query) {
                $query->orWhere('reg_periksa.no_rawat', '=', $this->cariNorawat)
                    ->orWhere('reg_periksa.no_rkm_medis', '=', $this->cariNorawat)
                    ->orWhere('bridging_ranap.no_sep', '=', $this->cariNorawat)
                    ->orWhere('bridging_ralan.no_sep', '=', $this->cariNorawat);
            })
            ->whereIn('reg_periksa.status_lanjut', ['Ranap', 'Ralan'])
            ->orderByRaw("CASE WHEN reg_periksa.status_lanjut = 'Ranap' THEN 1 ELSE 2 END") // Prioritaskan pasien rawat inap
            ->orderBy('reg_periksa.tgl_registrasi', 'desc') // Urutkan berdasarkan tanggal registrasi terbaru
            ->get();
        } else {
            $this->getPasien = [];
        }
    }
}
