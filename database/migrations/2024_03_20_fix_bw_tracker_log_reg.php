<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // First drop the existing table if it exists
        Schema::dropIfExists('bw_tracker_log_reg');

        // Create the table with proper structure
        Schema::create('bw_tracker_log_reg', function (Blueprint $table) {
            $table->id(); // This will create an auto-incrementing primary key
            $table->string('id_user');
            $table->string('nama_user');
            $table->timestamp('tanggal');
            $table->string('status');
            $table->text('keterangan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bw_tracker_log_reg');
    }
};
