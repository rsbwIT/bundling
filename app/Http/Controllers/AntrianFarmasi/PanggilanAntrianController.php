<?php

namespace App\Http\Controllers\AntrianFarmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PanggilanAntrianController extends Controller
{
    // Fungsi untuk menampilkan antrian
    public function panggilanDisplay()
    {
        // Ambil tanggal hari ini
        $tanggalHariIni = Carbon::now()->format('Y-m-d');

        // Mengambil antrian berdasarkan tanggal hari ini dan status MENUNGGU
        $antrian = DB::table('antrian')
            ->join('loket', DB::raw('CAST(antrian.keterangan AS CHAR)'), '=', DB::raw('CAST(loket.kd_pendaftaran AS CHAR)'))
            ->select(
                'antrian.keterangan',
                'loket.kd_loket',
                'loket.nama_loket',
                'antrian.nomor_antrian',
                'antrian.rekam_medik',
                'antrian.nama_pasien',
                'antrian.tanggal'
            )
            ->whereDate('antrian.tanggal', $tanggalHariIni) // Filter hanya untuk tanggal hari ini
            ->where('antrian.status', 'MENUNGGU') // Hanya antrian yang masih MENUNGGU
            ->orderBy('antrian.nomor_antrian', 'asc') // Urutkan berdasarkan nomor antrian terkecil
            ->first(); // Ambil antrian pertama dalam antrean

        return view('antrian-farmasi.display', compact('antrian'));
    }

    // Fungsi untuk memanggil antrian
    public function panggil(Request $request)
    {
        // Mendapatkan nomor antrian yang dipanggil
        $nomor_antrian = $request->input('nomor_antrian');

        // Ambil data antrian berdasarkan nomor antrian dan tanggal hari ini
        $antrian = DB::table('antrian')
            ->where('nomor_antrian', $nomor_antrian)
            ->whereDate('tanggal', Carbon::today()) // Hanya ambil antrian dari hari ini
            ->first();

        // Jika antrian tidak ditemukan, arahkan kembali ke halaman display
        if (!$antrian) {
            return redirect()->route('antrian.farmasi.display')->with('status', 'Antrian tidak ditemukan atau tidak berasal dari hari ini.');
        }

        // Perubahan status antrian secara bertahap
        if ($antrian->status == 'MENUNGGU') {
            DB::table('antrian')
                ->where('nomor_antrian', $nomor_antrian)
                ->update(['status' => 'DIPANGGIL']);
            return redirect()->route('antrian.farmasi.display')->with('status', 'Antrian nomor ' . $nomor_antrian . ' sedang dipanggil.');
        } elseif ($antrian->status == 'DIPANGGIL') {
            DB::table('antrian')
                ->where('nomor_antrian', $nomor_antrian)
                ->update(['status' => 'SELESAI']);
            return redirect()->route('antrian.farmasi.display')->with('status', 'Antrian nomor ' . $nomor_antrian . ' telah selesai.');
        } else {
            return redirect()->route('antrian.farmasi.display')->with('status', 'Antrian nomor ' . $nomor_antrian . ' sudah selesai.');
        }
    }

}
