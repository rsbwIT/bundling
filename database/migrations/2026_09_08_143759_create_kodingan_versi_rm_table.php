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
        Schema::create('kodingan_versi_rm', function (Blueprint $table) {
            $table->id();
            $table->string('no_rawat', 25)->unique();
            $table->text('icd10')->nullable();
            $table->text('icd9')->nullable();
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
        Schema::dropIfExists('kodingan_versi_rm');
    }
};
