<?php

namespace App\Http\Controllers\AntrianPendaftaran;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AntrianPendaftaranBaru extends Controller
{
    /**
     * 🩺 Halaman utama antrian pendaftaran baru
     */
    public function index(Request $request)
    {
        $tanggal = now()->format('Y-m-d');

        // Daftar dokter
        $dokters = DB::table('dokter')
            ->select('nm_dokter')
            ->orderBy('nm_dokter')
            ->get();

        // Daftar loket
        $lokets = DB::table('loket_pendaftaran')
            ->select('id', 'nama_loket', 'no_reg', 'status')
            ->orderBy('nama_loket')
            ->get();

        // Loket aktif dipilih user atau default pertama
        $loketName = $request->filled('loket')
            ? $request->loket
            : ($lokets->first()->nama_loket ?? '-');

        // Ambil baris terakhir per no_reg
        $latestStatus = DB::table('loket_pendaftaran as lp1')
            ->select('lp1.no_reg', 'lp1.status', 'lp1.nama_loket', 'lp1.nm_pasien', 'lp1.nm_dokter', 'lp1.updated_at')
            ->whereIn('lp1.id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('loket_pendaftaran')
                    ->groupBy('no_reg');
            });

        // Ambil data antrian hari ini
        $query = DB::table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->join('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->leftJoinSub($latestStatus, 'lp', function ($join) {
                $join->on('rp.no_reg', '=', 'lp.no_reg');
            })
            ->whereDate('rp.tgl_registrasi', $tanggal);

        if ($request->filled('dokter')) {
            $query->where('d.nm_dokter', $request->dokter);
        }

        $antrian = $query->select(
                'rp.no_reg',
                'p.nm_pasien',
                'd.nm_dokter',
                'rp.jam_reg as jam_mulai',
                DB::raw("COALESCE(lp.status, 'MENUNGGU') as status")
            )
            ->orderBy('rp.no_reg')
            ->get();

        // Info status loket aktif
        $statusLoket = DB::table('loket_pendaftaran')
            ->where('nama_loket', $loketName)
            ->orderByDesc('updated_at')
            ->first();

        return view('livewire.antrian-pendaftaran.antrianpendaftaranbaru', [
            'tanggal'     => $tanggal,
            'antrian'     => $antrian,
            'dokters'     => $dokters,
            'lokets'      => $lokets,
            'loketName'   => $loketName,
            'statusLoket' => $statusLoket,
        ]);
    }

    /**
     * 📢 Update status jadi DIPANGGIL
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
            // Insert baru, jangan update
            DB::table('loket_pendaftaran')->insert([
                'nama_loket' => $validated['nama_loket'],
                'no_reg'     => $validated['no_reg'],
                'nm_pasien'  => $validated['nm_pasien'],
                'nm_dokter'  => $validated['nm_dokter'],
                'status'     => 'DIPANGGIL',
                'updated_at' => now(),
            ]);

            return back()->with('success', '📢 Pasien telah dipanggil.');
        } catch (\Throwable $e) {
            Log::error('Update Loket Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui status.');
        }
    }

    /**
     * ✅ Tandai antrian sudah selesai
     */
    public function selesai(Request $request)
    {
        $request->validate([
            'no_reg' => 'required|string',
        ]);

        // Insert baru status SELESAI
        $latest = DB::table('loket_pendaftaran')
            ->where('no_reg', $request->no_reg)
            ->latest('updated_at')
            ->first();

        if ($latest) {
            DB::table('loket_pendaftaran')->insert([
                'nama_loket' => $latest->nama_loket,
                'no_reg'     => $latest->no_reg,
                'nm_pasien'  => $latest->nm_pasien,
                'nm_dokter'  => $latest->nm_dokter,
                'status'     => 'SELESAI',
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', '✅ Antrian telah ditandai selesai.');
    }

    /**
     * 📁 Ambil status gangguan BPJS TV dari file penyimpanan
     */
    public static function getBpjsTvStatus()
    {
        $filePath = storage_path('app/bpjs_tv_status.json');
        $default = [
            'is_gangguan' => false,
            'judul' => 'PEMBERITAHUAN GANGGUAN SISTEM BPJS KESEHATAN',
            'pesan' => 'Mohon maaf atas ketidaknyamanannya. Saat ini sistem / server BPJS Kesehatan pusat sedang mengalami kendala teknis jaringan.',
            'sub_pesan' => 'Pelayanan pendaftaran tetap dilayani secara manual oleh petugas loket pendaftaran. Silakan menunggu antrian Anda dipanggil.',
            'running_text' => 'PENGUMUMAN RESMI: SISTEM BRIDGING BPJS KESEHATAN SEDANG MENGALAMI GANGGUAN TEKNIS. PENDAFTARAN PASIEN TETAP DILAYANI SECARA MANUAL DI LOKET PENDAFTARAN. TERIMA KASIH ATAS KESABARAN ANDA 🙏',
            'updated_at' => now()->format('d/m/Y H:i:s'),
            'updated_by' => 'Sistem',
        ];

        if (file_exists($filePath)) {
            $content = @file_get_contents($filePath);
            if ($content) {
                $data = json_decode($content, true);
                if (is_array($data)) {
                    return array_merge($default, $data);
                }
            }
        }

        return $default;
    }

    /**
     * 💾 Simpan status gangguan BPJS TV ke file penyimpanan
     */
    public static function setBpjsTvStatus(array $data)
    {
        $current = self::getBpjsTvStatus();
        $updated = array_merge($current, $data);
        $dir = storage_path('app');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents(storage_path('app/bpjs_tv_status.json'), json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $updated;
    }

    /**
     * 📺 Halaman tampilan TV antrian
     */
    public function displayTv()
    {
        $antrian = DB::table('loket_pendaftaran as lp')
            ->select('lp.no_reg', 'lp.status', 'lp.nama_loket', 'lp.nm_pasien', 'lp.nm_dokter')
            ->whereIn('lp.id', function ($query) {
                $query->selectRaw('MAX(id)')
                      ->from('loket_pendaftaran')
                      ->groupBy('no_reg');
            })
            ->orderByRaw("
                CASE
                    WHEN lp.status = 'DIPANGGIL' THEN 0
                    WHEN lp.status = 'MENUNGGU' THEN 1
                    WHEN lp.status = 'SELESAI' THEN 2
                END ASC
            ")
            ->orderBy('lp.updated_at', 'desc')
            ->get();

        $bpjsStatus = self::getBpjsTvStatus();

        return view('livewire.antrian-pendaftaran.displaypendaftaranbaru', compact('antrian', 'bpjsStatus'));
    }

    /**
     * 📡 API JSON untuk TV (termasuk status BPJS)
     */
    public function apiTv()
    {
        $antrian = DB::table('loket_pendaftaran as lp')
            ->select('lp.no_reg', 'lp.status', 'lp.nama_loket', 'lp.nm_pasien', 'lp.nm_dokter')
            ->whereIn('lp.id', function ($query) {
                $query->selectRaw('MAX(id)')
                      ->from('loket_pendaftaran')
                      ->groupBy('no_reg');
            })
            ->orderByRaw("
                CASE
                    WHEN lp.status = 'DIPANGGIL' THEN 0
                    WHEN lp.status = 'MENUNGGU' THEN 1
                    WHEN lp.status = 'SELESAI' THEN 2
                END ASC
            ")
            ->orderBy('lp.updated_at', 'desc')
            ->get();

        $bpjsStatus = self::getBpjsTvStatus();

        return response()->json([
            'antrian' => $antrian,
            'bpjs'    => $bpjsStatus,
        ]);
    }

    /**
     * 📡 API JSON khusus status BPJS saja
     */
    public function apiBpjsStatus()
    {
        return response()->json(self::getBpjsTvStatus());
    }

    /**
     * ⚙️ Halaman menu manajemen status BPJS TV
     */
    public function settingBpjsView()
    {
        $status = self::getBpjsTvStatus();
        return view('antrian.status-bpjs-tv', compact('status'));
    }

    /**
     * 🔄 Update pengaturan status & pesan pengumuman BPJS TV
     */
    public function updateBpjsStatus(Request $request)
    {
        $request->validate([
            'judul' => 'required|string',
            'pesan' => 'required|string',
        ]);

        $isGangguan = $request->boolean('is_gangguan');
        $userObj = session('user') ?? session('auth');
        $userName = is_object($userObj) ? ($userObj->nama ?? $userObj->username ?? 'Petugas') : (is_array($userObj) ? ($userObj['nama'] ?? $userObj['id_user'] ?? 'Petugas') : 'Petugas');

        $data = [
            'is_gangguan'  => $isGangguan,
            'judul'        => $request->input('judul'),
            'pesan'        => $request->input('pesan'),
            'sub_pesan'    => $request->input('sub_pesan', ''),
            'running_text' => $request->input('running_text', ''),
            'updated_at'   => now()->format('d/m/Y H:i:s'),
            'updated_by'   => $userName,
        ];

        $saved = self::setBpjsTvStatus($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status dan pesan BPJS TV berhasil diperbarui!',
                'data'    => $saved,
            ]);
        }

        return redirect()->back()->with('success', 'Status dan pesan BPJS TV berhasil diperbarui.');
    }

    /**
     * ⚡ Quick Toggle status gangguan BPJS TV (ON/OFF)
     */
    public function toggleBpjsStatus(Request $request)
    {
        $current = self::getBpjsTvStatus();
        $newStatus = !$current['is_gangguan'];

        $userObj = session('user') ?? session('auth');
        $userName = is_object($userObj) ? ($userObj->nama ?? $userObj->username ?? 'Petugas') : (is_array($userObj) ? ($userObj['nama'] ?? $userObj['id_user'] ?? 'Petugas') : 'Petugas');

        $updated = self::setBpjsTvStatus([
            'is_gangguan' => $newStatus,
            'updated_at'  => now()->format('d/m/Y H:i:s'),
            'updated_by'  => $userName,
        ]);

        return response()->json([
            'success'     => true,
            'is_gangguan' => $newStatus,
            'message'     => $newStatus 
                ? 'Mode Gangguan BPJS diaktifkan (Layar Penuh di TV)' 
                : 'Mode Gangguan BPJS dinonaktifkan (TV Antrian Normal)',
            'data'        => $updated,
        ]);
    }
}
