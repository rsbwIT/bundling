<?php
$lines = file('c:\xampp\htdocs\bundling2\resources\views\layout\layoutDashboard.blade.php');
foreach($lines as $i => $line) {
    if(strpos($line, 'nav-header') !== false && strpos($line, 'Transaksi') !== false) {
        echo "Transaksi starts at line: " . ($i+1) . "\n";
    }
    if(strpos($line, 'nav-header') !== false && strpos($line, 'Gabung Berkas - Tools') !== false) {
        echo "Gabung Berkas starts at line: " . ($i+1) . "\n";
    }
}
