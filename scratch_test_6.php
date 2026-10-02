<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql_opr = "
SELECT 
    d.nm_dokter,
    SUM(o.biayaalat) as total_biayaalat
FROM operasi o
JOIN paket_operasi po ON o.kode_paket = po.kode_paket
JOIN dokter d ON o.operator1 = d.kd_dokter
LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON o.no_rawat = bp.no_rawat
WHERE (po.nm_perawatan LIKE '%alat%' OR po.nm_perawatan LIKE '%Alat%')
AND d.nm_dokter LIKE '%Marudut%'
AND bp.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30'
GROUP BY d.nm_dokter
";

try {
    $res = \DB::select($sql_opr);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

$sql_opr2 = "
SELECT 
    d.nm_dokter,
    SUM(o.biayaalat) as total_biayaalat
FROM operasi o
JOIN paket_operasi po ON o.kode_paket = po.kode_paket
JOIN dokter d ON o.operator1 = d.kd_dokter
JOIN reg_periksa rp ON o.no_rawat = rp.no_rawat
JOIN penjab pj ON rp.kd_pj = pj.kd_pj
WHERE (po.nm_perawatan LIKE '%alat%' OR po.nm_perawatan LIKE '%Alat%')
AND d.nm_dokter LIKE '%Marudut%'
AND (o.tgl_operasi BETWEEN '2026-09-01' AND '2026-09-30')
GROUP BY d.nm_dokter
";

try {
    $res = \DB::select($sql_opr2);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
