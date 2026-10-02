<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = "
    SELECT 
        r.no_rawat,
        r.kso as rvp_kso, 
        jpi.kso as master_kso,
        piu.totalpiutang,
        piu.uangmuka,
        (piu.uangmuka - piu.totalpiutang) as selisih,
        jpi.nm_perawatan
    FROM rawat_inap_dr r 
    JOIN jns_perawatan_inap jpi ON r.kd_jenis_prw = jpi.kd_jenis_prw
    JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
    JOIN penjab pj ON rp.kd_pj = pj.kd_pj
    JOIN dokter d ON r.kd_dokter = d.kd_dokter
    LEFT JOIN (SELECT no_rawat, MAX(tgl_bayar) as tgl_bayar FROM bayar_piutang GROUP BY no_rawat) bp ON r.no_rawat = bp.no_rawat
    LEFT JOIN piutang_pasien piu ON r.no_rawat = piu.no_rawat
    WHERE (jpi.nm_perawatan LIKE '%alat%' OR jpi.nm_perawatan LIKE '%Alat%')
    AND d.nm_dokter LIKE '%Exsa%'
    AND pj.png_jawab LIKE '%BPJS%'
    AND bp.tgl_bayar BETWEEN '2026-09-01' AND '2026-09-30'
";

try {
    $res = \DB::select($sql);
    $total_new_logic = 0;
    foreach($res as $r) {
        $selisih = $r->uangmuka - $r->totalpiutang;
        $kso_dipilih = ($selisih < 0) ? $r->master_kso : $r->rvp_kso;
        $total_new_logic += $kso_dipilih;
    }
    
    echo "Total if using SELISIH MINES logic for Rawat Inap BPJS: " . number_format($total_new_logic, 0, ',', '.') . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
