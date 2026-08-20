<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// php batal_mjk.php KODEBOOKING "Keterangan"
$kodebooking = $argv[1] ?? null;
$keterangan  = $argv[2] ?? 'Batal ambil antrean';

// Jika tidak ada argumen, tampilkan daftar booking yang bisa dibatalkan
if (!$kodebooking) {
    echo "=== BOOKING YANG BISA DIBATALKAN (status: Belum/Checkin, tgl >= hari ini) ===\n";
    $rows = \Illuminate\Support\Facades\DB::table('referensi_mobilejkn_bpjs')
        ->where('tanggalperiksa', '>=', date('Y-m-d'))
        ->whereIn('status', ['Belum', 'Checkin'])
        ->orderBy('tanggalperiksa')
        ->limit(10)
        ->get(['nobooking', 'tanggalperiksa', 'status', 'statuskirim', 'no_rawat']);

    if ($rows->isEmpty()) {
        echo "Tidak ada booking yang bisa dibatalkan.\n";
    } else {
        foreach ($rows as $r) {
            echo "  php batal_mjk.php " . $r->nobooking . "  | Tgl: " . $r->tanggalperiksa . " | Status: " . $r->status . "\n";
        }
    }
    echo "\nUsage: php batal_mjk.php KODEBOOKING \"Keterangan\"\n";
    exit(0);
}

// Cek info booking
$booking = \Illuminate\Support\Facades\DB::table('referensi_mobilejkn_bpjs')
    ->where('nobooking', $kodebooking)
    ->first();

echo "=== INFO BOOKING ===\n";
if ($booking) {
    echo "No Booking   : " . $booking->nobooking . "\n";
    echo "Tgl Periksa  : " . $booking->tanggalperiksa . "\n";
    echo "Status       : " . ($booking->status ?? '-') . "\n";
    echo "Status Kirim : " . ($booking->statuskirim ?? '-') . "\n";
    echo "No Rawat     : " . ($booking->no_rawat ?? '-') . "\n";

    if ($booking->tanggalperiksa < date('Y-m-d')) {
        echo "\n[PERINGATAN] Tanggal periksa sudah LAMPAU (" . $booking->tanggalperiksa . ").\n";
        echo "             BPJS tidak mengizinkan pembatalan tanggal lampau.\n";
        exit(1);
    }
} else {
    echo "Booking tidak ditemukan di DB lokal.\n";
}

echo "\n=== PROSES BATAL ===\n";
echo "Kode Booking : $kodebooking\n";
echo "Keterangan   : $keterangan\n";

$ref = new \App\Services\Bpjs\ReferensiBPJS();

$data = json_encode([
    'kodebooking' => $kodebooking,
    'keterangan'  => $keterangan
]);

$result = $ref->batalAntranMJKN($data);

echo "\n=== HASIL ===\n";
echo $result . "\n";

$decoded = json_decode($result, true);
if (isset($decoded['metadata'])) {
    $code = $decoded['metadata']['code'] ?? '-';
    $msg  = $decoded['metadata']['message'] ?? '-';
    echo "\nCode    : $code\n";
    echo "Message : $msg\n";

    if ($code == 200) {
        echo "\n[SUKSES] Antrean berhasil dibatalkan!\n";
    } elseif ($code == 201) {
        echo "\n[GAGAL] Code 201 - Kemungkinan penyebab:\n";
        echo "  1. Antrean sudah CHECKIN (tidak bisa dibatalkan via API)\n";
        echo "  2. Tanggal periksa sudah lampau\n";
        echo "  3. Booking tidak ditemukan di sistem BPJS\n";
    }
}
