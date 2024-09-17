<?php

namespace App\Http\Livewire\BrigingBpjs;

use Livewire\Component;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Services\Bpjs\ReferensiBPJS;

class SepVclaim extends Component
{
    protected $referensi;
    public function __construct()
    {
        $this->referensi = new ReferensiBPJS;
    }

    public function mount()
    {
        $this->CariSepVclaim();
    }
    public function render()
    {
        $this->CariSepVclaim();
        return view('livewire.briging-bpjs.sep-vclaim');
    }

    // 1 ===================================================================================
    public $getSep;
    public $getSep2;
    public $getRegPeriksa;
    public $cari_no_rawat;
    public $cari_no_sep;
    public $getSetting;
    public function CariSepVclaim()
    {
        try {
            $caceService = DB::table('setting')
                ->select(
                    'setting.nama_instansi',
                    'setting.kabupaten',
                    'setting.propinsi',
                    'kode_ppk'
                )
                ->first();
            $data = json_decode($this->referensi->CariSepVclaim1($this->cari_no_sep));
            $data2 = json_decode($this->referensi->CariSepVclaim2($this->cari_no_sep));

            $responsesSep = $data->response;
            $responsesSep2 = $data2->response;
            $reg_periksa = DB::table('reg_periksa')
                ->select(
                    'dokter.nm_dokter',
                    'reg_periksa.no_rawat',
                    'pasien.nm_pasien',
                    'reg_periksa.umurdaftar',
                    'reg_periksa.no_reg',
                    'reg_periksa.status_lanjut',
                    'pasien.no_tlp'
                )
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->where('reg_periksa.no_rawat', $this->cari_no_rawat)
                ->first();

            $this->getSetting =  [[$caceService][0]];
            $this->getSep = [[$responsesSep][0]];
            $this->getSep2 = [[$responsesSep2][0]];
            $this->getRegPeriksa = [[$reg_periksa][0]];
            // dd($this->getRegPeriksa);
        } catch (\Throwable $th) {
        }
    }

    // 2 ===================================================================================
    public function SimpanSep()
    {
        $getDokterDpjp = DB::table('maping_dokter_dpjpvclaim')
            ->select('dokter.nm_dokter')
            ->join('dokter', 'maping_dokter_dpjpvclaim.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('maping_dokter_dpjpvclaim.kd_dokter_bpjs', $this->getSep[0]['kontrol']['kdDokter'])
            ->first();
        $jnsPelayanan = ($this->getSep[0]['jnsPelayanan'] = 'Rawat Jalan') ? '2' : '1';
        $tglpulang = ($this->getSep[0]['jnsPelayanan'] = 'Rawat Jalan') ? $this->getSep[0]['tglSep'] . " " . "00:00:00" : '0000-00-00 00:00:00';
        $asalRujukan = ($this->getSep2[0]['provPerujuk']['asalRujukan'] = '1') ? '1.Faskes 1' : '2. Faskes 2(RS)';
        $poliEksekutif = ($this->getSep[0]['poliEksekutif'] == '0') ? '0. Tidak' : '1.Ya';
        $cob = ($this->getSep[0]['cob'] == '0') ? '0. Tidak' : '1.Ya';
        $katarak = ($this->getSep[0]['katarak'] == '0') ? '0. Tidak' : '1.Ya';
        $suplesi = ($this->getSep[0]['lokasiKejadian']['ketKejadian'] == null) ? '0. Tidak' : '1.Ya';
        $data = [
            'no_sep' => $this->getSep[0]['noSep'],
            'no_rawat' => $this->getRegPeriksa[0]['no_rawat'],
            'tglsep' => $this->getSep[0]['tglSep'],
            'tglrujukan' => $this->getSep2[0]['provPerujuk']['tglRujukan'],
            'no_rujukan' => $this->getSep2[0]['provPerujuk']['noRujukan'],
            'kdppkrujukan' => $this->getSep2[0]['provPerujuk']['kdProviderPerujuk'],
            'nmppkrujukan' => $this->getSep2[0]['provPerujuk']['nmProviderPerujuk'],
            'kdppkpelayanan' => $this->getSetting[0]['kode_ppk'],
            'nmppkpelayanan' => $this->getSetting[0]['nama_instansi'],
            'jnspelayanan' => $jnsPelayanan,
            'catatan' => $this->getSep[0]['catatan'],
            'diagawal' => trim(str_replace('-', '', substr($this->getSep2[0]['diagnosa'], 0, 6))),
            'nmdiagnosaawal' => $this->getSep2[0]['diagnosa'],
            'kdpolitujuan' => trim(str_replace('-', '', substr($this->getSep2[0]['poli'], 0, 6))),
            'nmpolitujuan' => $this->getSep[0]['poli'],
            'klsrawat' => $this->getSep[0]['klsRawat']['klsRawatHak'],
            'klsnaik' => $this->getSep[0]['klsRawat']['klsRawatNaik'],
            'pembiayaan' => $this->getSep[0]['klsRawat']['klsRawatNaik'],
            'pjnaikkelas' => $this->getSep[0]['klsRawat']['penanggungJawab'],
            'lakalantas' => $this->getSep[0]['kdStatusKecelakaan'],
            'user' => session('auth.id_user'),
            'nomr' => $this->getSep[0]['peserta']['noMr'],
            'nama_pasien' => $this->getSep[0]['peserta']['nama'],
            'tanggal_lahir' => $this->getSep[0]['peserta']['tglLahir'],
            'peserta' => $this->getSep[0]['peserta']['jnsPeserta'],
            'jkel' => $this->getSep[0]['peserta']['kelamin'],
            'no_kartu' => $this->getSep[0]['peserta']['noKartu'],
            'tglpulang' => $tglpulang,
            'asal_rujukan' => $asalRujukan, //revisi
            'eksekutif' => $poliEksekutif,
            'cob' => $cob,
            'notelep' => $this->getRegPeriksa[0]['no_tlp'],
            'katarak' => $katarak,
            'tglkkl' => $this->getSep[0]['lokasiKejadian']['tglKejadian'],
            'keterangankkl' => $this->getSep[0]['lokasiKejadian']['ketKejadian'],
            'suplesi' => $suplesi,
            'no_sep_suplesi' => "",
            'kdprop' => $this->getSep[0]['lokasiKejadian']['kdProp'],
            'nmprop' => "",
            'kdkab' => $this->getSep[0]['lokasiKejadian']['kdKab'],
            'nmkab' => "",
            'kdkec' => $this->getSep[0]['lokasiKejadian']['kdKec'],
            'nmkec' => "",
            'noskdp' => $this->getSep[0]['kontrol']['noSurat'],
            'kddpjp' => $this->getSep[0]['dpjp']['kdDPJP'],
            'nmdpdjp' => $getDokterDpjp->nm_dokter,
            'tujuankunjungan' => $this->getSep[0]['tujuanKunj']['kode'],
            'flagprosedur' => $this->getSep[0]['flagProcedure']['kode'],
            'penunjang' => $this->getSep[0]['kdPenunjang']['kode'],
            'asesmenpelayanan' => $this->getSep[0]['assestmenPel']['kode'],
            'kddpjplayanan' => $this->getSep[0]['dpjp']['kdDPJP'],
            'nmdpjplayanan' => $this->getSep[0]['dpjp']['nmDPJP'],
        ];
        dd($data);
        // DB::table('users')->insert($data);

    }
}
