<?php

namespace App\Http\Controllers\Farmasi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SepResepController extends Controller
{
    // ===============================
    // 1 LIST PASIEN
    // ===============================
    function ListPasienFarmasi(){
        $tanggl1 = date('Y-m-d');
        $tanggl2 = date('Y-m-d');

        $daftarPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_reg',
                'reg_periksa.status_bayar',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'bridging_sep.no_sep',
                'piutang.nota_piutang',
                'piutang.tgl_piutang',
                'piutang.jns_jual',
                'poliklinik.nm_poli'
            )
            ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->leftJoin('bridging_sep','bridging_sep.no_rawat','=','reg_periksa.no_rawat')
            ->join('piutang',function($join) {
                $join->on('piutang.no_rkm_medis','=','pasien.no_rkm_medis')
                     ->on('reg_periksa.no_rawat','=','piutang.nota_piutang');
            })
            ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
            ->where('reg_periksa.kd_pj','=', 'BPJ')
            ->whereBetween('piutang.tgl_piutang',[$tanggl1, $tanggl2])
            ->orderBy('reg_periksa.no_rawat','asc')
            ->get();

        $downloadBerkas = DB::table('file_farmasi')
            ->select('no_rawat')
            ->whereIn('no_rawat', $daftarPasien->pluck('no_rawat')->toArray())
            ->where('jenis_berkas', 'SEP-RESEP')
            ->get();

        // ================= RINGKASAN =================
        $totalPasien = $daftarPasien->count();

        $noRawatTerbundling = $downloadBerkas
            ->pluck('no_rawat')
            ->unique();

        $totalSudahTerbundling = $daftarPasien
            ->whereIn('no_rawat', $noRawatTerbundling)
            ->count();

        $totalBelumTerbundling = $totalPasien - $totalSudahTerbundling;
        // ============================================

        session(['tgl1' => $tanggl1]);
        session(['tgl2' => $tanggl2]);

        return view('farmasi.listpasien', [
            'daftarPasien' => $daftarPasien,
            'downloadBerkas' => $downloadBerkas,
            'totalPasien' => $totalPasien,
            'totalSudahTerbundling' => $totalSudahTerbundling,
            'totalBelumTerbundling' => $totalBelumTerbundling
        ]);
    }

    // ===============================
    // 2 CARI LIST PASIEN
    // ===============================
    function CariListPasienFarmasi(Request $request){
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;

        $daftarPasien = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_reg',
                'reg_periksa.status_bayar',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'bridging_sep.no_sep',
                'piutang.jns_jual',
                'piutang.nota_piutang',
                'piutang.tgl_piutang',
                'poliklinik.nm_poli'
            )
            ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->leftJoin('bridging_sep','bridging_sep.no_rawat','=','reg_periksa.no_rawat')
            ->join('piutang',function($join) {
                $join->on('piutang.no_rkm_medis','=','pasien.no_rkm_medis')
                     ->on('reg_periksa.no_rawat','=','piutang.nota_piutang');
            })
            ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
            ->where('reg_periksa.kd_pj','=', 'BPJ')
            ->whereBetween('piutang.tgl_piutang',[$tanggl1, $tanggl2])
            ->where(function($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->orderBy('reg_periksa.no_rawat','asc')
            ->get();

        $downloadBerkas = DB::table('file_farmasi')
            ->select('no_rawat')
            ->whereIn('no_rawat', $daftarPasien->pluck('no_rawat')->toArray())
            ->where('jenis_berkas', 'SEP-RESEP')
            ->get();

        // ================= RINGKASAN =================
        $totalPasien = $daftarPasien->count();

        $noRawatTerbundling = $downloadBerkas
            ->pluck('no_rawat')
            ->unique();

        $totalSudahTerbundling = $daftarPasien
            ->whereIn('no_rawat', $noRawatTerbundling)
            ->count();

        $totalBelumTerbundling = $totalPasien - $totalSudahTerbundling;
        // ============================================

        session(['tgl1' => $request->tgl1]);
        session(['tgl2' => $request->tgl2]);
        session(['cariNomor' => $cariNomor]);

        return view('farmasi.listpasien', [
            'daftarPasien' => $daftarPasien,
            'downloadBerkas' => $downloadBerkas,
            'totalPasien' => $totalPasien,
            'totalSudahTerbundling' => $totalSudahTerbundling,
            'totalBelumTerbundling' => $totalBelumTerbundling
        ]);
    }
}
