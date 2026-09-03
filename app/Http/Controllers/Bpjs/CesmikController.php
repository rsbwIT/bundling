<?php

namespace App\Http\Controllers\Bpjs;

use Spatie\PdfToImage\Pdf;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\QueryResumeDll;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CesmikController extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function Casemix(Request $request)
    {
        $getSetting = $this->cacheService->getSetting();
        $noRawat = $request->cariNorawat;
        $noSep = $request->cariNoSep;

        $cekNorawat = DB::table('reg_periksa')
            ->select('status_lanjut', 'kd_poli', 'kd_dokter', 'no_rkm_medis', 'tgl_registrasi')
            ->where('no_rawat', '=', $noRawat);
        $jumlahData = $cekNorawat->count();
        $statusLanjut = $cekNorawat->first();

        $settingBundling = DB::table('bw_setting_bundling')
            ->select('bw_setting_bundling.nama_berkas', 'bw_setting_bundling.urutan')
            ->where('bw_setting_bundling.status', '1')
            ->orderBy('bw_setting_bundling.urutan', 'asc')
            ->get();

        if ($jumlahData > 0) {
            // INITIAL DATA
            $resume_ralan = '';
            $lembarFisio = null;

            // 1 BERKAS SEP
            $getSEP = QueryResumeDll::getSEP($noRawat, $noSep);

            // 2 BERKAS RESUME
            $suhuTubuh = DB::table('pemeriksaan_ralan')->where('no_rawat', $noRawat)->value('suhu_tubuh');
            $isFisio = in_array($suhuTubuh, ['2', '3', '5', '7']);

            if ($isFisio) {
                // 3 BERKAS RESUME FISO
                $resume_ralan = QueryResumeDll::getResumeRalan($noRawat);
                $getResume = QueryResumeDll::getResumeFiso($noRawat) ?: $resume_ralan;
                $getKamarInap = '';
                $cekPasienKmrInap = '';
                
                // Cari lembar berdasarkan tanggal registrasi dan nomor RM
                $lembarFisio = DB::table('fisioterapi_kunjungan')
                    ->where('no_rkm_medis', $statusLanjut->no_rkm_medis)
                    ->whereDate('tanggal', $statusLanjut->tgl_registrasi)
                    ->value('lembar');
                    
                // Ambil data fisioterapi untuk ditampilkan di web
                $dataFisio = DB::table('fisioterapi_kunjungan as fk')
                    ->join('fisioterapi_form as ff', function ($join) {
                        $join->on('fk.no_rkm_medis', '=', 'ff.no_rkm_medis')
                             ->on('fk.lembar', '=', 'ff.lembar');
                    })
                    ->join('pasien as p', 'fk.no_rkm_medis', '=', 'p.no_rkm_medis')
                    ->select(
                        'p.nm_pasien',
                        'fk.no_rawat',
                        'fk.no_rkm_medis',
                        'fk.kunjungan',
                        'fk.program',
                        'fk.tanggal',
                        'fk.ttd_pasien',
                        'fk.ttd_dokter',
                        'fk.ttd_terapis',
                        'fk.lembar',
                        'ff.diagnosa',
                        'ff.ft',
                        'ff.st'
                    )
                    ->where('fk.no_rkm_medis', $statusLanjut->no_rkm_medis)
                    ->where('fk.lembar', $lembarFisio)
                    ->whereDate('fk.tanggal', '<=', $statusLanjut->tgl_registrasi)
                    ->orderBy('fk.kunjungan', 'ASC')
                    ->get();

                if ($dataFisio->isNotEmpty()) {
                    $firstFisio = $dataFisio->first();
                    $dokterPJFisio = DB::table('reg_periksa')
                        ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                        ->where('reg_periksa.no_rawat', $firstFisio->no_rawat)
                        ->select('dokter.nm_dokter', 'dokter.kd_dokter')
                        ->first();
                    $tanggalPertamaFisio = $firstFisio->tanggal;
                    
                    $getFisioData = [
                        'data' => $dataFisio,
                        'first' => $firstFisio,
                        'dokterPJ' => $dokterPJFisio,
                        'tanggalPertama' => $tanggalPertamaFisio
                    ];
                } else {
                    $getFisioData = null;
                }
            } else {
                if ($statusLanjut->status_lanjut === 'Ranap') {
                    // 4 BERKAS RESUME RANAP
                    $getResume = QueryResumeDll::getResumeRanap($noRawat);
                    if ($getResume) {
                        $getKamarInap = DB::table('kamar_inap')
                            ->select([
                                'kamar_inap.tgl_keluar',
                                'kamar_inap.jam_keluar',
                                'kamar_inap.kd_kamar',
                                'bangsal.nm_bangsal'
                            ])
                            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                            ->whereIn('kamar_inap.no_rawat', [$getResume->no_rawat])
                            ->orderByDesc('tgl_keluar')
                            ->orderByDesc('jam_keluar')
                            ->limit(1)
                            ->first();
                        $cekPasienKmrInap = DB::table('kamar_inap')
                            ->whereIn('kamar_inap.no_rawat', [$getResume->no_rawat])
                            ->count();
                    } else {
                        $getKamarInap = '';
                        $cekPasienKmrInap = '';
                    }
                } else if (!$isFisio) {
                    // 5 BERKAS RESUME RALAN
                    $getResume = QueryResumeDll::getResumeRalan($noRawat);
                    $getKamarInap = '';
                    $cekPasienKmrInap = '';
                }
            }

            // 6 RIANCIAN BIAYA
            $bilingRalan = QueryResumeDll::getBiling($noRawat);

            // 7 BERKAS LABORAT
            $getLaborat = QueryResumeDll::getLaborat($noRawat);

            // 8 BERKAS RADIOLOGI
            $getRadiologi = QueryResumeDll::getRadiologi($noRawat);

            // 9 AWAL MEDIS
            $awalMedis = QueryResumeDll::getAwalMedis($noRawat);

            // 10  SURAT KEMATIAN
            $getSudartKematian = QueryResumeDll::getSuratKematian($noRawat);

            // 11 LAPORAN OPERASi
            $getLaporanOprasi = QueryResumeDll::getLaporanOprasi($noRawat, $statusLanjut->status_lanjut);

            // 12 SOAPIE PASIEN
            if ($statusLanjut->status_lanjut === 'Ranap') {
                $getSoapie = QueryResumeDll::getSoapieRanap($noRawat);
            } else {
                $getSoapie = QueryResumeDll::getSoapieRalan($noRawat);
            }

            // 13 TRIASE PASIEN
            $getTriaseIGD = QueryResumeDll::getTriaseIGD($noRawat);

            // 14 SURAT PRI BPJS
            $getSuratPriBpjs = QueryResumeDll::suratPriBpjs($noRawat);

            // 15 BERKAS INACBG
            $kodeInacbg = DB::table('master_berkas_digital')->where('nama', 'INACBG')->value('kode');
            $getInacbg = DB::table('berkas_digital_perawatan')
                ->where('no_rawat', $noRawat)
                ->where('kode', $kodeInacbg)
                ->where('lokasi_file', 'like', '%' . $noSep . '%')
                ->first();

            // 16 SEMUA BERKAS DIGITAL LAINNYA
            $semuaBerkasDigital = DB::table('berkas_digital_perawatan')
                ->join('master_berkas_digital', 'berkas_digital_perawatan.kode', '=', 'master_berkas_digital.kode')
                ->select('master_berkas_digital.nama', 'master_berkas_digital.kode', 'berkas_digital_perawatan.lokasi_file')
                ->where('berkas_digital_perawatan.no_rawat', $noRawat)
                ->when($kodeInacbg, function($query) use ($kodeInacbg) {
                    return $query->where('berkas_digital_perawatan.kode', '!=', $kodeInacbg);
                })
                ->get();
                
            $settingBundlingArray = DB::table('bw_setting_bundling')->pluck('status', 'nama_berkas')->toArray();
            
            // Jadikan "Berkas Digital Keperawatan" sebagai MASTER SWITCH untuk semua file digital (kecuali INACBG)
            $masterSwitch = $settingBundlingArray['Berkas Digital Keperawatan'] ?? '0';
            
            if ($masterSwitch != '1') {
                // Kosongkan semua berkas digital jika switch utama dimatikan
                $semuaBerkasDigital = collect([]);
            }
        } else {
            $getSetting = '';
            $settingBundling = '';
            $jumlahData = '';
            $getSEP = '';
            $statusLanjut = '';
            $getResume = '';
            $getKamarInap = '';
            $cekPasienKmrInap = '';
            $bilingRalan = '';
            $getLaborat = '';
            $getRadiologi = '';
            $awalMedis = '';
            $getSudartKematian = '';
            $getLaporanOprasi = '';
            $getSoapie = '';
            $getTriaseIGD = '';
            $getSuratPriBpjs = '';
            $resume_ralan = '';
            $getInacbg = '';
            $semuaBerkasDigital = [];
            $lembarFisio = null;
            $getFisioData = null;
        }

        // VIEW
        return view('bpjs.cesmik', [
            'getSetting' => $getSetting,
            'settingBundling' => $settingBundling,
            'jumlahData' => $jumlahData,
            'getSEP' => $getSEP,
            'statusLanjut' => $statusLanjut,
            'getResume' => $getResume,
            'getKamarInap' => $getKamarInap,
            'cekPasienKmrInap' => $cekPasienKmrInap,
            'bilingRalan' => $bilingRalan,
            'getLaborat' => $getLaborat,
            'getRadiologi' => $getRadiologi,
            'awalMedis' => $awalMedis,
            'getSudartKematian' => $getSudartKematian,
            'getLaporanOprasi' => $getLaporanOprasi,
            'getSoapie' => $getSoapie,
            'getTriaseIGD' => $getTriaseIGD,
            'getSuratPriBpjs' => $getSuratPriBpjs,
            'resume_ralan' => $resume_ralan,
            'getInacbg' => $getInacbg,
            'semuaBerkasDigital' => $semuaBerkasDigital,
            'lembarFisio' => $lembarFisio,
            'getFisioData' => $getFisioData ?? null,
            'getSetting' => $getSetting, // Also make sure getSetting is passed (it already is but I'll leave it as is if it's there)
        ]);
    }
}
