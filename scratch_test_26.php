<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "SHOW COLUMNS FROM jns_perawatan";
try {
    $res = \DB::select($sql);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
