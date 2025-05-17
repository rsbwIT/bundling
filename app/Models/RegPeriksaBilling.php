<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegPeriksaBilling extends Model
{
    protected $table = 'reg_periksa';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'stts'
    ];

    public function logActivities()
    {
        return $this->hasMany(LogActivity::class, 'no_rawat', 'no_rawat');
    }
}
