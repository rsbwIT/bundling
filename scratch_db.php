<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$penjab = \DB::select('SELECT kd_pj, png_jawab FROM penjab');
echo "PENJAB:\n";
print_r($penjab);

$bayar_piutang_cols = \DB::select('SHOW COLUMNS FROM bayar_piutang');
echo "\nbayar_piutang COLUMNS:\n";
print_r($bayar_piutang_cols);

$reg_periksa_cols = \DB::select('SHOW COLUMNS FROM reg_periksa');
echo "\nreg_periksa COLUMNS:\n";
print_r($reg_periksa_cols);
