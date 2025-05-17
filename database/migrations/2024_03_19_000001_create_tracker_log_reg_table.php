<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bw_tracker_log_reg', function (Blueprint $table) {
            $table->id();
            $table->string('id_user');
            $table->string('nama_user');
            $table->string('tanggal', 20); // Format: d/m/Y H:i:s
            $table->string('status');
            $table->text('keterangan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bw_tracker_log_reg');
    }
};
