<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class PiutangRalan extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function CariPiutangRalan(Request $request)
    {
        $pencarian = '/cari-piutang-ralan';
        $penjab = $this->cacheService->getPenjab();

        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');

        $status = ($request->statusLunas == "Lunas") ? "Lunas" : (($request->statusLunas == "Belum Lunas") ? "Belum Lunas" : "");
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));

        $piutangRalan = DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'reg_periksa.tgl_registrasi',
                'dokter.nm_dokter',
                'penjab.png_jawab',
                'piutang_pasien.uangmuka',
                'piutang_pasien.totalpiutang',
                'bayar_piutang.tgl_bayar',
                'reg_periksa.status_lanjut'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->leftJoin('bayar_piutang', 'bayar_piutang.no_rawat', '=', 'reg_periksa.no_rawat')
            ->where('reg_periksa.status_lanjut', '=', 'Ralan')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggl1, $tanggl2])
            ->where(function ($query) use ($status, $kdPenjamin) {
                if ($status) {
                    $query->where('piutang_pasien.status', $status);
                }
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
            })
            ->where(function ($query) use ($status, $cariNomor) {
                if ($status) {
                    $query->where('piutang_pasien.status', $status);
                }
                $query->orWhere('reg_periksa.no_rawat', 'like', $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->orderBy('reg_periksa.tgl_registrasi', 'asc')
            ->get();
        $noRawats = $piutangRalan->pluck('no_rawat')->toArray();

        $bridgingSep = DB::table('bridging_sep')->select('no_rawat', 'no_sep', 'jnspelayanan')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $notaJalan = DB::table('nota_jalan')->select('no_rawat', 'no_nota')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $billings = DB::table('billing')->select('no_rawat', 'nm_perawatan', 'totalbiaya', 'status')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');
        $bayarPiutang = DB::table('bayar_piutang')->select('no_rawat', 'besar_cicilan', 'diskon_piutang', 'tidak_terbayar')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $piutangRalan->map(function ($item) use ($bridgingSep, $notaJalan, $billings, $bayarPiutang) {
            // NOMOR SEP
            $itemSeps = isset($bridgingSep[$item->no_rawat]) ? $bridgingSep[$item->no_rawat] : collect();
            $item->getNoSep = $itemSeps->filter(function($sep) use ($item) {
                if ($item->status_lanjut == 'Ralan') {
                    return $sep->jnspelayanan == '2';
                } else {
                    return $sep->jnspelayanan == '1';
                }
            })->values();

            // NOMOR NOTA
            $item->getNomorNota = isset($notaJalan[$item->no_rawat]) ? $notaJalan[$item->no_rawat] : collect();

            // BILLING
            $itemBillings = isset($billings[$item->no_rawat]) ? $billings[$item->no_rawat] : collect();
            $filterBilling = function($status) use ($itemBillings) {
                return $itemBillings->where('status', $status)->values();
            };

            $item->getLaborat = $filterBilling('Laborat');
            $item->getRadiologi = $filterBilling('Radiologi');
            $item->getObat = $filterBilling('Obat');
            $item->getRalanDokter = $filterBilling('Ralan Dokter');
            $item->getRalanDrParamedis = $filterBilling('Ralan Dokter Paramedis');
            $item->getRalanParamedis = $filterBilling('Ralan Paramedis');
            $item->getTambahan = $filterBilling('Tambahan');
            $item->getPotongan = $filterBilling('Potongan');
            $item->getRegistrasi = $filterBilling('Registrasi');
            $item->getOprasi = $filterBilling('Operasi');

            // SUDAH DIBAYAR / DISKON / TIDAK TERBAYAR
            $item->getSudahBayar = isset($bayarPiutang[$item->no_rawat]) ? $bayarPiutang[$item->no_rawat] : collect();

            return $item;
        });

        return view('laporan.piutangRalan', [
            'pencarian' => $pencarian,
            'penjab' => $penjab,
            'piutangRalan' => $piutangRalan,
        ]);
    }
}
