<?php

namespace App\Http\Controllers\AI;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class User extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->cari;

        $data = DB::table('user as u')
            ->selectRaw("
                p.nama as nama_petugas,
                u.*,
                CAST(AES_DECRYPT(u.id_user,'nur') AS CHAR(50)) as username_raw,
                CAST(AES_DECRYPT(u.password,'windi') AS CHAR(50)) as password_raw,
                TRIM(CAST(AES_DECRYPT(u.id_user,'nur') AS CHAR(50))) as username_asli,
                TRIM(CAST(AES_DECRYPT(u.password,'windi') AS CHAR(50))) as password_asli,
                IF(CAST(AES_DECRYPT(u.id_user,'nur') AS CHAR(50)) LIKE '% %', 1, 0) as username_ada_spasi,
                IF(CAST(AES_DECRYPT(u.password,'windi') AS CHAR(50)) LIKE '% %', 1, 0) as password_ada_spasi
            ")
            ->leftJoin('petugas as p', function ($join) {
                $join->on(
                    'p.nip',
                    '=',
                    DB::raw("TRIM(CAST(AES_DECRYPT(u.id_user,'nur') AS CHAR(50)))")
                );
            })
            ->when($cari, function ($q) use ($cari) {
                $q->where('p.nama', 'like', "%{$cari}%")
                  ->orWhereRaw("TRIM(CAST(AES_DECRYPT(u.id_user,'nur') AS CHAR(50))) LIKE ?", ["%{$cari}%"]);
            })
            ->orderBy('p.nama')
            ->get();

        return view('ai.user', compact('data', 'cari'));
    }

    public function getAkses($username)
    {
        $user = DB::table('user')
            ->whereRaw("TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))) = ?", [$username])
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }

        $ignore = ['id_user', 'password', 'created_at', 'updated_at'];
        $akses = [];

        foreach ((array)$user as $key => $value) {

            if (in_array($key, $ignore)) continue;

            // 🔥 normalisasi boolean
            $akses[$key] = ($value == 1 || $value === 'true') ? 'true' : 'false';
        }

        return response()->json([
            'status' => true,
            'akses' => $akses
        ]);
    }

    public function updateAkses(Request $request)
    {
        try {

            $username = $request->id_user;

            // 🔥 pastikan akses array
            $akses = $request->akses;
            if (!is_array($akses)) {
                $akses = [];
            }

            if (!$username) {
                return response()->json([
                    'status' => false,
                    'message' => 'User kosong'
                ]);
            }

            $user = DB::table('user')
                ->whereRaw("TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))) = ?", [$username])
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User tidak ditemukan'
                ]);
            }

            // 🔥 ambil kolom dari user (AMAN)
            $columns = array_keys((array)$user);

            $ignore = ['id_user', 'password', 'created_at', 'updated_at'];

            $update = [];

            foreach ($akses as $key => $val) {

                if (in_array($key, $ignore)) continue;
                if (!in_array($key, $columns)) continue;

                // 🔥 convert ke integer (WAJIB kalau DB tinyint)
                $update[$key] = ($val === 'true') ? 1 : 0;
            }

            if (empty($update)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada perubahan'
                ]);
            }

            DB::table('user')
                ->whereRaw("TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))) = ?", [$username])
                ->update($update);

            return response()->json([
                'status' => true,
                'message' => 'Akses berhasil disimpan'
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'ERROR: '.$e->getMessage()
            ], 500);
        }
    }

    public function perbaikiSpasi(Request $request)
    {
        try {
            $username = $request->id_user;

            if (!$username) {
                return response()->json([
                    'status' => false,
                    'message' => 'User tidak valid'
                ]);
            }

            // Bersihkan spasi di id_user dan password untuk user ini
            DB::table('user')
                ->whereRaw("TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))) = ?", [$username])
                ->update([
                    'id_user' => DB::raw("AES_ENCRYPT(TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))), 'nur')"),
                    'password' => DB::raw("AES_ENCRYPT(TRIM(CAST(AES_DECRYPT(password,'windi') AS CHAR(50))), 'windi')")
                ]);

            return response()->json([
                'status' => true,
                'message' => 'Spasi tersembunyi berhasil dibersihkan!'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR: '.$e->getMessage()
            ], 500);
        }
    }
}