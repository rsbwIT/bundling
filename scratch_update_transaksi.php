<?php
$f = 'c:\xampp\htdocs\bundling2\resources\views\layout\layoutDashboard.blade.php';
$c = file_get_contents($f);

// We want to find the Transaksi block
$transaksi_start = strpos($c, 'Transaksi');
if ($transaksi_start !== false) {
    // Find the next </ul> which closes the Transaksi treeview
    $treeview_start = strpos($c, '<ul class="nav nav-treeview">', $transaksi_start);
    $treeview_end = strpos($c, '</ul>', $treeview_start);
    
    if ($treeview_start !== false && $treeview_end !== false) {
        $transaksi_block = substr($c, $treeview_start, $treeview_end - $treeview_start);
        
        // Wrap all <li> in this block
        $new_transaksi_block = preg_replace_callback('/<li class="nav-item">\s*<a href="\{\{ (url|route)\((.*?)\) \}\}".*?<\/li>/is', function($matches) {
            $url = $matches[2];
            $content = $matches[0];
            $check = $matches[1] === 'route' ? "route($url)" : $url;
            return "@if(\\App\\Helpers\\AksesHelper::cek($check))\n" . $content . "\n@endif";
        }, $transaksi_block);
        
        $c = substr_replace($c, $new_transaksi_block, $treeview_start, $treeview_end - $treeview_start);
        file_put_contents($f, $c);
        echo "Updated Transaksi block\n";
    }
}
