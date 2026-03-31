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

            // Skip jika sudah ada
            $existing = DB::table('berkas_pegawai')
                ->where('nik', $nik)
                ->where('kode_berkas', $kode)
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            $fileName = $file->getClientOriginalName();

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
}
