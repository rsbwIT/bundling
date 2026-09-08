<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasTable('kodingan_versi_rm')) {
    Schema::create('kodingan_versi_rm', function (Blueprint $table) {
        $table->id();
        $table->string('no_rawat', 25)->unique();
        $table->text('icd10')->nullable();
        $table->text('icd9')->nullable();
        $table->timestamps();
    });
    echo "Table created successfully.\n";
} else {
    echo "Table already exists.\n";
}
