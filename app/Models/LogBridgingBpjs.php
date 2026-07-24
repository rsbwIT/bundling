<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogBridgingBpjs extends Model
{
    protected $table = 'bw_log_bridging_bpjs';
    public $timestamps = false;
    
    protected $fillable = [
        'layanan',
        'endpoint',
        'method',
        'request_payload',
        'response_payload',
        'status_code',
        'durasi_ms',
        'waktu_request'
    ];
}
