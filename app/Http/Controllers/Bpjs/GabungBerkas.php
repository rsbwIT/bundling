<?php

namespace App\Http\Controllers\Bpjs;

use setasign\Fpdi\Fpdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class GabungBerkas extends Controller
{
    function gabungBerkas(Request $request){
        try {
            \App\Services\GabungPdfService::printPdf($request->cariNorawat, $request->no_rkm_medis);

            Session::forget('tgl1');
            Session::forget('tgl2');
            Session::forget('statusLanjut');
            if ($request->statusLanjut === 'Ranap') {
                return redirect('/cari-list-pasein-ranap?tgl1=' . $request->tgl1 . '&tgl2=' . $request->tgl2);
            }else{
                return redirect('/cari-list-pasein-ralan?tgl1=' . $request->tgl1 . '&tgl2=' . $request->tgl2);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('GabungBerkas Controller Error: ' . $e->getMessage());
            return redirect()->back()->with('errorBundling', 'Gagal Menggabungkan Berkas: ' . $e->getMessage());
        }
    }
}
