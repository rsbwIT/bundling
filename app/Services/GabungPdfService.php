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

        $settingBundlingArray = DB::table('bw_setting_bundling')->pluck('status', 'nama_berkas')->toArray();
        $masterSwitch = $settingBundlingArray['Berkas Digital Keperawatan'] ?? '0';

        $cekSCAN = collect([]);
        if ($masterSwitch == '1') {
            $cekSCAN = DB::table('berkas_digital_perawatan')
                ->join('master_berkas_digital', 'berkas_digital_perawatan.kode', '=', 'master_berkas_digital.kode')
                ->select('berkas_digital_perawatan.*', 'master_berkas_digital.nama')
                ->where('berkas_digital_perawatan.no_rawat', $no_rawat)
                ->when($kodeInacbg, function($query) use ($kodeInacbg) {
                    return $query->where('berkas_digital_perawatan.kode', '!=', $kodeInacbg);
                })
                ->get()
                ->filter(function ($item) {
                    $file_name = basename($item->lokasi_file);
                    $path1 = storage_path('app/public/file_scan/' . $file_name);
                    $path2 = public_path('storage/file_scan/' . $file_name);
                    
                    if (file_exists($path1) || file_exists($path2)) {
                        return true;
                    }
                    
                    // Coba ambil dari URL KHANZA jika tidak ada di lokal
                    $urlWebapps = env('URL_KHANZA') . "/webapps/berkasrawat/" . $item->lokasi_file;
                    $tempPath = storage_path('app/public/file_scan/temp_' . $file_name);
                    
                    // Pastikan folder exist
                    if (!file_exists(storage_path('app/public/file_scan'))) {
                        @mkdir(storage_path('app/public/file_scan'), 0777, true);
                    }
                    
                    try {
                        $response = \Illuminate\Support\Facades\Http::timeout(15)->get($urlWebapps);
                        if ($response->successful()) {
                            file_put_contents($tempPath, $response->body());
                            return true;
                        }
                    } catch (\Exception $e) {
                        // Gagal mengambil
                        \Illuminate\Support\Facades\Log::error("Gagal download PDF dari Khanza: " . $e->getMessage());
                    }
                    
                    return false;
                });
        } else {
            $oldScan = DB::table('bw_file_casemix_scan')->where('no_rawat', $no_rawat)->first();
            if ($oldScan) {
                // Buat struktur yang sama agar bisa diloop di bawah
                $cekSCAN = collect([(object)['lokasi_file' => $oldScan->file]]);
            }
        }


        // Ambil path file jika ada
        $pdfFiles = [];
        
        // Fungsi pembantu agar bisa jalan di Laptop maupun di APANEL
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
        
        if ($cekSCAN && count($cekSCAN) > 0) {
            foreach ($cekSCAN as $scan) {
                $file_name = basename($scan->lokasi_file); 
                $path = $getValidPath('file_scan/' . $file_name);
                
                // Jika tidak ketemu, cari file temp-nya
                if (!$path) {
                    $path = $getValidPath('file_scan/temp_' . $file_name);
                }
                
                if ($path) $pdfFiles[] = $path;
            }
        }


        // Pastikan tidak ada file yang diambil dua kali
        $pdfFiles = array_unique($pdfFiles);

        // Mulai proses penggabungan PDF
        $pdf = new Fpdi();
        $importedPages = 0;

        foreach ($pdfFiles as $pdfPath) {
            if (file_exists($pdfPath)) {
                try {
                    $pageCount = $pdf->setSourceFile($pdfPath);
                    for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                        $template = $pdf->importPage($pageNumber);
                        $size = $pdf->getTemplateSize($template);
                        $pdf->AddPage($size['orientation'], $size);
                        $pdf->useTemplate($template);
                        $importedPages++;
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("FPDI Error parsing $pdfPath: " . $e->getMessage());
                }
            }
        }

        if ($importedPages > 0) {
            $no_rawatSTR = str_replace('/', '', $no_rawat);
            
            // Coba ambil nomor SEP
            $nosep = DB::table('bridging_sep')->where('no_rawat', $no_rawat)->value('no_sep');
            
            // Gunakan nomor SEP sebagai nama file jika ada, jika tidak gunakan HASIL-norawat
            $nama_file = $nosep ? $nosep : 'HASIL-' . $no_rawatSTR;
            $path_file = $nama_file . '.pdf';

            // Deteksi folder public web server (mendukung APANEL/cPanel public_html)
            $outputDir = public_path('hasil_pdf');
            if (file_exists(base_path('../public_html'))) {
                $outputDir = base_path('../public_html/hasil_pdf');
            } elseif (file_exists(base_path('../../public_html'))) {
                $outputDir = base_path('../../public_html/hasil_pdf');
            }
            
            // Hapus file lama jika ada untuk menghindari penumpukan
            $fileLama = DB::table('bw_file_casemix_hasil')->where('no_rawat', $no_rawat)->first();
            if ($fileLama && $fileLama->file !== $path_file) {
                $pathLama = $outputDir . '/' . $fileLama->file;
                if (file_exists($pathLama)) {
                    @unlink($pathLama);
                }
            }
            
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0777, true);
            }
            $outputPath = $outputDir . '/' . $path_file;
            
            // Simpan file PDF baru
            $pdf->Output($outputPath, 'F');

            // Update ke database
            DB::beginTransaction();
            try {
                DB::table('bw_file_casemix_hasil')->updateOrInsert(
                    ['no_rawat' => $no_rawat],
                    [
                        'no_rkm_medis' => $no_rkm_medis,
                        'file' => $path_file,
                    ]
                );
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } else {
            \Illuminate\Support\Facades\Log::warning("Tidak ada halaman PDF yang berhasil di-import untuk no_rawat: $no_rawat");
        }
    }
}
