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
            ->select('status_lanjut', 'kd_poli')
            ->where('no_rawat', '=', $noRawat);
        $jumlahData = $cekNorawat->count();
        $statusLanjut = $cekNorawat->first();

        $settingBundling = DB::table('bw_setting_bundling')
            ->select('bw_setting_bundling.nama_berkas', 'bw_setting_bundling.urutan')
            ->where('bw_setting_bundling.status', '1')
            ->orderBy('bw_setting_bundling.urutan', 'asc')
            ->get();

        if ($jumlahData > 0) {
            // 1 BERKAS SEP
            $getSEP = QueryResumeDll::getSEP($noRawat, $noSep);

            // 2 BERKAS RESUME
            if ($statusLanjut->kd_poli === 'U0061' || $statusLanjut->kd_poli === 'FIS') { // U0061 = FisoTerapi
                // 3 BERKAS RESUME FISO
                $getResume = QueryResumeDll::getResumeFiso($noRawat);
                $getKamarInap = '';
                $cekPasienKmrInap = '';
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
                } else {
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
        ]);
    }
}
