<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor_antrian');
            $table->string('rekam_medik', 50);
            $table->string('nama_pasien', 100);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('antrian');
    }
};
