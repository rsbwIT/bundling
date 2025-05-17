<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BwTrackerLogReg extends Model
{
    protected $table = 'bw_tracker_log_reg2';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';

    protected $fillable = [
        'id_user',
        'nama_user',
        'tanggal',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'id' => 'integer',
        'tanggal' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Format tanggal sesuai yang diinginkan (d/m/Y H:i:s)
            if (!$model->tanggal) {
                $model->tanggal = now();
            }

            // Get user name from pegawai table if not set
            if (!$model->nama_user && $model->id_user) {
                $user = DB::table('pegawai')
                    ->select('nama')
                    ->where('nik', $model->id_user)
                    ->first();

                if ($user) {
                    $model->nama_user = $user->nama;
                } else {
                    return false; // Don't create log if user not found
                }
            }

            // Don't create log if no user info
            if (!$model->id_user || !$model->nama_user) {
                return false;
            }

            // Ensure status is uppercase
            if ($model->status) {
                $model->status = strtoupper($model->status);
            }
        });
    }
}
