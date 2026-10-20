<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GambaranDarahTepiController extends Controller
{
    /**
     * Tampilkan daftar permintaan lab yang ada pemeriksaan 'Gambaran Darah Tepi'
     */
    public function index(Request $request)
    {
        $tgl1 = $request->get('tgl1', date('Y-m-d', strtotime('-30 days')));
        $tgl2 = $request->get('tgl2', date('Y-m-d'));
        $cari = $request->get('cari');

        $query = DB::table('permintaan_lab')
            ->join('permintaan_pemeriksaan_lab', 'permintaan_lab.noorder', '=', 'permintaan_pemeriksaan_lab.noorder')
            ->join('jns_perawatan_lab', 'permintaan_pemeriksaan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->join('reg_periksa', 'permintaan_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('dokter', 'permintaan_lab.dokter_perujuk', '=', 'dokter.kd_dokter')
            ->leftJoin('bw_hasil_gambaran_darah_tepi as hasil', 'permintaan_lab.noorder', '=', 'hasil.noorder')
            ->where('jns_perawatan_lab.nm_perawatan', 'like', '%gambaran darah tepi%')
            ->whereBetween('permintaan_lab.tgl_permintaan', [$tgl1, $tgl2])
            ->select(
                'permintaan_lab.noorder',
                'permintaan_lab.no_rawat',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'permintaan_lab.status as status_lanjut',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'dokter.nm_dokter as dokter_pengirim',
                'jns_perawatan_lab.nm_perawatan',
                'hasil.id as id_hasil',
                'hasil.tanggal as tgl_hasil_isi',
                'hasil.nm_dokter_pemeriksa'
            );

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('permintaan_lab.noorder', 'like', "%$cari%")
                  ->orWhere('permintaan_lab.no_rawat', 'like', "%$cari%")
                  ->orWhere('pasien.no_rkm_medis', 'like', "%$cari%")
                  ->orWhere('pasien.nm_pasien', 'like', "%$cari%");
            });
        }

        $list = $query->groupBy('permintaan_lab.noorder')
            ->orderBy('permintaan_lab.tgl_permintaan', 'desc')
            ->orderBy('permintaan_lab.jam_permintaan', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('lab.gambaran_darah_tepi.index', compact('list', 'tgl1', 'tgl2', 'cari'));
    }

    /**
     * Tampilkan form isian hasil pemeriksaan
     */
    public function form($noorder)
    {
        $permintaan = DB::table('permintaan_lab')
            ->join('reg_periksa', 'permintaan_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('dokter as dokter_pengirim', 'permintaan_lab.dokter_perujuk', '=', 'dokter_pengirim.kd_dokter')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('permintaan_lab.noorder', $noorder)
            ->select(
                'permintaan_lab.noorder',
                'permintaan_lab.no_rawat',
                'permintaan_lab.tgl_permintaan',
                'permintaan_lab.jam_permintaan',
                'reg_periksa.status_lanjut',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'dokter_pengirim.kd_dokter as kd_dokter_pengirim',
                'dokter_pengirim.nm_dokter as nm_dokter_pengirim',
                'poliklinik.nm_poli'
            )
            ->first();

        if (!$permintaan) {
            return redirect()->back()->with('error', 'Data permintaan lab tidak ditemukan.');
        }

        // Cari ruang rawat jika rawat inap
        $ruang = '-';
        if ($permintaan->status_lanjut == 'Ranap') {
            $kamar = DB::table('kamar_inap')
                ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->where('kamar_inap.no_rawat', $permintaan->no_rawat)
                ->orderBy('kamar_inap.tgl_masuk', 'desc')
                ->value('bangsal.nm_bangsal');
            $ruang = $kamar ?: $permintaan->nm_poli;
        } else {
            $ruang = $permintaan->nm_poli;
        }

        // Ambil data hasil jika sudah pernah disimpan
        $hasil = DB::table('bw_hasil_gambaran_darah_tepi')->where('noorder', $noorder)->first();

        // Daftar dokter untuk pemeriksa (default Sp.PK)
        $dokterList = DB::table('dokter')->where('status', '1')->orderBy('nm_dokter')->get();

        // Setting RS
        $setting = DB::table('setting')->first();

        return view('lab.gambaran_darah_tepi.form', compact('permintaan', 'ruang', 'hasil', 'dokterList', 'setting'));
    }

    /**
     * Simpan hasil pemeriksaan
     */
    public function store(Request $request, $noorder)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'no_rawat' => 'required',
        ]);

        $dokter = DB::table('dokter')->where('kd_dokter', $request->kd_dokter_pemeriksa)->first();
        $nm_dokter = $dokter ? $dokter->nm_dokter : $request->nm_dokter_pemeriksa;

        $data = [
            'noorder'              => $noorder,
            'no_rawat'              => $request->no_rawat,
            'tanggal'               => $request->tanggal,
            'kd_dokter_pemeriksa'   => $request->kd_dokter_pemeriksa,
            'nm_dokter_pemeriksa'   => $nm_dokter,
            'eritrosit'             => $request->eritrosit,
            'leukosit'              => $request->leukosit,
            'trombosit'             => $request->trombosit,
            'kesan'                 => $request->kesan,
            'kesimpulan'            => $request->kesimpulan,
            'saran'                 => $request->saran,
            'manual_diff'           => $request->manual_diff,
            'wbc'                   => $request->wbc,
            'hb'                    => $request->hb,
            'plt'                   => $request->plt,
            'keterangan_tambahan'   => $request->keterangan_tambahan,
            'updated_at'            => now(),
        ];

        $cek = DB::table('bw_hasil_gambaran_darah_tepi')->where('noorder', $noorder)->first();

        if ($cek) {
            DB::table('bw_hasil_gambaran_darah_tepi')->where('noorder', $noorder)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('bw_hasil_gambaran_darah_tepi')->insert($data);
        }

        return redirect()->route('lab.darah-tepi.form', $noorder)->with('success', 'Hasil Gambaran Darah Tepi berhasil disimpan.');
    }

    /**
     * Hapus hasil pemeriksaan yang sudah diisi
     */
    public function destroy($noorder)
    {
        DB::table('bw_hasil_gambaran_darah_tepi')->where('noorder', $noorder)->delete();
        return redirect()->route('lab.darah-tepi.form', $noorder)->with('success', 'Data hasil pemeriksaan berhasil dihapus.');
    }

    /**
     * Cetak hasil pemeriksaan sesuai format lembar fisik
     */
    public function cetak($noorder)
    {
        $hasil = DB::table('bw_hasil_gambaran_darah_tepi')->where('noorder', $noorder)->first();
        if (!$hasil) {
            return redirect()->back()->with('error', 'Hasil pemeriksaan belum diisi/disimpan.');
        }

        $permintaan = DB::table('permintaan_lab')
            ->join('reg_periksa', 'permintaan_lab.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('dokter as dokter_pengirim', 'permintaan_lab.dokter_perujuk', '=', 'dokter_pengirim.kd_dokter')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('permintaan_lab.noorder', $noorder)
            ->select(
                'permintaan_lab.noorder',
                'permintaan_lab.no_rawat',
                'permintaan_lab.tgl_permintaan',
                'reg_periksa.status_lanjut',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.tgl_lahir',
                'pasien.jk',
                'dokter_pengirim.nm_dokter as dokter_pengirim',
                'poliklinik.nm_poli'
            )
            ->first();

        // Ruang
        $ruang = '-';
        if ($permintaan->status_lanjut == 'Ranap') {
            $kamar = DB::table('kamar_inap')
                ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->where('kamar_inap.no_rawat', $permintaan->no_rawat)
                ->orderBy('kamar_inap.tgl_masuk', 'desc')
                ->value('bangsal.nm_bangsal');
            $ruang = $kamar ?: $permintaan->nm_poli;
        } else {
            $ruang = $permintaan->nm_poli;
        }

        // Setting RS
        $setting = DB::table('setting')->first();

        return view('lab.gambaran_darah_tepi.cetak', compact('hasil', 'permintaan', 'ruang', 'setting'));
    }
}
