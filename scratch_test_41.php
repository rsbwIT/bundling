<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
    SELECT 
        SUM(IF(COALESCE(piu.uangmuka, 0) - COALESCE(piu.totalpiutang, 0) < 0, jp.kso, r.kso)) as total_operasi_kso
    FROM operasi o
    JOIN reg_periksa rp ON o.no_rawat = rp.no_rawat
    JOIN penjab pj ON rp.kd_pj = pj.kd_pj
    JOIN dokter d ON (d.kd_dokter = o.operator1 OR d.kd_dokter = o.operator2 OR d.kd_dokter = o.operator3)
    LEFT JOIN rawat_inap_dr r ON r.no_rawat = o.no_rawat
    LEFT JOIN jns_perawatan_inap jp ON jp.kd_jenis_prw = r.kd_jenis_prw
    LEFT JOIN piutang_pasien piu ON o.no_rawat = piu.no_rawat
    LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON o.no_rawat = bp.no_rawat
    WHERE d.nm_dokter LIKE '%Exsa%'
    AND (pj.png_jawab LIKE '%BPJS%' OR pj.png_jawab LIKE '%COB%')
    AND bp.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30'
";

try {
    $res = \DB::select($sql);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
