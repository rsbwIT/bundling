<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBwSettingFisioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('bw_setting_fisio')) {
            Schema::create('bw_setting_fisio', function (Blueprint $table) {
                $table->id();
                $table->string('suhu_tubuh', 10)->unique();
                $table->string('keterangan', 100)->nullable();
                $table->timestamps();
            });

            // Isi nilai default: 2, 3, 5, 6, 7
            $defaults = ['2', '3', '5', '6', '7'];
            foreach ($defaults as $val) {
                DB::table('bw_setting_fisio')->insert([
                    'suhu_tubuh' => $val,
                    'keterangan' => 'Kunjungan ' . $val,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bw_setting_fisio');
    }
}
