<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarMenuBundling extends Model
{
    protected $table = 'daftar_menu_bundling';

    protected $fillable = [
        'parent_id',
        'nama_menu',
        'icon',
        'url',
        'urutan',
        'aktif',
        'hak_akses',
    ];

    public function parent()
    {
        return $this->belongsTo(MenuBundling::class, 'parent_id', 'id');
    }
}
