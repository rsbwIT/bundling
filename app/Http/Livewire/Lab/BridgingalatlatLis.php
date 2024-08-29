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
    public function mount(Request $request)
    {
        $this->carinomor =  ($request->no_rawat) ? $request->no_rawat : '';
        $this->status_lanjut = ($request->no_rawat) ? $request->status_lanjut : '';
        $this->tanggal1 = date('Y-m-d');
        $this->cito = 'Y';
        $this->tanggal2 = date('Y-m-d');
        $this->getDataKhanza();
        $this->Setting();
    }
    public function render()
    {
        $this->getDataKhanza();
        $this->Setting();
        return view('livewire.lab.bridgingalatlat-lis');
    }

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
                    'order_control' => 'N',
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
                    'bed_id' => ($data[$key]['status_lanjut'] === 'Ralan') ? '0000' : $data[$key]['kd_kamar'],
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
    public function getDataLIS($noorder)
    {
        // try {
        $Service = new  ServiceSoftmedik();
        $data = $Service->ServiceSoftmedixGet($noorder);
        $this->detailDataLis = $data;
        $this->detailDataLis['response']['sampel']['result_test'] = collect($this->detailDataLis['response']['sampel']['result_test'])->map(function ($item) {
            $khanza = DB::table('template_laboratorium')
                ->select('template_laboratorium.id_template')
                ->join('jns_perawatan_lab', 'template_laboratorium.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                ->where('template_laboratorium.id_template', $item['test_id'])
                ->first();
            $item['id_template'] = $khanza->id_template ?? '-';
            return $item;
        });

        // dd($this->detailDataLis);
        // } catch (\Throwable $th) {
        //     $this->detailDataLis = [];
        // }
    }

    public $check = [];
    function getTestLAB($key)
    {
        // DETAIL PERIKSA LAB
        $uniqueTests = [];
        $resultDetailPeriksaLab = [];
        foreach ($this->detailDataLis['response']['sampel']['result_test'] as  $item) {
            if (!in_array($item['nama_test'], $uniqueTests) && $item['test_id'] == $item['id_template']) {
                $resultDetailPeriksaLab[] = [
                    'test_id' => $item['test_id'],
                    'nama_test' => $item['nama_test'],
                    'id_template' => $item['id_template'],
                    'kode_paket' => $item['kode_paket']
                ];
                $uniqueTests[] = $item['nama_test'];
            }
        }
        $resultDetailPeriksaLab = collect($resultDetailPeriksaLab)->map(function ($item) {
            $khanza = DB::table('template_laboratorium')
                ->select('template_laboratorium.kd_jenis_prw')
                ->join('jns_perawatan_lab', 'template_laboratorium.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                ->where('template_laboratorium.kd_jenis_prw', $item['kode_paket'])
                ->first();
            $item['kd_jenis_prw'] = $khanza->kd_jenis_prw ?? '-';
            return $item;
        });
        // / DETAIL PERIKSA LAB

        // PERIKSA LAB
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
                ->select('jns_perawatan_lab.kd_jenis_prw', 'jns_perawatan_lab.nm_perawatan', 'jns_perawatan_lab.bagian_rs', 'jns_perawatan_lab.bhp', 'jns_perawatan_lab.tarif_perujuk')
                ->where('jns_perawatan_lab.kd_jenis_prw', $item['kode_paket'])
                ->first();
            $item['no_rawat'] = $this->getDatakhanza[$key]['no_rawat'] ?? '-';
            $item['nip'] = 'DARI FORM' ?? '-';
            $item['nm_perawatan'] = $khanza->nm_perawatan ?? '-';
            $item['tgl_periksa'] = Carbon::parse($this->detailDataLis['response']['sampel']['acc_date'])->format('Y-m-d') ?? '-';
            $item['jam'] = Carbon::parse($this->detailDataLis['response']['sampel']['acc_date'])->format('h:m:s') ?? '-';
            $item['dokter_perujuk'] = $this->getDatakhanza[$key]['kd_dr_perujuk'] ?? '-';
            $item['bagian_rs'] = $khanza->bagian_rs ?? '-';
            $item['bhp'] = $khanza->bhp ?? '-';
            return $item;
        });

        dd($resultPeriksaLab);
        // dd($this->getDatakhanza);
    }
}
