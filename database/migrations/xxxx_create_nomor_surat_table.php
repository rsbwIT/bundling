<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
     Schema::create('nomor_surat', function (Blueprint $table) {
        $table->id();
        $table->string('jenis_surat', 10);
        $table->integer('nomor_urut');
        $table->string('nomor_surat', 150);
        $table->string('kode_rs', 20)->default('RSBW');
        $table->string('bulan', 10);
        $table->string('tahun', 4);
        $table->string('no_sep', 50);
        $table->timestamps();
    });
}

public function down(): void
{
Schema::dropIfExists('nomor_surat');
}
};