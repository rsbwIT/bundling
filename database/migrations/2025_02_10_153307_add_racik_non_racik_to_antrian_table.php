<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->string('racik_non_racik')->nullable(); // Menambahkan kolom untuk Racik / Non-Racik
        });
    }

    public function down()
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn('racik_non_racik'); // Menghapus kolom jika rollback
        });
    }
};
