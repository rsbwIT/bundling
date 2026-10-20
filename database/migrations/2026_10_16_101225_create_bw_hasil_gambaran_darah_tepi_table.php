<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bw_hasil_gambaran_darah_tepi', function (Blueprint $table) {
            $table->id();
            $table->string('noorder', 20)->unique();
            $table->string('no_rawat', 20)->index();
            $table->date('tanggal')->nullable();
            $table->string('kd_dokter_pemeriksa', 20)->nullable();
            $table->string('nm_dokter_pemeriksa', 100)->nullable();
            
            // Isian Hasil Pemeriksaan
            $table->text('eritrosit')->nullable();
            $table->text('leukosit')->nullable();
            $table->text('trombosit')->nullable();
            $table->text('kesan')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->text('saran')->nullable();
            $table->text('manual_diff')->nullable();
            
            // Keterangan baris bawah
            $table->string('wbc', 50)->nullable();
            $table->string('hb', 50)->nullable();
            $table->string('plt', 50)->nullable();
            $table->text('keterangan_tambahan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bw_hasil_gambaran_darah_tepi');
    }
};
