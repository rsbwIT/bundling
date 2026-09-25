<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$menu = DB::table('daftar_menu_bundling')->where('url', '/lab-pk')->first();
$akses = DB::table('user_akses_bundling')->where('menu_id', $menu->id)->get();

print_r($menu);
print_r($akses);
