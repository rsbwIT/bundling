<?php

namespace App\Http\Livewire\Lab;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\Lab\QueryLab;
use Illuminate\Support\Facades\DB;
use App\Services\Lab\ServiceSoftmedik;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BridgingalatlatLis extends Component
{
    public $carinomor;
    public $status_lanjut;
    public $tanggal1;
    public $tanggal2;
    public $cito;
    public $set_dokter_penerima;
    public $set_kd_dokter_penerima;
    public $set_nama_petugas;
    public $set_nip_petugas;
    public $testok;
    public function mount(Request $request)
    {
        $this->carinomor =  ($request->no_rawat) ? $request->no_rawat : '';
        $this->status_lanjut = ($request->no_rawat) ? $request->status_lanjut : '';
        $this->tanggal1 = date('Y-m-d');
        $this->cito = 'Y';
        $this->tanggal2 = date('Y-m-d');
        $this->getDataKhanza();
        $this->Setting();
        $this->getDokter();
        $this->getPetugas();
        $this->set_dokter_penerima = 'Pilih Dokter';
        $this->set_kd_dokter_penerima = '';
        $this->set_nama_petugas = 'Pilih Petugas';
        $this->set_nip_petugas = '';
        $this->testok = false;
    }
    public function render()
    {
        $this->getDokter();
        $this->getPetugas();
        $this->getDataKhanza();
        $this->Setting();
        return view('livewire.lab.bridgingalatlat-lis');
    }

    // ===========================================================================================================
    // Dropdown Manual DOKTER
    public function setDokterPenerima($kd_dokter, $nm_dokter)
    {
        $this->set_dokter_penerima = $nm_dokter;
        $this->set_kd_dokter_penerima = $kd_dokter;
        $this->cariDokter = '';
    }
    // Dropdown Manual DOKTER
    public $cariDokter;
    public $getDokter;
    public function getDokter()
    {
        $cariDokter = $this->cariDokter;
        if ($cariDokter) {
            try {
                $this->getDokter = DB::table('dokter')
                    ->select('dokter.kd_dokter', 'dokter.nm_dokter')
                    ->where('dokter.status', '=', '1')
                    ->where(function ($query) use ($cariDokter) {
                        $query->orwhere('dokter.nm_dokter', 'LIKE', "%$cariDokter%")
                            ->orwhere('dokter.kd_dokter', 'LIKE', "%$cariDokter%");
                    })
                    ->get();
            } catch (\Throwable $th) {
                $this->getDokter = [];
            }
        } else {
            $this->getDokter = [];
        }
    }
    //  Dropdown Manual PETUGAS
    public function setPetugasPenerima($nip, $nama)
    {
        $this->set_nama_petugas = $nama;
        $this->set_nip_petugas = $nip;
        $this->cariPetugas = '';
    }
    //  Dropdown Manual PETUGAS
    public $cariPetugas;
    public $getPetugas;
    public function getPetugas()
    {
        $cariPetugas = $this->cariPetugas;
        if ($cariPetugas) {
            try {
                $this->getPetugas = DB::table('petugas')
                    ->select('petugas.nip', 'petugas.nama')
                    ->where('petugas.status', '=', '1')
                    ->where(function ($query) use ($cariPetugas) {
                        $query->orwhere('petugas.nip', 'LIKE', "%$cariPetugas%")
                            ->orwhere('petugas.nama', 'LIKE', "%$cariPetugas%");
                    })
                    ->get();
            } catch (\Throwable $th) {
                $this->getPetugas = [];
            }
        } else {
            $this->getPetugas = [];
        }
    }
    // ===========================================================================================================

    public $Setting;
    function Setting()
    {
        $cache = new CacheService();
        $settingData = $cache->getSetting();
        $this->Setting = (array) $settingData;
    }

    public $getDatakhanza;
    public function getDataKhanza()
    {
        $this->getDatakhanza = QueryLab::getDatakhanza($this->carinomor, $this->tanggal1,  $this->tanggal2, $this->status_lanjut);
    }


    public $response;
    public function sendDataToLIS($key)
    {
        $Service = new  ServiceSoftmedik();
        // try {
        $data = $this->getDatakhanza;
        $order_test = [];
        foreach ($data[$key]['Permintaan'] as $permintaan) {
            $dataLab =  DB::table('template_laboratorium')
                ->select('template_laboratorium.kd_jenis_prw', 'template_laboratorium.id_template')
                ->where('template_laboratorium.kd_jenis_prw', $permintaan['kd_jenis_prw'])
                ->get();
            if (count($dataLab) > 1) {
                $order_test[] = $permintaan['kd_jenis_prw'];
            } else {
                $order_test[] = $dataLab->isEmpty() ? null : (string)$dataLab[0]->id_template;
            }
        }
        $sendToLis = [
            'order' => [
                'msh' => [
                    'product' => 'SOFTMEDIX LIS',
                    'version' => $Service->version(),
                    'user_id' => $Service->user_id(),
                    'key' => $Service->key(),
                ],
                'pid' => [
                    'pmrn' => $data[$key]['no_rkm_medis'],
                    'pname' => $data[$key]['nm_pasien'],
                    'sex' => $data[$key]['jk'],
                    'birth_dt' => Carbon::parse($data[$key]['tgl_lahir'])->format('d.m.Y'),
                    'address' => $data[$key]['alamat'],
                    'no_tlp' => $data[$key]['no_tlp'],
                    'no_hp' => $data[$key]['no_tlp'],
                    'email' => ($data[$key]['email']) ? $data[$key]['email'] : '-',
                    'nik' => ($data[$key]['nip']) ? $data[$key]['nip'] : '-',
                ],
                'obr' => [
                    'order_control' => $data[$key]['order_control'],
                    'ptype' => ($data[$key]['status_lanjut'] === 'Ralan') ? 'OP' : 'IP',
                    'reg_no' => $data[$key]['noorder'],
                    'order_lab' => $data[$key]['noorder'],
                    'provider_id' => $data[$key]['kd_pj'],
                    'provider_name' => $data[$key]['png_jawab'],
                    'order_date' => Carbon::parse($data[$key]['tgl_permintaan'])->format('d.m.Y') . ' ' . Carbon::parse($data[$key]['jam_permintaan'])->format('h:m:s'),
                    'clinician_id' => $data[$key]['kd_dr_perujuk'],
                    'clinician_name' => $data[$key]['dr_perujuk'],
                    'bangsal_id' => ($data[$key]['status_lanjut'] === 'Ralan') ? $data[$key]['kd_poli'] : $data[$key]['kd_bangsal'],
                    'bangsal_name' => ($data[$key]['status_lanjut'] === 'Ralan') ? $data[$key]['nm_poli'] : $data[$key]['nm_bangsal'],
                    // 'bed_id' => ($data[$key]['status_lanjut'] === 'Ralan') ? '0000' : $data[$key]['kd_bangsal'],
                    'bed_id' => 'KELAS3242A',
                    'bed_name' => ($data[$key]['status_lanjut'] === 'Ralan') ? '0000' : $data[$key]['nm_bangsal'],
                    'class_id' => ($data[$key]['status_lanjut'] === 'Ralan') ? '0' : substr($data[$key]['kelas'], 6),
                    'class_name' => ($data[$key]['status_lanjut'] === 'Ralan') ? '0' : $data[$key]['kelas'],
                    'cito' => $data[$key]['cito'],
                    'med_legal' => $data[$key]['med_legal'],
                    'user_id' => session('auth')['id_user'],
                    'reserve1' => $data[$key]['reserve1'],
                    'reserve2' => $data[$key]['reserve2'],
                    'reserve3' => $data[$key]['reserve3'],
                    'reserve4' => $data[$key]['reserve4'],
                    'order_test' => $order_test,
                ],
            ],
        ];
        $this->response = $Service->ServiceSoftmedixPOST($sendToLis);
        // dd($sendToLis);
        if ($this->response) {
            if ($this->response['response']['code'] === "200") {
                session()->flash('response200', $this->response['response']['message']);
            } else {
                session()->flash('response500', $this->response['response']['message']);
            }
        }
        // } catch (\Throwable $th) {
        // }
    }

    public $detailDataLis;
    public function getDataLIS($noorder, $kd_dokter, $nm_dokter, $id_user, $user)
    {
        try {
            $Service = new  ServiceSoftmedik();
            $data = $Service->ServiceSoftmedixGet($noorder);
            $this->detailDataLis = $data;
            $this->detailDataLis['response']['kd_dokter'] = $kd_dokter;
            $this->detailDataLis['response']['sampel']['result_test'] = collect($this->detailDataLis['response']['sampel']['result_test'])->map(function ($item) {
                $khanza = DB::table('template_laboratorium')
                    ->select('template_laboratorium.id_template', 'template_laboratorium.Pemeriksaan')
                    ->join('jns_perawatan_lab', 'template_laboratorium.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                    ->where('template_laboratorium.id_template', $item['test_id'])
                    ->first();
                $item['id_template'] = $khanza->id_template ?? '-';
                $item['Pemeriksaan'] = $khanza->Pemeriksaan ?? '-';
                $item['kd_dokter'] = $kd_dokter ?? '';
                return $item;
            });
            $this->set_dokter_penerima = $nm_dokter;
            $this->set_kd_dokter_penerima = $kd_dokter;
            $this->set_nama_petugas = $user;
            $this->set_nip_petugas = $id_user;
        } catch (\Throwable $th) {
            $this->detailDataLis = [];
        }
    }

    function getTestLAB($key)
    {
        $uniqueTests = [];
        $resultDetailPeriksaLab = [];
        foreach ($this->detailDataLis['response']['sampel']['result_test'] as  $item) {
            if (!in_array($item['nama_test'], $uniqueTests) && $item['test_id'] == $item['id_template']) {
                $resultDetailPeriksaLab[] = [
                    'kode_paket' => $item['kode_paket'],
                    'id_template' => $item['id_template'],
                    'hasil' => $item['hasil'],
                    'nilai_normal' => $item['nilai_normal'],
                    'Pemeriksaan' => $item['Pemeriksaan'],
                ];
                $uniqueTests[] = $item['nama_test'];
            }
        }
        $resultDetailPeriksaLab = collect($resultDetailPeriksaLab)->map(function ($item) use ($key) {
            $khanza = DB::table('template_laboratorium')
                ->select(
                    'template_laboratorium.kd_jenis_prw',
                    'template_laboratorium.bagian_rs',
                    'template_laboratorium.bhp',
                    'template_laboratorium.bagian_perujuk',
                    'template_laboratorium.bagian_dokter',
                    'template_laboratorium.bagian_laborat',
                    'template_laboratorium.kso',
                    'template_laboratorium.menejemen',
                    'template_laboratorium.biaya_item'
                )
                ->join('jns_perawatan_lab', 'template_laboratorium.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                ->where('template_laboratorium.kd_jenis_prw', $item['kode_paket'])
                ->first();
            $item['no_rawat'] = $this->getDatakhanza[$key]['no_rawat'] ?? '-';
            $item['kd_jenis_prw'] = $khanza->kd_jenis_prw ?? '-';
            $item['tgl_periksa'] = Carbon::parse($this->detailDataLis['response']['sampel']['acc_date'])->format('Y-m-d') ?? '-';
            $item['jam'] = Carbon::parse($this->detailDataLis['response']['sampel']['acc_date'])->format('h:m:s') ?? '-';
            // $item['bagian_rs'] = (int)$khanza->bagian_rs ?? 0;
            // $item['bhp'] = (int)$khanza->bhp ?? '-';
            // $item['bagian_perujuk'] = (int)$khanza->bagian_perujuk ?? '-';
            // $item['bagian_dokter'] = (int)$khanza->bagian_dokter ?? '-';
            // $item['bagian_laborat'] = (int)$khanza->bagian_laborat ?? '-';
            // $item['kso'] = (int)$khanza->kso ?? '-';
            // $item['menejemen'] = (int)$khanza->menejemen ?? '-';
            // $item['biaya_item'] = (int)$khanza->biaya_item ?? '-';
            $item['bagian_rs'] =  $khanza ? $khanza->bagian_rs : 0;
            $item['bhp'] = $khanza ? $khanza->bhp : 0;
            $item['bagian_perujuk'] = $khanza ? $khanza->bagian_perujuk : 0;
            $item['bagian_dokter'] = $khanza ? $khanza->bagian_dokter : 0;
            $item['bagian_laborat'] = $khanza ? $khanza->bagian_laborat : 0;
            $item['kso'] = $khanza ? $khanza->kso : 0;
            $item['menejemen'] = $khanza ? $khanza->menejemen : 0;
            $item['biaya_item'] = $khanza ? $khanza->biaya_item : 0;
            return $item;
        });



        // ================================================================================================================================================
        // 2 PERIKSA LAB
        $uniqueKodePaket = [];
        $resultPeriksaLab = [];
        foreach ($this->detailDataLis['response']['sampel']['result_test'] as  $item) {
            if (!in_array($item['kode_paket'], $uniqueKodePaket)) {
                $resultPeriksaLab[] = [
                    'kode_paket' => $item['kode_paket']
                ];
                $uniqueKodePaket[] = $item['kode_paket'];
            }
        }
        $resultPeriksaLab = collect($resultPeriksaLab)->map(function ($item) use ($key) {
            $khanza = DB::table('jns_perawatan_lab')
                ->select(
                    'jns_perawatan_lab.kd_jenis_prw',
                    'jns_perawatan_lab.nm_perawatan',
                    'jns_perawatan_lab.bagian_rs',
                    'jns_perawatan_lab.bhp',
                    'jns_perawatan_lab.tarif_perujuk',
                    'jns_perawatan_lab.tarif_tindakan_dokter',
                    'jns_perawatan_lab.tarif_tindakan_petugas',
                    'jns_perawatan_lab.kso',
                    'jns_perawatan_lab.menejemen',
                    'jns_perawatan_lab.total_byr',
                    'jns_perawatan_lab.kategori'
                )
                ->where('jns_perawatan_lab.kd_jenis_prw', $item['kode_paket'])
                ->first();
            $item['no_rawat'] = $this->getDatakhanza[$key]['no_rawat'] ?? '-';
            $item['nip'] = $this->set_nip_petugas == '' ? '-' : $this->set_nip_petugas;;
            $item['nm_perawatan'] = $khanza->nm_perawatan ?? '-';
            $item['tgl_periksa'] = Carbon::parse($this->detailDataLis['response']['sampel']['acc_date'])->format('Y-m-d') ?? '-';
            $item['jam'] = Carbon::parse($this->detailDataLis['response']['sampel']['acc_date'])->format('h:m:s') ?? '-';
            $item['dokter_perujuk'] = $this->getDatakhanza[$key]['kd_dr_perujuk'] ?? '-';
            $item['bagian_rs'] = $khanza ? $khanza->bagian_rs : 0;
            $item['bhp'] = $khanza ? $khanza->bhp : 0;
            $item['tarif_perujuk'] = $khanza ? $khanza->tarif_perujuk : 0;
            $item['tarif_tindakan_dokter'] = $khanza ? $khanza->tarif_tindakan_dokter : 0;
            $item['tarif_tindakan_petugas'] = $khanza ? $khanza->tarif_tindakan_petugas : 0;
            $item['kso'] = $khanza ? $khanza->kso : 0;
            $item['menejemen'] = $khanza ? $khanza->menejemen : 0;
            $item['biaya'] = $khanza ? $khanza->total_byr : 0;
            $item['kd_dokter'] = $this->set_kd_dokter_penerima == '' ? '-' : $this->set_kd_dokter_penerima;
            $item['status'] = $this->getDatakhanza[$key]['status_lanjut'] ?? '-';
            $item['kategori'] = $khanza->kategori ?? '-';
            return $item;
        });
// dd($resultDetailPeriksaLab, $resultPeriksaLab);
        foreach ($resultPeriksaLab as $item) {
            DB::table('periksa_lab')->insert([
                'no_rawat' => $item['no_rawat'],
                'nip' => $item['nip'],
                'kd_jenis_prw' => $item['kode_paket'],
                'tgl_periksa' => $item['tgl_periksa'],
                'jam' => $item['jam'],
                'dokter_perujuk' => $item['dokter_perujuk'],
                'bagian_rs' => $item['bagian_rs'],
                'bhp' => $item['bhp'],
                'tarif_perujuk' => $item['tarif_perujuk'],
                'tarif_tindakan_dokter' => $item['tarif_tindakan_dokter'],
                'tarif_tindakan_petugas' => $item['tarif_tindakan_petugas'],
                'kso' => $item['kso'],
                'menejemen' => $item['menejemen'],
                'biaya' => $item['biaya'],
                'kd_dokter' => $item['kd_dokter'],
                'status' => $item['status'],
                'kategori' => $item['kategori'],
            ]);
        }
        foreach ($resultDetailPeriksaLab as $item) {
            DB::table('detail_periksa_lab')->insert([
                'no_rawat' => $item['no_rawat'],
                'kd_jenis_prw' => $item['kd_jenis_prw'],
                'tgl_periksa' => $item['tgl_periksa'],
                'jam' => $item['jam'],
                'id_template' => $item['id_template'],
                'nilai' => $item['hasil'],
                'nilai_rujukan' => $item['nilai_normal'],
                'keterangan' => $item['Pemeriksaan'],
                'bagian_rs' => $item['bagian_rs'],
                'bhp' => $item['bhp'],
                'bagian_perujuk' => $item['bagian_perujuk'],
                'bagian_dokter' => $item['bagian_dokter'],
                'bagian_laborat' => $item['bagian_laborat'],
                'kso' => $item['kso'],
                'menejemen' => $item['menejemen'],
                'biaya_item' => $item['biaya_item'],
            ]);
        }
        $this->testok = true;
    }

    // TEST ========================================================================================================================
}
