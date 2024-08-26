<?php
// app/Services/CacheService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheService
{
    public function getPenjab()
    {
        $cacheKeyPenjab = 'cache_penjamin';
        if (Cache::has($cacheKeyPenjab)) {
            $penjab = Cache::get($cacheKeyPenjab);
        }else{
            $penjab = DB::table('penjab')
                ->select('penjab.kd_pj', 'penjab.png_jawab')
                ->where('penjab.status', '=', '1')
                ->orderBy('penjab.png_jawab','asc')
                ->get();
            Cache::put($cacheKeyPenjab, $penjab, 720);
        }
        return $penjab;
    }

    public function getPetugas()
    {
        $cacheKeyPetugas = 'cache_petugas';
        if (Cache::has($cacheKeyPetugas)) {
            $petugas = Cache::get($cacheKeyPetugas);
        }else{
            $petugas = DB::table('petugas')
                ->select('petugas.nip', 'petugas.nama')
                ->where('petugas.status', '=', '1')
                ->get();
            Cache::put($cacheKeyPetugas, $petugas, 720);
        }
        return $petugas;
    }

    public function getDokter()
    {
        $cacheKeyDokter = 'cache_dokter';
        if (Cache::has($cacheKeyDokter)) {
            $dokter = Cache::get($cacheKeyDokter);
        }else{
            $dokter = DB::table('dokter')
                ->select('dokter.kd_dokter', 'dokter.nm_dokter')
                ->where('dokter.status', '=', '1')
                ->get();
            Cache::put($cacheKeyDokter, $dokter, 720);
        }
        return $dokter;
    }

    public function getSetting() {
        $cache_settingRS = 'cache_settingRS';
        if (Cache::has($cache_settingRS)) {
            $getSeting = Cache::get($cache_settingRS);
        }else{
             $getSeting = DB::table('setting')
                ->select('setting.nama_instansi',
                    'setting.alamat_instansi',
                    'setting.kabupaten',
                    'setting.propinsi',
                    'setting.kontak',
                    'setting.email',
                    'setting.aktifkan',
                    'setting.kode_ppk',
                    'setting.kode_ppkinhealth',
                    'setting.kode_ppkkemenkes',
                    'setting.wallpaper',
                    'setting.logo')
                ->first();
            Cache::put($cache_settingRS, $getSeting, 720);
        }
        return $getSeting;
    }
}
