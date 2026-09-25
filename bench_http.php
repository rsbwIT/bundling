<?php
$start = microtime(true);
$request = Illuminate\Http\Request::create('/bpjs/inacbg/resume-modal', 'GET', ['norawat' => '2026/09/22/000001']);
$response = app()->handle($request);
$end = microtime(true);
echo "Time HTTP: " . ($end - $start) . " seconds\n";
echo "Status: " . $response->getStatusCode() . "\n";
