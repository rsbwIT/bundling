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
        Schema::create('bw_tracker_log_reg', function (Blueprint $table) {
            $table->id();
            $table->string('id_user');
            $table->string('nama_user');
            $table->timestamp('tanggal');
            $table->string('status');
            $table->text('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bw_tracker_log_reg');
    }
};
