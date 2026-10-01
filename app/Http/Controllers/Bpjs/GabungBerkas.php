<?php

namespace App\Http\Controllers\Bpjs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Services\GabungPdfService;

class GabungBerkas extends Controller
{
    function gabungBerkas(Request $request)
    {
        // PERBAIKAN: kunci per pasien. Kalau tombol Gabung diklik dua kali / ada user lain
        // memproses pasien yang sama, proses kedua ditolak (tidak menulis file yang sama bersamaan).
        $lock = null;
        try {
            $lock = Cache::lock('gabung_berkas_' . md5((string) $request->cariNorawat), 180);
            if (!$lock->get()) {
                return redirect()->back()->with('errorBundling', 'Berkas pasien ini sedang diproses, tunggu sebentar lalu coba lagi');
            }
        } catch (\Throwable $e) {
            $lock = null; // driver cache tidak mendukung lock -> lanjut tanpa kunci
        }

        try {
            $hasil = GabungPdfService::printPdf($request->cariNorawat, $request->no_rkm_medis);

            Session::forget('tgl1');
            Session::forget('tgl2');
            Session::forget('statusLanjut');

            // Kalau ada berkas yang gagal digabung, beri tahu user (halaman casemix menampilkan errorBundling)
            if (!empty($hasil['dilewati'])) {
                return redirect()->back()->with(
                    'errorBundling',
                    'Berkas tergabung (' . $hasil['halaman'] . ' halaman), TETAPI ada yang dilewati: ' . implode(', ', $hasil['dilewati'])
                );
            }

            if ($request->statusLanjut === 'Ranap') {
                return redirect('/cari-list-pasein-ranap?tgl1=' . $request->tgl1 . '&tgl2=' . $request->tgl2);
            }
            return redirect('/cari-list-pasein-ralan?tgl1=' . $request->tgl1 . '&tgl2=' . $request->tgl2);
        } catch (\Throwable $e) {
            Log::error('GabungBerkas Controller Error: ' . $e->getMessage());
            return redirect()->back()->with('errorBundling', 'Gagal Menggabungkan Berkas: ' . $e->getMessage());
        } finally {
            if ($lock) {
                $lock->release();
            }
        }
    }
}
