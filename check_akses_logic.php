<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helpers\AksesHelper;

$nik = '21903861';

$userAkses = \DB::table('user_akses_bundling')
                ->join('daftar_menu_bundling', 'user_akses_bundling.menu_id', '=', 'daftar_menu_bundling.id')
                ->where('user_akses_bundling.username', $nik)
                ->where('user_akses_bundling.status', 'true')
                ->pluck('daftar_menu_bundling.url')
                ->toArray();

echo "User Akses for 21903861:\n";
print_r($userAkses);

$nik = 'D0000004';

$userAkses2 = \DB::table('user_akses_bundling')
                ->join('daftar_menu_bundling', 'user_akses_bundling.menu_id', '=', 'daftar_menu_bundling.id')
                ->where('user_akses_bundling.username', $nik)
                ->where('user_akses_bundling.status', 'true')
                ->pluck('daftar_menu_bundling.url')
                ->toArray();

echo "\nUser Akses for D0000004:\n";
print_r($userAkses2);

