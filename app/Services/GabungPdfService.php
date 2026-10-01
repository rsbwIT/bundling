<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GabungPdfService
{
    /**
     * Gabungkan berkas pasien menjadi satu PDF.
     *
     * @return array ['halaman' => int, 'dilewati' => string[]]
     *               'dilewati' = daftar berkas yang gagal digabung (agar user diberi tahu)
     */
    public static function printPdf($no_rawat, $no_rkm_medis)
    {
        $dilewati = [];

        $cekINACBG = DB::table('bw_file_casemix_inacbg')->where('no_rawat', $no_rawat)->first();
        $cekRESUMEDLL = DB::table('bw_file_casemix_remusedll')->where('no_rawat', $no_rawat)->first();
        $kodeInacbg = DB::table('master_berkas_digital')->where('nama', 'INACBG')->value('kode');

        $settingBundlingArray = DB::table('bw_setting_bundling')->pluck('status', 'nama_berkas')->toArray();
        $masterSwitch = $settingBundlingArray['Berkas Digital Keperawatan'] ?? '0';

        $cekSCAN = collect([]);
        if ($masterSwitch == '1') {
            $cekSCAN = DB::table('berkas_digital_perawatan')
                ->join('master_berkas_digital', 'berkas_digital_perawatan.kode', '=', 'master_berkas_digital.kode')
                ->select('berkas_digital_perawatan.*', 'master_berkas_digital.nama')
                ->where('berkas_digital_perawatan.no_rawat', $no_rawat)
                ->when($kodeInacbg, function ($query) use ($kodeInacbg) {
                    return $query->where('berkas_digital_perawatan.kode', '!=', $kodeInacbg);
                })
                ->get()
                ->filter(function ($item) use (&$dilewati) {
                    $file_name = basename($item->lokasi_file);
                    $path1 = storage_path('app/public/file_scan/' . $file_name);
                    $path2 = public_path('storage/file_scan/' . $file_name);
                    $tempPath = storage_path('app/public/file_scan/temp_' . $file_name);

                    // Berkas sudah ada di lokal -> langsung pakai
                    if (file_exists($path1) || file_exists($path2)) {
                        return true;
                    }

                    // PERBAIKAN: salinan hasil download sebelumnya (temp_) dipakai lagi
                    // selama umurnya < 12 jam, jadi tidak download ulang setiap klik.
                    if (file_exists($tempPath) && (time() - filemtime($tempPath)) < 43200) {
                        return true;
                    }

                    // Belum ada -> ambil dari server Khanza
                    $urlWebapps = env('URL_KHANZA') . "/webapps/berkasrawat/" . $item->lokasi_file;

                    if (!file_exists(storage_path('app/public/file_scan'))) {
                        @mkdir(storage_path('app/public/file_scan'), 0777, true);
                    }

                    try {
                        // connect_timeout: kalau server Khanza mati, gagal dalam 3 detik (bukan 15)
                        $response = Http::timeout(15)
                            ->withOptions(['connect_timeout' => 3])
                            ->get($urlWebapps);

                        if ($response->successful()) {
                            file_put_contents($tempPath, $response->body(), LOCK_EX);
                            return true;
                        }
                    } catch (\Throwable $e) {
                        Log::error("Gagal download berkas dari Khanza: " . $e->getMessage());
                    }

                    $dilewati[] = $file_name . ' (tidak ditemukan)';
                    return false;
                });
        } else {
            $oldScan = DB::table('bw_file_casemix_scan')->where('no_rawat', $no_rawat)->first();
            if ($oldScan) {
                $cekSCAN = collect([(object)['lokasi_file' => $oldScan->file]]);
            }
        }

        // Fungsi pembantu agar bisa jalan di Laptop maupun di server (aaPanel)
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

        $pdfFiles = [];

        if ($cekINACBG) {
            $path = $getValidPath('file_inacbg/' . $cekINACBG->file);
            if ($path) $pdfFiles[] = $path; else $dilewati[] = $cekINACBG->file . ' (INACBG tidak ditemukan)';
        }

        if ($cekRESUMEDLL) {
            $path = $getValidPath('resume_dll/' . $cekRESUMEDLL->file);
            if ($path) $pdfFiles[] = $path; else $dilewati[] = $cekRESUMEDLL->file . ' (resume tidak ditemukan)';
        }

        if ($cekSCAN && count($cekSCAN) > 0) {
            foreach ($cekSCAN as $scan) {
                $file_name = basename($scan->lokasi_file);
                $path = $getValidPath('file_scan/' . $file_name);

                if (!$path) {
                    $path = $getValidPath('file_scan/temp_' . $file_name);
                }

                if ($path) $pdfFiles[] = $path;
            }
        }

        $pdfFiles = array_values(array_unique($pdfFiles));

        // ---------- Proses penggabungan ----------
        $pdf = new Fpdi();
        $importedPages = 0;

        foreach ($pdfFiles as $pdfPath) {
            if (!file_exists($pdfPath)) {
                continue;
            }

            $ext = strtolower(pathinfo($pdfPath, PATHINFO_EXTENSION));

            try {
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    // PERBAIKAN: gambar (JPG/PNG) dijadikan 1 halaman PDF, tidak lagi dilewati diam-diam
                    self::tambahGambar($pdf, $pdfPath);
                    $importedPages++;
                    continue;
                }

                $pageCount = $pdf->setSourceFile($pdfPath);
                for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                    $template = $pdf->importPage($pageNumber);
                    $size = $pdf->getTemplateSize($template);
                    $pdf->AddPage($size['orientation'], $size);
                    $pdf->useTemplate($template);
                    $importedPages++;
                }
            } catch (\Throwable $e) {
                Log::error("Gagal menggabung $pdfPath: " . $e->getMessage());
                $dilewati[] = basename($pdfPath) . ' (format tidak didukung / rusak)';
            }
        }

        if ($importedPages === 0) {
            Log::warning("Tidak ada halaman PDF yang berhasil di-import untuk no_rawat: $no_rawat");
            throw new \Exception("Berkas (INACBG/Scan/Khanza) belum lengkap atau tidak ditemukan!");
        }

        $no_rawatSTR = str_replace('/', '', $no_rawat);

        // Nomor SEP (prioritaskan Ranap = 1)
        $nosep = DB::table('bridging_sep')
            ->where('no_rawat', $no_rawat)
            ->orderBy('jnspelayanan', 'asc')
            ->value('no_sep');

        $nama_file = $nosep ? $nosep : 'HASIL-' . $no_rawatSTR;
        $path_file = $nama_file . '.pdf';

        // Deteksi folder public web server (mendukung aaPanel/cPanel public_html)
        $outputDir = public_path('hasil_pdf');
        if (file_exists(base_path('../public_html'))) {
            $outputDir = base_path('../public_html/hasil_pdf');
        } elseif (file_exists(base_path('../../public_html'))) {
            $outputDir = base_path('../../public_html/hasil_pdf');
        }

        // Hapus file lama jika namanya berbeda
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

        $pdf->Output($outputDir . '/' . $path_file, 'F');

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
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return ['halaman' => $importedPages, 'dilewati' => $dilewati];
    }

    /** Tempel gambar ke halaman A4 (otomatis diperkecil agar muat, tetap proporsional). */
    private static function tambahGambar(Fpdi $pdf, string $path): void
    {
        $info = getimagesize($path);
        if (!$info) {
            throw new \Exception('Bukan gambar yang valid');
        }
        [$w, $h] = $info;

        $pageW = 210; $pageH = 297; $margin = 10;
        $ratio = min(($pageW - 2 * $margin) / $w, ($pageH - 2 * $margin) / $h);
        $newW = $w * $ratio;
        $newH = $h * $ratio;

        $pdf->AddPage('P', 'A4');
        $pdf->Image($path, ($pageW - $newW) / 2, ($pageH - $newH) / 2, $newW, $newH);
    }
}
