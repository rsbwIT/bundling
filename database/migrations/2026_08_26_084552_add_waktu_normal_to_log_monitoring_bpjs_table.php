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
        Schema::table('log_monitoring_bpjs', function (Blueprint $table) {
            $table->dateTime('waktu_normal')->nullable()->after('waktu_gangguan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('log_monitoring_bpjs', function (Blueprint $table) {
            $table->dropColumn('waktu_normal');
        });
    }
};
