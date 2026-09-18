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
        Schema::create('log_performa', function (Blueprint $table) {
            $table->id();
            $table->string('url_menu');
            $table->string('method')->default('GET');
            $table->float('waktu_loading_detik', 8, 3);
            $table->timestamp('waktu_akses')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_performa');
    }
};
