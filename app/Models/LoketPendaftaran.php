<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoketPendaftaran extends Model
{
    protected $table = 'loket_pendaftaran'; // nama tabel
    protected $primaryKey = 'id';           // sesuaikan kalau bukan "id"
    public $timestamps = false;             // kalau tabel tidak pakai created_at, updated_at
}