<?php

namespace App\Services;

use PDF;
use App\Services\CacheService;
use App\Services\QueryResumeDll;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PrintPdfService
{

    public static function printPdf($no_rawat, $no_sep)
    {
        $cacheService = new CacheService();
        $getSetting = $cacheService->getSetting();
        $cariNoSep = $no_sep;
        $noRawat = $no_rawat;
        $cekNorawat = DB::table('reg_periksa')
            ->select('reg_periksa.status_lanjut', 'pasien.nm_pasien', 'reg_periksa.no_rkm_medis', 'reg_periksa.kd_poli')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('no_rawat', '=', $noRawat);
        $jumlahData = $cekNorawat->count();
        $statusLanjut = $cekNorawat->first();
        $getpasien = $cekNorawat->first();

        $settingBundling = DB::table('bw_setting_bundling')
            ->select('bw_setting_bundling.nama_berkas', 'bw_setting_bundling.urutan')
            ->where('bw_setting_bundling.status', '1')
            ->orderBy('bw_setting_bundling.urutan', 'asc')
            ->get();


        if ($jumlahData > 0) {
            // 1 BERKAS SEP
            $getSEP = QueryResumeDll::getSEP($noRawat, $cariNoSep);

            // 2 BERKAS RESUME
            if ($statusLanjut->kd_poli === 'U0061' || $statusLanjut->kd_poli === 'FIS') { // U0061 = FisoTerapi
                $getResume = QueryResumeDll::getResumeFiso($noRawat);
                $getKamarInap = '';
            } else {
                if ($statusLanjut->status_lanjut === 'Ranap') {
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
                    } else {
                        $getKamarInap = '';
                    }
                } else {
                    $getResume = QueryResumeDll::getResumeRalan($noRawat);
                    $getKamarInap = '';
                }
            }

            // RIANCIAN BIAYA
            $bilingRalan = QueryResumeDll::getBiling($noRawat);

            // BERKAS LABORAT
            $getLaborat = QueryResumeDll::getLaborat($noRawat);


            // BERKAS RADIOLOGI
            $getRadiologi = QueryResumeDll::getRadiologi($noRawat);

            // AWAL MEDIS
            $awalMedis = QueryResumeDll::getAwalMedis($noRawat);

            // SURAT KEMATIAN
            $getSudartKematian = QueryResumeDll::getSuratKematian($noRawat);

            // LAPORAN OPERASI
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
            $settingBundling = '';
            $getSetting = '';
            $jumlahData = '';
            $getSEP = '';
            $statusLanjut = '';
            $getResume = '';
            $$getKamarInap = '';
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
        $pdf = PDF::loadView('bpjs.printcasemix', [
            'settingBundling' => $settingBundling,
            'getSetting' => $getSetting,
            'jumlahData' => $jumlahData,
            'getSEP' => $getSEP,
            'statusLanjut' => $statusLanjut,
            'getResume' => $getResume,
            'getKamarInap' => $getKamarInap,
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

        $no_rawatSTR = str_replace('/', '', $noRawat);
        $pdfFilename = 'RESUMEDLL-' . $no_rawatSTR . '.pdf';
        Storage::disk('public')->put('resume_dll/' . $pdfFilename, $pdf->output());
        $cekBerkas = DB::table('bw_file_casemix_remusedll')->where('no_rawat', $noRawat)
            ->exists();
        if (!$cekBerkas) {
            DB::table('bw_file_casemix_remusedll')->insert([
                'no_rkm_medis' => $getpasien->no_rkm_medis,
                'no_rawat' => $noRawat,
                'file' => $pdfFilename,
            ]);
        }
    }

    public static function printPdfResep($no_rawat, $no_sep)
    {
        $cacheService = new CacheService();
        $getSetting = $cacheService->getSetting();
        $noRawat = $no_rawat;
        $noSep = $no_sep;

        $cekNorawat = DB::table('reg_periksa')
            ->select('reg_periksa.status_lanjut', 'pasien.nm_pasien', 'reg_periksa.no_rkm_medis', 'reg_periksa.kd_poli')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('no_rawat', '=', $noRawat);
        $jumlahData = $cekNorawat->count();
        $getpasien = $cekNorawat->first();

        if ($jumlahData > 0) {
            // 1 BERKAS SEP
            $getSEP = QueryResumeDll::getSEP($noRawat, $noSep);
            if ($getSEP) {
                $getSEP->getResep = QueryResumeDll::getResepObat($getSEP->no_rawat);
            }
        } else {
            $getSetting = '';
            $jumlahData = '';
            $getSEP = '';
        }
        $pdf = PDF::loadView('farmasi.print-berkas-sep-resep2', [
            'getSetting' => $getSetting,
            'jumlahData' => $jumlahData,
            'getSEP' => $getSEP,
        ]);

        $no_rawatSTR = str_replace('/', '', $noRawat);
        $pdfFilename = 'RESEP2-' . $no_rawatSTR . '.pdf';
        Storage::disk('public')->put('resep_sep_farmasi/' . $pdfFilename, $pdf->output());
        $cekBerkas = DB::table('file_farmasi')->where('no_rawat', $noRawat)
            ->where('jenis_berkas', 'HASIL-FARMASI2')
            ->exists();
        if (!$cekBerkas) {
            DB::table('file_farmasi')->insert([
                'no_rkm_medis' => $getpasien->no_rkm_medis,
                'no_rawat' => $noRawat,
                'nama_pasein' => $getpasien->nm_pasien,
                'jenis_berkas' => 'HASIL-FARMASI2',
                'file' => $pdfFilename,
            ]);
        }
    }
}
