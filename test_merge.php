<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$no_rawat = '2026/07/22/000482';
$settingBundlingArray = DB::table('bw_setting_bundling')->pluck('status', 'nama_berkas')->toArray();
$masterSwitch = $settingBundlingArray['Berkas Digital Keperawatan'] ?? '0';
echo 'MasterSwitch: ' . $masterSwitch . "\n";

$cekSCAN = collect([]);
if ($masterSwitch == '1') {
    $kodeInacbg = DB::table('master_berkas_digital')->where('nama', 'INACBG')->value('kode');
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
                echo "Found locally: $file_name\n";
                return true;
            }
            
            $urlWebapps = env('URL_KHANZA') . '/webapps/berkasrawat/' . $item->lokasi_file;
            $tempPath = storage_path('app/public/file_scan/temp_' . $file_name);
            
            if (!file_exists(storage_path('app/public/file_scan'))) {
                @mkdir(storage_path('app/public/file_scan'), 0777, true);
            }
            
            try {
                $content = @file_get_contents($urlWebapps);
                if ($content !== false) {
                    file_put_contents($tempPath, $content);
                    echo "Downloaded from KHANZA: $file_name\n";
                    return true;
                } else {
                    echo "Failed to download from KHANZA: $urlWebapps\n";
                }
            } catch (\Exception $e) {
                echo "Exception downloading from KHANZA: " . $e->getMessage() . "\n";
            }
            return false;
        });
}

echo "Files found: " . count($cekSCAN) . "\n";

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

if ($cekSCAN && count($cekSCAN) > 0) {
    foreach ($cekSCAN as $scan) {
        $file_name = basename($scan->lokasi_file); 
        $path = $getValidPath('file_scan/' . $file_name);
        
        if (!$path) {
            $path = $getValidPath('file_scan/temp_' . $file_name);
        }
        
        if ($path) {
            $pdfFiles[] = $path;
            echo "Added to merge queue: $path\n";
        }
    }
}
