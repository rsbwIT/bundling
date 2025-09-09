<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasienPrint extends Model
{
    protected $table = 'pasien';   // 👈 kalau tabel di DB tetap bernama pasien
    protected $primaryKey = 'id';  // 👈 ganti kalau primary key pakai no_rm
    public $timestamps = false;    // kalau tabel tidak ada kolom created_at / updated_at

    protected $fillable = [
        'nama',
        'no_kartu_bpjs',
        'no_sep',
        'poli_tujuan',
        'diagnosis',
        'tgl_registrasi',
    ];
}