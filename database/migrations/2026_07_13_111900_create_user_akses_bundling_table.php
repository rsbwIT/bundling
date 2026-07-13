<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('user_akses_bundling')) {
            Schema::create('user_akses_bundling', function (Blueprint $table) {
                $table->id();
                $table->string('username', 100)->unique();
                $table->text('menu_ids')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_akses_bundling');
    }
};
