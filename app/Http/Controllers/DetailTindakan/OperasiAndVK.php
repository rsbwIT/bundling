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
        $tanggl1 = $request->tgl1 ?: date('Y-m-d');
        $tanggl2 = $request->tgl2 ?: date('Y-m-d');
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

                // Lokasi Ruangan
                DB::raw('IF(operasi.status="Ralan",
            (SELECT nm_poli FROM poliklinik WHERE poliklinik.kd_poli=reg_periksa.kd_poli),
            (SELECT bangsal.nm_bangsal FROM kamar_inap
                INNER JOIN kamar ON kamar_inap.kd_kamar=kamar.kd_kamar
                INNER JOIN bangsal ON kamar.kd_bangsal=bangsal.kd_bangsal
                WHERE kamar_inap.no_rawat=operasi.no_rawat LIMIT 1)
        ) AS ruangan'),

                // Operator & Biaya
                'operator1.nm_dokter AS operator1',
                DB::raw('SUM(operasi.biayaoperator1) AS biayaoperator1'),
                'operator2.nm_dokter AS operator2',
                DB::raw('SUM(operasi.biayaoperator2) AS biayaoperator2'),
                'operator3.nm_dokter AS operator3',
                DB::raw('SUM(operasi.biayaoperator3) AS biayaoperator3'),

                // Asisten Operator
                'asisten_operator1.nama AS asisten_operator1',
                DB::raw('SUM(operasi.biayaasisten_operator1) AS biayaasisten_operator1'),
                'asisten_operator2.nama AS asisten_operator2',
                DB::raw('SUM(operasi.biayaasisten_operator2) AS biayaasisten_operator2'),
                'asisten_operator3.nama AS asisten_operator3',
                DB::raw('SUM(operasi.biayaasisten_operator3) AS biayaasisten_operator3'),

                // Instrumen & Lainnya
                'instrumen.nama AS instrumen',
                DB::raw('SUM(operasi.biayainstrumen) AS biayainstrumen'),

                // Dokter Anak dan Anestesi
                'dokter_anak.nm_dokter AS dokter_anak',
                DB::raw('SUM(operasi.biayadokter_anak) AS biayadokter_anak'),
                'perawaat_resusitas.nama AS perawaat_resusitas',
                DB::raw('SUM(operasi.biayaperawaat_resusitas) AS biayaperawaat_resusitas'),
                'dokter_anestesi.nm_dokter AS dokter_anestesi',
                DB::raw('SUM(operasi.biayadokter_anestesi) AS biayadokter_anestesi'),

                // Asisten Anestesi
                'asisten_anestesi.nama AS asisten_anestesi',
                DB::raw('SUM(operasi.biayaasisten_anestesi) AS biayaasisten_anestesi'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.asisten_anestesi2) AS asisten_anestesi2'),
                DB::raw('SUM(operasi.biayaasisten_anestesi2) AS biayaasisten_anestesi2'),

                // Bidan
                'bidan.nama AS bidan',
                DB::raw('SUM(operasi.biayabidan) AS biayabidan'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.bidan2) AS bidan2'),
                DB::raw('SUM(operasi.biayabidan2) AS biayabidan2'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.bidan3) AS bidan3'),
                DB::raw('SUM(operasi.biayabidan3) AS biayabidan3'),

                // Perawat Luar & Omloop
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.perawat_luar) AS perawat_luar'),
                DB::raw('SUM(operasi.biayaperawat_luar) AS biayaperawat_luar'),

                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.omloop) AS omloop'),
                DB::raw('SUM(operasi.biaya_omloop) AS biaya_omloop'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.omloop2) AS omloop2'),
                DB::raw('SUM(operasi.biaya_omloop2) AS biaya_omloop2'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.omloop3) AS omloop3'),
                DB::raw('SUM(operasi.biaya_omloop3) AS biaya_omloop3'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.omloop4) AS omloop4'),
                DB::raw('SUM(operasi.biaya_omloop4) AS biaya_omloop4'),
                DB::raw('(SELECT nama FROM petugas WHERE petugas.nip=operasi.omloop5) AS omloop5'),
                DB::raw('SUM(operasi.biaya_omloop5) AS biaya_omloop5'),

                // Dokter Pendamping
                DB::raw('(SELECT nm_dokter FROM dokter WHERE dokter.kd_dokter=operasi.dokter_pjanak) AS dokter_pjanak'),
                DB::raw('SUM(operasi.biaya_dokter_pjanak) AS biaya_dokter_pjanak'),
                DB::raw('(SELECT nm_dokter FROM dokter WHERE dokter.kd_dokter=operasi.dokter_umum) AS dokter_umum'),
                DB::raw('SUM(operasi.biaya_dokter_umum) AS biaya_dokter_umum'),

                // Biaya Alat, OK, Akomodasi
                'operasi.biayaalat',
                DB::raw('SUM(operasi.biayasewaok) AS biayasewaok'),
                'operasi.akomodasi',
                DB::raw('SUM(operasi.bagian_rs) AS bagian_rs'),
                'operasi.biayasarpras',

                // Pembayaran & Status
                'bayar_piutang.besar_cicilan',
                'piutang_pasien.uangmuka',
                DB::raw("IF(penjab.png_jawab LIKE '%umum%', COALESCE(nota_inap.tanggal, nota_jalan.tanggal), bayar_piutang.tgl_bayar) as tgl_bayar"),
                DB::raw("COALESCE(nota_inap.no_nota, nota_jalan.no_nota) as no_nota"),
                'piutang_pasien.status'
            )

            // ===== JOIN SECTION =====
            ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('paket_operasi', 'operasi.kode_paket', '=', 'paket_operasi.kode_paket')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('dokter as operator1', 'operator1.kd_dokter', '=', 'operasi.operator1')
            ->leftJoin('dokter as operator2', 'operator2.kd_dokter', '=', 'operasi.operator2')
            ->leftJoin('dokter as operator3', 'operator3.kd_dokter', '=', 'operasi.operator3')
            ->leftJoin('dokter as dokter_anak', 'dokter_anak.kd_dokter', '=', 'operasi.dokter_anak')
            ->leftJoin('dokter as dokter_anestesi', 'dokter_anestesi.kd_dokter', '=', 'operasi.dokter_anestesi')
            ->leftJoin('petugas as asisten_operator1', 'asisten_operator1.nip', '=', 'operasi.asisten_operator1')
            ->leftJoin('petugas as asisten_operator2', 'asisten_operator2.nip', '=', 'operasi.asisten_operator2')
            ->leftJoin('petugas as asisten_operator3', 'asisten_operator3.nip', '=', 'operasi.asisten_operator3')
            ->leftJoin('petugas as asisten_anestesi', 'asisten_anestesi.nip', '=', 'operasi.asisten_anestesi')
            ->leftJoin('petugas as bidan', 'bidan.nip', '=', 'operasi.bidan')
            ->leftJoin('petugas as instrumen', 'instrumen.nip', '=', 'operasi.instrumen')
            ->leftJoin('petugas as perawaat_resusitas', 'perawaat_resusitas.nip', '=', 'operasi.perawaat_resusitas')
            ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
            ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'operasi.no_rawat')
            ->leftJoin('nota_jalan', 'operasi.no_rawat', '=', 'nota_jalan.no_rawat')
            ->leftJoin('nota_inap', 'operasi.no_rawat', '=', 'nota_inap.no_rawat')

            // ===== FILTER SECTION =====
            ->where(function ($query) use ($kdPenjamin, $kdPetugas, $kdDokter, $status, $tanggl1, $tanggl2) {
                if (!empty($kdPenjamin)) {
                    $query->whereIn('penjab.kd_pj', $kdPenjamin);
                }
                if (!empty($kdPetugas)) {
                    $query->whereIn('asisten_operator1.nip', $kdPetugas);
                }
                if (!empty($kdDokter)) {
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

            // ===== PENCARIAN NOMOR RAWAT / RM / NAMA =====
            ->where(function ($query) use ($cariNomor) {
                if (!empty($cariNomor)) {
                    $query->where(function($q) use ($cariNomor) {
                $q->where('reg_periksa.no_rawat', 'like', $cariNomor . '%')
                    ->orWhere('reg_periksa.no_rkm_medis', 'like', $cariNomor . '%')
                    ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                                });
                }
            })

            // ===== GROUP & ORDER =====
            ->groupBy(
                'operasi.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'operasi.kode_paket',
                'paket_operasi.nm_perawatan',
                'operasi.tgl_operasi',
                'penjab.png_jawab',
                'penjab.kd_pj',
                'operator1.nm_dokter',
                'operator2.nm_dokter',
                'operator3.nm_dokter',
                'asisten_operator1.nama',
                'asisten_operator2.nama',
                'asisten_operator3.nama',
                'instrumen.nama',
                'dokter_anak.nm_dokter',
                'perawaat_resusitas.nama',
                'dokter_anestesi.nm_dokter',
                'asisten_anestesi.nama',
                'bidan.nama',
                'operasi.biayaalat',
                'operasi.akomodasi',
                'operasi.biayasarpras',
                'bayar_piutang.besar_cicilan',
                'piutang_pasien.uangmuka',
                'bayar_piutang.tgl_bayar',
                'piutang_pasien.status',
                'nota_inap.tanggal',
                'nota_jalan.tanggal',
                'nota_inap.no_nota',
                'nota_jalan.no_nota'
            )
            ->orderBy('penjab.kd_pj', 'asc')
            ->get();
            
        $noRawats = $OperasiAndVK->pluck('no_rawat')->toArray();
        $billings = DB::table('billing')->select('no_rawat', 'totalbiaya', 'status')->whereIn('no_rawat', $noRawats)->get()->groupBy('no_rawat');

        $OperasiAndVK->map(function ($item) use ($billings) {
            $itemBillings = isset($billings[$item->no_rawat]) ? $billings[$item->no_rawat] : collect();
            
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
        return view('detail-tindakan.operasi-and-vk', [
            'penjab' => $penjab,
            'petugas' => $petugas,
            'dokter' => $dokter,
            'OperasiAndVK' => $OperasiAndVK,
        ]);
    }
}
