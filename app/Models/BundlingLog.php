<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BundlingLog extends Model
{
    use HasFactory;

    protected $table = 'bw_tracker_log_reg2';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'id_user',
        'nama_user',
        'tanggal',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'username');
    }
}
