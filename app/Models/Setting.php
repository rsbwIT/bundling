<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $connection = 'sik';  // Menentukan koneksi yang digunakan untuk model ini
    protected $table = 'settings';  // Nama tabel di database

    // Tentukan kolom yang bisa diisi jika ada
    protected $fillable = [
        'nama_instansi',
        'alamat',
        'telepon',
    ];
}

