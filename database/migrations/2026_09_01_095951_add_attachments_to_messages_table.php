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
        Schema::table('messages', function (Blueprint $table) {
            $table->string('attachment_path')->nullable();
            $table->string('attachment_type')->nullable();
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE messages MODIFY message TEXT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['attachment_path', 'attachment_type']);
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE messages MODIFY message TEXT NOT NULL;');
    }
};
