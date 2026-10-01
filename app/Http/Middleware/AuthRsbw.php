<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class AuthRsbw
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('auth')) {
            Session::flash('reqLogin', 'Anda harus login');
            return redirect('/login');
        }

        $idUser = session('auth')['id_user'];
        $paswdUser = session('auth')['password'];

        // Kunci cache memakai hash (password tidak disimpan mentah di nama kunci).
        // Hasil validasi + hak akses disimpan 5 menit, jadi tidak query ke tabel user di setiap klik.
        $cacheKey = 'auth_rsbw_' . sha1($idUser . '|' . $paswdUser);
        $permissionValue = Cache::get($cacheKey);

        if (!$permissionValue) {
            // SATU query: validasi akun sekaligus ambil hak akses (sebelumnya 2 query)
            $permissionValue = DB::table('user')
                ->select('penyakit', 'obat', 'pasien', 'inacbg_klaim_baru_otomatis', 'edit_registrasi', 'registrasi')
                ->whereRaw("aes_decrypt(user.id_user, 'nur') = ? AND aes_decrypt(user.password, 'windi') = ?", [$idUser, $paswdUser])
                ->first();

            // PERBAIKAN: akun tidak valid -> ke halaman login (sebelumnya error 500 karena null)
            if (!$permissionValue) {
                return redirect('/login');
            }

            Cache::put($cacheKey, $permissionValue, now()->addMinutes(5));
        }

        session([
            'penyakit' => $permissionValue->penyakit,
            'obat' => $permissionValue->obat,
            'pasien' => $permissionValue->pasien,
            'inacbg_klaim_baru_otomatis' => $permissionValue->inacbg_klaim_baru_otomatis,
            'edit_registrasi' => $permissionValue->edit_registrasi,
            'registrasi' => $permissionValue->registrasi,
        ]);

        return $next($request);
    }
}
