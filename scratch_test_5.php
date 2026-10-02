<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
SELECT 
    d.nm_dokter,
    pj.png_jawab,
    jp.nm_perawatan,
    t.biaya_rawat,
    t.material,
    t.bhp,
    t.tarif_tindakandr,
    t.kso,
    t.tgl_perawatan
FROM (
    SELECT r.no_rawat, r.kd_dokter, r.biaya_rawat, r.material, r.bhp, r.tarif_tindakandr, r.kso, r.tgl_perawatan, jp.nm_perawatan, r.kd_jenis_prw 
    FROM rawat_jl_dr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
    UNION ALL
    SELECT r.no_rawat, r.kd_dokter, r.biaya_rawat, r.material, r.bhp, r.tarif_tindakandr, r.kso, r.tgl_perawatan, jp.nm_perawatan, r.kd_jenis_prw 
    FROM rawat_jl_drpr r JOIN jns_perawatan jp ON r.kd_jenis_prw = jp.kd_jenis_prw
    UNION ALL
    SELECT r.no_rawat, r.kd_dokter, r.biaya_rawat, r.material, r.bhp, r.tarif_tindakandr, r.kso, r.tgl_perawatan, jpi.nm_perawatan, r.kd_jenis_prw 
    FROM rawat_inap_dr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
    UNION ALL
    SELECT r.no_rawat, r.kd_dokter, r.biaya_rawat, r.material, r.bhp, r.tarif_tindakandr, r.kso, r.tgl_perawatan, jpi.nm_perawatan, r.kd_jenis_prw 
    FROM rawat_inap_drpr r JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
) as t
JOIN jns_perawatan jp ON t.kd_jenis_prw = jp.kd_jenis_prw
JOIN dokter d ON t.kd_dokter = d.kd_dokter
JOIN reg_periksa rp ON t.no_rawat = rp.no_rawat
JOIN penjab pj ON rp.kd_pj = pj.kd_pj
LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON t.no_rawat = bp.no_rawat
WHERE (t.nm_perawatan LIKE '%alat%' OR t.nm_perawatan LIKE '%Alat%')
AND d.nm_dokter LIKE '%Marudut%'
AND (bp.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30' OR t.tgl_perawatan BETWEEN '2026-09-01' AND '2026-09-30')
";

try {
    $res = \DB::select($sql);
    $total_biaya_rawat = 0;
    $total_kso = 0;
    $total_material = 0;
    $total_jmdr = 0;
    foreach($res as $r) {
        $total_biaya_rawat += $r->biaya_rawat;
        $total_kso += $r->kso;
        $total_material += $r->material;
        $total_jmdr += $r->tarif_tindakandr;
        // echo "{$r->tgl_perawatan} | {$r->nm_perawatan} | Biaya: {$r->biaya_rawat} | Mat: {$r->material} | BHP: {$r->bhp} | JM: {$r->tarif_tindakandr} | KSO: {$r->kso}\n";
    }
    echo "Total Biaya Rawat: " . $total_biaya_rawat . "\n";
    echo "Total KSO: " . $total_kso . "\n";
    echo "Total Material: " . $total_material . "\n";
    echo "Total JM DR: " . $total_jmdr . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
