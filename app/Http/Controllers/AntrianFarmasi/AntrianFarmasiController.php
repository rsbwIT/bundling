<?php

namespace App\Http\Controllers\AntrianFarmasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AntrianFarmasiController extends Controller
{
    // Menampilkan form antrian
    public function showForm()
    {
        $today = now()->format('Y-m-d');

        // Mengambil nomor antrian terbesar hari ini berdasarkan jenis
        $nomorAntrianRacik = DB::table('antrian')->where('tanggal', $today)->where('racik_non_racik', 'RACIK')->max('nomor_antrian');
        $nomorAntrianNonRacik = DB::table('antrian')->where('tanggal', $today)->where('racik_non_racik', 'NON RACIK')->max('nomor_antrian');

        // Jika belum ada nomor antrian, set ke A000 atau B000
        $nomorAntrianRacik = $nomorAntrianRacik ? $nomorAntrianRacik : 'A000';
        $nomorAntrianNonRacik = $nomorAntrianNonRacik ? $nomorAntrianNonRacik : 'B000';

        return view('antrian-farmasi.form-antrian', compact('nomorAntrianRacik', 'nomorAntrianNonRacik'));
    }

    // Menambahkan kolom racik_non_racik pada tabel antrian
    public function up()
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->string('racik_non_racik')->nullable(); // Kolom untuk Racik / Non-Racik
        });
    }

    // Menghapus kolom racik_non_racik dari tabel antrian
    public function down()
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn('racik_non_racik');
        });
    }

    // Menyimpan data antrian
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'rekamMedik' => 'required|string|max:50',
            'namaPasien' => 'required|string|max:100',
            'racik_non_racik' => 'required|string',
        ]);

        $today = now()->format('Y-m-d');
        $jenisObat = $request->racik_non_racik;

        // Ambil nomor antrian terbesar berdasarkan jenis obat (racik atau non-racik)
        $nomorAntrian = DB::table('antrian')->where('tanggal', $today)
            ->where('racik_non_racik', $jenisObat)
            ->max('nomor_antrian');

        // Tentukan awalan nomor antrian dan urutan berikutnya
        $prefix = ($jenisObat == 'RACIK') ? 'A' : 'B';
        $nomorAntrian = $nomorAntrian ? $nomorAntrian : $prefix . '000';

        // Generate nomor antrian berikutnya
        $nomorAntrianNext = $this->generateNextAntrianNumber($nomorAntrian);

        // Menyimpan data antrian ke database
        DB::table('antrian')->insert([
            'nomor_antrian' => $nomorAntrianNext,
            'rekam_medik' => $request->rekamMedik,
            'nama_pasien' => $request->namaPasien,
            'tanggal' => $today,
            'racik_non_racik' => $jenisObat,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['nomorAntrian' => $nomorAntrianNext]); // Mengembalikan nomor antrian
    }

    // Fungsi untuk menghasilkan nomor antrian berikutnya
    private function generateNextAntrianNumber($nomorAntrian)
    {
        $prefix = substr($nomorAntrian, 0, 1); // Ambil huruf awalan (A atau B)
        $number = (int)substr($nomorAntrian, 1); // Ambil angka setelah awalan
        $nextNumber = str_pad($number + 1, 3, '0', STR_PAD_LEFT); // Menambah nomor dengan 1 dan padding 3 digit

        return $prefix . $nextNumber; // Gabungkan kembali awalan dan nomor antrian
    }

    // Mendapatkan data pasien berdasarkan nomor rekam medis
    public function fetchPatient($rekamMedik)
    {
        $pasien = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('reg_periksa.no_rkm_medis', $rekamMedik)
            ->select('pasien.nm_pasien')
            ->first();

        return response()->json(['nama_pasien' => $pasien->nm_pasien ?? null]);
    }

    // Mencetak antrian berdasarkan nomor antrian
    public function cetakAntrian($nomorAntrian)
    {
        $antrian = DB::table('antrian')->where('nomor_antrian', $nomorAntrian)->first();
        $setting = DB::table('setting')->first();

        if (!$antrian) {
            return redirect()->route('antrian.form')->with('error', 'Nomor Antrian tidak ditemukan.');
        }

        return view('antrian-farmasi.cetak', compact('antrian', 'setting'));
    }

    // Mendapatkan nomor antrian berikutnya berdasarkan jenis obat
    public function getNextAntrian($jenisObat)
    {
        $today = now()->format('Y-m-d');

        // Tentukan prefix berdasarkan jenis obat
        $prefix = ($jenisObat == 'RACIK') ? 'A' : 'B'; // FIXED: 'RACIK' gets 'A', 'NON_RACIK' gets 'B'

        // Ambil nomor antrian terbesar berdasarkan jenis obat dan tanggal
        $nomorAntrian = DB::table('antrian')
            ->where('tanggal', $today)
            ->where('racik_non_racik', $jenisObat)
            ->max('nomor_antrian');

        // Tentukan nomor antrian berikutnya
        $nomorAntrian = $nomorAntrian ? $nomorAntrian : $prefix . '000';
        $nextNomorAntrian = $this->generateNextAntrianNumber($nomorAntrian);

        return response()->json(['nomorAntrian' => $nextNomorAntrian]);
    }
}
