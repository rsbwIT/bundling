<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

print_r(DB::select("SHOW COLUMNS FROM diagnosa_pasien"));
print_r(DB::select("SHOW COLUMNS FROM prosedur_pasien"));
