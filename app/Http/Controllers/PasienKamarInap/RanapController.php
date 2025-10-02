<?php

namespace App\Http\Controllers\PasienKamarInap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; // untuk request ke WA API

class RanapController extends Controller
{
    /**
     * Simpan tabel Ranap sebagai gambar dan kirim ke WhatsApp
     */
    public function saveAndSendWA(Request $request)
    {
        $image = $request->image; // base64 dari JS
        $phone = $request->phone; // nomor WA tujuan

        if (!$image || !$phone) {
            return response()->json(['message' => 'Data gambar atau nomor WA tidak ada!'], 400);
        }

        // Hapus prefix data:image/png;base64,
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
        $image = base64_decode($image);

        // Simpan ke storage
        $filename = 'ranap_' . date('Ymd_His') . '.png';
        $path = 'public/ranap/' . $filename;
        Storage::put($path, $image);

        // URL public file
        $fileUrl = asset('storage/ranap/' . $filename);

        // === Kirim ke WA (contoh menggunakan WA Gateway API) ===
        // Ganti sesuai API WA yang kamu pakai
        try {
            $response = Http::post('https://api-wa.example.com/send', [
                'phone' => $phone,
                'type'  => 'image',
                'image' => $fileUrl,
                'caption' => '📋 Daftar Pasien Ranap'
            ]);

            if ($response->failed()) {
                return response()->json(['message' => 'Gagal mengirim ke WA!'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error saat kirim WA: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Gambar berhasil disimpan & dikirim ke WA!']);
    }
}