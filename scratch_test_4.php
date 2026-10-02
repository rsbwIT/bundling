<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
SELECT 
    d.nm_dokter,
    SUM(t.biaya_rawat) as total_biaya,
    SUM(t.kso) as kso
FROM (
    SELECT r.no_rawat, r.kd_jenis_prw, r.kd_dokter, r.biaya_rawat, r.kso, jp.nm_perawatan FROM rawat_jl_dr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
    UNION ALL
    SELECT r.no_rawat, r.kd_jenis_prw, r.kd_dokter, r.biaya_rawat, r.kso, jp.nm_perawatan FROM rawat_jl_drpr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
    UNION ALL
    SELECT r.no_rawat, r.kd_jenis_prw, r.kd_dokter, r.biaya_rawat, r.kso, jp.nm_perawatan FROM rawat_inap_dr r JOIN jns_perawatan_inap jp ON r.kd_jenis_prw = jp.kd_jenis_prw
    UNION ALL
    SELECT r.no_rawat, r.kd_jenis_prw, r.kd_dokter, r.biaya_rawat, r.kso, jp.nm_perawatan FROM rawat_inap_drpr r JOIN jns_perawatan_inap jp ON r.kd_jenis_prw = jp.kd_jenis_prw
) as t
JOIN dokter d ON t.kd_dokter = d.kd_dokter
JOIN reg_periksa rp ON t.no_rawat = rp.no_rawat
JOIN penjab pj ON rp.kd_pj = pj.kd_pj
LEFT JOIN nota_jalan nj ON t.no_rawat = nj.no_rawat
LEFT JOIN nota_inap ni ON t.no_rawat = ni.no_rawat
LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON t.no_rawat = bp.no_rawat
WHERE (t.nm_perawatan LIKE '%alat%' OR t.nm_perawatan LIKE '%Alat%')
AND d.nm_dokter LIKE '%Exsa%'
AND bp.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30'
GROUP BY d.nm_dokter
";

try {
    $res = \DB::select($sql);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

$sql_opr = "
SELECT 
    d.nm_dokter,
    SUM(o.biayaalat) as total_biayaalat
FROM operasi o
JOIN paket_operasi po ON o.kode_paket = po.kode_paket
JOIN dokter d ON o.operator1 = d.kd_dokter
LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON o.no_rawat = bp.no_rawat
WHERE (po.nm_perawatan LIKE '%alat%' OR po.nm_perawatan LIKE '%Alat%')
AND d.nm_dokter LIKE '%Exsa%'
AND bp.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30'
GROUP BY d.nm_dokter
";

try {
    $res = \DB::select($sql_opr);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
