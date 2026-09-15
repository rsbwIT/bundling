<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/bpjs/inacbg/resume-modal?norawat=2026%2F09%2F11%2F000540', 'GET');

$controller = new \App\Http\Controllers\Bpjs\bridginginacbg2();
try {
    $response = $controller->getResumeModalHtml($request);
    echo $response->getStatusCode() . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
