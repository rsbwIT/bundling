<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fisioterapi_header', function (Blueprint $table) {
            $table->id();
            $table->string('no_rawat')->unique();
            $table->text('diagnosa')->nullable();
            $table->text('ft')->nullable();
            $table->text('st')->nullable();
            $table->timestamps();
        });

        Schema::create('fisioterapi_kunjungan', function (Blueprint $table) {
            $table->id();
            $table->string('no_rawat');
            $table->integer('kunjungan_ke');
            $table->string('program')->nullable();
            $table->date('tanggal')->nullable();

            $table->longText('ttd_pasien')->nullable();
            $table->longText('ttd_dokter')->nullable();
            $table->longText('ttd_terapis')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fisioterapi_header');
        Schema::dropIfExists('fisioterapi_kunjungan');
    }
};
