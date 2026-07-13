<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nomor_surat_keterangan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat', 150);
            $table->date('tanggal');
            $table->string('no_rawat', 50);
            $table->timestamps();

            $table->index('no_rawat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomor_surat_keterangan');
    }
};
