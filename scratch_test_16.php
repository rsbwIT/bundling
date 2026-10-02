<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
    SELECT * FROM bayar_piutang WHERE no_rawat = '2026/08/24/000678'
";

try {
    $res = \DB::select($sql);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

$sql2 = "
    SELECT r.no_rawat, r.kso, jp.nm_perawatan FROM rawat_jl_dr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw WHERE r.no_rawat = '2026/08/24/000678'
    UNION ALL
    SELECT r.no_rawat, r.kso, jpi.nm_perawatan FROM rawat_inap_dr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw WHERE r.no_rawat = '2026/08/24/000678'
";

try {
    $res = \DB::select($sql2);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

