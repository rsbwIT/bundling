<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
    SELECT 
        SUM(t.kso) as total_kso
    FROM (
        SELECT r.no_rawat, r.kd_dokter, r.kso, r.tgl_perawatan, jp.nm_perawatan 
        FROM rawat_jl_dr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso, r.tgl_perawatan, jp.nm_perawatan 
        FROM rawat_jl_drpr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso, r.tgl_perawatan, jpi.nm_perawatan 
        FROM rawat_inap_dr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso, r.tgl_perawatan, jpi.nm_perawatan 
        FROM rawat_inap_drpr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
    ) as t
    JOIN dokter d ON t.kd_dokter = d.kd_dokter
    JOIN reg_periksa rp ON t.no_rawat = rp.no_rawat
    JOIN penjab pj ON rp.kd_pj = pj.kd_pj
    LEFT JOIN (SELECT no_rawat, MAX(tgl_byr) as tgl_bayar FROM billing GROUP BY no_rawat) b ON t.no_rawat = b.no_rawat
    WHERE (t.nm_perawatan LIKE '%alat%' OR t.nm_perawatan LIKE '%Alat%')
    AND d.nm_dokter LIKE '%Nasrull%'
    AND pj.png_jawab LIKE '%BPJS%'
    AND b.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30'
";

try {
    $res = \DB::select($sql);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
