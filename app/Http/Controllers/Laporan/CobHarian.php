<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CobHarian extends Controller
{
    public function CobHarian(Request $request)
    {
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $stsLanjut = $request->stsLanjut;
        $getCobHarian =  DB::table('detail_piutang_pasien')
            ->select(
                'detail_piutang_pasien.no_rawat',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                'reg_periksa.kd_dokter',
                'dokter.nm_dokter',
                'piutang_pasien.uangmuka',
                'piutang_pasien.sisapiutang',
                'piutang_pasien.STATUS',
                'reg_periksa.status_lanjut'
            )
            ->join('reg_periksa', 'detail_piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('piutang_pasien', 'reg_periksa.no_rawat', '=', 'piutang_pasien.no_rawat')
            ->whereBetween('detail_piutang_pasien.tgltempo', [$tanggl1, $tanggl2])
            ->where('reg_periksa.status_lanjut', $stsLanjut)
            ->where(function ($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->groupBy('detail_piutang_pasien.no_rawat')
            ->having(DB::raw('COUNT(detail_piutang_pasien.no_rawat)'), '>', 1)
            ->orderBy('detail_piutang_pasien.no_rawat', 'ASC')
            ->get();
        $getCobHarian->map(function ($item) {
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
            // GET PENJAB (COB)
            $item->getPenjabCOB = DB::table('detail_piutang_pasien')
                ->select('penjab.png_jawab', 'detail_piutang_pasien.totalpiutang')
                ->join('penjab', 'detail_piutang_pasien.kd_pj', '=', 'penjab.kd_pj')
                ->where('detail_piutang_pasien.no_rawat', '=', $item->no_rawat)
                ->get();
        });

        return view('laporan.cob-harian', [
            'getCobHarian' => $getCobHarian,
        ]);
    }
}
