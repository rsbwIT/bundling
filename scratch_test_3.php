<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
SELECT nm_perawatan, 'jns_perawatan' as tbl FROM jns_perawatan WHERE nm_perawatan LIKE '%Ginjal%'
UNION ALL
SELECT nm_perawatan, 'jns_perawatan_inap' as tbl FROM jns_perawatan_inap WHERE nm_perawatan LIKE '%Ginjal%'
UNION ALL
SELECT nm_perawatan, 'paket_operasi' as tbl FROM paket_operasi WHERE nm_perawatan LIKE '%Ginjal%'
";

try {
    $res = \DB::select($sql);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
