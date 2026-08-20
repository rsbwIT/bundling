<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$today = date('Y-m-d');

echo "=== STATUS SEMUA BOOKING (diurutkan terbaru) ===\n";
$rows = \Illuminate\Support\Facades\DB::table('referensi_mobilejkn_bpjs')
    ->orderByDesc('tanggalperiksa')
    ->limit(20)
    ->get(['nobooking', 'tanggalperiksa', 'status', 'statuskirim']);

$groups = ['Belum' => [], 'Checkin' => [], 'Batal' => [], 'Gagal' => [], 'lampau' => []];

foreach ($rows as $r) {
    $lampau = $r->tanggalperiksa < $today ? ' [LAMPAU]' : '';
    $line = sprintf("  %-20s | Tgl: %s | status=%-8s | kirim=%s%s\n",
        $r->nobooking, $r->tanggalperiksa, $r->status, $r->statuskirim, $lampau
    );
    echo $line;
}

echo "\n=== YANG MUNGKIN BISA DIBATALKAN (tgl >= hari ini, status Belum) ===\n";
$bisa = \Illuminate\Support\Facades\DB::table('referensi_mobilejkn_bpjs')
    ->where('tanggalperiksa', '>=', $today)
    ->where('status', 'Belum')
    ->orderBy('tanggalperiksa')
    ->limit(5)
    ->get(['nobooking', 'tanggalperiksa', 'status']);

if ($bisa->isEmpty()) {
    echo "  Tidak ada booking yang memenuhi syarat.\n";
} else {
    foreach ($bisa as $r) {
        echo "  => php batal_mjk.php " . $r->nobooking . "\n";
    }
}
