<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
SELECT 
    t.no_rawat,
    rp.no_rkm_medis,
    jp.nm_perawatan,
    t.biaya_rawat,
    t.kso,
    t.tgl_perawatan
FROM (
    SELECT r.no_rawat, r.kd_dokter, r.biaya_rawat, r.kso, r.tgl_perawatan, r.kd_jenis_prw 
    FROM rawat_jl_dr r 
    UNION ALL
    SELECT r.no_rawat, r.kd_dokter, r.biaya_rawat, r.kso, r.tgl_perawatan, r.kd_jenis_prw 
    FROM rawat_jl_drpr r 
    UNION ALL
    SELECT r.no_rawat, r.kd_dokter, r.biaya_rawat, r.kso, r.tgl_perawatan, r.kd_jenis_prw 
    FROM rawat_inap_dr r 
    UNION ALL
    SELECT r.no_rawat, r.kd_dokter, r.biaya_rawat, r.kso, r.tgl_perawatan, r.kd_jenis_prw 
    FROM rawat_inap_drpr r 
) as t
JOIN jns_perawatan jp ON t.kd_jenis_prw = jp.kd_jenis_prw
JOIN dokter d ON t.kd_dokter = d.kd_dokter
JOIN reg_periksa rp ON t.no_rawat = rp.no_rawat
WHERE (jp.nm_perawatan LIKE '%alat%' OR jp.nm_perawatan LIKE '%Alat%')
AND d.nm_dokter LIKE '%Marudut%'
AND t.tgl_perawatan >= '2026-06-01'
ORDER BY t.tgl_perawatan ASC
";

try {
    $res = \DB::select($sql);
    foreach($res as $r) {
        echo "{$r->tgl_perawatan} | {$r->no_rawat} | {$r->no_rkm_medis} | {$r->nm_perawatan} | Biaya: {$r->biaya_rawat} | KSO: {$r->kso}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
