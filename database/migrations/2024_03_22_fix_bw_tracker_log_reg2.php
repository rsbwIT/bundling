<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop the existing table if it exists
        Schema::dropIfExists('bw_tracker_log_reg2');

        // Create the table with proper structure
        Schema::create('bw_tracker_log_reg2', function (Blueprint $table) {
            $table->bigIncrements('id'); // This will create an auto-incrementing primary key
            $table->string('id_user');
            $table->string('nama_user');
            $table->timestamp('tanggal')->useCurrent();
            $table->string('status');
            $table->text('keterangan');
        });

        // Set the auto increment start value to 1000
        DB::statement('ALTER TABLE bw_tracker_log_reg2 AUTO_INCREMENT = 1');
    }

    public function down()
    {
        Schema::dropIfExists('bw_tracker_log_reg2');
    }
};
