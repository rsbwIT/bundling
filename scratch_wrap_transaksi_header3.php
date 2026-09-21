<?php
$f = 'c:/xampp/htdocs/bundling2/resources/views/layout/layoutDashboard.blade.php';
$content = file_get_contents($f);

$start_marker = '<li class="nav-header">Transaksi</li>';
$end_marker = '<li class="nav-header">Gabung Berkas - Tools</li>';

$start_pos = strpos($content, $start_marker);
$end_pos = strpos($content, $end_marker, $start_pos);

if ($start_pos === false || $end_pos === false) {
    die("Markers not found");
}

$block = substr($content, $start_pos, $end_pos - $start_pos);

$urls = [];
if (preg_match_all('/href="\{\{\s*(url|route)\((.*?)\)\s*\}\}"/', $block, $matches)) {
    foreach ($matches[2] as $match) {
        $urls[] = trim($match, "\"'");
    }
}

$urls = array_unique($urls);
$url_str = implode("', '", $urls);

$pattern = '/<li class="nav-header user-panel"><\/li>\s*<li class="nav-header">Transaksi<\/li>/s';
$replacement = "@if(\\App\\Helpers\\AksesHelper::cekAny(['" . $url_str . "']))\n                        <li class=\"nav-header user-panel\"></li>\n                        <li class=\"nav-header\">Transaksi</li>\n                        @endif";

$content = preg_replace($pattern, $replacement, $content);
file_put_contents('c:/xampp/htdocs/bundling2/scratch_wrap_transaksi_header3.blade.php', $content);
echo "Done replacing.";
