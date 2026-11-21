<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RekapKlaimInacbgController extends Controller
{
    /**
     * Tampilan utama halaman Rekap Klaim INACBG Terkirim
     */
    public function index(Request $request)
    {
        $tgl1 = $request->input('tgl1', date('Y-m-d'));
        $tgl2 = $request->input('tgl2', date('Y-m-d'));
        $tipeTanggal = $request->input('tipe_tanggal', 'tgl_kirim'); // 'tgl_kirim' atau 'tgl_registrasi'
        $statusLanjut = $request->input('status_lanjut', 'Semua');
        $statusKirim = $request->input('status_kirim', 'Semua');
        $keyword = trim($request->input('keyword', ''));

        // Ambil daftar klaim dari bridging_inacbg_terkirim (dengan join ke reg_periksa & pasien)
        $query = DB::table('bridging_inacbg_terkirim as b')
            ->join('reg_periksa as r', 'b.no_rawat', '=', 'r.no_rawat')
            ->join('pasien as p', 'r.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('poliklinik as pol', 'r.kd_poli', '=', 'pol.kd_poli')
            ->leftJoin('dokter as d', 'r.kd_dokter', '=', 'd.kd_dokter')
            ->leftJoin('bw_rekap_klaim_inacbg as c', 'b.no_sep', '=', 'c.no_sep')
            ->select(
                'b.no_rawat',
                'b.no_sep',
                'b.nama_coder',
                'b.nik_coder',
                'b.tgl_kirim',
                'r.no_rkm_medis',
                'p.nm_pasien',
                'r.status_lanjut',
                'r.tgl_registrasi',
                'pol.nm_poli',
                'd.nm_dokter',
                'c.cbg_code',
                'c.cbg_desc',
                'c.tarif_inacbg',
                'c.tarif_rs',
                'c.kemenkes_dc_status',
                'c.kemenkes_dc_sent_dttm',
                'c.bpjs_dc_status',
                'c.bpjs_dc_sent_dttm',
                'c.klaim_status_cd',
                'c.updated_at as last_synced_at'
            );

        if ($tipeTanggal === 'tgl_registrasi') {
            $query->whereDate('r.tgl_registrasi', '>=', $tgl1)
                  ->whereDate('r.tgl_registrasi', '<=', $tgl2);
        } else {
            $query->whereDate('b.tgl_kirim', '>=', $tgl1)
                  ->whereDate('b.tgl_kirim', '<=', $tgl2);
        }

        if ($statusLanjut !== 'Semua') {
            $query->where('r.status_lanjut', $statusLanjut);
        }

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('b.no_sep', 'like', "%{$keyword}%")
                    ->orWhere('b.no_rawat', 'like', "%{$keyword}%")
                    ->orWhere('r.no_rkm_medis', 'like', "%{$keyword}%")
                    ->orWhere('p.nm_pasien', 'like', "%{$keyword}%")
                    ->orWhere('b.nama_coder', 'like', "%{$keyword}%");
            });
        }

        if ($statusKirim === 'Terkirim') {
            $query->where('c.kemenkes_dc_status', 'sent');
        } elseif ($statusKirim === 'Belum_Sinkron') {
            $query->whereNull('c.kemenkes_dc_status');
        }

        $listKlaim = $query->orderBy('b.tgl_kirim', 'desc')->get();

        // Ringkasan Statistik
        $totalKlaim = $listKlaim->count();
        $totalTerkirimKemenkes = $listKlaim->where('kemenkes_dc_status', 'sent')->count();
        $totalNominalInacbg = $listKlaim->sum('tarif_inacbg');
        $totalNominalRs = $listKlaim->sum('tarif_rs');

        return view('bpjs.rekap_klaim_inacbg', compact(
            'listKlaim',
            'tgl1',
            'tgl2',
            'tipeTanggal',
            'statusLanjut',
            'statusKirim',
            'keyword',
            'totalKlaim',
            'totalTerkirimKemenkes',
            'totalNominalInacbg',
            'totalNominalRs'
        ));
    }

    /**
     * Cek & Sinkronkan 1 SEP Langsung ke E-Klaim Web Service
     */
    public function syncSingle(Request $request)
    {
        $noSep = trim($request->input('no_sep'));
        if (empty($noSep)) {
            return response()->json(['status' => false, 'message' => 'Nomor SEP tidak boleh kosong']);
        }

        $res = $this->fetchAndSaveClaimFromEklaim($noSep);
        return response()->json($res);
    }

    /**
     * Batch Sinkronisasi seluruh SEP pada rentang tanggal ke E-Klaim Web Service
     */
    public function syncBatch(Request $request)
    {
        $seps = $request->input('seps', []);
        if (empty($seps) || !is_array($seps)) {
            return response()->json(['status' => false, 'message' => 'Tidak ada SEP yang dipilih']);
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($seps as $noSep) {
            $res = $this->fetchAndSaveClaimFromEklaim($noSep);
            if ($res['status']) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Selesai sinkronisasi E-Klaim: {$successCount} berhasil, {$failedCount} gagal.",
            'success_count' => $successCount,
            'failed_count' => $failedCount
        ]);
    }

    /**
     * Panggil Web Service E-Klaim dan simpan ke bw_rekap_klaim_inacbg
     */
    private function fetchAndSaveClaimFromEklaim($noSep)
    {
        try {
            $bridging = new bridginginacbg2();
            $refMethod = new \ReflectionMethod($bridging, 'requestInacbg');
            $refMethod->setAccessible(true);

            $res = $refMethod->invoke($bridging, [
                'metadata' => ['method' => 'get_claim_data'],
                'data'     => ['nomor_sep' => $noSep]
            ]);

            if (isset($res['metadata']['code']) && $res['metadata']['code'] == 200 && isset($res['response']['data'])) {
                $d = $res['response']['data'];

                // Hitung total tarif RS
                $tarifRsTotal = 0;
                if (!empty($d['tarif_rs']) && is_array($d['tarif_rs'])) {
                    $tarifRsTotal = array_sum(array_map('floatval', $d['tarif_rs']));
                }

                $tarifInacbg = (float) ($d['grouper']['response_inacbg']['tariff'] ?? 0);
                $cbgCode = $d['grouper']['response_inacbg']['cbg']['code'] ?? null;
                $cbgDesc = $d['grouper']['response_inacbg']['cbg']['description'] ?? null;

                // Ambil data registrasi
                $reg = DB::table('bridging_sep')
                    ->join('reg_periksa', 'bridging_sep.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->where('bridging_sep.no_sep', $noSep)
                    ->select('reg_periksa.no_rawat', 'reg_periksa.no_rkm_medis', 'reg_periksa.status_lanjut', 'reg_periksa.tgl_registrasi')
                    ->first();

                $noRawat = $reg ? $reg->no_rawat : '-';
                $noRkmMedis = $reg ? $reg->no_rkm_medis : ($d['nomor_rm'] ?? null);
                $statusLanjut = $reg ? $reg->status_lanjut : ($d['jenis_rawat'] == 1 ? 'Ranap' : 'Ralan');
                $tglRegistrasi = $reg ? $reg->tgl_registrasi : null;

                DB::table('bw_rekap_klaim_inacbg')->updateOrInsert(
                    ['no_sep' => $noSep],
                    [
                        'no_rawat'              => $noRawat,
                        'no_rkm_medis'          => $noRkmMedis,
                        'nm_pasien'             => $d['nama_pasien'] ?? null,
                        'status_lanjut'         => $statusLanjut,
                        'tgl_registrasi'        => $tglRegistrasi,
                        'coder_nik'             => $d['coder_nik'] ?? null,
                        'coder_nm'              => $d['coder_nm'] ?? null,
                        'cbg_code'              => $cbgCode,
                        'cbg_desc'              => $cbgDesc,
                        'tarif_inacbg'          => $tarifInacbg,
                        'tarif_rs'              => $tarifRsTotal,
                        'kemenkes_dc_status'    => $d['kemenkes_dc_status_cd'] ?? null,
                        'kemenkes_dc_sent_dttm' => (!empty($d['kemenkes_dc_sent_dttm']) && $d['kemenkes_dc_sent_dttm'] !== '0000-00-00 00:00:00') ? $d['kemenkes_dc_sent_dttm'] : null,
                        'bpjs_dc_status'        => $d['bpjs_dc_status_cd'] ?? null,
                        'bpjs_dc_sent_dttm'     => (!empty($d['bpjs_dc_sent_dttm']) && $d['bpjs_dc_sent_dttm'] !== '0000-00-00 00:00:00') ? $d['bpjs_dc_sent_dttm'] : null,
                        'klaim_status_cd'       => $d['klaim_status_cd'] ?? null,
                        'updated_at'            => now()
                    ]
                );

                return [
                    'status' => true,
                    'message' => 'Data E-Klaim berhasil disinkronkan',
                    'data' => [
                        'no_sep' => $noSep,
                        'cbg_code' => $cbgCode,
                        'cbg_desc' => $cbgDesc,
                        'tarif_inacbg' => $tarifInacbg,
                        'tarif_rs' => $tarifRsTotal,
                        'kemenkes_status' => $d['kemenkes_dc_status_cd'] ?? '-',
                        'bpjs_status' => $d['bpjs_dc_status_cd'] ?? '-',
                        'sent_dttm' => $d['kemenkes_dc_sent_dttm'] ?? '-'
                    ]
                ];
            } else {
                return [
                    'status' => false,
                    'message' => $res['metadata']['message'] ?? 'Data klaim tidak ditemukan di server E-Klaim'
                ];
            }
        } catch (\Exception $e) {
            Log::error('SYNC EKLAIM ERROR', ['no_sep' => $noSep, 'msg' => $e->getMessage()]);
            return [
                'status' => false,
                'message' => 'Gagal koneksi ke server E-Klaim: ' . $e->getMessage()
            ];
        }
    }
}
