<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\DefaultService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    private function generateMathCaptcha()
    {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        $answer = $num1 + $num2;
        session(['captcha_answer' => $answer]);
        return "$num1 + $num2 = ?";
    }

    public function refreshCaptcha()
    {
        $question = $this->generateMathCaptcha();
        return response()->json(['question' => $question]);
    }

    protected function throttleKey(Request $request)
    {
        $userId = Str::lower(trim((string) $request->input('id_user', 'guest')));
        return 'login:' . ($userId ?: 'guest') . '|' . $request->ip();
    }

    function Login(Request $request){
        $getSetting = $this->cacheService->getSetting();
        $captchaQuestion = $this->generateMathCaptcha();

        $throttleKey = $this->throttleKey($request);
        $lockoutSeconds = 0;
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $lockoutSeconds = RateLimiter::availableIn($throttleKey);
        } elseif (session('lockout_seconds')) {
            $lockoutSeconds = (int) session('lockout_seconds');
        }

        return view('auth.login',[
            'getSetting' => $getSetting,
            'captchaQuestion' => $captchaQuestion,
            'lockoutSeconds' => $lockoutSeconds
        ]);
    }
    function mesinLogin(Request $request){
        $throttleKey = $this->throttleKey($request);

        // Jika salah login lebih dari 3 kali, kunci selama 1 menit (60 detik)
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Session::flash('errorLogin', "Terlalu banyak percobaan login salah (lebih dari 3 kali). Silakan tunggu {$seconds} detik lagi.");
            session()->flash('lockout_seconds', $seconds);
            return redirect('/login')->withInput($request->except('password', 'captcha'));
        }

        $userCaptcha = $request->input('captcha');
        $expectedCaptcha = session('captcha_answer');
        session()->forget('captcha_answer');

        if ($userCaptcha === null || (int)$userCaptcha !== (int)$expectedCaptcha) {
            RateLimiter::hit($throttleKey, 60);
            $remaining = RateLimiter::remaining($throttleKey, 3);

            if ($remaining <= 0) {
                $seconds = RateLimiter::availableIn($throttleKey);
                Session::flash('errorLogin', "Anda telah salah login 3 kali. Silakan tunggu {$seconds} detik untuk mencoba lagi.");
                session()->flash('lockout_seconds', $seconds);
            } else {
                Session::flash('errorLogin', "Jawaban captcha tidak sesuai. Kesempatan tersisa: {$remaining} kali.");
            }

            return redirect('/login')->withInput($request->except('password', 'captcha'));
        }

        $data = [
            'id_user' => $request->id_user,
            'password' => $request->password,
        ];

        // PERBAIKAN KEAMANAN: login selalu dicek ke database (password wajib cocok).
        // Sebelumnya hasil login di-cache berdasarkan id_user saja, sehingga dalam 12 menit
        // setelah user login, orang lain bisa masuk sebagai user itu dengan password apa saja.
        $result = DB::table('user')
            ->select('id_user', 'password')
            ->whereRaw("aes_decrypt(user.id_user, 'nur') = ? AND aes_decrypt(user.password, 'windi') = ?", [$data['id_user'], $data['password']])
            ->first();
        // dd(Cache::get($cacheKey));


        if(!$result){
            RateLimiter::hit($throttleKey, 60);
            $remaining = RateLimiter::remaining($throttleKey, 3);

            if ($remaining <= 0) {
                $seconds = RateLimiter::availableIn($throttleKey);
                Session::flash('errorLogin', "Anda telah salah login 3 kali. Silakan tunggu {$seconds} detik untuk mencoba lagi.");
                session()->flash('lockout_seconds', $seconds);
            } else {
                Session::flash('errorLogin', "ID Pengguna atau Kata Sandi salah. Kesempatan tersisa: {$remaining} kali.");
            }

            return redirect('/login')->withInput($request->except('password', 'captcha'));
        }else{
            // Login sukses: reset counter kesalahan
            RateLimiter::clear($throttleKey);

            $userLogin = DB::table('pegawai')
                ->select('pegawai.nik', 'pegawai.nama', 'pegawai.photo')
                ->where('pegawai.nik', '=', $request->id_user)
                ->first();
            
            // Map 'photo' to 'foto' so it works seamlessly with avatar and ProfileController
            if ($userLogin && isset($userLogin->photo)) {
                $userLogin->foto = $userLogin->photo;
                
                // If it's a local upload path, convert it to relative URL
                if (strpos($userLogin->foto, 'uploads/') === 0) {
                    $parsed = parse_url(asset($userLogin->foto));
                    $userLogin->foto = ($parsed['path'] ?? '') . (isset($parsed['query']) ? '?'.$parsed['query'] : '');
                }
            }

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
