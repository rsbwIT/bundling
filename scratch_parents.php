<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$menus = DB::table('daftar_menu_bundling')
    ->whereNotNull('parent_id')
    ->select('parent_id')
    ->distinct()
    ->get();
    
foreach($menus as $m) { 
    $parent = DB::table('menu_bundling')->where('id', $m->parent_id)->first(); 
    if($parent) echo $parent->id . ' - ' . $parent->nama_menu . "\n"; 
}
