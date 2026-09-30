<?php

namespace App\Http\Controllers\Bpjs;

use PDF;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\QueryResumeDll;
use App\Services\PrintPdfService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PrintCesmikController extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function printCasemix(Request $request){
        $cariNoSep = $request->cariNoSep;
        $noRawat = $request->cariNorawat;

        PrintPdfService::printPdf($noRawat, $cariNoSep);

        // Gabung berkas secara otomatis setelah menyimpan PDF
        $getpasien = DB::table('reg_periksa')
            ->select('no_rkm_medis')
            ->where('no_rawat', '=', $noRawat)
            ->first();
            
        if ($getpasien) {
            \App\Services\GabungPdfService::printPdf($noRawat, $getpasien->no_rkm_medis);
        }
        Session::flash('successSaveINACBG', 'PDF');
        $redirectUrl = url('/casemix-home-cari');
        $csrfToken = Session::token();
        $redirectUrlWithToken = $redirectUrl . '?' . http_build_query(['_token' => $csrfToken, 'cariNorawat' => $cariNoSep, 'cariNorawat' => $noRawat,]);
        return redirect($redirectUrlWithToken);
    }
}
