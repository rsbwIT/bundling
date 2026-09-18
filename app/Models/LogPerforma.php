<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPerforma extends Model
{
    use HasFactory;

    protected $table = 'log_performa';
    public $timestamps = false;
    protected $fillable = ['url_menu', 'method', 'waktu_loading_detik', 'waktu_akses'];
}
