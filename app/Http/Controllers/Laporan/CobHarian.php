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
        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');
        $tglLunas1 = $request->tgl_lunas1;
        $tglLunas2 = $request->tgl_lunas2;
        $filterType = $request->filter_type ?? 'tempo';
        $stsLanjut = $request->stsLanjut;
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));

        $penjab = DB::table('penjab')
            ->where('png_jawab', 'like', '%COB%')
            ->get();

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
            ->when($kdPenjamin, function ($query) use ($kdPenjamin) {
                return $query->whereIn('reg_periksa.kd_pj', $kdPenjamin);
            })
            ->when($filterType == 'lunas' && $tglLunas1 && $tglLunas2, function ($query) use ($tglLunas1, $tglLunas2) {
                return $query->whereNotNull('detail_lunas_cob.tgl_lunas')
                    ->whereBetween('detail_lunas_cob.tgl_lunas', [$tglLunas1, $tglLunas2]);
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
            ->groupBy('detail_piutang_pasien.no_rawat')
            ->having(DB::raw('COUNT(detail_piutang_pasien.no_rawat)'), '>', 1)
            ->orderBy('detail_piutang_pasien.no_rawat', 'ASC')
            ->get();

        $noRawatList = $getCobHarian->pluck('no_rawat')->toArray();

        $billingData = [];
        $penjabCobData = [];
        $lunasCobData = collect([]);

        if (!empty($noRawatList)) {
            $billingData = DB::table('billing')
                ->select('no_rawat', 'status', 'totalbiaya', 'nm_perawatan', 'no')
                ->whereIn('no_rawat', $noRawatList)
                ->get()
                ->groupBy('no_rawat');

            $penjabCobData = DB::table('detail_piutang_pasien')
                ->select(
                    'detail_piutang_pasien.no_rawat',
                    'penjab.png_jawab',
                    'detail_piutang_pasien.totalpiutang'
                )
                ->join('penjab', 'detail_piutang_pasien.kd_pj', '=', 'penjab.kd_pj')
                ->whereIn('detail_piutang_pasien.no_rawat', $noRawatList)
                ->get()
                ->groupBy('no_rawat');

            $lunasCobData = DB::table('detail_lunas_cob')
                ->select(
                    'no_rawat',
                    'tgl_lunas',
                    'nominal_cob',
                    DB::raw("(SELECT akun_bayar.nama_bayar 
                  FROM akun_bayar 
                  WHERE akun_bayar.nama_bayar = detail_lunas_cob.akun_bayar
                  LIMIT 1) AS akun_bayar")
                )
                ->whereIn('no_rawat', $noRawatList)
                ->get()
                ->keyBy('no_rawat');
        }

        $getCobHarian->map(function ($item) use ($billingData, $penjabCobData, $lunasCobData) {
            $billings = collect($billingData->get($item->no_rawat, []));

            // NOMOR NOTA
            $item->getNomorNota = $billings->where('no', 'No.Nota')->values();

            // REGISTRASI
            $item->getRegistrasi = $billings->where('status', 'Registrasi')->values();

            // OBAT
            $item->getObat = $billings->where('status', 'Obat')->values();

            // RETUR OBAT
            $item->getReturObat = $billings->where('status', 'Retur Obat')->values();

            // RESEP PULANG
            $item->getResepPulang = $billings->where('status', 'Resep Pulang')->values();

            // RALAN DOKTER
            $item->getRalanDokter = $billings->where('status', 'Ralan Dokter')->values();

            // RALAN DOKTER PARAMEDIS
            $item->getRalanDrParamedis = $billings->where('status', 'Ralan Dokter Paramedis')->values();

            // RALAN PARAMEDIS
            $item->getRalanParamedis = $billings->where('status', 'Ralan Paramedis')->values();

            // RANAP DOKTER
            $item->getRanapDokter = $billings->where('status', 'Ranap Dokter')->values();

            // RANAP DOKTER PARAMEDIS
            $item->getRanapDrParamedis = $billings->where('status', 'Ranap Dokter Paramedis')->values();

            // RANAP PARAMEDIS
            $item->getRanapParamedis = $billings->where('status', 'Ranap Paramedis')->values();

            // OPERASI
            $item->getOprasi = $billings->where('status', 'Operasi')->values();

            // LABORAT
            $item->getLaborat = $billings->where('status', 'Laborat')->values();

            // RADIOLOGI
            $item->getRadiologi = $billings->where('status', 'Radiologi')->values();

            // TAMBAHAN
            $item->getTambahan = $billings->where('status', 'Tambahan')->values();

            // POTONGAN
            $item->getPotongan = $billings->where('status', 'Potongan')->values();

            // KAMAR
            $item->getKamarInap = $billings->where('status', 'Kamar')->values();

            // PENJAB COB
            $item->getPenjabCOB = collect($penjabCobData->get($item->no_rawat, []))->values();

            // AKUN BAYAR COB
            $item->getLunasCob = $lunasCobData->get($item->no_rawat);

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
            'penjab' => $penjab
        ]);
    }


    public function simpanCob(Request $request)
    {
        $request->validate([
            'no_rawat'    => 'required',
            'tgl_lunas'   => 'required|date_format:d/m/Y',
            'nominal_cob' => 'required',
            'akun_bayar'  => 'required'
        ]);

        try {

            // FORMAT TANGGAL
            $tglLunas = \Carbon\Carbon::createFromFormat(
                'd/m/Y',
                $request->tgl_lunas
            )->format('Y-m-d');

            // FORMAT NOMINAL
            $nominal = str_replace(
                '.',
                '',
                $request->nominal_cob
            );

            DB::table('detail_lunas_cob')->insert([
                'no_rawat'    => $request->no_rawat,
                'tgl_lunas'   => $tglLunas,
                'nominal_cob' => $nominal,
                'akun_bayar'  => $request->akun_bayar
            ]);

            return back()->with(
                'success',
                'Data COB berhasil disimpan'
            );
        } catch (QueryException $ex) {

            if ($ex->getCode() == '23000') {

                return back()->with(
                    'error',
                    'Data gagal disimpan: nomor rawat sudah ada'
                );
            }

            return back()->with(
                'error',
                $ex->getMessage()
            );
        }
    }
}
