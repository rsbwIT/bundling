<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingSuplierNonMedis extends Model
{
    use HasFactory;

    protected $table = 'mapping_suplier_non_medis';
    protected $guarded = [];
}
