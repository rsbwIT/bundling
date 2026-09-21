<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$menus = DB::table('daftar_menu_bundling')->where('parent_id', 39)->get();
foreach($menus as $m) echo $m->nama_menu . "\n";
