<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'selesai'])->default('menunggu')->after('racik_non_racik');
            $table->timestamps(); // Menambahkan created_at & updated_at
        });
    }

    public function down()
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn(['status', 'created_at', 'updated_at']);
        });
    }
};
