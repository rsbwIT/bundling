<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = \Illuminate\Support\Facades\DB::table('referensi_mobilejkn_bpjs')
    ->where('tanggalperiksa', '>=', date('Y-m-d'))
    ->get(['nobooking', 'no_rawat']);
    
foreach($rows as $row) {
    echo "nobooking: " . $row->nobooking . " | no_rawat: " . $row->no_rawat . "\n";
}
