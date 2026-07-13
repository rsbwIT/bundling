<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('menu_bundling')) {
            Schema::create('menu_bundling', function (Blueprint $table) {
                $table->id();
                $table->string('nama_menu', 100);
                $table->string('icon', 100)->nullable();
                $table->integer('urutan')->default(0);
                $table->enum('aktif', ['Y', 'N'])->default('Y');
                $table->string('hak_akses', 100)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('daftar_menu_bundling')) {
            Schema::create('daftar_menu_bundling', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('nama_menu', 100);
                $table->string('icon', 100)->nullable();
                $table->string('url', 150)->nullable();
                $table->integer('urutan')->default(0);
                $table->enum('aktif', ['Y', 'N'])->default('Y');
                $table->string('hak_akses', 100)->nullable();
                $table->timestamps();

                $table->foreign('parent_id')
                    ->references('id')
                    ->on('menu_bundling')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_menu_bundling');
        Schema::dropIfExists('menu_bundling');
    }
};
