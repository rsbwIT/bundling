<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
    SELECT 
        r.no_rawat,
        r.kso as rvp_kso, 
        jp.kso as master_kso,
        piu.totalpiutang,
        piu.uangmuka,
        jp.nm_perawatan,
        pj.png_jawab,
        bp.tgl_bayar
    FROM rawat_jl_dr r 
    JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
    JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
    JOIN penjab pj ON rp.kd_pj = pj.kd_pj
    JOIN dokter d ON r.kd_dokter = d.kd_dokter
    LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON r.no_rawat = bp.no_rawat
    LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
    WHERE (jp.nm_perawatan LIKE '%alat%' OR jp.nm_perawatan LIKE '%Alat%')
    AND d.nm_dokter LIKE '%Exsa%'
    AND pj.png_jawab NOT LIKE '%BPJS%'
    AND pj.png_jawab NOT LIKE '%UMUM%'
    AND pj.png_jawab NOT LIKE '%INHEALTH%'
    AND bp.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30'
    UNION ALL
    SELECT 
        r.no_rawat,
        r.kso as rvp_kso, 
        jpi.kso as master_kso,
        piu.totalpiutang,
        piu.uangmuka,
        jpi.nm_perawatan,
        pj.png_jawab,
        bp.tgl_bayar
    FROM rawat_inap_dr r 
    JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
    JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
    JOIN penjab pj ON rp.kd_pj = pj.kd_pj
    JOIN dokter d ON r.kd_dokter = d.kd_dokter
    LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON r.no_rawat = bp.no_rawat
    LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
    WHERE (jpi.nm_perawatan LIKE '%alat%' OR jpi.nm_perawatan LIKE '%Alat%')
    AND d.nm_dokter LIKE '%Exsa%'
    AND pj.png_jawab NOT LIKE '%BPJS%'
    AND pj.png_jawab NOT LIKE '%UMUM%'
    AND pj.png_jawab NOT LIKE '%INHEALTH%'
    AND bp.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30'
";

try {
    $res = \DB::select($sql);
    print_r($res);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
