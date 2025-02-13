<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRacikNonRacikToAntrianTable extends Migration
{
    public function up()
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->string('racik_non_racik')->nullable(); // Menambahkan kolom racik_non_racik
        });
    }

    public function down()
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn('racik_non_racik'); // Menghapus kolom racik_non_racik
        });
    }
}
