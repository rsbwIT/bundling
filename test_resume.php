<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create(
        '/bpjs/inacbg/resume-modal',
        'GET',
        ['norawat' => '2026/09/11/000540']
    )
);
echo $response->getContent();
