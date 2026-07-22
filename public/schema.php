<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$p = DB::table('pegawai')->where('photo', 'like', '%'.date('Y').'%')->orderBy('id', 'desc')->get();
foreach($p as $r) {
    echo $r->nama . " => " . $r->photo . "\n";
}
