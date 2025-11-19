<?php

namespace App\Http\Controllers\Farmasi;

use PDF;
use setasign\Fpdi\Fpdi;
use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class BundlingFarmasi extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    // ============================================================
    // PRINT BERKAS SEP + RESEP
    // ============================================================
    function PrintBerkasSepResep(Request $request)
    {
        $getSetting = $this->cacheService->getSetting();
        $noRawat   = $request->cariNoRawat;
        $noSep     = $request->cariNoSep;

        // Inisialisasi variabel default
        $berkasResep = collect();
        $resepKronis = collect();
        $getLaborat  = collect();
        $getSEP      = null;
        $getpasien   = null;
        $jumlahData  = 0;

        // ============================================================
        // CEK PASIEN DAN REG_PERIKSA
        // ============================================================
        $cekNorawat = DB::table('reg_periksa')
            ->select(
                'reg_periksa.status_lanjut',
                'pasien.nm_pasien',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_poli',
                'reg_periksa.tgl_registrasi'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('no_rawat', $noRawat);

        $jumlahData = $cekNorawat->count();
        $getpasien  = $cekNorawat->first();

        if ($jumlahData > 0) {

            // ============================================================
            // AMBIL DATA SEP
            // ============================================================
            $getSEP = DB::table('bridging_sep')
                ->select('bridging_sep.*', 'reg_periksa.no_reg', 'reg_periksa.status_lanjut', 'reg_periksa.kd_pj')
                ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'bridging_sep.no_rawat')
                ->where('bridging_sep.no_rawat', $noRawat)
                ->where('bridging_sep.no_sep', $noSep)
                ->first();

            // ============================================================
            // AMBIL DATA RESEP (PIUTANG)
            // ============================================================
            $berkasResep = DB::table('piutang')
                ->select(
                    'piutang.nota_piutang',
                    'piutang.nm_pasien',
                    'piutang.no_rkm_medis',
                    'piutang.tgl_piutang',
                    'piutang.tgltempo',
                    'piutang.nip',
                    'piutang.catatan',
                    'piutang.ongkir',
                    'piutang.uangmuka',
                    'piutang.sisapiutang',
                    'bangsal.nm_bangsal',
                    'resep_obat.no_resep',
                    'resep_obat.no_rawat as resep_no_rawat',
                    'resep_obat.kd_dokter as kd_dokter',
                    'dokter.nm_dokter',
                    'petugas.nama as nama_petugas'
                )
                ->join('petugas', 'piutang.nip', '=', 'petugas.nip')
                ->leftJoin('bangsal', 'piutang.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->join('detailpiutang', 'piutang.nota_piutang', '=', 'detailpiutang.nota_piutang')
                ->join('databarang', 'detailpiutang.kode_brng', '=', 'databarang.kode_brng')
                ->leftJoin('resep_obat', 'resep_obat.no_rawat', '=', 'piutang.nota_piutang')
                ->leftJoin('dokter', 'resep_obat.kd_dokter', '=', 'dokter.kd_dokter')
                ->where('piutang.nota_piutang', $noRawat)
                ->groupBy(
                    'piutang.nota_piutang',
                    'piutang.nm_pasien',
                    'piutang.no_rkm_medis',
                    'piutang.tgl_piutang',
                    'piutang.tgltempo',
                    'piutang.nip',
                    'piutang.catatan',
                    'piutang.ongkir',
                    'piutang.uangmuka',
                    'piutang.sisapiutang',
                    'bangsal.nm_bangsal',
                    'resep_obat.no_resep',
                    'resep_obat.no_rawat',
                    'resep_obat.kd_dokter',
                    'dokter.nm_dokter',
                    'petugas.nama'
                )
                ->orderBy('piutang.tgl_piutang', 'asc')
                ->get();

            // DETAIL RESEP
            foreach ($berkasResep as $itemresep) {

                $detailberkasResep = DB::table('detailpiutang')
                    ->select(
                        'detailpiutang.nota_piutang',
                        'detailpiutang.kode_brng',
                        'databarang.nama_brng',
                        'detailpiutang.kode_sat',
                        'kodesatuan.satuan',
                        'detailpiutang.h_jual',
                        'detailpiutang.jumlah',
                        'detailpiutang.subtotal',
                        'detailpiutang.dis',
                        'detailpiutang.bsr_dis',
                        'detailpiutang.total',
                        'detailpiutang.no_batch',
                        'detailpiutang.no_faktur',
                        'detailpiutang.aturan_pakai'
                    )
                    ->join('databarang', 'detailpiutang.kode_brng', '=', 'databarang.kode_brng')
                    ->join('kodesatuan', 'detailpiutang.kode_sat', '=', 'kodesatuan.kode_sat')
                    ->where('detailpiutang.nota_piutang', $itemresep->nota_piutang)
                    ->orderBy('detailpiutang.kode_brng', 'asc')
                    ->get();

                $itemresep->detailberkasResep = $detailberkasResep;
            }

            // ============================================================
            // RESEP KRONIS (PAKAI QUERY DARI USER)
            // ============================================================
            $resepKronis = DB::table('pasien')
                ->select(
                    'pasien.nm_pasien',
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.no_rawat',
                    'penjab.png_jawab',
                    'dokter.nm_dokter',
                    'detailpiutang.nota_piutang',
                    'databarang.nama_brng',
                    'detailpiutang.jumlah',
                    'detailpiutang.aturan_pakai',
                    'piutang.catatan'
                )
                ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->join('piutang', 'pasien.no_rkm_medis', '=', 'piutang.no_rkm_medis')
                ->join('detailpiutang', 'piutang.nota_piutang', '=', 'detailpiutang.nota_piutang')
                ->join('databarang', 'detailpiutang.kode_brng', '=', 'databarang.kode_brng')
                ->where('reg_periksa.no_rawat', $noRawat)
                ->where('piutang.catatan', 'LIKE', '%KRONIS%')
                ->whereColumn('piutang.nota_piutang', 'reg_periksa.no_rawat')
                ->orderBy('databarang.nama_brng', 'asc')
                ->get();

            // ============================================================
            // LABORATORIUM
            // ============================================================
            $getLaborat = DB::table('periksa_lab')
                ->select(
                    'periksa_lab.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.alamat',
                    'pasien.umur',
                    'petugas.nama as nama_petugas',
                    'petugas.nip',
                    'periksa_lab.tgl_periksa',
                    'periksa_lab.jam',
                    'periksa_lab.dokter_perujuk',
                    'periksa_lab.kd_dokter',
                    'dokter.nm_dokter',
                    'dokter_pj.nm_dokter as nm_dokter_pj',
                    'penjab.png_jawab',
                    'kamar_inap.kd_kamar',
                    'kamar.kd_bangsal',
                    'poliklinik.nm_poli',
                    'bangsal.nm_bangsal'
                )
                ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('petugas', 'periksa_lab.nip', '=', 'petugas.nip')
                ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
                ->join('dokter', 'periksa_lab.kd_dokter', '=', 'dokter.kd_dokter')
                ->join('dokter as dokter_pj', 'periksa_lab.dokter_perujuk', '=', 'dokter_pj.kd_dokter')
                ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('periksa_lab.kategori', 'PK')
                ->where('periksa_lab.no_rawat', $noRawat)
                ->groupBy('periksa_lab.no_rawat', 'periksa_lab.tgl_periksa', 'periksa_lab.jam')
                ->orderBy('periksa_lab.tgl_periksa', 'desc')
                ->orderBy('periksa_lab.jam', 'desc')
                ->get();

            foreach ($getLaborat as $periksa) {

                $getPeriksaLab = DB::table('periksa_lab')
                    ->select('jns_perawatan_lab.kd_jenis_prw', 'jns_perawatan_lab.nm_perawatan', 'periksa_lab.biaya')
                    ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                    ->where([
                        ['periksa_lab.kategori', 'PK'],
                        ['periksa_lab.no_rawat', $periksa->no_rawat],
                        ['periksa_lab.tgl_periksa', $periksa->tgl_periksa],
                        ['periksa_lab.jam', $periksa->jam],
                    ])
                    ->orderBy('jns_perawatan_lab.kd_jenis_prw', 'asc')
                    ->get();

                foreach ($getPeriksaLab as $detaillab) {

                    $detaillab->getDetailLab = DB::table('detail_periksa_lab')
                        ->select(
                            'detail_periksa_lab.no_rawat',
                            'detail_periksa_lab.tgl_periksa',
                            'template_laboratorium.Pemeriksaan',
                            'detail_periksa_lab.nilai',
                            'template_laboratorium.satuan',
                            'detail_periksa_lab.nilai_rujukan',
                            'detail_periksa_lab.biaya_item',
                            'detail_periksa_lab.keterangan',
                            'detail_periksa_lab.kd_jenis_prw'
                        )
                        ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template')
                        ->where([
                            ['detail_periksa_lab.kd_jenis_prw', $detaillab->kd_jenis_prw],
                            ['detail_periksa_lab.no_rawat', $periksa->no_rawat],
                            ['detail_periksa_lab.tgl_periksa', $periksa->tgl_periksa],
                            ['detail_periksa_lab.jam', $periksa->jam],
                        ])
                        ->orderBy('template_laboratorium.urut', 'asc')
                        ->get();
                }

                $periksa->getPeriksaLab = $getPeriksaLab;
            }
        }

        // ============================================================
        // GENERATE PDF
        // ============================================================
        $pdf = PDF::loadView('farmasi.print-berkas-sep-resep', [
            'getSetting'   => $getSetting,
            'noRawat'      => $noRawat,
            'noSep'        => $noSep,
            'getpasien'    => $getpasien,
            'jumlahData'   => $jumlahData,
            'getSEP'       => $getSEP,
            'berkasResep'  => $berkasResep,
            'getLaborat'   => $getLaborat,
            'resepKronis'  => $resepKronis,
        ]);

        $no_rawatSTR = str_replace('/', '', $noRawat);
        $pdfFilename = 'SEP-RESEP-' . $no_rawatSTR . '.pdf';

        Storage::disk('public')->put('file_sepresep_farmasi/' . $pdfFilename, $pdf->output());

        // SIMPAN KE DATABASE
        $cekBerkas = DB::table('file_farmasi')
            ->where('no_rawat', $noRawat)
            ->where('jenis_berkas', 'SEP-RESEP')
            ->exists();

        if (!$cekBerkas) {
            DB::table('file_farmasi')->insert([
                'no_rkm_medis' => $getpasien->no_rkm_medis ?? null,
                'no_rawat'     => $noRawat,
                'nama_pasein'  => $getpasien->nm_pasien ?? null,
                'jenis_berkas' => 'SEP-RESEP',
                'file'         => $pdfFilename,
            ]);
        }

        // REDIRECT
        $redirectUrl = url('/view-sep-resep');
        $csrfToken   = Session::token();

        return redirect(
            $redirectUrl . '?' . http_build_query([
                '_token'       => $csrfToken,
                'cariNoRawat' => $noRawat,
                'cariNoSep'   => $noSep,
            ])
        )->with('successSavePDF', 'Berhasil menyimpan file ke bentuk PDF');
    }


    // ============================================================
    // GABUNG PDF (SCAN + SEP-RESEP)
    // ============================================================
    function GabungBergkas(Request $request)
    {
        $cekNorawat = DB::table('reg_periksa')
            ->select('reg_periksa.status_lanjut', 'pasien.nm_pasien', 'reg_periksa.no_rkm_medis')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('no_rawat', $request->no_rawat);

        $getpasien = $cekNorawat->first();

        $cekFileScan = DB::table('file_farmasi')
            ->where('no_rawat', $request->no_rawat)
            ->where('jenis_berkas', 'FILE-SCAN-FARMASI')
            ->first();

        $cekSepResep = DB::table('file_farmasi')
            ->where('no_rawat', $request->no_rawat)
            ->where('jenis_berkas', 'SEP-RESEP')
            ->first();

        if (!$cekFileScan) {
            return back()->with('errorGabung', 'FILE SCAN tidak ditemukan');
        }

        $pdf = new Fpdi();

        // FILE SEP RESEP
        $pdfSepResep = public_path('storage/file_sepresep_farmasi/' . $cekSepResep->file);
        $sepCount = $pdf->setSourceFile($pdfSepResep);

        for ($i = 1; $i <= $sepCount; $i++) {
            $template = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($template);
            $pdf->AddPage($size['orientation'], $size);
            $pdf->useTemplate($template);
        }

        // FILE SCAN FARMASI
        $pdfScan = public_path('storage/file_scan_farmasi/' . $cekFileScan->file);
        $scanCount = $pdf->setSourceFile($pdfScan);

        for ($i = 1; $i <= $scanCount; $i++) {
            $template = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($template);
            $pdf->AddPage($size['orientation'], $size);
            $pdf->useTemplate($template);
        }

        $no_rawatSTR = str_replace('/', '', $request->no_rawat);
        $outputName  = 'HASIL-FARMASI-' . $no_rawatSTR . '.pdf';

        $outputPath = public_path('hasil_farmasi_pdf/' . $outputName);
        $pdf->Output($outputPath, 'F');

        // SIMPAN DB
        $cekGabung = DB::table('file_farmasi')
            ->where('no_rawat', $request->no_rawat)
            ->where('jenis_berkas', 'HASIL-FARMASI')
            ->exists();

        if (!$cekGabung) {
            DB::table('file_farmasi')->insert([
                'no_rkm_medis' => $getpasien->no_rkm_medis,
                'no_rawat'     => $request->no_rawat,
                'nama_pasein'  => $getpasien->nm_pasien,
                'jenis_berkas' => 'HASIL-FARMASI',
                'file'         => $outputName,
            ]);
        }

        return back()->with('successGabungberkas', 'Berhasil menggabungkan file Khanza & file scan');
    }
}
