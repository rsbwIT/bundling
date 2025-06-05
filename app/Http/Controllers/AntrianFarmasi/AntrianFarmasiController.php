<?php

namespace App\Http\Controllers\AntrianFarmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AntrianFarmasiController extends Controller
{
    // Menampilkan halaman antrian
    public function index()
    {
        $antrian = DB::table('antrian')
            ->where('tanggal', Carbon::today()->toDateString()) // Hanya tampilkan antrian hari ini
            ->orderBy('nomor_antrian', 'asc')
            ->get();

        return view('antrian-farmasi.index', compact('antrian'));
    }

    // Mengambil antrian farmasi
    public function ambilAntrian(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $noRkmMedis = $request->no_rkm_medis;

        // Cek apakah pasien terdaftar hari ini
        $pasien = DB::table('pasien')
            ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->where('pasien.no_rkm_medis', $noRkmMedis)
            ->where('reg_periksa.tgl_registrasi', $today)
            ->first();

        if (!$pasien) {
            return redirect()->back()->with('error', 'Pasien tidak ditemukan atau tidak terdaftar hari ini!');
        }

        // Tentukan kategori obat
        if ($request->racik_non_racik == 'B') {
            $kategoriObat = 'B';
            $kategoriKeterangan = 'RACIKAN';
        } else {
            $kategoriObat = 'A';
            $kategoriKeterangan = 'NON RACIK';
        }

        // Ambil nomor antrian terakhir berdasarkan kategori
        $lastAntrian = DB::table('antrian')
            ->where('tanggal', $today)
            ->where('racik_non_racik', $kategoriObat)
            ->orderBy('nomor_antrian', 'desc')
            ->first();

        // Tentukan nomor antrian berikutnya
        $nomorAntrian = $lastAntrian ? (int)substr($lastAntrian->nomor_antrian, 1) + 1 : 1;
        $nomorAntrian = $kategoriObat . str_pad($nomorAntrian, 3, '0', STR_PAD_LEFT);

        // Simpan antrian dengan keterangan kategori obat
        DB::table('antrian')->insert([
            'nomor_antrian'   => $nomorAntrian,
            'rekam_medik'     => $pasien->no_rkm_medis,
            'nama_pasien'     => $pasien->nm_pasien,
            'no_rawat'        => $pasien->no_rawat,
            'tanggal'         => $today,
            'racik_non_racik' => $kategoriObat,
            'keterangan'      => $kategoriKeterangan,
            'status'          => 'MENUNGGU',
            'created_at'      => now(),
            'updated_at'      => now()
        ]);

        // Kirim data pasien ke session (bukan hanya string)
        return redirect()->route('antrian-farmasi.index')
            ->with('success', "Antrian berhasil diambil! Nomor Anda: $nomorAntrian ($kategoriKeterangan)")
            ->with('nomorAntrian', $nomorAntrian)
            ->with('kategoriKeterangan', $kategoriKeterangan)
            ->with('pasien', $pasien);  // Mengirimkan objek pasien lengkap ke session
    }
    // Update status antrian menjadi "SELESAI"
    public function updateStatus($id)
    {
        DB::table('antrian')
            ->where('id', $id)
            ->update([
                'status' => 'SELESAI',
                'updated_at' => now()
            ]);

        return redirect()->route('antrian-farmasi.index')->with('success', 'Status antrian diperbarui!');
    }

    // Ambil data pasien berdasarkan nomor rekam medis
    public function getPasien($no_rkm_medis)
    {
        $pasien = DB::table('reg_periksa')
            ->select('reg_periksa.no_rawat', 'pasien.nm_pasien')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('reg_periksa.tgl_registrasi', Carbon::today()->toDateString())
            ->where('reg_periksa.no_rkm_medis', $no_rkm_medis)
            ->first();

        if ($pasien) {
            return response()->json([
                'nama_pasien' => $pasien->nm_pasien,
                'no_rawat'    => $pasien->no_rawat
            ]);
        } else {
            return response()->json(['error' => 'Pasien tidak ditemukan'], 404);
        }
    }

    // Menampilkan halaman cetak antrian

    public function cetakAntrian($nomorAntrian)
    {
        $today = Carbon::today()->toDateString(); // Ambil tanggal hari ini

        // Mengambil data antrian berdasarkan nomor antrian dan tanggal hari ini
        $antrian = DB::table('antrian')
            ->where('nomor_antrian', $nomorAntrian)
            ->where('tanggal', $today)
            ->first();

        // Cek jika data antrian tidak ditemukan
        if (!$antrian) {
            return redirect()->route('antrian-farmasi.index')->with('error', 'Nomor Antrian tidak ditemukan atau bukan antrian hari ini.');
        }

        // Mengambil data pasien berdasarkan nomor rekam medis yang ada di antrian
        $pasien = DB::table('pasien')
            ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->where('reg_periksa.no_rawat', $antrian->no_rawat)
            ->where('reg_periksa.tgl_registrasi', $today) // Hanya ambil data pasien yang terdaftar hari ini
            ->first();

        // Mengambil data setting dari database
        $setting = DB::table('setting')->first();

        // Cek jika data pasien tidak ditemukan
        if (!$pasien) {
            return redirect()->route('antrian-farmasi.index')->with('error', 'Pasien tidak ditemukan.');
        }

        // Mengembalikan view cetak dengan data antrian, pasien, dan setting
        return view('antrian-farmasi.cetak', compact('antrian', 'pasien', 'setting'));
    }

    public function panggil()
    {
        return view('antrian-farmasi.panggilfarmasifix');

        $this->emit('panggilDitekan');
    }





//     public function panggil($no_rawat)
// {
//     logger()->info("panggil() dipanggil dengan no_rawat = $no_rawat");

//     $this->emit('panggilDitekan', $no_rawat);
// }

}
