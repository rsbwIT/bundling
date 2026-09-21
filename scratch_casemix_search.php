<?php
$f = 'c:/xampp/htdocs/bundling2/resources/views/layout/layoutDashboard.blade.php';
$lines = file($f);
foreach ($lines as $i => $line) {
    if (strpos($line, 'Casemix') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
