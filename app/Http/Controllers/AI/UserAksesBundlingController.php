<?php

namespace App\Http\Controllers\AI; // Keep it under AI namespace or just Controllers

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserAksesBundlingController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil daftar pegawai / user
        $users = DB::table('user as u')
            ->selectRaw("
                COALESCE(d.nm_dokter, p.nama) as nama_petugas,
                TRIM(CAST(AES_DECRYPT(u.id_user,'nur') AS CHAR(50))) as username_asli
            ")
            ->leftJoin('petugas as p', function ($join) {
                $join->on('p.nip', '=', DB::raw("TRIM(CAST(AES_DECRYPT(u.id_user,'nur') AS CHAR(50)))"));
            })
            ->leftJoin('dokter as d', function ($join) {
                $join->on('d.kd_dokter', '=', DB::raw("TRIM(CAST(AES_DECRYPT(u.id_user,'nur') AS CHAR(50)))"));
            })
            ->orderByRaw('COALESCE(d.nm_dokter, p.nama)')
            ->get();

        // 2. Ambil daftar menu bundling
        $menus = DB::table('daftar_menu_bundling')
            ->where('aktif', 'Y')
            ->orderBy('urutan')
            ->get();

        return view('ai.akses-bundling', compact('users', 'menus'));
    }

    public function getAkses($username)
    {
        $akses = DB::table('user_akses_bundling')
            ->where('username', $username)
            ->get()
            ->keyBy('menu_id');

        return response()->json([
            'status' => true,
            'akses' => $akses
        ]);
    }

    public function updateAkses(Request $request)
    {
        try {
            $username = $request->username;
            $menu_ids = $request->menu_ids ?? []; // array of menu_id that are checked

            if (!$username) {
                return response()->json([
                    'status' => false,
                    'message' => 'Username tidak valid'
                ]);
            }

            // Pertama, ubah semua status menjadi false untuk user ini
            // Tapi karena mungkin menu baru ada, lebih baik kita loop daftar_menu_bundling
            $allMenus = DB::table('daftar_menu_bundling')->pluck('id')->toArray();

            $dataToUpsert = [];
            foreach ($allMenus as $menuId) {
                $status = in_array($menuId, $menu_ids) ? 'true' : 'false';
                
                // Gunakan updateOrInsert
                DB::table('user_akses_bundling')->updateOrInsert(
                    ['username' => $username, 'menu_id' => $menuId],
                    ['status' => $status, 'updated_at' => now()]
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'Hak akses menu bundling berhasil disimpan!'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR: ' . $e->getMessage()
            ], 500);
        }
    }
}
