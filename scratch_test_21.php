<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = \DB::select('SHOW TABLES');
$found = [];
foreach ($tables as $t) {
    $tbl = array_values((array)$t)[0];
    try {
        // check if table has no_rawat
        $cols = \DB::select("SHOW COLUMNS FROM `$tbl`");
        $has_no_rawat = false;
        foreach($cols as $col) {
            if ($col->Field == 'no_rawat') {
                $has_no_rawat = true;
                break;
            }
        }
        if ($has_no_rawat) {
            $res = \DB::select("SELECT * FROM `$tbl` WHERE no_rawat = '2026/08/24/000678'");
            if (count($res) > 0) {
                echo "Found in $tbl!\n";
                // print_r($res);
            }
        }
    } catch (\Exception $e) {
    }
}
