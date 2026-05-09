<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Database\QueryException;
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
        $tglLunas1 = $request->tgl_lunas1;
        $tglLunas2 = $request->tgl_lunas2;
        $filterType = $request->filter_type ?? 'tempo';
        $stsLanjut = $request->stsLanjut;

        $getCobHarian = DB::table('detail_piutang_pasien')
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
            ->leftJoin('detail_lunas_cob', 'detail_piutang_pasien.no_rawat', '=', 'detail_lunas_cob.no_rawat')
            ->when($filterType == 'tempo' && $tanggl1 && $tanggl2, function ($query) use ($tanggl1, $tanggl2) {
                return $query->whereBetween('detail_piutang_pasien.tgltempo', [$tanggl1, $tanggl2]);
            })
            ->where('reg_periksa.status_lanjut', $stsLanjut)
            ->when($filterType == 'lunas' && $tglLunas1 && $tglLunas2, function ($query) use ($tglLunas1, $tglLunas2) {
                return $query->whereNotNull('detail_lunas_cob.tgl_lunas')
                    ->whereBetween('detail_lunas_cob.tgl_lunas', [$tglLunas1, $tglLunas2]);
            })
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

            // OBAT
            $item->getObat = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Obat')
                ->get();

            // RETUR OBAT
            $item->getReturObat = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Retur Obat')
                ->get();

            // RESEP PULANG
            $item->getResepPulang = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Resep Pulang')
                ->get();

            // RALAN DOKTER
            $item->getRalanDokter = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ralan Dokter')
                ->get();

            // RALAN DOKTER PARAMEDIS
            $item->getRalanDrParamedis = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ralan Dokter Paramedis')
                ->get();

            // RALAN PARAMEDIS
            $item->getRalanParamedis = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ralan Paramedis')
                ->get();

            // RANAP DOKTER
            $item->getRanapDokter = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ranap Dokter')
                ->get();

            // RANAP DOKTER PARAMEDIS
            $item->getRanapDrParamedis = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ranap Dokter Paramedis')
                ->get();

            // RANAP PARAMEDIS
            $item->getRanapParamedis = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Ranap Paramedis')
                ->get();

            // OPERASI
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

            // KAMAR
            $item->getKamarInap = DB::table('billing')
                ->select('totalbiaya')
                ->where('no_rawat', $item->no_rawat)
                ->where('status', '=', 'Kamar')
                ->get();

            // PENJAB COB
            $item->getPenjabCOB = DB::table('detail_piutang_pasien')
                ->select(
                    'penjab.png_jawab',
                    'detail_piutang_pasien.totalpiutang'
                )
                ->join('penjab', 'detail_piutang_pasien.kd_pj', '=', 'penjab.kd_pj')
                ->where('detail_piutang_pasien.no_rawat', '=', $item->no_rawat)
                ->get();

            // AKUN BAYAR COB

            $item->getLunasCob = DB::table('detail_lunas_cob')
                ->select(
                    'tgl_lunas',
                    'nominal_cob',
                    DB::raw("(SELECT akun_bayar.nama_bayar 
                  FROM akun_bayar 
                  WHERE akun_bayar.nama_bayar = detail_lunas_cob.akun_bayar
                  LIMIT 1) AS akun_bayar")
                )
                ->where('no_rawat', $item->no_rawat)
                ->first();

            return $item;
        });

        return view('laporan.cob-harian', [
            'getCobHarian' => $getCobHarian,
            // 'akunBayar' => DB::table('akun_bayar')->get(),
            'akunBayar' => DB::table('akun_bayar')
                ->whereIn('kd_rek', [
                    '112010',
                    '112030',
                    '112011',
                    '112110',
                    '112090'
                ])
                ->get(),
        ]);
    }


    public function simpanCob(Request $request)
    {
        $request->validate([
            'no_rawat' => 'required',
            'tgl_lunas' => 'required|date_format:d/m/Y',
            'nominal_cob' => 'required',
            'akun_bayar' => 'required'
        ]);

        $tglLunas = \Carbon\Carbon::createFromFormat('d/m/Y', $request->tgl_lunas)->format('Y-m-d');

        try {
            DB::table('detail_lunas_cob')->insert([
                'no_rawat' => $request->no_rawat,
                'tgl_lunas' => $tglLunas,
                'nominal_cob' => $request->nominal_cob,
                'akun_bayar' => $request->akun_bayar
            ]);

            return redirect()->back()->with(
                'success',
                'Data COB berhasil disimpan'
            );
        } catch (QueryException $ex) {
            if ($ex->getCode() === '23000') {
                return redirect()->back()->with(
                    'error',
                    'Data gagal disimpan: nomor sudah ada.'
                );
            }

            throw $ex;
        }
    }
}
