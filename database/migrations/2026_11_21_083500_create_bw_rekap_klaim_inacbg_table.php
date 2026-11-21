<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('bw_rekap_klaim_inacbg')) {
            Schema::create('bw_rekap_klaim_inacbg', function (Blueprint $table) {
                $table->string('no_sep', 40)->primary();
                $table->string('no_rawat', 25)->index();
                $table->string('no_rkm_medis', 15)->nullable()->index();
                $table->string('nm_pasien', 100)->nullable();
                $table->string('status_lanjut', 10)->nullable();
                $table->date('tgl_registrasi')->nullable()->index();
                $table->dateTime('tgl_kirim')->nullable()->index();
                $table->string('coder_nik', 30)->nullable();
                $table->string('coder_nm', 100)->nullable();
                $table->string('cbg_code', 20)->nullable();
                $table->string('cbg_desc', 255)->nullable();
                $table->decimal('tarif_inacbg', 15, 2)->default(0);
                $table->decimal('tarif_rs', 15, 2)->default(0);
                $table->string('kemenkes_dc_status', 30)->nullable();
                $table->dateTime('kemenkes_dc_sent_dttm')->nullable();
                $table->string('bpjs_dc_status', 30)->nullable();
                $table->dateTime('bpjs_dc_sent_dttm')->nullable();
                $table->string('klaim_status_cd', 30)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bw_rekap_klaim_inacbg');
    }
};
