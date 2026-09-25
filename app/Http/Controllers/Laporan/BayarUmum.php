<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BayarUmum extends Controller
{

    function CariBayarUmum(Request $request) {

        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');
        $stsLanjut = $request->stsLanjut;

        $bayarUmum = DB::table('reg_periksa')
            ->select('reg_periksa.no_rawat',
                'reg_periksa.kd_dokter',
                'reg_periksa.kd_poli',
                'reg_periksa.status_lanjut',
                'billing.tgl_byr',
                'pasien.nm_pasien',
                'penjab.png_jawab',
                'pasien.no_rkm_medis')
            ->join('pasien','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
            ->join('billing','billing.no_rawat','=','reg_periksa.no_rawat')
            ->join('penjab','penjab.kd_pj','=','reg_periksa.kd_pj')
            ->join('dokter','reg_periksa.kd_dokter','=','dokter.kd_dokter')
            ->where('penjab.kd_pj', 'UMU')
            ->whereBetween('billing.tgl_byr', [$tanggl1 , $tanggl2])
            ->whereNotIn('reg_periksa.no_rawat', function ($query) {
                $query->select('piutang_pasien.no_rawat')->from('piutang_pasien');
            })
            ->where(function ($query) use ($stsLanjut) {
                if ($stsLanjut) {
                    $query->where('reg_periksa.status_lanjut', $stsLanjut);
                }
            })
            ->where(function ($query) use ($cariNomor) {
                if (!empty($cariNomor)) {
                    $query->where(function($q) use ($cariNomor) {
                $q->orWhere('reg_periksa.no_rawat', 'like', $cariNomor . '%');
                $q->orWhere('reg_periksa.no_rkm_medis', 'like', $cariNomor . '%');
                $q->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                                });
                }
            })
            ->where('billing.no','=','No.Nota')
            ->orderBy('reg_periksa.status_lanjut', 'DESC')
            ->get();
            
        $noRawats = $bayarUmum->pluck('no_rawat')->toArray();
        $billings = DB::table('billing')->select('no_rawat', 'nm_perawatan', 'totalbiaya', 'status', 'no')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $bayarUmum->map(function ($item) use ($billings) {
            $itemBillings = isset($billings[$item->no_rawat]) ? $billings[$item->no_rawat] : collect();
            
            $item->getNomorNota = $itemBillings->where('no', 'No.Nota')->values();
            $item->getRegistrasi = $itemBillings->where('status', 'Registrasi')->values();
            $item->getObat = $itemBillings->where('status', 'Obat')->values();
            $item->getReturObat = $itemBillings->where('status', 'Retur Obat')->values();
            $item->getResepPulang = $itemBillings->where('status', 'Resep Pulang')->values();
            $item->getRalanDokter = $itemBillings->where('status', 'Ralan Dokter')->values();
            $item->getRalanDrParamedis = $itemBillings->where('status', 'Ralan Dokter Paramedis')->values();
            $item->getRalanParamedis = $itemBillings->where('status', 'Ralan Paramedis')->values();
            $item->getRanapDokter = $itemBillings->where('status', 'Ranap Dokter')->values();
            $item->getRanapDrParamedis = $itemBillings->where('status', 'Ranap Dokter Paramedis')->values();
            $item->getRanapParamedis = $itemBillings->where('status', 'Ranap Paramedis')->values();
            $item->getOprasi = $itemBillings->where('status', 'Operasi')->values();
            $item->getLaborat = $itemBillings->where('status', 'Laborat')->values();
            $item->getRadiologi = $itemBillings->where('status', 'Radiologi')->values();
            $item->getTambahan = $itemBillings->where('status', 'Tambahan')->values();
            $item->getPotongan = $itemBillings->where('status', 'Potongan')->values();
            $item->getKamarInap = $itemBillings->where('status', 'Kamar')->values();
            
            return $item;
        });

        return  view('laporan.bayarUmum', [
            'bayarUmum' => $bayarUmum,
        ]);
    }
}
