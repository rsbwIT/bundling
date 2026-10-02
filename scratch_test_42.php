<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $res = \DB::select("SHOW COLUMNS FROM rawat_jl_dr");
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
