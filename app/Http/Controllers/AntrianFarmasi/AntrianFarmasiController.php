<?php

namespace App\Http\Controllers\AntrianFarmasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AntrianFarmasiController extends Controller
{
    // Menampilkan form antrian
    public function showForm()
    {
        $today = now()->format('Y-m-d');

        // Cek apakah ada antrian untuk hari ini
        $existingAntrian = DB::table('antrian')->where('tanggal', $today)->first();

        // Jika tidak ada antrian hari ini, set nomor antrian ke 1
        if (!$existingAntrian) {
            $nomorAntrian = 1;
        } else {
            // Jika ada, ambil nomor antrian terbesar dan tambah 1
            $nomorAntrian = DB::table('antrian')
                ->where('tanggal', $today)
                ->max('nomor_antrian') + 1;
        }

        return view("antrian-farmasi.form-antrian", compact('nomorAntrian'));
    }

    // Menyimpan data antrian
    public function store(Request $request)
    {
        $request->validate([
            'rekamMedik' => 'required|string|max:50',
            'namaPasien' => 'required|string|max:100',
        ]);

        $today = now()->format('Y-m-d');

        // Cek apakah ada antrian untuk hari ini
        $existingAntrian = DB::table('antrian')->where('tanggal', $today)->first();

        // Jika tidak ada antrian hari ini, set nomor antrian ke 1
        if (!$existingAntrian) {
            $nomorAntrian = 1;
        } else {
            // Jika ada, ambil nomor antrian terbesar dan tambah 1
            $nomorAntrian = DB::table('antrian')
                ->where('tanggal', $today)
                ->max('nomor_antrian') + 1;
        }

        // Menyimpan data antrian ke database
        DB::table('antrian')->insert([
            'nomor_antrian' => $nomorAntrian,
            'rekam_medik' => $request->rekamMedik,
            'nama_pasien' => $request->namaPasien,
            'tanggal' => $today,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('antrian.form')->with('status', 'Nomor Antrian Anda: ' . $nomorAntrian);
    }

    // Mendapatkan data pasien berdasarkan nomor rekam medis
    public function fetchPatient($rekamMedik)
    {
        $pasien = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('reg_periksa.no_rkm_medis', $rekamMedik)
            ->select('pasien.nm_pasien')
            ->first();

        if ($pasien) {
            return response()->json(['nama_pasien' => $pasien->nm_pasien]);
        } else {
            return response()->json(['nama_pasien' => null]);
        }
    }
}
