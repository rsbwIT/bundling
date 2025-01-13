<?php

namespace App\Services\Lab;

use Carbon\Carbon;
use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class ServiceSoftmedik
{

    public $url;
    public $version;
    public $user_id;
    public $key;
    public $client;
    public function __construct()
    {
        $this->client = new Client();
        $dotenv = Dotenv::createUnsafeImmutable(getcwd());
        $dotenv->safeLoad();

        $this->url = getenv('URL_SOFTMEDIX');
        $this->version = getenv('SOFTMEDIX_VERSION');
        $this->user_id = getenv('SOFTMEDIX_USERID');
        $this->key = getenv('SOFTMEDIX_KEY');
    }

    public function url()
    {
        return $this->url;
    }
    public function version()
    {
        return $this->version;
    }
    public function user_id()
    {
        return $this->user_id;
    }
    public function key()
    {
        return $this->key;
    }


    public function ServiceSoftmedixPOST($data, $key)
    {
        try {
            $kd_jenis_prw = [];
            foreach ($data[$key]['Permintaan'] as $permintaan) {
                $kd_jenis_prw[] = $permintaan['kd_jenis_prw'];
            }
            $dataLab = DB::table('template_laboratorium')
                ->select('kd_jenis_prw', 'id_template')
                ->whereIn('kd_jenis_prw', $kd_jenis_prw)
                ->get();
            $order_test = [];
            foreach ($dataLab as $value) {
                $order_test[] = (string)$value->id_template;
            }
            // dd($order_test);
            $sendToLis = [
                'order' => [
                    'msh' => [
                        'product' => 'SOFTMEDIX LIS',
                        'version' => self::version(),
                        'user_id' => self::user_id(),
                        'key' => self::key(),
                    ],
                    'pid' => [
                        'pmrn' => $data[$key]['no_rkm_medis'],
                        'pname' => $data[$key]['nm_pasien'],
                        'sex' => $data[$key]['jk'],
                        'birth_dt' => Carbon::parse($data[$key]['tgl_lahir'])->format('d.m.Y'),
                        'address' => str_replace(' ', '', $data[$key]['alamat']),
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
                        'bed_id' => ($data[$key]['status_lanjut'] === 'Ralan') ? '0000' : $data[$key]['kd_bangsal'],
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
            // dd($order_test);

            $response = $this->client->post(self::url() . '/wslis/bridging/order', [
                'json' => $sendToLis,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            $responseBody = $response->getBody();
            return json_decode($responseBody, true);
        } catch (\Exception $e) {
            dd('Error: ' . $e->getMessage());
        }
    }

    public function ServiceSoftmedixGet($noorder)
    {
        try {
            $response = $this->client->get(self::url() . '/wslis/bridging/result/' . self::user_id() . '/' . self::key() . '/' . $noorder);
            $responseBody = $response->getBody();
            return json_decode($responseBody, true);
        } catch (\Exception $e) {
            dd('Error: ' . $e->getMessage());
        }
    }


}
