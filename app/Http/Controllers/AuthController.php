<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\DefaultService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    function Login(){
        $getSetting = $this->cacheService->getSetting();
        // Session::flush();
        return view('auth.login',[
            'getSetting' => $getSetting
        ]);
    }
    function mesinLogin(Request $request){
        $data = [
            'id_user' => $request->id_user,
            'password' => $request->password,
        ];

        $cacheKey = 'user_' . $data['id_user'];
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
        } else {
            $result = DB::table('user')
            ->select('id_user', 'password')
            ->whereRaw("aes_decrypt(user.id_user, 'nur') = ? AND aes_decrypt(user.password, 'windi') = ?", [$data['id_user'], $data['password']])
            ->first();
            Cache::put($cacheKey, $result, 720);
        }
        // dd(Cache::get($cacheKey));


        if(!$result){
            Session::flash('errorLogin', 'Cek kembali akun anda');
            return redirect('/login');
        }else{
            $userLogin = DB::table('pegawai')
                ->select('pegawai.nama')
                ->where('pegawai.nik', '=', $request->id_user)
                ->first();
            session(['user' => $userLogin]);
            session(['auth' => $data]);
            return redirect()->intended('/')->with('sucsessLogin', 'Berhasil Masuk');
        }
    }

    function Logout(Request $request){
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('sucsessLogout', 'Keluar Aplikasi');
    }

    function Maintance() {
        return view('layout.maintance');
    }
}
