<?php

namespace App\Http\Controllers\AntrianFarmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PanggilPasien extends Controller
{
    public function panggil()
    {
        // Ambil tanggal hari ini
        $today = Carbon::now()->toDateString();  // Mendapatkan tanggal hari ini dalam format 'YYYY-MM-DD'

        // Ambil antrian pertama yang menunggu dan sesuai dengan tanggal hari ini dari tabel 'antrian'
        $antrian = DB::connection('db')  // Pastikan koneksi 'db' digunakan
            ->table('antrian')   // Nama tabel yang digunakan
            ->where('status', 'MENUNGGU')  // Cari status 'MENUNGGU'
            ->whereDate('created_at', $today)  // Filter berdasarkan tanggal hari ini
            ->first();  // Ambil satu antrian pertama

        if ($antrian) {
            // Update status antrian pertama menjadi 'PANGGIL'
            DB::connection('db')
                ->table('antrian')
                ->where('id', $antrian->id)  // Cari berdasarkan id yang ditemukan
                ->update(['status' => 'PANGGIL']);  // Update status menjadi 'PANGGIL'

            // Ambil antrian berikutnya yang menunggu dan sesuai dengan tanggal hari ini
            $antrianBerikutnya = DB::connection('db')
                ->table('antrian')
                ->where('status', 'MENUNGGU')
                ->whereDate('created_at', $today)  // Filter berdasarkan tanggal hari ini
                ->skip(1)  // Lewati satu data yang sudah dipanggil
                ->first();  // Ambil antrian berikutnya

            if ($antrianBerikutnya) {
                // Update status antrian berikutnya menjadi 'SELESAI'
                DB::connection('db')
                    ->table('antrian')
                    ->where('id', $antrianBerikutnya->id)
                    ->update(['status' => 'SELESAI']);
            }
        }

        // Kembali ke halaman sebelumnya dengan pesan status
        return redirect()->route('antrian.view')->with('status', 'Antrian dipanggil dan antrian berikutnya selesai');
    }
}
