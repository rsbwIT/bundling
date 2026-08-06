<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cmd = app(\App\Console\Commands\KirimAntreanMjkn::class);
$ref = new ReflectionMethod($cmd, 'getListTaskFromBpjs');
$ref->setAccessible(true);
$result = $ref->invoke($cmd, rtrim(env('API_BPJS_ANTROL', 'https://apijkn.bpjs-kesehatan.go.id/antreanrs/'), '/'), env('CONS_ID'), env('SECRET_KEY'), env('USER_KEY_ANTROL'), '20260801000083');
var_dump($result);
