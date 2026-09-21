<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

DB::table('daftar_menu_bundling')->whereIn('url', ['/', '/monitoring-performa'])->delete();
echo 'Deleted from DB';
