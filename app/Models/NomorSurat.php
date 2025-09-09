<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NomorSurat extends Model
{
protected $table = 'nomor_surat';
protected $fillable = [
'jenis_surat', 'nomor_urut', 'nomor_surat',
'kode_rs', 'bulan', 'tahun', 'no_sep'
];
}
