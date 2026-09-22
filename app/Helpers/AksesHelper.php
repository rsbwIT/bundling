<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class AksesHelper
{
    private static $semuaMenu = null;
    private static $userAkses = null;

    public static function cekAny(array $urls)
    {
        foreach ($urls as $url) {
            if (self::cek($url)) {
                return true;
            }
        }
        return false;
    }

    public static function cek($url)
    {
        $user = session('auth');
        if (!$user) {
            return false;
        }

        $nik = $user['id_user'];
        
        // Admin utama selalu memiliki akses
        if ($nik == '01091999') {
            return true;
        }

        // Jika url kosong (seperti label header parent), loloskan sementara
        if (empty($url) || $url == '#') {
            return true;
        }

        // Jika url berbentuk absolute (misal dari fungsi route()), ambil path-nya saja
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($url);
            $url = $parsed['path'] ?? $url;
        }

        // Bersihkan url dari prefix/slash berlebih
        $cleanUrl = ltrim($url, '/');
        
        // --- OPTIMASI: Load data dari database HANYA SEKALI per request (menghindari ratusan query berulang) ---
        if (self::$semuaMenu === null) {
            self::$semuaMenu = DB::table('daftar_menu_bundling')->pluck('url')->toArray();
        }
        
        if (self::$userAkses === null) {
            self::$userAkses = DB::table('user_akses_bundling')
                ->join('daftar_menu_bundling', 'user_akses_bundling.menu_id', '=', 'daftar_menu_bundling.id')
                ->where('user_akses_bundling.username', $nik)
                ->where('user_akses_bundling.status', 'true')
                ->pluck('daftar_menu_bundling.url')
                ->toArray();
        }

        // Cari menu di database daftar_menu_bundling (mirip dengan LIKE %cleanUrl%)
        $isMenuTerdaftar = false;
        foreach (self::$semuaMenu as $dbUrl) {
            if (stripos($dbUrl ?? '', $cleanUrl) !== false) {
                $isMenuTerdaftar = true;
                break;
            }
        }

        // Jika menu belum terdaftar di database, kita loloskan
        if (!$isMenuTerdaftar) {
            return true;
        }

        // Cek hak akses dari data userAkses
        foreach (self::$userAkses as $aksesUrl) {
            if (stripos($aksesUrl ?? '', $cleanUrl) !== false) {
                return true;
            }
        }

        return false;
    }
}
