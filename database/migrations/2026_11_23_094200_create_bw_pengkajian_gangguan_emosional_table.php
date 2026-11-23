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
        if (!Schema::hasTable('bw_pengkajian_gangguan_emosional')) {
            Schema::create('bw_pengkajian_gangguan_emosional', function (Blueprint $table) {
                $table->string('no_rawat', 25)->primary();
                $table->dateTime('tanggal')->nullable();
                $table->string('informasi_dari', 30)->nullable();
                $table->text('alasan_masuk')->nullable();
                $table->string('predisposisi_riwayat_jiwa_lalu', 10)->nullable();
                $table->string('predisposisi_pengobatan_sebelumnya', 20)->nullable();
                $table->json('predisposisi_trauma_masa_lalu')->nullable();
                $table->text('predisposisi_trauma_lain_ket')->nullable();
                $table->string('predisposisi_riwayat_keluarga', 10)->nullable();
                $table->text('predisposisi_pengalaman_buruk')->nullable();
                $table->text('sosial_orang_berarti')->nullable();
                $table->text('sosial_hambatan_berhubungan')->nullable();
                $table->text('spiritual_nilai_keyakinan')->nullable();
                $table->text('spiritual_kegiatan_ibadah')->nullable();
                $table->string('mental_kesadaran', 30)->nullable();
                $table->string('mental_penampilan', 40)->nullable();
                $table->text('mental_penampilan_ket')->nullable();
                $table->string('mental_pembicaraan', 30)->nullable();
                $table->text('mental_pembicaraan_ket')->nullable();
                $table->json('mental_persepsi')->nullable();
                $table->string('mental_alam_perasaan', 30)->nullable();
                $table->text('mental_alam_perasaan_ket')->nullable();
                $table->json('masalah_keperawatan')->nullable();
                $table->text('masalah_keperawatan_lain_ket')->nullable();
                $table->text('rencana_keperawatan')->nullable();
                $table->string('nip_petugas', 30)->nullable();
                $table->string('nama_petugas', 100)->nullable();
                $table->dateTime('tgl_pengkajian_petugas')->nullable();
                $table->string('kd_dokter_dpjp', 20)->nullable();
                $table->string('nama_dokter_dpjp', 100)->nullable();
                $table->dateTime('tgl_verifikasi_dpjp')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bw_pengkajian_gangguan_emosional');
    }
};
