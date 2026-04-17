<?php

namespace App\Http\Controllers\BerkasPegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BerkasPegawaiController extends Controller
{
    /**
     * Nama berkas yang hanya bisa diupload/dilihat oleh admin
     */
    private $restrictedNamaBerkas = [
        'Hasil Tes Wawancara',
        'Hasil Tes Tertulis',
        'Hasil Tes Praktik',
        'Hasil Tes Kesehatan',
        'Penilaian Kinerja Masa Percobaan',
        'Berkas Re-Kredensial',
    ];
    /**
     * Halaman utama berkas pegawai
     */
    public function index()
    {
        $nik = session('auth')['id_user'];

        $pegawai = DB::table('pegawai')
            ->select('pegawai.nik', 'pegawai.nama', 'pegawai.jk', 'pegawai.tmp_lahir', 'pegawai.tgl_lahir', 'pegawai.photo')
            ->where('pegawai.nik', $nik)
            ->first();

        $masterBerkas = DB::table('master_berkas_pegawai')
            ->orderBy('kategori')
            ->orderBy('no_urut')
            ->get();

        // Filter: sembunyikan berkas restricted dari karyawan
        $masterBerkas = $masterBerkas->filter(function ($item) {
            return !in_array($item->nama_berkas, $this->restrictedNamaBerkas);
        });

        $kategoriList = $masterBerkas->pluck('kategori')->unique()->values();
        $masterBerkasGrouped = $masterBerkas->groupBy('kategori');

        $berkas = DB::table('berkas_pegawai')
            ->join('master_berkas_pegawai', 'berkas_pegawai.kode_berkas', '=', 'master_berkas_pegawai.kode')
            ->select(
                'berkas_pegawai.nik',
                'berkas_pegawai.tgl_uploud',
                'berkas_pegawai.kode_berkas',
                'berkas_pegawai.berkas',
                'master_berkas_pegawai.nama_berkas',
                'master_berkas_pegawai.kategori',
                'master_berkas_pegawai.no_urut'
            )
            ->where('berkas_pegawai.nik', $nik)
            ->whereNotIn('master_berkas_pegawai.nama_berkas', $this->restrictedNamaBerkas)
            ->orderBy('master_berkas_pegawai.kategori')
            ->orderBy('master_berkas_pegawai.no_urut')
            ->get();

        return view('berkas-pegawai.index', compact('pegawai', 'berkas', 'kategoriList', 'masterBerkasGrouped'));
    }

    /**
     * Upload berkas pegawai via SFTP
     */
    public function upload(Request $request)
    {
        $request->validate([
            'kode_berkas'   => 'required|array',
            'kode_berkas.*' => 'required|string',
            'files'         => 'required|array',
            'files.*'       => 'required|file|max:5120',
        ]);

        $nik        = session('auth')['id_user'];
        $kodeBerkas = $request->kode_berkas;
        $files      = $request->file('files');

        $uploaded = 0;
        $skipped  = 0;

        foreach ($files as $index => $file) {
            $kode = $kodeBerkas[$index] ?? null;
            if (!$kode || !$file) continue;

            // Cek apakah berkas restricted (hanya admin boleh upload)
            $master = DB::table('master_berkas_pegawai')
                ->where('kode', $kode)
                ->first();

            if ($master && in_array($master->nama_berkas, $this->restrictedNamaBerkas)) {
                $skipped++;
                continue;
            }

            // Skip jika sudah ada
            $existing = DB::table('berkas_pegawai')
                ->where('nik', $nik)
                ->where('kode_berkas', $kode)
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            $master = DB::table('master_berkas_pegawai')
                ->where('kode', $kode)
                ->first();

            $noUrut    = $master ? $master->no_urut : '00';
            $extension = $file->getClientOriginalExtension();
            $fileName  = $nik . '_' . $kode . '_' . $noUrut . '.' . $extension;

            try {
                Storage::disk('sftp_berkas')->put($fileName, file_get_contents($file));
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Gagal upload ke server: ' . $e->getMessage());
            }

            DB::table('berkas_pegawai')->insert([
                'nik'         => $nik,
                'tgl_uploud'  => Carbon::now()->format('Y-m-d'),
                'kode_berkas' => $kode,
                'berkas'      => 'pages/berkaspegawai/berkas/' . $fileName,
            ]);

            $uploaded++;
        }

        $msg = "$uploaded berkas berhasil diupload";
        if ($skipped > 0) {
            $msg .= ", $skipped dilewati (sudah ada)";
        }

        return redirect()->route('berkas.pegawai')
            ->with('success', $msg);
    }

    /**
     * Hapus berkas pegawai via SFTP
     */
    public function destroy(Request $request)
    {
        $nik         = session('auth')['id_user'];
        $kode_berkas = $request->kode_berkas;

        $berkas = DB::table('berkas_pegawai')
            ->where('nik', $nik)
            ->where('kode_berkas', $kode_berkas)
            ->first();

        if ($berkas) {
            try {
                $fileName = basename($berkas->berkas);
                Storage::disk('sftp_berkas')->delete($fileName);
            } catch (\Exception $e) {
                // file mungkin sudah tidak ada
            }

            DB::table('berkas_pegawai')
                ->where('nik', $nik)
                ->where('kode_berkas', $kode_berkas)
                ->delete();
        }

        return redirect()->route('berkas.pegawai')
            ->with('success', 'Berkas berhasil dihapus');
    }

    /**
     * NIK yang diizinkan mengakses semua berkas pegawai
     */
    private $allowedNikSemuaBerkas = [
        '06893020003',
        '0206020143',
        '1121020577',
        '01091999',
        '0525020755',
    ];

    /**
     * Menampilkan daftar pegawai beserta jumlah berkasnya (Untuk admin/semua)
     */
    public function semuaBerkas()
    {
        $currentNik = session('auth')['id_user'];

        if (!in_array($currentNik, $this->allowedNikSemuaBerkas)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // $pegawaiList = DB::table('pegawai')
        //     ->select('pegawai.nik', 'pegawai.nama', 'pegawai.jk')
        //     ->leftJoin('berkas_pegawai', 'pegawai.nik', '=', 'berkas_pegawai.nik')
        //     ->selectRaw('COUNT(berkas_pegawai.kode_berkas) as jumlah_berkas')
        //     ->groupBy('pegawai.nik', 'pegawai.nama', 'pegawai.jk')
        //     ->orderBy('pegawai.nama')
        //     ->get();

        $pegawaiList = DB::table('pegawai')
            ->leftJoin('petugas', 'pegawai.nik', '=', 'petugas.nip')
            ->leftJoin('dokter', 'pegawai.nik', '=', 'dokter.kd_dokter')
            ->leftJoin('berkas_pegawai', 'pegawai.nik', '=', 'berkas_pegawai.nik')
            ->select(
                'pegawai.nik',
                'pegawai.nama',
                'pegawai.jk',
                'pegawai.photo',
                DB::raw('COUNT(berkas_pegawai.kode_berkas) as jumlah_berkas')
            )
            ->where(function ($q) {
                $q->where('petugas.status', '1')
                  ->orWhere('dokter.kd_dokter', '!=', null);
            })
            ->groupBy('pegawai.nik', 'pegawai.nama', 'pegawai.jk')
            ->orderBy('pegawai.nama')
            ->get();

        $masterBerkas = DB::table('master_berkas_pegawai')
            ->orderBy('kategori')
            ->orderBy('no_urut')
            ->get();

        $kategoriList = $masterBerkas->pluck('kategori')->unique()->values();
        $masterBerkasGrouped = $masterBerkas->groupBy('kategori');

        return view('berkas-pegawai.semua', compact('pegawaiList', 'kategoriList', 'masterBerkasGrouped'));
    }

    /**
     * Menampilkan detail berkas untuk 1 pegawai
     */
    public function detailBerkas($nik)
    {
        $currentNik = session('auth')['id_user'];

        if (!in_array($currentNik, $this->allowedNikSemuaBerkas)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $pegawai = DB::table('pegawai')
            ->select('pegawai.nik', 'pegawai.nama', 'pegawai.jk', 'pegawai.tmp_lahir', 'pegawai.tgl_lahir', 'pegawai.photo')
            ->where('pegawai.nik', $nik)
            ->first();

        if (!$pegawai) {
            abort(404, 'Pegawai tidak ditemukan');
        }

        $berkas = DB::table('berkas_pegawai')
            ->join('master_berkas_pegawai', 'berkas_pegawai.kode_berkas', '=', 'master_berkas_pegawai.kode')
            ->select(
                'berkas_pegawai.nik',
                'berkas_pegawai.tgl_uploud',
                'berkas_pegawai.kode_berkas',
                'berkas_pegawai.berkas',
                'master_berkas_pegawai.nama_berkas',
                'master_berkas_pegawai.kategori',
                'master_berkas_pegawai.no_urut'
            )
            ->where('berkas_pegawai.nik', $nik)
            ->orderBy('master_berkas_pegawai.kategori')
            ->orderBy('master_berkas_pegawai.no_urut')
            ->get();

        return view('berkas-pegawai.detail', compact('pegawai', 'berkas'));
    }

    /**
     * Upload berkas untuk pegawai tertentu (oleh admin)
     */
    public function uploadForPegawai(Request $request)
    {
        $currentNik = session('auth')['id_user'];

        if (!in_array($currentNik, $this->allowedNikSemuaBerkas)) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $request->validate([
            'nik'           => 'required|string',
            'kode_berkas'   => 'required|array',
            'kode_berkas.*' => 'required|string',
            'files'         => 'required|array',
            'files.*'       => 'required|file|max:5120',
        ]);

        $nik        = $request->nik;
        $kodeBerkas = $request->kode_berkas;
        $files      = $request->file('files');

        $uploaded = 0;
        $skipped  = 0;

        foreach ($files as $index => $file) {
            $kode = $kodeBerkas[$index] ?? null;
            if (!$kode || !$file) continue;

            $existing = DB::table('berkas_pegawai')
                ->where('nik', $nik)
                ->where('kode_berkas', $kode)
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            $master = DB::table('master_berkas_pegawai')
                ->where('kode', $kode)
                ->first();

            $noUrut    = $master ? $master->no_urut : '00';
            $extension = $file->getClientOriginalExtension();
            $fileName  = $nik . '_' . $kode . '_' . $noUrut . '.' . $extension;

            try {
                Storage::disk('sftp_berkas')->put($fileName, file_get_contents($file));
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Gagal upload ke server: ' . $e->getMessage());
            }

            DB::table('berkas_pegawai')->insert([
                'nik'         => $nik,
                'tgl_uploud'  => Carbon::now()->format('Y-m-d'),
                'kode_berkas' => $kode,
                'berkas'      => 'pages/berkaspegawai/berkas/' . $fileName,
            ]);

            $uploaded++;
        }

        $msg = "$uploaded berkas berhasil diupload";
        if ($skipped > 0) {
            $msg .= ", $skipped dilewati (sudah ada)";
        }

        return redirect()->route('berkas.pegawai.semua')
            ->with('success', $msg);
    }

    /**
     * Hapus berkas pegawai tertentu (oleh admin)
     */
    public function destroyForPegawai(Request $request)
    {
        $currentNik = session('auth')['id_user'];

        if (!in_array($currentNik, $this->allowedNikSemuaBerkas)) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $nik         = $request->nik;
        $kode_berkas = $request->kode_berkas;

        $berkas = DB::table('berkas_pegawai')
            ->where('nik', $nik)
            ->where('kode_berkas', $kode_berkas)
            ->first();

        if ($berkas) {
            try {
                $fileName = basename($berkas->berkas);
                Storage::disk('sftp_berkas')->delete($fileName);
            } catch (\Exception $e) {
                // file mungkin sudah tidak ada
            }

            DB::table('berkas_pegawai')
                ->where('nik', $nik)
                ->where('kode_berkas', $kode_berkas)
                ->delete();
        }

        return redirect()->route('berkas.pegawai.detail', $nik)
            ->with('success', 'Berkas berhasil dihapus');
    }
}
