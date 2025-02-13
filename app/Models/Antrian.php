<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang digunakan
    protected $table = 'antrian';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'nomor_antrian',
        'rekam_medik',
        'nama_pasien',
        'tanggal',
        'racik_non_racik',
        'status',
        'created_at',
        'updated_at',
    ];

    // Tentukan tipe data untuk kolom tertentu jika perlu
    protected $casts = [
        'tanggal' => 'date',  // Cast tanggal menjadi tipe data date
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
