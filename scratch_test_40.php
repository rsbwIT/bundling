<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
    SELECT 
        SUM(
            CASE 
                WHEN d.nm_dokter LIKE '%Nasrul%' OR d.nm_dokter LIKE '%Boby%' OR d.nm_dokter LIKE '%Bobby%' OR d.nm_dokter LIKE '%Exsa%' THEN t.rvp_kso 
                WHEN t.selisih < 0 THEN t.master_kso
                ELSE t.rvp_kso
            END
        ) as total_biaya
    FROM (
        SELECT r.no_rawat, r.kd_dokter, r.kso as rvp_kso, jp.kso as master_kso, jp.nm_perawatan, (COALESCE(piu.uangmuka, 0) - COALESCE(piu.totalpiutang, 0)) as selisih 
        FROM rawat_jl_dr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso as rvp_kso, jp.kso as master_kso, jp.nm_perawatan, (COALESCE(piu.uangmuka, 0) - COALESCE(piu.totalpiutang, 0)) as selisih 
        FROM rawat_jl_drpr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso as rvp_kso, jpi.kso as master_kso, jpi.nm_perawatan, (COALESCE(piu.uangmuka, 0) - COALESCE(piu.totalpiutang, 0)) as selisih 
        FROM rawat_inap_dr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
        UNION ALL
        SELECT r.no_rawat, r.kd_dokter, r.kso as rvp_kso, jpi.kso as master_kso, jpi.nm_perawatan, (COALESCE(piu.uangmuka, 0) - COALESCE(piu.totalpiutang, 0)) as selisih 
        FROM rawat_inap_drpr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
    ) as t
    JOIN dokter d ON t.kd_dokter = d.kd_dokter
    JOIN reg_periksa rp ON t.no_rawat = rp.no_rawat
    JOIN penjab pj ON rp.kd_pj = pj.kd_pj
    LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON t.no_rawat = bp.no_rawat
    WHERE (t.nm_perawatan LIKE '%alat%' OR t.nm_perawatan LIKE '%Alat%')
    AND d.nm_dokter LIKE '%Exsa%'
    AND (pj.png_jawab LIKE '%BPJS%' OR pj.png_jawab LIKE '%COB%')
    AND bp.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30'
";

try {
    $res = \DB::select($sql);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
