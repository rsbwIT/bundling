<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $data = \DB::table('nomor_surat_sdm as nsk')
            ->leftJoin('reg_periksa as rp', 'nsk.no_rawat', '=', 'rp.no_rawat')
            ->leftJoin('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->select(
                'nsk.id',
                'nsk.no_rawat',
                'nsk.no_surat',
                'nsk.jenis_surat',
                'nsk.tanggal',
                'p.nm_pasien',
                'd.nm_dokter'
            )
            ->get();
    echo "Success! Found " . count($data) . " records.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
