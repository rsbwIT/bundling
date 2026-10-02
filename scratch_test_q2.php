<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
SELECT 
    d.nm_dokter,
    pj.png_jawab,
    t.no_rawat,
    nj.tanggal as tgl_nj,
    ni.tanggal as tgl_ni,
    bp.tgl_bayar
FROM (
    SELECT r.no_rawat, r.kd_jenis_prw, r.kd_dokter, r.biaya_rawat, r.tgl_perawatan FROM rawat_jl_dr r 
    UNION ALL
    SELECT r.no_rawat, r.kd_jenis_prw, r.kd_dokter, r.biaya_rawat, r.tgl_perawatan FROM rawat_jl_drpr r 
    UNION ALL
    SELECT r.no_rawat, r.kd_jenis_prw, r.kd_dokter, r.biaya_rawat, r.tgl_perawatan FROM rawat_inap_dr r 
    UNION ALL
    SELECT r.no_rawat, r.kd_jenis_prw, r.kd_dokter, r.biaya_rawat, r.tgl_perawatan FROM rawat_inap_drpr r 
) as t
JOIN jns_perawatan jp ON t.kd_jenis_prw = jp.kd_jenis_prw
JOIN dokter d ON t.kd_dokter = d.kd_dokter
JOIN reg_periksa rp ON t.no_rawat = rp.no_rawat
JOIN penjab pj ON rp.kd_pj = pj.kd_pj
LEFT JOIN nota_jalan nj ON t.no_rawat = nj.no_rawat
LEFT JOIN nota_inap ni ON t.no_rawat = ni.no_rawat
LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON t.no_rawat = bp.no_rawat
WHERE (jp.nm_perawatan LIKE '%alat%' OR jp.nm_perawatan LIKE '%Alat%')
AND pj.png_jawab LIKE '%UMUM%'
AND (t.tgl_perawatan BETWEEN '2026-08-01' AND '2026-09-30' OR COALESCE(ni.tanggal, nj.tanggal) BETWEEN '2026-08-01' AND '2026-09-30')
LIMIT 10
";

try {
    $res = \DB::select($sql);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
