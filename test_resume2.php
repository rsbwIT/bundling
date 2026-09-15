<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/bpjs/inacbg/resume-modal', 'GET', ['norawat' => '2026/09/11/000540']);

$controller = new \App\Http\Controllers\Bpjs\bridginginacbg2();
try {
    $response = $controller->getResumeModalHtml($request);
    echo $response->getContent();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
