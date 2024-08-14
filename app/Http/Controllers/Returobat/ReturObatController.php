<?php

namespace App\Http\Controllers\Returobat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Hashids\Hashids;

class ReturObatController extends Controller
{
    public function Obat(Request $request)
    {
        $cari_no_rawat = $request->cariNorm;
        $hashids = new Hashids();

        // Get kd_bangsal
        $kdBangsal = DB::table('detail_pemberian_obat')
            ->join('bangsal', 'detail_pemberian_obat.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->select('detail_pemberian_obat.no_rawat', 'bangsal.kd_bangsal')
            ->where('detail_pemberian_obat.no_rawat', $cari_no_rawat)
            // ->where('detail_pemberian_obat.kd_bangsal', $bangsal)
            ->get();

        // Pemberuian Obat Pasien
        $allObat = DB::table('detail_pemberian_obat')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->join('bangsal', 'bangsal.kd_bangsal', '=', 'detail_pemberian_obat.kd_bangsal')
            ->where('detail_pemberian_obat.no_rawat', $cari_no_rawat)
            ->where('detail_pemberian_obat.kd_bangsal', '<>', 'DepOK')
            ->select(
                'detail_pemberian_obat.tgl_perawatan as tanggal_beri',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'databarang.nama_brng',
                'detail_pemberian_obat.embalase',
                'detail_pemberian_obat.tuslah',
                'detail_pemberian_obat.jml',
                'detail_pemberian_obat.biaya_obat',
                'detail_pemberian_obat.total'
            );
        $getAllObat = $allObat->get();
        $sumAllObat = $allObat->sum('detail_pemberian_obat.total');

        // RETUR
        $allRetur = DB::table('returjual')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'returjual.no_rkm_medis')
            ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'returjual.no_rkm_medis')
            ->join('detreturjual', 'detreturjual.no_retur_jual', '=', 'returjual.no_retur_jual')
            ->join('databarang', 'databarang.kode_brng', '=', 'detreturjual.kode_brng')
            ->where(function ($query) use ($cari_no_rawat) {
                for ($i = 1; $i <= 20; $i++) {
                    $query->orWhere('returjual.no_retur_jual', '=', $cari_no_rawat . sprintf('%02d', $i));
                }
            })
            ->select(
                'returjual.no_retur_jual',
                'returjual.tgl_retur',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'databarang.nama_brng',
                'detreturjual.jml_retur',
                'detreturjual.h_retur',
                'detreturjual.subtotal'
            )
            ->distinct();
        $getAllRetur = $allRetur->get();
        $sumAllRetur = $allRetur->sum('detreturjual.subtotal');
        $TotalObtBersih = $sumAllObat - $sumAllRetur;
        // Feching data ke views
        return view('obat.returObat', [
            'kdBangsal' => $kdBangsal,
            'cari_no_rawat' => $cari_no_rawat,
            // total obat dan retur
            'getAllObat' => $getAllObat,
            'sumAllObat' => $sumAllObat,
            'getAllRetur' => $getAllRetur,
            'sumAllRetur' => $sumAllRetur,
            'TotalObtBersih' => $TotalObtBersih,
        ]);
    }

    // PRINT Function
    function Print(Request $request, $id)
    {
        $noRawat = urldecode($id);
        // PASIEN
        $pasien = DB::table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->where('reg_periksa.no_rawat', $noRawat)
            ->select('pasien.no_rkm_medis', 'pasien.nm_pasien', 'reg_periksa.no_rawat', 'penjab.png_jawab');
        $getPasien = $pasien->first();

        // Pemberuian Obat Pasien
        $allObat = DB::table('detail_pemberian_obat')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'detail_pemberian_obat.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('databarang', 'databarang.kode_brng', '=', 'detail_pemberian_obat.kode_brng')
            ->join('bangsal', 'bangsal.kd_bangsal', '=', 'detail_pemberian_obat.kd_bangsal')
            ->where('detail_pemberian_obat.no_rawat', $noRawat)
            ->where('detail_pemberian_obat.kd_bangsal', '<>', 'DepOK')
            ->select(
                'detail_pemberian_obat.tgl_perawatan as tanggal_beri',
                'pasien.no_rkm_medis',
                'reg_periksa.no_rawat',
                'pasien.nm_pasien',
                'databarang.nama_brng',
                'detail_pemberian_obat.embalase',
                'detail_pemberian_obat.tuslah',
                'detail_pemberian_obat.jml',
                'detail_pemberian_obat.biaya_obat',
                'detail_pemberian_obat.total'
            );
        $getAllObat = $allObat->get();
        $sumAllObat = $allObat->sum('detail_pemberian_obat.total');

        // RETUR
        $allRetur = DB::table('returjual')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'returjual.no_rkm_medis')
            ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'returjual.no_rkm_medis')
            ->join('detreturjual', 'detreturjual.no_retur_jual', '=', 'returjual.no_retur_jual')
            ->join('databarang', 'databarang.kode_brng', '=', 'detreturjual.kode_brng')
            ->where(function ($query) use ($noRawat) {
                for ($i = 1; $i <= 20; $i++) {
                    $query->orWhere('returjual.no_retur_jual', '=', $noRawat . sprintf('%02d', $i));
                }
            })
            ->select(
                'returjual.no_retur_jual',
                'returjual.tgl_retur',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'databarang.nama_brng',
                'detreturjual.jml_retur',
                'detreturjual.h_retur',
                'detreturjual.subtotal'
            )
            ->distinct();
        $getAllRetur = $allRetur->get();
        $sumAllRetur = $allRetur->sum('detreturjual.subtotal');
        $TotalObtBersih = $sumAllObat - $sumAllRetur;

        return view('obat.printObat', [
            'getAllObat' => $getAllObat,
            'getPasien' => $getPasien,
            'sumAllObat' => $sumAllObat,
            'getAllRetur' => $getAllRetur,
            'sumAllRetur' => $sumAllRetur,
            'TotalObtBersih' => $TotalObtBersih,
        ]);
    }
}
