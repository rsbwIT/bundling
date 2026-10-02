<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
    SELECT 
        t.nm_perawatan,
        SUM(t.master_kso) as total_master,
        SUM(t.rvp_kso) as total_rvp,
        COUNT(*) as jml
    FROM (
        SELECT r.no_rawat, r.kd_dokter, r.kso as rvp_kso, jp.kso as master_kso, jp.nm_perawatan 
        FROM rawat_jl_dr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso as rvp_kso, jp.kso as master_kso, jp.nm_perawatan 
        FROM rawat_jl_drpr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso as rvp_kso, jpi.kso as master_kso, jpi.nm_perawatan 
        FROM rawat_inap_dr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso as rvp_kso, jpi.kso as master_kso, jpi.nm_perawatan 
        FROM rawat_inap_drpr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
    ) as t
    JOIN dokter d ON t.kd_dokter = d.kd_dokter
    JOIN reg_periksa rp ON t.no_rawat = rp.no_rawat
    JOIN penjab pj ON rp.kd_pj = pj.kd_pj
    LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON t.no_rawat = bp.no_rawat
    WHERE (t.nm_perawatan LIKE '%alat%' OR t.nm_perawatan LIKE '%Alat%')
    AND d.nm_dokter LIKE '%Exsa%'
    AND pj.png_jawab LIKE '%BPJS%'
    AND bp.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30'
    GROUP BY t.nm_perawatan
    ORDER BY total_master DESC
";

try {
    $res = \DB::select($sql);
    $tot_master = 0;
    $tot_rvp = 0;
    foreach($res as $r) {
        $tot_master += $r->total_master;
        $tot_rvp += $r->total_rvp;
        echo "{$r->jml}x | {$r->nm_perawatan} | Master: " . number_format($r->total_master, 0, ',', '.') . " | RVP: " . number_format($r->total_rvp, 0, ',', '.') . "\n";
    }
    echo "========================================\n";
    echo "TOTAL MASTER: " . number_format($tot_master, 0, ',', '.') . "\n";
    echo "TOTAL RVP: " . number_format($tot_rvp, 0, ',', '.') . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
