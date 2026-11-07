<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('master_kategori_non_medis')) {
            Schema::create('master_kategori_non_medis', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kategori')->unique();
                $table->enum('jenis', ['BEBAN', 'INVESTASI'])->default('BEBAN');
                $table->timestamps();
            });

            // Seeding kategori yang sudah ada dari mapping_suplier_non_medis
            $existing = DB::table('mapping_suplier_non_medis')
                ->whereNotNull('kategori')
                ->where('kategori', '!=', '')
                ->distinct()
                ->pluck('kategori');

            $now = now();
            foreach ($existing as $kat) {
                $kat = trim($kat);
                if (empty($kat)) continue;

                $jenis = (stripos($kat, 'investasi') !== false || stripos($kat, 'pembelian alat') !== false)
                    ? 'INVESTASI'
                    : 'BEBAN';

                DB::table('master_kategori_non_medis')->insertOrIgnore([
                    'nama_kategori' => $kat,
                    'jenis' => $jenis,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('master_kategori_non_medis');
    }
};

