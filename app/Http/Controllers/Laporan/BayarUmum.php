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
                $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $q->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $q->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                                });
                }
            })
            ->where('billing.no','=','No.Nota')
            ->orderBy('reg_periksa.status_lanjut', 'DESC')
            ->get();
            $bayarUmum->map(function ($item) {
                // NOMOR NOTA
                $item->getNomorNota = DB::table('billing')
                    ->select('nm_perawatan')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('no', '=', 'No.Nota')
                    ->get();
                // REGISTRASI
                $item->getRegistrasi = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Registrasi')
                    ->get();
                // Obat+Emb+Tsl / OBAT
                $item->getObat = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Obat')
                    ->get();
                // Retur Obat
                $item->getReturObat = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Retur Obat')
                ->get();
                // Retur Obat
                $item->getReturObat = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Retur Obat')
                    ->get();
                // Resep Pulang
                $item->getResepPulang = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Resep Pulang')
                    ->get();
                // RALAN DOKTER / 1 Paket Tindakan
                $item->getRalanDokter = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ralan Dokter')
                    ->get();
                // RALAN DOKTER PARAMEDIS / 2 Paket Tindakan
                $item->getRalanDrParamedis = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ralan Dokter Paramedis')
                    ->get();
                // RALAN PARAMEDIS / 3 Paket Tindakan
                $item->getRalanParamedis = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ralan Paramedis')
                    ->get();
                // RANAP DOKTER / 4 Paket Tindakan
                $item->getRanapDokter = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ranap Dokter')
                    ->get();
                // RANAP DOKTER PARAMEDIS / 5 Paket Tindakan
                $item->getRanapDrParamedis = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ranap Dokter Paramedis')
                    ->get();
                // RANAP PARAMEDIS / 6 Ranap Paramedis
                $item->getRanapParamedis = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Ranap Paramedis')
                    ->get();
                // OPRASI
                $item->getOprasi = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Operasi')
                ->get();
                // LABORAT
                $item->getLaborat = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Laborat')
                    ->get();
                // RADIOLOGI
                $item->getRadiologi = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Radiologi')
                    ->get();
                // TAMBAHAN
                $item->getTambahan = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Tambahan')
                    ->get();
                     // POTONGAN
                $item->getPotongan = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Potongan')
                ->get();
                // KAMAR INAP
                $item->getKamarInap = DB::table('billing')
                    ->select('totalbiaya')
                    ->where('no_rawat', $item->no_rawat)
                    ->where('status', '=', 'Kamar')
                    ->get();
                return $item;
            });

        return  view('laporan.bayarUmum', [
            'bayarUmum' => $bayarUmum,
        ]);
    }
}
