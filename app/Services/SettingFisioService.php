<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class SettingFisioService
{
    private static $filePath = null;

    private static function getPath()
    {
        if (!self::$filePath) {
            self::$filePath = storage_path('app/setting_fisio_suhu.json');
        }
        return self::$filePath;
    }

    /**
     * Ambil array nilai suhu_tubuh yang diidentifikasi sebagai Resume Fisio.
     * Mengambil langsung dari tabel database `bw_setting_fisio`.
     * 
     * @return array
     */
    public static function getSuhuTubuhFisio(): array
    {
        // 1. Coba ambil dari Database MySQL (Tabel bw_setting_fisio)
        try {
            if (Schema::hasTable('bw_setting_fisio')) {
                $dbValues = DB::table('bw_setting_fisio')
                    ->orderByRaw('CAST(suhu_tubuh AS UNSIGNED) ASC')
                    ->pluck('suhu_tubuh')
                    ->toArray();

                if (!empty($dbValues)) {
                    return array_values(array_map('strval', $dbValues));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('SettingFisioService DB Error: ' . $e->getMessage());
        }

        // 2. Fallback: baca dari file JSON lokal
        $path = self::getPath();
        if (file_exists($path)) {
            $content = @file_get_contents($path);
            if ($content) {
                $data = json_decode($content, true);
                if (is_array($data) && isset($data['suhu_tubuh']) && is_array($data['suhu_tubuh'])) {
                    return array_values(array_map('strval', $data['suhu_tubuh']));
                }
            }
        }

        // 3. Fallback default jika database dan file belum ada
        return ['2', '3', '5', '6', '7'];
    }

    /**
     * Simpan array nilai suhu_tubuh ke database (bw_setting_fisio) dan file JSON backup.
     * 
     * @param array $values
     * @return bool
     */
    public static function setSuhuTubuhFisio(array $values): bool
    {
        $cleaned = array_values(array_unique(array_filter(array_map('trim', array_map('strval', $values)), function ($val) {
            return $val !== '';
        })));

        sort($cleaned, SORT_NATURAL);

        // 1. Simpan ke Database MySQL (Tabel bw_setting_fisio)
        try {
            if (Schema::hasTable('bw_setting_fisio')) {
                DB::transaction(function () use ($cleaned) {
                    DB::table('bw_setting_fisio')->delete();
                    foreach ($cleaned as $val) {
                        DB::table('bw_setting_fisio')->insert([
                            'suhu_tubuh' => $val,
                            'keterangan' => 'Kunjungan ' . $val,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
            }
        } catch (\Throwable $e) {
            Log::error('SettingFisioService Save DB Error: ' . $e->getMessage());
            return false;
        }

        // 2. Simpan juga backup ke file JSON
        try {
            $dir = storage_path('app');
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            $data = [
                'suhu_tubuh' => $cleaned,
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];

            @file_put_contents(self::getPath(), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            // ignore
        }

        return true;
    }

    /**
     * Cek apakah suatu nilai suhu_tubuh termasuk ke dalam settingan fisio.
     * 
     * @param mixed $suhuTubuh
     * @return bool
     */
    public static function isFisio($suhuTubuh): bool
    {
        if ($suhuTubuh === null || $suhuTubuh === '') {
            return false;
        }

        $val = trim((string) $suhuTubuh);
        $allowed = self::getSuhuTubuhFisio();
        return in_array($val, $allowed, true);
    }
}
