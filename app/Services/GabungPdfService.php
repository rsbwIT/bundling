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
        $kodeInacbg = DB::table('master_berkas_digital')->where('nama', 'INACBG')->value('kode');

        $cekSCAN = DB::table('berkas_digital_perawatan')
            ->where('no_rawat', $no_rawat)
            ->when($kodeInacbg, function($query) use ($kodeInacbg) {
                return $query->where('kode', '!=', $kodeInacbg);
            })
            ->get()
            ->filter(function ($item) {
                $file_name = basename($item->lokasi_file);
                $path1 = storage_path('app/public/file_scan/' . $file_name);
                $path2 = public_path('storage/file_scan/' . $file_name);
                return file_exists($path1) || file_exists($path2);
            })
            ->first(); // ambil yang pertama yang ADA FILE-nya


        // Ambil path file jika ada
        $pdfFiles = [];
        
        $getValidPath = function ($relativePath) {
            $storagePath = storage_path('app/public/' . $relativePath);
            if (file_exists($storagePath)) {
                return $storagePath;
            }
            $publicPath = public_path('storage/' . $relativePath);
            if (file_exists($publicPath)) {
                return $publicPath;
            }
            return null;
        };

        if ($cekINACBG) {
            $path = $getValidPath('file_inacbg/' . $cekINACBG->file);
            if ($path) $pdfFiles[] = $path;
        }
        if ($cekRESUMEDLL) {
            $path = $getValidPath('resume_dll/' . $cekRESUMEDLL->file);
            if ($path) $pdfFiles[] = $path;
        }
        if ($cekSCAN) {
            $file_name = basename($cekSCAN->lokasi_file);
            $path = $getValidPath('file_scan/' . $file_name);
            if ($path) $pdfFiles[] = $path;
        }

        // Pastikan tidak ada file yang diambil dua kali
        $pdfFiles = array_unique($pdfFiles);

        // Mulai proses penggabungan PDF
        $pdf = new Fpdi();
        foreach ($pdfFiles as $pdfPath) {
            if (file_exists($pdfPath)) {
                try {
                    $pageCount = $pdf->setSourceFile($pdfPath);
                    for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                        $template = $pdf->importPage($pageNumber);
                        $size = $pdf->getTemplateSize($template);
                        $pdf->AddPage($size['orientation'], $size);
                        $pdf->useTemplate($template);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("FPDI Error parsing $pdfPath: " . $e->getMessage());
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
