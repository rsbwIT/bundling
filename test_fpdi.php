<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use setasign\Fpdi\Fpdi;

$pdfPath = storage_path('app/public/file_scan/temp_KRONOLOGIS_411566_M_LATIF.pdf');

$pdf = new Fpdi();
try {
    $pageCount = $pdf->setSourceFile($pdfPath);
    echo "Successfully parsed PDF with $pageCount pages.\n";
} catch (\Exception $e) {
    echo "FPDI Error parsing $pdfPath: " . $e->getMessage() . "\n";
}
