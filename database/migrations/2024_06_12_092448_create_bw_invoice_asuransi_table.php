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
        Schema::create('bw_invoice_asuransi', function (Blueprint $table) {
            $table->string('nomor_tagihan', 50)->primary();
            $table->string('kode_asuransi', 100)->nullable();
            $table->string('nama_asuransi', 100)->nullable();
            $table->string('alamat_asuransi', 150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bw_invoice_asuransi');
    }
};
