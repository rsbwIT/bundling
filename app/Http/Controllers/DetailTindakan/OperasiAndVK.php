<?php

namespace App\Http\Controllers\DetailTindakan;

use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OperasiAndVK extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function OperasiAndVK(Request $request)
    {
        $penjab = $this->cacheService->getPenjab();
        $petugas = $this->cacheService->getPetugas();
        $dokter = $this->cacheService->getDokter();

        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));
        $kdPetugas = ($request->input('kdPetugas') == null) ? "" : explode(',', $request->input('kdPetugas'));
        $kdDokter = ($request->input('kdDokter')  == null) ? "" : explode(',', $request->input('kdDokter'));
        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1;
        $tanggl2 = $request->tgl2;
        $status = ($request->statusLunas == null ? "Lunas" : $request->statusLunas);

        $OperasiAndVK = DB::table('operasi')
            ->select(
                'operasi.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'operasi.kode_paket',
                'paket_operasi.nm_perawatan',
                'operasi.tgl_operasi',
                'penjab.png_jawab',
                'penjab.kd_pj',
                DB::raw('IF(operasi.status="Ralan", (SELECT nm_poli FROM poliklinik WHERE poliklinik.kd_poli=reg_periksa.kd_poli), (SELECT bangsal.nm_bangsal FROM kamar_inap INNER JOIN kamar ON kamar_inap.kd_kamar=kamar.kd_kamar INNER JOIN bangsal ON kamar.kd_bangsal=bangsal.kd_bangsal WHERE kamar_inap.no_rawat=operasi.no_rawat LIMIT 1)) AS ruangan'),
                'operator1.nm_dokter AS operator1',
                DB::raw('SUM(operasi.biayaoperator1) as biayaoperator1'),
                'operator2.nm_dokter AS operator2',
                DB::raw('SUM(operasi.biayaoperator2) as biayaoperator2'),
                'operator3.nm_dokter AS operator3',
                DB::raw('SUM(operasi.biayaoperator3) as biayaoperator3'),
                'asisten_operator1.nama AS asisten_operator1',
                DB::raw('SUM(operasi.biayaasisten_operator1) as biayaasisten_operator1'),
                'asisten_operator2.nama AS asisten_operator2',
                DB::raw('SUM(operasi.biayaasisten_operator2) as biayaasisten_operator2'),
                'asisten_operator3.nama AS asisten_operator3',
                DB::raw('SUM(operasi.biayaasisten_operator3) as biayaasisten_operator3'),
                'instrumen.nama AS instrumen',
                DB::raw('SUM(operasi.biayainstrumen) as biayainstrumen'),
                'dokter_anak.nm_dokter AS dokter_anak',
                DB::raw('SUM(operasi.biayadokter_anak) as biayadokter_anak'),
                'perawaat_resusitas.nama AS perawaat_resusitas',
                DB::raw('SUM(operasi.biayaperawaat_resusitas) as biayaperawaat_resusitas'),
                'dokter_anestesi.nm_dokter AS dokter_anestesi',
                DB::raw('SUM(operasi.biayadokter_anestesi) as biayadokter_anestesi'),
                'asisten_anestesi.nama AS asisten_anestesi',
                DB::raw('SUM(operasi.biayaasisten_anestesi) as biayaasisten_anestesi'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.asisten_anestesi2) AS asisten_anestesi2'),
                DB::raw('SUM(operasi.biayaasisten_anestesi2) as biayaasisten_anestesi2'),
                'bidan.nama AS bidan',
                DB::raw('SUM(operasi.biayabidan) as biayabidan'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.bidan2) AS bidan2'),
                DB::raw('SUM(operasi.biayabidan2) as biayabidan2'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.bidan3) AS bidan3'),
                DB::raw('SUM(operasi.biayabidan3) as biayabidan3'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.perawat_luar) AS perawat_luar'),
                DB::raw('SUM(operasi.biayaperawat_luar) as biayaperawat_luar'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.omloop) AS omloop'),
                DB::raw('SUM(operasi.biaya_omloop) as biaya_omloop'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.omloop2) AS omloop2'),
                DB::raw('SUM(operasi.biaya_omloop2) as biaya_omloop2'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.omloop3) AS omloop3'),
                DB::raw('SUM(operasi.biaya_omloop3) as biaya_omloop3'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.omloop4) AS omloop4'),
                DB::raw('SUM(operasi.biaya_omloop4) as biaya_omloop4'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.omloop5) AS omloop5'),
                DB::raw('SUM(operasi.biaya_omloop5) as biaya_omloop5'),
                DB::raw('(SELECT nm_dokter FROM dokter WHERE dokter.kd_dokter=operasi.dokter_pjanak) AS dokter_pjanak'),
                DB::raw('SUM(operasi.biaya_dokter_pjanak) as biaya_dokter_pjanak'),
                DB::raw('(SELECT nm_dokter FROM dokter WHERE dokter.kd_dokter=operasi.dokter_umum) AS dokter_umum'),
                DB::raw('SUM(operasi.biaya_dokter_umum) as biaya_dokter_umum'),
                // DB::raw('SUM(operasi.biayaalat) as biayaalat'),
                'operasi.biayaalat',
                DB::raw('SUM(operasi.biayasewaok) as biayasewaok'),
                // DB::raw('SUM(operasi.akomodasi) as akomodasi'),
                'operasi.akomodasi',
                DB::raw('SUM(operasi.bagian_rs) as bagian_rs'),
                // DB::raw('SUM(operasi.biayasarpras) as biayasarpras'),
                'operasi.biayasarpras',
                'bayar_piutang.besar_cicilan',
                'piutang_pasien.uangmuka',
                'bayar_piutang.tgl_bayar',
                'piutang_pasien.status'
            )
            ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('paket_operasi', 'operasi.kode_paket', '=', 'paket_operasi.kode_paket')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('dokter as operator1', 'operator1.kd_dokter', '=', 'operasi.operator1')
            ->join('dokter as operator2', 'operator2.kd_dokter', '=', 'operasi.operator2')
            ->join('dokter as operator3', 'operator3.kd_dokter', '=', 'operasi.operator3')
            ->join('dokter as dokter_anak', 'dokter_anak.kd_dokter', '=', 'operasi.dokter_anak')
            ->join('dokter as dokter_anestesi', 'dokter_anestesi.kd_dokter', '=', 'operasi.dokter_anestesi')
            ->join('petugas as asisten_operator1', 'asisten_operator1.nip', '=', 'operasi.asisten_operator1')
            ->join('petugas as asisten_operator2', 'asisten_operator2.nip', '=', 'operasi.asisten_operator2')
            ->join('petugas as asisten_operator3', 'asisten_operator3.nip', '=', 'operasi.asisten_operator3')
            ->join('petugas as asisten_anestesi', 'asisten_anestesi.nip', '=', 'operasi.asisten_anestesi')
            ->join('petugas as bidan', 'bidan.nip', '=', 'operasi.bidan')
            ->join('petugas as instrumen', 'instrumen.nip', '=', 'operasi.instrumen')
            ->join('petugas as perawaat_resusitas', 'perawaat_resusitas.nip', '=', 'operasi.perawaat_resusitas')
            ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'operasi.no_rawat')
            ->where(function ($query) use ($kdPenjamin, $kdPetugas, $kdDokter, $status,  $tanggl1, $tanggl2) {
                if ($kdPenjamin) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
                if ($kdPetugas) {
                    $query->whereIn('asisten_operator1.nip', $kdPetugas);
                }
                if ($kdDokter) {
                    $query->whereIn('operator1.kd_dokter', $kdDokter);
                }
                if ($status == "Lunas") {
                    $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                        ->where('piutang_pasien.status', 'Lunas');
                } elseif ($status == "Belum Lunas") {
                    $query->whereBetween('piutang_pasien.tgl_piutang', [$tanggl1, $tanggl2])
                        ->where('piutang_pasien.status', 'Belum Lunas');
                }
            })
            ->where(function ($query) use ($cariNomor) {
                $query->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%');
                $query->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%');
                $query->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
            })
            ->groupBy('operasi.no_rawat', 'operasi.tgl_operasi', 'bayar_piutang.besar_cicilan')
            ->orderBy('penjab.kd_pj', 'asc')
            ->get();
        $OperasiAndVK->map(function ($item) {
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
            return $item;
        });
        return view('detail-tindakan.operasi-and-vk', [
            'penjab' => $penjab,
            'petugas' => $petugas,
            'dokter' => $dokter,
            'OperasiAndVK' => $OperasiAndVK,
        ]);
    }
}
