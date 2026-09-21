<?php
$f = 'c:/xampp/htdocs/bundling2/resources/views/layout/layoutDashboard.blade.php';
$content = file_get_contents($f);

// We want to wrap the parent <li class="nav-item"> that are dropdown menus.
// ONLY within the "Transaksi" block!

$start_marker = '<li class="nav-header">Transaksi</li>';
$end_marker = '{{-- MENU FARMASI --}}';

$start_pos = strpos($content, $start_marker);
$end_pos = strpos($content, $end_marker, $start_pos);

if ($start_pos === false || $end_pos === false) {
    die("Markers not found");
}

$before = substr($content, 0, $start_pos);
$target_block = substr($content, $start_pos, $end_pos - $start_pos);
$after = substr($content, $end_pos);

$lines = explode("\n", $target_block);
$out = [];
$in_parent_li = false;
$parent_urls = [];
$parent_li_depth = 0;
$buffer = [];

foreach ($lines as $i => $line) {
    if (!$in_parent_li) {
        // Look for the start of a parent li
        // Usually it's: <li class="nav-item"> (with some indentation) followed by <a href="#" class="nav-link">
        if (preg_match('/^\s*<li class="nav-item">\s*(?:\{\{--.*?--\}\})?\s*$/', $line)) {
            // Check next line to see if it's a dropdown link
            $nextLine = isset($lines[$i+1]) ? $lines[$i+1] : '';
            if (strpos($nextLine, 'href="#"') !== false) {
                // It's a parent!
                $in_parent_li = true;
                $parent_li_depth = 1;
                $buffer[] = $line;
                $parent_urls = [];
                continue;
            }
        }
        $out[] = $line;
    } else {
        $buffer[] = $line;
        if (preg_match('/<li[^>]*>/', $line)) {
            $parent_li_depth++;
        }
        if (preg_match('/<\/li>/', $line)) {
            $parent_li_depth--;
        }
        
        // collect urls
        if (preg_match('/href="\{\{\s*(url|route)\((.*?)\)\s*\}\}"/', $line, $m)) {
            $url = trim($m[2], "\"'");
            $parent_urls[] = "'" . $url . "'";
        }
        
        if ($parent_li_depth === 0) {
            // End of parent li!
            $in_parent_li = false;
            if (!empty($parent_urls)) {
                $urls_str = implode(', ', array_unique($parent_urls));
                $out[] = "@if(\\App\\Helpers\\AksesHelper::cekAny([" . $urls_str . "]))";
                foreach ($buffer as $b) {
                    $out[] = $b;
                }
                $out[] = "@endif";
            } else {
                foreach ($buffer as $b) {
                    $out[] = $b;
                }
            }
            $buffer = [];
        }
    }
}

$new_content = $before . implode("\n", $out) . $after;
file_put_contents('c:/xampp/htdocs/bundling2/scratch_wrap_parents_3.blade.php', $new_content);
echo "Done writing scratch_wrap_parents_3.blade.php\n";
