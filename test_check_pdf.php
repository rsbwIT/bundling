<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use setasign\Fpdi\Fpdi;

$pdf = new Fpdi();
$count = $pdf->setSourceFile(public_path('hasil_pdf/0801R0020726V009408.pdf'));
echo "Page count: " . $count . "\n";
