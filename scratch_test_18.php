<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = \DB::select('SHOW TABLES');
$found = [];
foreach ($tables as $t) {
    $tbl = array_values((array)$t)[0];
    if (strpos($tbl, 'piutang') !== false || strpos($tbl, 'bayar') !== false) {
        $found[] = $tbl;
    }
}
print_r($found);

