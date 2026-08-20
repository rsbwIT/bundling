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
    public function up(): void
    {
        Schema::create('log_monitoring_bpjs', function (Blueprint $table) {
            $table->id();
            $table->string('service_id')->index();
            $table->string('service_name');
            $table->string('url');
            $table->dateTime('waktu_gangguan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_monitoring_bpjs');
    }
};
