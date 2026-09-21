<?php
$f = 'c:/xampp/htdocs/bundling2/resources/views/layout/layoutDashboard.blade.php';
$content = file_get_contents($f);

$start_marker = '<li class="nav-header">Transaksi</li>';
$end_marker = '<li class="nav-header">Gabung Berkas - Tools</li>'; // this is the next header

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

// also include the urls from the checkAny we just added if any are missing, but the regex above will catch all the actual links inside this block.
$urls = array_unique($urls);
$url_str = implode("', '", $urls);

// Now wrap the header. 
// Note that before the header there is `<li class="nav-header user-panel"></li>`. We should probably wrap that too.
$to_replace = '<li class="nav-header user-panel"></li>
                        <li class="nav-header">Transaksi</li>';

$replacement = "@if(\\App\\Helpers\\AksesHelper::cekAny(['" . $url_str . "']))\n                        " . $to_replace;

// we must also close the if right after it? NO! The user wants the HEADER to be hidden. We just wrap the header itself, or the entire block?
// If we wrap the entire block, we only have one `@if` and one `@endif`. But we already added `@if` to each parent. It's safer to just wrap the header.
$replacement .= "\n                        @endif";

$content = str_replace($to_replace, $replacement, $content);
file_put_contents('c:/xampp/htdocs/bundling2/scratch_wrap_transaksi_header.blade.php', $content);
echo "Done";
