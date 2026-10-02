<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
    SELECT 
        pj.png_jawab,
        SUM(jp.kso) as master_kso,
        SUM(r.kso) as rvp_kso
    FROM rawat_jl_dr r 
    JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
    JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
    JOIN penjab pj ON rp.kd_pj = pj.kd_pj
    JOIN dokter d ON r.kd_dokter = d.kd_dokter
    WHERE (jp.nm_perawatan LIKE '%alat%' OR jp.nm_perawatan LIKE '%Alat%')
    AND d.nm_dokter LIKE '%Exsa%'
    AND r.tgl_perawatan BETWEEN '2026-09-01' AND '2026-09-30'
    GROUP BY pj.png_jawab
";

try {
    $res = \DB::select($sql);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

$sql2 = "
    SELECT 
        pj.png_jawab,
        SUM(jpi.kso) as master_kso,
        SUM(r.kso) as rvp_kso
    FROM rawat_inap_dr r 
    JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
    JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
    JOIN penjab pj ON rp.kd_pj = pj.kd_pj
    JOIN dokter d ON r.kd_dokter = d.kd_dokter
    WHERE (jpi.nm_perawatan LIKE '%alat%' OR jpi.nm_perawatan LIKE '%Alat%')
    AND d.nm_dokter LIKE '%Exsa%'
    AND r.tgl_perawatan BETWEEN '2026-09-01' AND '2026-09-30'
    GROUP BY pj.png_jawab
";

try {
    $res = \DB::select($sql2);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
