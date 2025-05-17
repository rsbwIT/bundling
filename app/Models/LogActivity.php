<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RegPeriksaBilling;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LogActivity extends Model
{
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Format tanggal sesuai yang diinginkan (d/m/Y H:i:s)
            if (!$model->tanggal) {
                $model->tanggal = Carbon::now()->format('d/m/Y H:i:s');
            }

            // Get session data if not already set
            if (!$model->id_user && Session::has('auth')) {
                $auth = Session::get('auth');
                $model->id_user = $auth['id_user'] ?? null;
            }

            // Get user name from pegawai table if not set
            if (!$model->nama_user && $model->id_user) {
                $user = DB::table('pegawai')
                    ->select('nama')
                    ->where('nik', $model->id_user)
                    ->first();
                $model->nama_user = $user ? $user->nama : $model->id_user;
            }

            // Set default values if still not set
            if (!$model->id_user) {
                $model->id_user = '0';
                $model->nama_user = 'SYSTEM';
            }

            // Ensure status is uppercase
            if ($model->status) {
                $model->status = strtoupper($model->status);
            }
        });
    }

    public function registration()
    {
        return $this->belongsTo(RegPeriksaBilling::class, 'no_rawat', 'no_rawat');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'username');
    }

    /**
     * Create a new log entry with proper session data
     */
    public static function createLog($status, $keterangan)
    {
        try {
            $userId = null;
            $userName = null;

            if (Session::has('auth')) {
                $auth = Session::get('auth');
                $userId = $auth['id_user'];

                // Get user name
                $user = DB::table('pegawai')
                    ->select('nama')
                    ->where('nik', $userId)
                    ->first();

                $userName = $user ? $user->nama : $userId;
            }

            return self::create([
                'id_user' => $userId ?? '0',
                'nama_user' => $userName ?? 'SYSTEM',
                'tanggal' => Carbon::now()->format('d/m/Y H:i:s'),
                'status' => strtoupper($status),
                'keterangan' => $keterangan
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create log entry', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
