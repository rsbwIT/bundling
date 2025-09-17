<?php

namespace App\Http\Controllers\SuratBiometrik\Formulir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Sep_TTD extends Controller
{
    public function form($no_sep)
    {
        $sep = DB::table('bridging_sep')
            ->join('reg_periksa', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('sep_ttd', 'sep_ttd.no_sep', '=', 'bridging_sep.no_sep')
            ->select(
                'bridging_sep.no_sep',
                'pasien.nm_pasien',
                'sep_ttd.ttd as ttd'
            )
            ->where('bridging_sep.no_sep', $no_sep)
            ->first();

        return view('suratbiometrik.formulir.sep_ttd', compact('sep'));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'no_sep' => 'required',
            'nama'   => 'required',
            'ttd'    => 'required',
        ]);

        // Ambil base64
        $image = $request->ttd;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        // Nama file unik
        $imageName = 'ttd_sep_' . $request->no_sep . '_' . time() . '.png';

        // Pastikan folder ada
        if (!Storage::disk('public')->exists('ttd')) {
            Storage::disk('public')->makeDirectory('ttd');
        }

        // Simpan file
        Storage::disk('public')->put('ttd/' . $imageName, base64_decode($image));

        // Simpan atau update ke tabel sep_ttd
        DB::table('sep_ttd')->updateOrInsert(
            ['no_sep' => $request->no_sep],
            [
                'nama'       => $request->nama,
                'ttd'        => $imageName,
                'created_at' => now(),
            ]
        );

        return redirect()
            ->route('sep.formTtd', $request->no_sep)
            ->with('success', 'Tanda tangan berhasil disimpan.');
    }
}
