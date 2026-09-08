<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

print_r(DB::select("SHOW COLUMNS FROM penyakit")); 
print_r(DB::select("SHOW COLUMNS FROM icd9"));
