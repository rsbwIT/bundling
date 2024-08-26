<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrackerSQL
{
    public static function TrackerSimpan($table, $data)
    {
        $values = implode(', ', array_map(function ($value) {
            return is_string($value) ? "'$value'" : $value;
        }, array_values($data)));
        return DB::table('trackersql')->insert([
            'tanggal' => Carbon::now()->format('Y-m-d H:i:s'),
            'sqle' => 'Insert Into ' . $table . '(' . $values . ')',
            'usere' => session('auth')['id_user'],
        ]);
    }
}
