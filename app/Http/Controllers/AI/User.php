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

    public function searchPegawai(Request $request)
    {
        $q = $request->q;
        if(!$q) return response()->json(['data' => []]);

        $pegawai = DB::table('pegawai')
            ->select('nik as nip', 'nama', DB::raw("'Pegawai' as jenis"))
            ->where('nama', 'like', "%{$q}%")
            ->orWhere('nik', 'like', "%{$q}%")
            ->limit(20)
            ->get();

        $dokter = DB::table('dokter')
            ->select('kd_dokter as nip', 'nm_dokter as nama', DB::raw("'Dokter' as jenis"))
            ->where('nm_dokter', 'like', "%{$q}%")
            ->orWhere('kd_dokter', 'like', "%{$q}%")
            ->limit(20)
            ->get();

        $data = $pegawai->merge($dokter);

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        try {
            $username = $request->username;
            $copy_from = $request->copy_from;

            if (!$username) {
                return response()->json(['status' => false, 'message' => 'NIP tidak boleh kosong']);
            }

            // Check jika user sudah ada
            $cek = DB::table('user')
                ->whereRaw("TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))) = ?", [$username])
                ->first();

            if ($cek) {
                return response()->json(['status' => false, 'message' => 'User sudah terdaftar']);
            }

            $insertData = [];
            
            // Dapatkan daftar kolom dari tabel user
            $kolomUser = DB::getSchemaBuilder()->getColumnListing('user');
            
            foreach ($kolomUser as $kolom) {
                if ($kolom === 'id_user' || $kolom === 'password' || $kolom === 'created_at' || $kolom === 'updated_at') continue;
                $insertData[$kolom] = 0; // default 0 / false
            }

            $insertData['id_user'] = DB::raw("AES_ENCRYPT('{$username}', 'nur')");
            $insertData['password'] = DB::raw("AES_ENCRYPT('{$username}', 'windi')");

            if ($copy_from) {
                $userCopy = DB::table('user')
                    ->whereRaw("TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))) = ?", [$copy_from])
                    ->first();
                
                if ($userCopy) {
                    foreach ((array)$userCopy as $key => $val) {
                        if (in_array($key, ['id_user', 'password', 'created_at', 'updated_at'])) continue;
                        $insertData[$key] = $val;
                    }
                }
            }

            DB::table('user')->insert($insertData);

            return response()->json(['status' => true, 'message' => 'User berhasil ditambahkan']);

        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'ERROR: ' . $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $username = $request->username;
            $password = $request->password;

            if (!$username || !$password) {
                return response()->json(['status' => false, 'message' => 'Data tidak lengkap']);
            }

            DB::table('user')
                ->whereRaw("TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))) = ?", [$username])
                ->update([
                    'password' => DB::raw("AES_ENCRYPT('{$password}', 'windi')")
                ]);

            return response()->json(['status' => true, 'message' => 'Password berhasil diubah']);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'ERROR: ' . $e->getMessage()], 500);
        }
    }

    public function getList()
    {
        $data = DB::table('user as u')
            ->selectRaw("
                p.nama as nama_petugas,
                TRIM(CAST(AES_DECRYPT(u.id_user,'nur') AS CHAR(50))) as username_asli
            ")
            ->leftJoin('petugas as p', function ($join) {
                $join->on('p.nip', '=', DB::raw("TRIM(CAST(AES_DECRYPT(u.id_user,'nur') AS CHAR(50)))"));
            })
            ->orderBy('p.nama')
            ->get();
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function copyAkses(Request $request)
    {
        try {
            $from_user = $request->from_user;
            $to_users = $request->to_users;

            if (!$from_user || empty($to_users)) {
                return response()->json(['status' => false, 'message' => 'Data tidak lengkap']);
            }

            $userCopy = DB::table('user')
                ->whereRaw("TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))) = ?", [$from_user])
                ->first();

            if (!$userCopy) {
                return response()->json(['status' => false, 'message' => 'User sumber tidak ditemukan']);
            }

            $updateData = [];
            foreach ((array)$userCopy as $key => $val) {
                if (in_array($key, ['id_user', 'password', 'created_at', 'updated_at'])) continue;
                $updateData[$key] = $val;
            }

            if (empty($updateData)) {
                return response()->json(['status' => false, 'message' => 'Tidak ada hak akses untuk disalin']);
            }

            foreach ($to_users as $to) {
                DB::table('user')
                    ->whereRaw("TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))) = ?", [$to])
                    ->update($updateData);
            }

            return response()->json(['status' => true, 'message' => 'Hak akses berhasil disalin']);

        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'ERROR: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $username = $request->username;
            if (!$username) {
                return response()->json(['status' => false, 'message' => 'Username tidak valid']);
            }

            DB::table('user')
                ->whereRaw("TRIM(CAST(AES_DECRYPT(id_user,'nur') AS CHAR(50))) = ?", [$username])
                ->delete();

            return response()->json(['status' => true, 'message' => 'User berhasil dihapus']);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'ERROR: ' . $e->getMessage()], 500);
        }
    }
}