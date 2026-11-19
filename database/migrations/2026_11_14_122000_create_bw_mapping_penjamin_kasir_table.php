<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bw_mapping_penjamin_kasir')) {
            Schema::create('bw_mapping_penjamin_kasir', function (Blueprint $table) {
                $table->string('kd_pj', 15)->primary();
                $table->string('kategori', 30)->default('Asuransi'); // 'BPJS', 'BPJS COB', 'Asuransi', 'Umum'
                $table->string('nama_akun_piutang', 150)->nullable();
                $table->string('kd_rek_piutang', 25)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bw_mapping_penjamin_kasir');
    }
};

