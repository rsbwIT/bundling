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
        if (!Schema::hasTable('bw_detail_invoice_asuransi')) {
            Schema::create('bw_detail_invoice_asuransi', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_tagihan', 50)->collation('utf8mb4_general_ci')->index();
                $table->string('no_rawat', 17)->collation('latin1_swedish_ci')->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bw_detail_invoice_asuransi');
    }
};
