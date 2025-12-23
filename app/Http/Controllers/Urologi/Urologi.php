<?php

namespace App\Http\Controllers\Urologi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DNS2D;

class Urologi extends Controller
{

    // LIST PASIEN UROLOGI

    public function index(Request $request)
    {
        $tanggalMulai   = $request->tanggal_mulai ?? date('Y-m-d');
        $tanggalSelesai = $request->tanggal_selesai ?? date('Y-m-d');

        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->whereIn('poliklinik.kd_poli', ['U0017', 'U0059', 'U0060'])
            ->whereBetween('reg_periksa.tgl_registrasi', [
                $tanggalMulai,
                $tanggalSelesai
            ])
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'poliklinik.nm_poli'
            )
            ->orderBy('reg_periksa.tgl_registrasi')
            ->orderByRaw("CAST(SUBSTRING(reg_periksa.no_rawat, -6) AS UNSIGNED)")
            ->get();

        return view('urologi.urologi', compact(
            'data',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }




    public function formUsg(Request $request)
    {
        $no_rawat = $request->no_rawat;

        $pasien = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien'
            )
            ->first();

        return view('urologi.form_usg', compact('pasien'));
    }

    // SIMPAN HASIL USG

    public function simpanUsg(Request $request)
    {
        $request->validate([
            'no_rawat'     => 'required',
            'no_rkm_medis' => 'required',
            'hasil_usg'    => 'required',
        ]);

        $cek = DB::table('hasil_usg_urologi')
            ->where('no_rawat', $request->no_rawat)
            ->exists();

        if ($cek) {
            return redirect()->back()
                ->with('error', 'Hasil USG untuk nomor rawat ini sudah pernah disimpan');
        }

        DB::table('hasil_usg_urologi')->insert([
            'no_rawat'     => $request->no_rawat,
            'no_rkm_medis' => $request->no_rkm_medis,
            'hasil_usg'    => $request->hasil_usg,
            'created_at'   => now()
        ]);

        return redirect()->back()
            ->with('success', 'Hasil USG berhasil disimpan');
    }

    

    public function cetakUsg($no_rawat)
    {
        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('hasil_usg_urologi', 'hasil_usg_urologi.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->crossJoin('setting')
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->select(
                'setting.nama_instansi',
                'setting.alamat_instansi',
                'setting.kabupaten',
                'setting.propinsi',
                'setting.kontak',
                'setting.email',
                'setting.logo',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'reg_periksa.umurdaftar',
                'pasien.jk',
                'reg_periksa.no_rawat',
                'hasil_usg_urologi.created_at',
                'poliklinik.nm_poli',
                'hasil_usg_urologi.hasil_usg',
                'dokter.nm_dokter'
            )
            ->first();

        if (!$data) {
            abort(404);
        }

        /* =========================
       QR CODE (LEGAL TEXT)
    ========================= */
        $qrText =
            'Dikeluarkan di ' . $data->nama_instansi .
            ', ' . $data->kabupaten .
            ' Ditandatangani secara elektronik oleh ' .
            $data->nm_dokter .
            ' pada ' . date('d-m-Y H:i', strtotime($data->created_at));

        $qrBase64 = DNS2D::getBarcodePNG($qrText, 'QRCODE');

        return view('urologi.cetak_usg_urologi', compact('data', 'qrBase64'));
    }
}
