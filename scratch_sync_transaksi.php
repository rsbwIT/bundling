<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$html = file_get_contents('c:\xampp\htdocs\bundling2\resources\views\layout\layoutDashboard.blade.php');
$lines = explode("\n", $html);

// We only want lines 705 to 1337
$transaksi_lines = array_slice($lines, 704, 1337-704);
$transaksi_html = implode("\n", $transaksi_lines);

preg_match_all('/<a\s+href="\{\{\s*(?:url|route)\(\s*\'([^\']+)\'/i', $transaksi_html, $matches);
$urls = $matches[1];

echo "Found " . count($urls) . " urls in Transaksi block.\n";

$inserted = 0;
foreach($urls as $u) {
    if($u == '#' || empty($u)) continue;
    $cleanUrl = ltrim($u, '/');
    if(empty($cleanUrl)) $cleanUrl = '/';
    
    // Attempt to guess the menu name from HTML (roughly)
    // Actually, just let sync script do its best, or just hardcode dummy names if they don't exist.
    // Wait, the script I wrote before `sync_all_menus.php` already does this better. Let's just use it on the snippet.
    
    $exists = DB::table('daftar_menu_bundling')->where('url', 'LIKE', '%'.$cleanUrl.'%')->first();
    if(!$exists) {
        DB::table('daftar_menu_bundling')->insert([
            'nama_menu' => 'Menu ' . $cleanUrl,
            'url' => '/' . $cleanUrl,
            'aktif' => 'Y',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $inserted++;
    }
}
echo "Inserted $inserted missing menus.\n";

// Then, let's run `assign_all_parents.php` logic ONLY on Transaksi block to fix their parents.
