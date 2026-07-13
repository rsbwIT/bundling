<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NomorSuratKeterangan extends Model
{
    protected $table = 'nomor_surat_sdm';

    protected $fillable = [
        'no_rawat',
        'no_surat',
        'jenis_surat',
        'tanggal',
        'isi_surat',
    ];

    protected $casts = [
        'isi_surat' => 'array',
    ];
}
