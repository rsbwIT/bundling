<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Listnama extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $tgl1 = $request->input('tgl1', $tanggal ?? date('Y-m-d'));
        $tgl2 = $request->input('tgl2', $tanggal ?? date('Y-m-d'));
        $statusLanjut = $request->input('status_lanjut', '');

        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'pasien.no_ktp',
                'pasien.alamat',
                'reg_periksa.status_lanjut'
            )
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2]);

        if ($statusLanjut) {
            $query->where('reg_periksa.status_lanjut', $statusLanjut);
        }

        $pasien = $query->orderBy('reg_periksa.no_rawat')->get();

        return view('surat.listnama', compact('pasien', 'tgl1', 'tgl2', 'statusLanjut'));
    }

    public function suratKeteranganDokter(Request $request)
    {
        $no_rawat = $request->input('no_rawat');

        if (!$no_rawat) {
            return redirect()->back()->with('error', 'No. Rawat wajib diisi');
        }

        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.umur',
                'pasien.tgl_lahir',
                'pasien.no_ktp',
                'pasien.alamat',
                'dokter.nm_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->first();

        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Cek apakah surat sudah pernah disimpan
        $saved = DB::table('nomor_surat_sdm')->where('no_rawat', $no_rawat)
            ->where('jenis_surat', 'SKD')->first();

        if ($saved) {
            $nomor_surat = $saved->no_surat;
            $isi_surat   = json_decode($saved->isi_surat, true) ?? [];
        } else {
            // Preview nomor surat (belum disimpan ke DB)
            $nomor_surat = $this->previewNomorSurat('SKD');
            $isi_surat   = [];
        }

        return view()->file(resource_path('views/surat/ket.dokter.blade.php'), compact('data', 'nomor_surat', 'isi_surat'));
    }

    public function suratKeteranganVaksin(Request $request)
    {
        $no_rawat = $request->input('no_rawat');

        if (!$no_rawat) {
            return redirect()->back()->with('error', 'No. Rawat wajib diisi');
        }

        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.umur',
                'pasien.tgl_lahir',
                'pasien.no_ktp',
                'pasien.alamat',
                'dokter.nm_dokter',
                'poliklinik.nm_poli'
            )
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->first();

        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Cek apakah surat sudah pernah disimpan
        $saved = DB::table('nomor_surat_sdm')->where('no_rawat', $no_rawat)
            ->where('jenis_surat', 'SKV')->first();

        if ($saved) {
            $nomor_surat = $saved->no_surat;
            $isi_surat   = json_decode($saved->isi_surat, true) ?? [];
        } else {
            // Preview nomor surat (belum disimpan ke DB)
            $nomor_surat = $this->previewNomorSurat('SKV');
            $isi_surat   = [];
        }

        return view()->file(resource_path('views/surat/ket.vaksin.blade.php'), compact('data', 'nomor_surat', 'isi_surat'));
    }

    /**
     * Preview nomor surat (hitung nomor berikutnya TANPA simpan ke DB)
     */
    private function previewNomorSurat($jenis_surat)
    {
        $tahun = date('Y');
        $bulan = date('m');

        $last = DB::table('nomor_surat_sdm')->where('jenis_surat', $jenis_surat)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderByDesc('id')
            ->first();

        $nomorUrut = 1;
        if ($last) {
            $parts = explode('/', $last->no_surat);
            $nomorUrut = (int)$parts[0] + 1;
        }

        return sprintf("%d/RSBW/%s/%s", $nomorUrut, $jenis_surat, $tahun);
    }

    /**
     * Simpan surat: CREATE record baru (nomor + isi) atau UPDATE isi jika sudah ada
     */
    public function simpanIsiSurat(Request $request)
    {
        try {
            $no_rawat    = $request->no_rawat;
            $jenis_surat = $request->jenis_surat;
            $isi_surat   = $request->isi_surat;

            if (!$no_rawat || !$jenis_surat) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }

            // Cek apakah sudah pernah disimpan
            $record = DB::table('nomor_surat_sdm')->where('no_rawat', $no_rawat)
                ->where('jenis_surat', $jenis_surat)
                ->first();

            if ($record) {
                // UPDATE isi surat saja
                DB::table('nomor_surat_sdm')->where('id', $record->id)->update([
                    'isi_surat'  => json_encode($isi_surat),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                return response()->json([
                    'status'  => true,
                    'message' => 'Isi surat berhasil diupdate',
                    'no_surat' => $record->no_surat
                ]);
            }

            // CREATE baru: generate nomor surat + simpan isi sekaligus
            $nomorSurat = $this->previewNomorSurat($jenis_surat);

            DB::table('nomor_surat_sdm')->insert([
                'no_surat'    => $nomorSurat,
                'jenis_surat' => $jenis_surat,
                'tanggal'     => date('Y-m-d'),
                'no_rawat'    => $no_rawat,
                'isi_surat'   => json_encode($isi_surat),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Surat berhasil disimpan dengan nomor: ' . $nomorSurat,
                'no_surat' => $nomorSurat
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'ERROR: ' . $e->getMessage()
            ], 500);
        }
    }

    public function suratTerisi(Request $request)
    {
        $cari = $request->input('cari');
        $tgl1 = $request->input('tgl1', date('Y-m-d'));
        $tgl2 = $request->input('tgl2', date('Y-m-d'));

        $query = DB::table('nomor_surat_sdm as nss')
            ->leftJoin('reg_periksa as rp', 'nss.no_rawat', '=', 'rp.no_rawat')
            ->leftJoin('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
            ->select(
                'nss.id',
                'nss.no_rawat',
                'nss.no_surat',
                'nss.jenis_surat',
                'nss.tanggal',
                'p.nm_pasien',
                'd.nm_dokter'
            )
            ->whereBetween('nss.tanggal', [$tgl1, $tgl2]);

        if ($cari) {
            $query->where(function($q) use ($cari) {
                $q->where('nss.no_surat', 'like', "%{$cari}%")
                  ->orWhere('nss.no_rawat', 'like', "%{$cari}%")
                  ->orWhere('p.nm_pasien', 'like', "%{$cari}%");
            });
        }

        $data = $query->orderByDesc('nss.id')->get();

        return view('surat.daftar_terisi', compact('data', 'cari', 'tgl1', 'tgl2'));
    }

    /**
     * Hapus record surat terisi
     */
    public function hapusSuratTerisi($id)
    {
        try {
            $surat = DB::table('nomor_surat_sdm')->where('id', $id)->first();
            if ($surat) {
                DB::table('nomor_surat_sdm')->where('id', $id)->delete();
            }
            return response()->json([
                'status' => true,
                'message' => 'Surat berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR: ' . $e->getMessage()
            ], 500);
        }
    }
}
