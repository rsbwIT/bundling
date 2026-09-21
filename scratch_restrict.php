<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$allowed_parents = [
    'Keuangan',
    'Detail Tindakan (Asuransi)',
    'Detail Tindakan (Bulanan)',
    'Detail Tindakan (Umum)',
    'Detail Tindakan (BPJS)'
];

$allowed_parent_ids = DB::table('menu_bundling')
    ->whereIn('nama_menu', $allowed_parents)
    ->pluck('id')
    ->toArray();

echo "Allowed Parent IDs: " . implode(", ", $allowed_parent_ids) . "\n";

// We want to KEEP menus that are IN these parents.
// And we want to DELETE menus that are NOT in these parents, and NOT the parents themselves.
// Wait, the "parents" are in `menu_bundling`.
// The actual urls are in `daftar_menu_bundling`.
// So we want to keep `daftar_menu_bundling` where `parent_id` IN $allowed_parent_ids.
// And DELETE everything else!

$deleted = DB::table('daftar_menu_bundling')
    ->whereNotIn('parent_id', $allowed_parent_ids)
    ->delete();

echo "Deleted $deleted menus from daftar_menu_bundling.\n";

// Let's also check if there are any remaining.
$remaining = DB::table('daftar_menu_bundling')->count();
echo "Remaining menus: $remaining\n";
