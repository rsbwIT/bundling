<?php

namespace App\Http\Controllers\Operasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class JadwalOperasi extends Controller
{
    /**
     * 🔹 Tampilkan daftar jadwal operasi hari ini (bisa difilter berdasarkan kd_pj)
     */
    public function index(Request $request)
    {
        try {
            $today = Carbon::today();
            $kd_pj = $request->kd_pj; // filter penjamin (opsional)

            $jadwal_operasi = DB::table('booking_operasi as bo')
                ->join('reg_periksa as rp', 'bo.no_rawat', '=', 'rp.no_rawat')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->leftJoin('ruang_ok as ro', 'bo.kd_ruang_ok', '=', 'ro.kd_ruang_ok')
                ->leftJoin('dokter as d', 'bo.kd_dokter', '=', 'd.kd_dokter')
                ->leftJoin('paket_operasi as po', 'bo.kode_paket', '=', 'po.kode_paket')
                ->leftJoin('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
                ->select(
                    'bo.no_rawat',
                    'p.nm_pasien',
                    'd.nm_dokter',
                    'ro.nm_ruang_ok',
                    'po.nm_perawatan',
                    'bo.tanggal',
                    'bo.jam_mulai',
                    'bo.jam_selesai',
                    'bo.status',
                    'rp.kd_pj',
                    'pj.png_jawab as penjamin'
                )
                ->whereDate('bo.tanggal', $today)
                ->when($kd_pj, fn($q) => $q->where('rp.kd_pj', $kd_pj))
                ->orderBy('bo.jam_mulai')
                ->get();

            // 🔸 Dropdown untuk form input
            $dokter = DB::table('dokter')->orderBy('nm_dokter')->get(['kd_dokter', 'nm_dokter']);
            $ruang_ok = DB::table('ruang_ok')->orderBy('nm_ruang_ok')->get(['kd_ruang_ok', 'nm_ruang_ok']);
            $paket_operasi = DB::table('paket_operasi')->orderBy('nm_perawatan')->get(['kode_paket', 'nm_perawatan']);
            $penjamin = DB::table('penjab')->orderBy('png_jawab')->get(['kd_pj', 'png_jawab']);

            // 🔸 Pasien yang masih dirawat (belum pulang)
            $pasien_dirawat = DB::table('kamar_inap as ki')
                ->join('reg_periksa as rp', 'ki.no_rawat', '=', 'rp.no_rawat')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->join('kamar as k', 'ki.kd_kamar', '=', 'k.kd_kamar')
                ->join('bangsal as b', 'k.kd_bangsal', '=', 'b.kd_bangsal')
                ->join('penjab as pj', 'rp.kd_pj', '=', 'pj.kd_pj')
                ->select(
                    'rp.no_rawat',
                    'p.no_rkm_medis',
                    'p.nm_pasien',
                    'b.nm_bangsal',
                    'pj.kd_pj',
                    'pj.png_jawab as penjamin',
                    'ki.tgl_masuk',
                    'ki.jam_masuk'
                )
                ->where(function ($q) {
                    $q->whereNull('ki.tgl_keluar')
                        ->orWhere('ki.tgl_keluar', '=', '0000-00-00')
                        ->orWhere('ki.tgl_keluar', '=', '');
                })
                ->orderBy('b.nm_bangsal')
                ->orderBy('ki.tgl_masuk')
                ->get();

            return view('livewire.jadwal-operasi.jadwal_operasi', compact(
                'jadwal_operasi',
                'dokter',
                'ruang_ok',
                'paket_operasi',
                'penjamin',
                'pasien_dirawat',
                'kd_pj'
            ));
        } catch (\Throwable $e) {
            Log::error('❌ Gagal load Jadwal Operasi: ' . $e->getMessage());
            return response()->view('errors.500', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔹 Simpan jadwal operasi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_rawat'     => 'required',
            'kode_paket'   => 'required',
            'tanggal'      => 'required|date',
            'jam_mulai'    => 'required',
            'jam_selesai'  => 'required',
            'status'       => 'required',
            'kd_dokter'    => 'required',
            'kd_ruang_ok'  => 'required',
            'kd_pj'        => 'required',
        ]);

        try {
            // 🔸 Cegah duplikasi
            $exists = DB::table('booking_operasi')
                ->where('no_rawat', $request->no_rawat)
                ->whereDate('tanggal', $request->tanggal)
                ->exists();

            if ($exists) {
                return back()->with('warning', '⚠️ Pasien ini sudah dijadwalkan operasi pada tanggal tersebut.');
            }

            DB::table('booking_operasi')->insert([
                'no_rawat'     => $request->no_rawat,
                'kode_paket'   => $request->kode_paket,
                'tanggal'      => $request->tanggal,
                'jam_mulai'    => $request->jam_mulai,
                'jam_selesai'  => $request->jam_selesai,
                'status'       => $request->status,
                'kd_dokter'    => $request->kd_dokter,
                'kd_ruang_ok'  => $request->kd_ruang_ok,
                'kd_pj'        => $request->kd_pj,
                'tgl_booking'  => now()->format('Y-m-d'),
                'jam_booking'  => now()->format('H:i:s'),
            ]);

            return back()->with('success', '✅ Jadwal operasi berhasil disimpan.');
        } catch (\Throwable $e) {
            Log::error('❌ Gagal simpan jadwal operasi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Update jadwal operasi
     */
    public function update(Request $request, $no_rawat)
    {
        $request->validate([
            'kode_paket'   => 'required',
            'tanggal'      => 'required|date',
            'jam_mulai'    => 'required',
            'jam_selesai'  => 'required',
            'status'       => 'required',
            'kd_dokter'    => 'required',
            'kd_ruang_ok'  => 'required',
            'kd_pj'        => 'required',
        ]);

        try {
            $updated = DB::table('booking_operasi')
                ->where('no_rawat', $no_rawat)
                ->update([
                    'kode_paket'   => $request->kode_paket,
                    'tanggal'      => $request->tanggal,
                    'jam_mulai'    => $request->jam_mulai,
                    'jam_selesai'  => $request->jam_selesai,
                    'status'       => $request->status,
                    'kd_dokter'    => $request->kd_dokter,
                    'kd_ruang_ok'  => $request->kd_ruang_ok,
                    'kd_pj'        => $request->kd_pj,
                ]);

            return back()->with(
                $updated ? 'success' : 'warning',
                $updated ? '✏️ Jadwal operasi berhasil diperbarui.' : '⚠️ Tidak ada data yang diubah.'
            );
        } catch (\Throwable $e) {
            Log::error('❌ Gagal update jadwal operasi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat update: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Hapus jadwal operasi
     */
    public function destroy($no_rawat)
    {
        try {
            DB::table('booking_operasi')->where('no_rawat', $no_rawat)->delete();
            return back()->with('success', '🗑️ Jadwal operasi berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('❌ Gagal hapus jadwal operasi: ' . $e->getMessage());
            return back()->with('error', 'Gagal hapus data: ' . $e->getMessage());
        }
    }
}
