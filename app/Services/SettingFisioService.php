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
     * Simpan array nilai suhu_tubuh ke database (bw_setting_fisio), catat ke tracker log, dan file JSON backup.
     * 
     * @param array $values
     * @param string|null $keteranganCustom
     * @return bool
     */
    public static function setSuhuTubuhFisio(array $values, ?string $keteranganCustom = null): bool
    {
        $oldValues = self::getSuhuTubuhFisio();

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

        // 2. Catat ke Tracker Log (bw_setting_fisio_log dan bw_tracker_log_reg2)
        try {
            self::recordTrackerLog($oldValues, $cleaned, $keteranganCustom);
        } catch (\Throwable $e) {
            Log::warning('SettingFisioService Tracker Log Error: ' . $e->getMessage());
        }

        // 3. Simpan juga backup ke file JSON
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
     * Catat log aktivitas perubahan ke tabel database tracker
     */
    public static function recordTrackerLog(array $oldValues, array $newValues, ?string $keteranganCustom = null): void
    {
        $auth = session('auth');
        $userObj = session('user');

        $userId = null;
        $userName = null;

        // Ambil ID User / NIK
        if (is_array($auth) && isset($auth['id_user'])) {
            $userId = (string) $auth['id_user'];
        } elseif (is_object($auth) && isset($auth->id_user)) {
            $userId = (string) $auth->id_user;
        } elseif (is_object($userObj)) {
            $userId = (string) ($userObj->nik ?? ($userObj->id_user ?? null));
        } elseif (is_array($userObj)) {
            $userId = (string) ($userObj['nik'] ?? ($userObj['id_user'] ?? null));
        }

        // Ambil Nama User
        if (is_object($userObj) && !empty($userObj->nama)) {
            $userName = (string) $userObj->nama;
        } elseif (is_array($userObj) && !empty($userObj['nama'])) {
            $userName = (string) $userObj['nama'];
        } elseif (is_array($auth) && !empty($auth['nama'])) {
            $userName = (string) $auth['nama'];
        }

        // Cari ke database pegawai jika nama belum didapat
        if (!$userName && $userId) {
            try {
                $peg = DB::table('pegawai')->select('nama')->where('nik', $userId)->first();
                if ($peg) {
                    $userName = $peg->nama;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $userId = $userId ?: '01091999';
        $userName = $userName ?: $userId;

        $oldStr = !empty($oldValues) ? '[' . implode(', ', $oldValues) . ']' : '[Kosong]';
        $newStr = !empty($newValues) ? '[' . implode(', ', $newValues) . ']' : '[Kosong]';
        
        $keterangan = $keteranganCustom ?: "Mengubah nilai kunjungan fisio dari {$oldStr} menjadi {$newStr}";
        $ip = request()->ip() ?? '127.0.0.1';

        // 1. Simpan ke tabel tracker khusus bw_setting_fisio_log
        if (Schema::hasTable('bw_setting_fisio_log')) {
            DB::table('bw_setting_fisio_log')->insert([
                'id_user' => $userId,
                'nama_user' => $userName,
                'aksi' => 'UPDATE_SETTING',
                'nilai_lama' => json_encode($oldValues),
                'nilai_baru' => json_encode($newValues),
                'keterangan' => $keterangan,
                'ip_address' => $ip,
                'created_at' => now(),
            ]);
        }

        // 2. Simpan juga ke bw_tracker_log_reg2 agar sinkron dengan log global
        if (Schema::hasTable('bw_tracker_log_reg2')) {
            DB::table('bw_tracker_log_reg2')->insert([
                'id_user' => $userId,
                'nama_user' => $userName,
                'tanggal' => now(),
                'status' => 'UPDATE_SETTING_FISIO',
                'keterangan' => $keterangan,
            ]);
        }
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
