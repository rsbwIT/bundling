<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$penyakit = Illuminate\Support\Facades\DB::table('penyakit')->where('kd_penyakit', 'Z09.8')->first();
var_dump($penyakit);
