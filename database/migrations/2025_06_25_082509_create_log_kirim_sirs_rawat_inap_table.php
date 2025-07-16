<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogKirimSirsRawatInapTable extends Migration
{
    public function up()
    {
        Schema::create('log_kirim_sirs_rawat_inap', function (Blueprint $table) {
            $table->id();
            $table->string('no_rawat');
            $table->timestamp('tgl_kirim')->useCurrent();
            $table->string('response_code')->nullable();
            $table->text('response_message')->nullable();
            $table->enum('status_kirim', ['BERHASIL', 'GAGAL'])->default('BERHASIL');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_kirim_sirs_rawat_inap');
    }
}
