<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomor_surat', function (Blueprint $table) {
            $table->string('no_rawat', 50)->nullable()->after('no_sep');
        });
    }

    public function down(): void
    {
        Schema::table('nomor_surat', function (Blueprint $table) {
            $table->dropColumn('no_rawat');
        });
    }
};
