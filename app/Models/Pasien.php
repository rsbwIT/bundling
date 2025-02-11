<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'pasien'; // Nama tabel sesuai dengan nama tabel di database

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter'); // Menyambungkan dengan tabel dokter melalui kolom 'kd_dokter'
    }
}
