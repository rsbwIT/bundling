<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\DB;

// class GabungPdfService
// {
//     public static function printPdf($no_rawat, $no_rkm_medis)
//     {
//         $cekINACBG = DB::table('bw_file_casemix_inacbg')->where('no_rawat', $no_rawat)->first();
//         $cekRESUMEDLL = DB::table('bw_file_casemix_remusedll')->where('no_rawat', $no_rawat)->first();
//         $cekSCAN = DB::table('bw_file_casemix_scan')->where('no_rawat', $no_rawat)->first();

//         // PROSES BNDLING=============================================
//         $pdfPathINACBG = $cekINACBG ? public_path('storage/file_inacbg/' . $cekINACBG->file) : null;
//         $pdfPathRESUMEDLL = $cekRESUMEDLL ? public_path('storage/resume_dll/' . $cekRESUMEDLL->file) : null;
//         $pdfPathSCAN = $cekSCAN ? public_path('storage/file_scan/' . $cekSCAN->file) : null;
//         $pdf = new Fpdi();
//         function importPages($pdf, $pdfPath)
//         {
//             $pageCount = $pdf->setSourceFile($pdfPath);
//             for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
//                 $template = $pdf->importPage($pageNumber);
//                 $size = $pdf->getTemplateSize($template);
//                 $pdf->AddPage($size['orientation'], $size);
//                 $pdf->useTemplate($template);
//             }
//         }
//         importPages($pdf, $pdfPathINACBG);
//         importPages($pdf, $pdfPathRESUMEDLL);

//         if ($pdfPathSCAN) {
//             importPages($pdf, $pdfPathSCAN);
//         }
//         $no_rawatSTR = str_replace('/', '', $no_rawat);
//         $path_file = 'HASIL' . '-' . $no_rawatSTR . '.pdf';
//         $outputPath = public_path('hasil_pdf/' . $path_file);
//         $pdf->Output($outputPath, 'F');
//         DB::beginTransaction();

//         $cekBerkas = DB::table('bw_file_casemix_hasil')
//             ->where('no_rawat', $no_rawat)
//             ->exists();
//         if (!$cekBerkas) {
//             DB::table('bw_file_casemix_hasil')->insert([
//                 'no_rkm_medis' => $no_rkm_medis,
//                 'no_rawat' => $no_rawat,
//                 'file' => $path_file,
//             ]);
//             DB::commit();
//         }
//     }
// }
class GabungPdfService
{
    public static function printPdf($no_rawat, $no_rkm_medis)
    {
        $cekINACBG = DB::table('bw_file_casemix_inacbg')->where('no_rawat', $no_rawat)->first();
        $cekRESUMEDLL = DB::table('bw_file_casemix_remusedll')->where('no_rawat', $no_rawat)->first();
        // $cekSCAN = DB::table('bw_file_casemix_scan')->where('no_rawat', $no_rawat)->first();
        // $cekSCAN = DB::table('berkas_digital_perawatan')->where('no_rawat', $no_rawat)->first();
        $cekSCAN = DB::table('berkas_digital_perawatan')
            ->where('no_rawat', $no_rawat)
            ->get()
            ->filter(function ($item) {
                $file_name = basename($item->lokasi_file);
                $path = storage_path('app/public/file_scan/' . $file_name);
                return file_exists($path);
            })
            ->first(); // ambil yang pertama yang ADA FILE-nya


        // Ambil path file jika ada
        $pdfFiles = [];
        if ($cekINACBG) {
            $pdfFiles[] = public_path('storage/file_inacbg/' . $cekINACBG->file);
        }
        if ($cekRESUMEDLL) {
            $pdfFiles[] = public_path('storage/resume_dll/' . $cekRESUMEDLL->file);
        }
        // if ($cekSCAN) {
        //     $pdfFiles[] = public_path('storage/file_scan/' . $cekSCAN->file);
        // }
        // if ($cekSCAN) {
        //     $pdfFiles[] = public_path('storage/' . $cekSCAN->lokasi_file);
        // }
        if ($cekSCAN) {
            // Ambil nama file dari path relatif di DB
            $file_name = basename($cekSCAN->lokasi_file); // misal pages/upload/021-20251021000350.pdf → 021-20251021000350.pdf

            // Path publik Laravel
            $public_file_path = public_path('storage/file_scan/' . $file_name);

            if (file_exists($public_file_path)) {
                $pdfFiles[] = $public_file_path;
            }
        }


        // Pastikan tidak ada file yang diambil dua kali
        $pdfFiles = array_unique($pdfFiles);

        // Mulai proses penggabungan PDF
        $pdf = new Fpdi();
        foreach ($pdfFiles as $pdfPath) {
            if (file_exists($pdfPath)) {
                $pageCount = $pdf->setSourceFile($pdfPath);
                for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                    $template = $pdf->importPage($pageNumber);
                    $size = $pdf->getTemplateSize($template);
                    $pdf->AddPage($size['orientation'], $size);
                    $pdf->useTemplate($template);
                }
            }
        }

        // Simpan hasil penggabungan
        $no_rawatSTR = str_replace('/', '', $no_rawat);
        $path_file = 'HASIL' . '-' . $no_rawatSTR . '.pdf';
        $outputPath = public_path('hasil_pdf/' . $path_file);
        $pdf->Output($outputPath, 'F');

        // Simpan ke database dengan transaksi
        DB::beginTransaction();
        try {
            $cekBerkas = DB::table('bw_file_casemix_hasil')->where('no_rawat', $no_rawat)->exists();
            if (!$cekBerkas) {
                DB::table('bw_file_casemix_hasil')->insert([
                    'no_rkm_medis' => $no_rkm_medis,
                    'no_rawat' => $no_rawat,
                    'file' => $path_file,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
