<?php

namespace App\Http\Controllers\Bpjs;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class BpjsController extends Controller
{
    function claimBpjs(Request $request) {
        $cariNoSep = $request->cariNoSep;
        $cariNorawat = $request->cariNorawat;
        $pasien = DB::table('reg_periksa')
        ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
        ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
        ->leftJoin('bridging_sep','bridging_sep.no_rawat','=','reg_periksa.no_rawat')
        ->where('bridging_sep.no_sep', '=', $cariNoSep)
        ->where('reg_periksa.no_rawat', '=', $cariNorawat)
        ->select('pasien.no_rkm_medis', 'pasien.nm_pasien', 'bridging_sep.no_sep', 'reg_periksa.no_rawat', 'penjab.png_jawab');
        $getPasien = $pasien->first();

        return view('bpjs.ulploadFileClaim', [
            'getPasien'=>$getPasien,
        ]);
    }

    // UPLOAD FILE
    function inputClaimBpjs(Request $request){
        // Berkas INACBG
        if ($request->hasFile('file_inacbg')) {
            $file = $request->file('file_inacbg');
            $no_rawatSTR = str_replace('/', '', $request->no_rawat);
            $path_file = 'INACBG' . '-' . $no_rawatSTR. '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->put('file_inacbg/' . $path_file, file_get_contents($file));
            $cekBerkas = DB::table('bw_file_casemix_inacbg')->where('no_rawat', $request->no_rawat)
                ->exists();
            if (!$cekBerkas){
                DB::table('bw_file_casemix_inacbg')->insert([
                    'no_rkm_medis' => $request->no_rkm_medis,
                    'no_rawat' => $request->no_rawat,
                    'file' => $path_file,
                ]);
            }
        }

        // Berkas SCAN
        if ($request->hasFile('file_scan') && $request->kode_berkas) {
            $file = $request->file('file_scan');
            $kode = $request->kode_berkas;
            $no_rawatSTR = str_replace('/', '', $request->no_rawat);
            $file_name = $kode . '-' . $no_rawatSTR. '.' . $file->getClientOriginalExtension();
            
            // Simpan ke storage local
            Storage::disk('public')->put('file_scan/' . $file_name, file_get_contents($file));
            
            // Upload SFTP ke server Khanza
            try {
                $local_path = $file->storeAs('temp', $file_name, 'local');
                $local_full_path = storage_path('app/' . $local_path);
                
                $sftp = new \phpseclib3\Net\SFTP(env('SFTP_HOST'), env('SFTP_PORT'));
                if ($sftp->login(env('SFTP_USERNAME'), env('SFTP_PASSWORD'))) {
                    $remote_path = 'pages/upload/' . $file_name;
                    $sftp_full_path = '/opt/lampp/htdocs/webapps/berkasrawat/' . $remote_path;
                    $sftp->put($sftp_full_path, $local_full_path, \phpseclib3\Net\SFTP::SOURCE_LOCAL_FILE);
                }
                
                if (file_exists($local_full_path)) {
                    unlink($local_full_path);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('SFTP Error Upload Berkas Ranap: ' . $e->getMessage());
            }

            $remote_path = 'pages/upload/' . $file_name;

            // Simpan ke berkas_digital_perawatan
            $cekSCAN = DB::table('berkas_digital_perawatan')
                ->where('no_rawat', $request->no_rawat)
                ->where('kode', $kode)
                ->first();

            if ($cekSCAN) {
                DB::table('berkas_digital_perawatan')
                    ->where('no_rawat', $request->no_rawat)
                    ->where('kode', $kode)
                    ->update([
                        'lokasi_file' => $remote_path,
                    ]);
            } else {
                DB::table('berkas_digital_perawatan')->insert([
                    'no_rawat' => $request->no_rawat,
                    'kode' => $kode,
                    'lokasi_file' => $remote_path,
                ]);
            }

            // Cek Master Switch
            $masterSwitch = DB::table('bw_setting_bundling')
                ->where('nama_berkas', 'Berkas Digital Keperawatan')
                ->value('status');

            if ($masterSwitch == '0') {
                $path_file_scan = 'SCAN' . '-' . $no_rawatSTR . '.' . $file->getClientOriginalExtension();
                Storage::disk('public')->put('file_scan/' . $path_file_scan, file_get_contents($file));
                
                $cekBerkasScan = DB::table('bw_file_casemix_scan')->where('no_rawat', $request->no_rawat)->exists();
                if (!$cekBerkasScan) {
                    DB::table('bw_file_casemix_scan')->insert([
                        'no_rkm_medis' => $request->no_rkm_medis,
                        'no_rawat' => $request->no_rawat,
                        'file' => $path_file_scan,
                    ]);
                } else {
                    DB::table('bw_file_casemix_scan')
                        ->where('no_rawat', $request->no_rawat)
                        ->update(['file' => $path_file_scan]);
                }
            }
        }
        Session::flash('successSaveINACBG', 'INACBG / SCAN');
        $redirectUrl = url('/casemix-home-cari');
        $csrfToken = Session::token();
        $cariNoSep = $request->no_sep;
        $cariNoRawat = $request->no_rawat;
        $redirectUrlWithToken = $redirectUrl . '?' . http_build_query(['_token' => $csrfToken, 'cariNorawat' => $cariNoSep, 'cariNorawat' => $cariNoRawat,]);
        return redirect($redirectUrlWithToken);
    }
}
