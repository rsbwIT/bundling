<?php

namespace App\Services\Bpjs;

use Dotenv\Dotenv;
use App\Services\Bpjs\cUrl;
use Bpjs\Bridging\Icare\BridgeIcare;
use Bpjs\Bridging\Antrol\BridgeAntrol;
use Bpjs\Bridging\Vclaim\BridgeVclaim;
use Bpjs\Bridging\Aplicare\BridgeAplicares;

class ReferensiBPJS
{
    protected $bridging;
    protected $antrol;
    protected $icare;
    protected $aplicare;
    protected $kode_rs;

    public function __construct()
    {
        $dotenv = Dotenv::createUnsafeImmutable(getcwd());
        $dotenv->safeLoad();
        $this->kode_rs = getenv('KODE_PPK_RS');
        $this->bridging = new BridgeVclaim();
        $this->antrol = new BridgeAntrol();
        $this->icare = new cUrl();
        $this->aplicare = new BridgeAplicares();
    }

    // 1 VCLAIM ======================================================
    public function getDiagnosa($kode)
    {
        try {
            $endpoint = 'referensi/diagnosa/' . $kode;
            return $this->bridging->getRequest($endpoint);
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function getPoli($kode)
    {
        try {
            $endpoint = 'referensi/poli/' . $kode;
            return $this->bridging->getRequest($endpoint);
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function getFasilitasKesehatan($parameter1, $parameter2)
    {
        try {
            $endpoint = 'referensi/faskes/' . $parameter1 . '/' . $parameter2;
            return $this->bridging->getRequest($endpoint);
        } catch (\Throwable $th) {
            return [];
        }
    }
    public function CariSepVclaim1($nomorsep)
    {
        try {
            $endpoint = 'SEP/' . $nomorsep;
            return $this->bridging->getRequest($endpoint);
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function CariSepVclaim2($nomorsep)
    {
        try {
            $endpoint = 'RencanaKontrol/nosep/' . $nomorsep;
            return $this->bridging->getRequest($endpoint);
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function CariSuplesi($nokartuPeserta, $tglSep)
    {
        try {
            $endpoint = 'sep/JasaRaharja/Suplesi/' . $nokartuPeserta . '/tglPelayanan/' . $tglSep;
            return $this->bridging->getRequest($endpoint);
        } catch (\Throwable $th) {
            return [];
        }
    }

    // 2 ANTROL ======================================================
    public function cekinBPJS($data)
    {
        $endpoint = 'antrean/updatewaktu';
        return $this->antrol->postRequest($endpoint, $data, "POST");
    }

    public function dashboardTanggal($tanggal)
    {
        $endpoint = "antrean/pendaftaran/tanggal/{$tanggal}";
        return $this->antrol->getRequest($endpoint);
    }

    public function cekantrianTaskID($kodebooking)
    {
        $endpoint = "antrean/pendaftaran/kodebooking/{$kodebooking}";
        return $this->antrol->getRequest($endpoint);
    }
    public function cekTaskID($data)
    {
        try {
            $endpoint = 'antrean/getlisttask';
            return $this->antrol->postRequest($endpoint, $data, "POST");
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function batalAntranMJKN($data)
    {
        try {
            $endpoint = 'antrean/batal';
            return $this->antrol->postRequest($endpoint, $data, "POST");
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function updateJadwalHfisDokter($data)
    {
        try {
            $endpoint = 'jadwaldokter/updatejadwaldokter';
            return $this->antrol->postRequest($endpoint, $data, "POST");
        } catch (\Throwable $th) {
            return [];
        }
    }
    public function getJadwalHfisDokter($kdpoli, $tanggal)
    {
        try {
            $endpoint = "jadwaldokter/kodepoli/{$kdpoli}/tanggal/{$tanggal}";
            return $this->antrol->getRequest($endpoint);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // ICARE
    public function validateICARE($data)
    {
        $endpoint = 'api/rs/validate';
        return $this->icare->postRequest($endpoint, $data);
    }

    // APLICARE
    public function getReferensiKelas()
    {
        $endpoint = 'aplicaresws/rest/ref/kelas';
        return $this->aplicare->getRequest($endpoint);
    }

    public function addRuangan($data)
    {
        $endpoint = 'aplicaresws/rest/bed/create/' . $this->kode_rs;
        return $this->aplicare->postRequest($endpoint, $data);
    }
    public function updateRuangan($data)
    {
        $endpoint = 'aplicaresws/rest/bed/update/' . $this->kode_rs;
        $response = $this->aplicare->postRequest($endpoint, $data);

        try {
            $decoded = json_decode($response, true);
            $message = $decoded['metadata']['message'] ?? '';

            if (
                is_array($decoded) &&
                (($decoded['metadata']['code'] ?? null) == 0) &&
                stripos($message, 'Data tidak ada di database') !== false
            ) {
                $createdResponse = $this->addRuangan($data);

                try {
                    $createdDecoded = json_decode($createdResponse, true);
                    if (is_array($createdDecoded) && isset($createdDecoded['metadata'])) {
                        $createdDecoded['metadata']['action'] = 'created';
                        $createdDecoded['metadata']['message'] = 'Ruangan belum ada di BPJS, data berhasil dibuat baru.';
                        return json_encode($createdDecoded);
                    }
                } catch (\Throwable $th) {
                    // fall through and return a friendly fallback response
                }

                return json_encode([
                    'metadata' => [
                        'code' => 1,
                        'message' => 'Ruangan belum ada di BPJS, data berhasil dibuat baru.',
                        'action' => 'created',
                    ],
                ]);
            }
        } catch (\Throwable $th) {
            // fall through and return the original response
        }

        return $response;
    }
    public function deleteRuang($data)
    {
        $endpoint = 'aplicaresws/rest/bed/delete/' . $this->kode_rs;
        return $this->aplicare->postRequest($endpoint, $data);
    }
    public function getRuangan()
    {
        $endpoint = 'aplicaresws/rest/bed/read/' . $this->kode_rs . '/1/100';
        return $this->aplicare->getRequest($endpoint);
    }
}
