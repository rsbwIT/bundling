<?php
$f = 'c:/xampp/htdocs/bundling2/resources/views/layout/layoutDashboard.blade.php';
$lines = file($f);
foreach ($lines as $i => $line) {
    if (strpos($line, 'Transaksi') !== false || strpos($line, 'Detail Tindakan') !== false || strpos($line, 'Keuangan') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
