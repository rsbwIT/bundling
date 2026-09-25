<?php
$start = microtime(true);
$str = 'Dikeluarkan di RS. BUMI WARAS, Kabupaten/Kota Bandar Lampung Ditandatangani secara elektronik oleh Nama Dokter ID 123 2026-09-23';
DNS2D::getBarcodePNG($str, 'QRCODE');
$end = microtime(true);
echo "Time QR: " . ($end - $start) . " seconds\n";
