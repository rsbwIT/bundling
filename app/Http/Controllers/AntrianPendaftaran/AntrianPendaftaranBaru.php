<?php

namespace App\Http\Controllers\AntrianPendaftaran;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Dokter;
use App\Models\LoketPendaftaran;


class AntrianPendaftaranBaru extends Controller
{
    /**
     * Tampilkan halaman antrian pendaftaran baru
     */
    public function index(Request $request)
    {
        $tanggal = now()->format('Y-m-d');

        // Daftar dokter & loket
        $dokters = DB::table('dokter')
            ->select('nm_dokter')
            ->orderBy('nm_dokter')
            ->get();

        $lokets = DB::table('loket_pendaftaran')
            ->select('id', 'nama_loket', 'no_reg', 'status')
            ->orderBy('nama_loket')
            ->get();

        // Loket aktif (dipilih user atau default pertama)
        $loketName = $request->filled('loket')
            ? $request->loket
            : ($lokets->first()->nama_loket ?? '-');

        // Antrian pasien hari ini
        $query = DB::table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->whereDate('rp.tgl_registrasi', $tanggal);

        if ($request->filled('dokter')) {
            $query->where('d.nm_dokter', $request->dokter);
        }

        $antrian = $query->select(
                'rp.no_reg',
                'p.nm_pasien',
                'd.nm_dokter',
                'rp.jam_reg as jam_mulai'
            )
            ->orderBy('rp.no_reg')
            ->get();

        // Ambil info status loket untuk tiap pasien sesuai loket yang sedang aktif
        $statusLoket = DB::table('loket_pendaftaran')
            ->where('nama_loket', $loketName)
            ->first();

        return view('livewire.antrian-pendaftaran.antrianpendaftaranbaru', [
            'tanggal'    => $tanggal,
            'antrian'    => $antrian,
            'dokters'    => $dokters,
            'lokets'     => $lokets,
            'loketName'  => $loketName,
            'statusLoket'=> $statusLoket, // menampilkan pasien/no_reg yang sedang dipanggil
        ]);
    }

    /**
     * Update status loket pendaftaran
     */
    public function updateStatus(Request $request)
{
    $validated = $request->validate([
        'nama_loket' => 'required|string',
        'no_reg'     => 'required|string',
        'nm_pasien'  => 'required|string',
        'nm_dokter'  => 'required|string',
    ]);

    try {
        // jika row untuk nama_loket belum ada, akan dibuat; kalau ada akan diupdate
        DB::table('loket_pendaftaran')->updateOrInsert(
            ['nama_loket' => $validated['nama_loket']],
            [
                'no_reg'     => $validated['no_reg'],
                'nm_pasien'  => $validated['nm_pasien'],   // ✅ update nama pasien
                'nm_dokter'  => $validated['nm_dokter'],   // ✅ update nama dokter
                'status'     => 'DIPANGGIL',
                'updated_at' => now(),
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'ok', 'message' => 'Status updated']);
        }

        return back()->with('success', 'Status antrian berhasil diperbarui.');
    } catch (\Throwable $e) {
        \Log::error('Update Loket Error: '.$e->getMessage());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan server'], 500);
        }

        return back()->with('error', 'Terjadi kesalahan saat memperbarui status.');
    }
}


    public function displayTv()
    {
        $antrian = DB::table('loket_pendaftaran as lp')
            ->select(
                'lp.no_reg',
                'lp.status',
                'lp.nama_loket',
                'lp.nm_pasien',
                'lp.nm_dokter'
            )
            ->orderByRaw("CASE WHEN lp.status = 'DIPANGGIL' THEN 0 ELSE 1 END")
            ->orderBy('lp.no_reg', 'asc')
            ->get();

        return view('livewire.antrian-pendaftaran.displaypendaftaranbaru', compact('antrian'));
    }

    public function apiTv()
    {
        $antrian = DB::table('loket_pendaftaran as lp')
            ->select(
                'lp.no_reg',
                'lp.status',
                'lp.nama_loket',
                'lp.nm_pasien',
                'lp.nm_dokter'
            )
            ->orderByRaw("CASE WHEN lp.status = 'DIPANGGIL' THEN 0 ELSE 1 END")
            ->orderBy('lp.no_reg', 'asc')
            ->get();

        return response()->json($antrian);
    }
}