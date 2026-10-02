<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tgl1 = '2026-09-01'; // Guessing the user's date range
$tgl2 = '2026-09-30';

$sql = "
    SELECT 
        d.nm_dokter,
        SUM(t.kso) as total_kso
    FROM (
        SELECT r.no_rawat, r.kd_dokter, r.kso, jp.nm_perawatan 
        FROM rawat_jl_dr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso, jp.nm_perawatan 
        FROM rawat_jl_drpr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso, jpi.nm_perawatan 
        FROM rawat_inap_dr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso, jpi.nm_perawatan 
        FROM rawat_inap_drpr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
    ) as t
    JOIN dokter d ON t.kd_dokter = d.kd_dokter
    JOIN reg_periksa rp ON t.no_rawat = rp.no_rawat
    JOIN penjab pj ON rp.kd_pj = pj.kd_pj
    LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON t.no_rawat = bp.no_rawat
    WHERE (t.nm_perawatan LIKE '%alat%' OR t.nm_perawatan LIKE '%Alat%')
    AND d.nm_dokter LIKE '%Nasrull%'
    AND pj.png_jawab LIKE '%BPJS%'
    AND bp.tgl_bayar BETWEEN ? AND ?
    GROUP BY d.nm_dokter
";

try {
    $res = \DB::select($sql, [$tgl1, $tgl2]);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
