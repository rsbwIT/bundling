<?php

namespace App\Services;

use App\Models\BundlingLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BundlingLogService
{
    public static function log($status, $keterangan = null, $dataLama = null, $dataBaru = null)
    {
        return BundlingLog::create([
            'id_user' => Auth::id(),
            'nama_user' => Auth::user()->name,
            'tanggal' => Carbon::now(),
            'status' => $status,
            'keterangan' => $keterangan,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru
        ]);
    }

    public static function logDelete($model, $keterangan = null)
    {
        return self::log(
            'DELETE',
            $keterangan,
            $model->toArray(),
            null
        );
    }

    public static function logUpdate($model, $dataLama, $keterangan = null)
    {
        return self::log(
            'UPDATE',
            $keterangan,
            $dataLama,
            $model->toArray()
        );
    }

    public static function logCreate($model, $keterangan = null)
    {
        return self::log(
            'CREATE',
            $keterangan,
            null,
            $model->toArray()
        );
    }
}
