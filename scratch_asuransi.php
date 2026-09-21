<?php
$f = 'c:/xampp/htdocs/bundling2/resources/views/layout/layoutDashboard.blade.php';
$c = file_get_contents($f);
$start = strpos($c, 'Detail Tindakan <span class="text-xs">(Asuransi)</span>');
if ($start !== false) {
    // Find the enclosing <li class="nav-item"> parent
    $li_start = strrpos(substr($c, 0, $start), '<li class="nav-item">');
    // Find the closing </li> for the parent (might be hard due to nesting)
    $ul_end = strpos($c, '</ul>', $start);
    echo substr($c, max(0, $li_start - 100), $ul_end - $li_start + 200);
} else {
    echo "Not found";
}
