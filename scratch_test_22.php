<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $res = \DB::select("SELECT * FROM billing WHERE no_rawat = '2026/08/24/000678'");
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
