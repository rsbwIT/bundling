<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BpjsFingerprintController extends Controller
{
    public function index() {
        return view('bpjs.fingerprint');
    }

    // Di Controller: Ubah checkDevice agar mengecek layanan Bridge Lokal
public function checkDevice()
{
    try {
        // Cek ke localhost tempat Bridge BPJS terinstal
        // Biasanya Bridge BPJS menyediakan endpoint status di port 8080 atau 9000
        $response = Http::timeout(1)->get('http://127.0.0.1:8080/status'); 

        if ($response->successful()) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    } catch (\Exception $e) {
        // Jika kabel dicabut, service bridge biasanya tidak merespon
        return response()->json(['success' => false]);
    }
}
    public function verifikasi(Request $request) {
        $noKartu = $request->no_kartu;
        try {
            $response = Http::asForm()
                ->withoutVerifying()
                ->timeout(30)
                ->post(env('BPJS_FP_URL'), [
                    'username'    => env('BPJS_FP_USERNAME'),
                    'password'    => env('BPJS_FP_PASSWORD'),
                    'card_number' => $noKartu,
                    'wait'        => 3000,
                    'exit'        => true
                ]);

            if ($response->successful()) {
                return response()->json(['success' => true, 'data' => $response->json()]);
            }
            return response()->json(['success' => false, 'message' => 'Gagal verifikasi ke BPJS']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Koneksi error']);
        }
    }
}