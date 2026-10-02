<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['detail_piutang_pasien', 'piutang_pasien', 'bayar_piutang', 'detail_penagihan_piutang'];
foreach ($tables as $t) {
    try {
        $res = \DB::select("SELECT * FROM $t WHERE no_rawat = '2026/08/24/000678'");
        if (count($res) > 0) {
            echo "Found in $t!\n";
            print_r($res);
        }
    } catch (\Exception $e) {
    }
}
