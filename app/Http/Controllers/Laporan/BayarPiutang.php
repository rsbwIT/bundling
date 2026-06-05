<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BayarPiutang extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    function CariBayarPiutang(Request $request)
    {
        $url = 'cari-bayar-piutang';
        $penjab = $this->cacheService->getPenjab();

        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $statusLanjut = $request->status_lanjut;

        $status = ($request->statusLunas == null ? "Lunas" : $request->statusLunas);
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));

        $bayarPiutang = DB::table('reg_periksa')
            ->select(
                'bayar_piutang.tgl_bayar',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'bayar_piutang.besar_cicilan',
                'bayar_piutang.catatan',
                'reg_periksa.no_rawat',
                'bayar_piutang.diskon_piutang',
                'bayar_piutang.tidak_terbayar',
                'reg_periksa.kd_pj',
                'penjab.png_jawab',
                'piutang_pasien.status',
                'piutang_pasien.uangmuka',
                'reg_periksa.status_lanjut'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin(DB::raw("
                (
                    SELECT
                        no_rawat,
                        MAX(tgl_bayar) AS tgl_bayar,
                        SUM(besar_cicilan) AS besar_cicilan,
                        SUM(diskon_piutang) AS diskon_piutang,
                        SUM(tidak_terbayar) AS tidak_terbayar,
                        GROUP_CONCAT(catatan SEPARATOR '; ') AS catatan
                    FROM bayar_piutang
                    GROUP BY no_rawat
                ) bayar_piutang
            "), 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')

            ->where(function ($query) use ($status, $kdPenjamin, $tanggl1, $tanggl2, $statusLanjut) {
                // Filter Penjamin
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }

                // Filter Status Piutang
                if ($status == "Lunas") {
                    $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                        ->where('piutang_pasien.status', 'Lunas');
                } elseif ($status == "Belum Lunas") {
                    $query->whereBetween('piutang_pasien.tgl_piutang', [$tanggl1, $tanggl2])
                        ->where('piutang_pasien.status', 'Belum Lunas');
                }

                // Filter Status Lanjut (Ralan / Ranap)
                if ($statusLanjut != null) {
                    $query->where('reg_periksa.status_lanjut', $statusLanjut);
                }
            })
            ->where(function ($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                    ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                    ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })

            ->groupBy(
                'reg_periksa.no_rawat',
                'bayar_piutang.tgl_bayar',
                'bayar_piutang.besar_cicilan',
                'bayar_piutang.diskon_piutang',
                'bayar_piutang.tidak_terbayar',
                'bayar_piutang.catatan',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'reg_periksa.kd_pj',
                'penjab.png_jawab',
                'piutang_pasien.status',
                'piutang_pasien.uangmuka',
                'reg_periksa.status_lanjut'
            )
            ->orderBy('bayar_piutang.no_rawat', 'asc')
            ->paginate(1000);

        $bayarPiutang->map(function ($item) {
            // 🟢 1. DATA PENJAB COB
            $item->getPenjabCOB = DB::table('detail_piutang_pasien')
                ->select(
                    'penjab.png_jawab',
                    'detail_piutang_pasien.totalpiutang'
                )
                ->join('penjab', 'detail_piutang_pasien.kd_pj', '=', 'penjab.kd_pj')
                ->where('detail_piutang_pasien.no_rawat', '=', $item->no_rawat)
                ->get();

            // 🟢 2. DATA LUNAS COB (DIUBAH KE TABEL 'detail_lunas_cob' SESUAI DATABASE ANDA)
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

            // NOMOR SEP
            $item->getNoSep = DB::table('bridging_sep')
                ->select('no_sep')
                ->where('no_rawat', $item->no_rawat)
                ->where(function ($query) use ($item) {
                    if ($item->status_lanjut == 'Ralan') {
                        $query->where('jnspelayanan', '=', '2');
                    } else {
                        $query->where('jnspelayanan', '=', '1');
                    }
                })
                ->get();

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

            return $item;
        });

        return view('laporan.bayarPiutang', [
            'url' => $url,
            'penjab' => $penjab,
            'bayarPiutang' => $bayarPiutang,
        ]);
    }
}