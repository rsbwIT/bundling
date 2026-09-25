<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$start = microtime(true);

$controller = new \App\Http\Controllers\Bpjs\bridginginacbg2();
$request = new \Illuminate\Http\Request();
$request->merge(['norawat' => '2026/09/22/000001']);

$response = $controller->getTriaseModalHtml($request);

$end = microtime(true);
$time = $end - $start;
echo "Time: " . $time . " seconds\n";
