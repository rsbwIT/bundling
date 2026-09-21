<?php
$f = 'c:/xampp/htdocs/bundling2/scratch_wrap_transaksi_header.blade.php';
$lines = file($f);
foreach ($lines as $i => $line) {
    if (strpos($line, 'Transaksi') !== false || strpos($line, 'cekAny') !== false && strpos($line, 'laporan-pengeluaran-harian') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
