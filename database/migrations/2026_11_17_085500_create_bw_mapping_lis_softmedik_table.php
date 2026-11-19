<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bw_mapping_lis_softmedik')) {
            Schema::create('bw_mapping_lis_softmedik', function (Blueprint $table) {
                $table->id();
                $table->string('kode_softmedik', 50)->index();
                $table->string('nama_test_softmedik', 150);
                $table->string('kd_jenis_prw', 20)->nullable()->index();
                $table->integer('id_template_khanza')->nullable()->index();
                $table->string('nama_pemeriksaan_khanza', 150)->nullable();
                $table->string('satuan', 50)->nullable();
                $table->string('nilai_rujukan', 150)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bw_mapping_lis_softmedik');
    }
};
