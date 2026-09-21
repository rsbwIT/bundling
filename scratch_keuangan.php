<?php
$f = 'c:/xampp/htdocs/bundling2/resources/views/layout/layoutDashboard.blade.php';
$c = file_get_contents($f);
$start = strpos($c, 'Keuangan');
if ($start !== false) {
    $end = strpos($c, '</ul>', $start);
    echo substr($c, max(0, $start - 200), $end - $start + 200);
} else {
    echo "Keuangan not found";
}
