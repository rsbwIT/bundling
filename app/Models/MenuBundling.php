<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuBundling extends Model
{
    protected $table = 'menu_bundling';

    protected $fillable = [
        'nama_menu',
        'icon',
        'urutan',
        'aktif',
        'hak_akses',
    ];

    public function children()
    {
        return $this->hasMany(DaftarMenuBundling::class, 'parent_id', 'id')
            ->where('aktif', 'Y')
            ->orderBy('urutan');
    }
}
