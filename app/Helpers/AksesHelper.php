<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class AksesHelper
{
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
            \Illuminate\Support\Facades\Log::info("AksesHelper: SUPER ADMIN BYPASS for " . $url);
            return true;
        }

        // Jika url kosong (seperti label header parent), loloskan sementara
        if (empty($url) || $url == '#') {
            return true;
        }

        // Cari menu di database daftar_menu_bundling
        // Jika url berbentuk absolute (misal dari fungsi route()), ambil path-nya saja
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($url);
            $url = $parsed['path'] ?? $url;
        }

        // Bersihkan url dari prefix/slash berlebih untuk mempermudah LIKE
        $cleanUrl = ltrim($url, '/');
        
        $menu = DB::table('daftar_menu_bundling')
            ->where('url', 'LIKE', '%' . $cleanUrl . '%')
            ->first();

        // Jika menu belum terdaftar di database, kita loloskan agar tidak menghilangkan fitur
        if (!$menu) {
            \Illuminate\Support\Facades\Log::info("AksesHelper: Menu not found for url: " . $url . " (cleanUrl: " . $cleanUrl . ")");
            return true;
        }

        // Cek hak akses di tabel user_akses_bundling
        $akses = DB::table('user_akses_bundling')
            ->where('username', $nik)
            ->where('menu_id', $menu->id)
            ->first();

        if ($akses && $akses->status == 'true') {
            \Illuminate\Support\Facades\Log::info("AksesHelper: Access GRANTED for " . $nik . " on " . $url);
            return true;
        }

        \Illuminate\Support\Facades\Log::info("AksesHelper: Access DENIED for " . $nik . " on " . $url);
        return false;
    }
}
