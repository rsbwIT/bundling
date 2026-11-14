<?php

namespace App\Http\Controllers\Rinciankasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class rincian extends Controller
{
    public function show(Request $request)
    {
        $noRawat = $request->input('no_rawat');
        if (!$noRawat) {
            return redirect()->back()->with('error', 'No. Rawat wajib diisi');
        }

        $pasien = $this->getPasienByNoRawat($noRawat);
        if (!$pasien) {
            return redirect()->back()->with('error', 'Data pasien tidak ditemukan');
        }

        $isRalan = $pasien->status_lanjut === 'Ralan';

        if ($isRalan) {
            $header = $this->getInvoiceHeaderRalan($noRawat);
            $kamar = [];
            $statusTambahan = $this->checkStatusTambahanRalan($noRawat);
        } else {
            $header = $this->getInvoiceHeader($noRawat);
            $kamar = $this->getAllKamarInap($noRawat);
            $statusTambahan = $this->checkStatusTambahan($noRawat);
        }

        $administrasi = $this->getAdministrasi($noRawat);
        $konsultasi = $this->getKonsultasi($noRawat);
        $sampel = $this->getPengambilanSampel($noRawat);
        $tindakanDokter = $this->getTindakanDokter($noRawat);
        $tindakanPerawat = $this->getTindakanPerawat($noRawat);
        $lab = $this->getPemeriksaanLab($noRawat);
        $radiologi = $this->getRadiologi($noRawat);
        $operasi = $this->getOperasi($noRawat);
        $obat = $this->getObatBHP($noRawat);
        $tambahanBiaya = $this->getTambahanBiaya($noRawat);

        // Data Eklaim / SEP & Akun untuk Pembayaran COB
        $sepInfo = DB::table('bridging_sep')->where('no_rawat', $noRawat)->first();
        $eklaimTarif = 0;
        $eklaimCbg = null;
        $eklaimDesc = null;

        if ($sepInfo && !empty($sepInfo->no_sep)) {
            $tarifInacbgData = $this->getTarifEklaim($sepInfo->no_sep);
            if ($tarifInacbgData['success']) {
                $eklaimTarif = $tarifInacbgData['tarif'];
                $eklaimCbg = $tarifInacbgData['cbg'];
                $eklaimDesc = $tarifInacbgData['deskripsi'];
            }
        }

        // Mapping Penjamin Kasir
        $kdPjPasien = $header->kd_pj ?? null;
        $mappingPenjamin = null;
        if ($kdPjPasien) {
            $mappingPenjamin = DB::table('bw_mapping_penjamin_kasir')
                ->where('kd_pj', $kdPjPasien)
                ->first();
        }

        // Akun Piutang & Bayar
        $akunPiutangBpjs = DB::table('akun_piutang')->where('nama_bayar', 'like', '2. Piutang BPJS%')->first();
        $akunPiutangCobList = DB::table('akun_piutang')
            ->where('nama_bayar', 'not like', '2. Piutang BPJS%')
            ->orderBy('nama_bayar', 'asc')
            ->get();
        $akunBayarList = DB::table('akun_bayar')->orderBy('nama_bayar', 'asc')->get();

        // Data Piutang & Pembayaran Saat Ini
        $currentPiutang = DB::table('piutang_pasien')->where('no_rawat', $noRawat)->first();
        $currentDetailPiutang = DB::table('detail_piutang_pasien')->where('no_rawat', $noRawat)->get();
        $currentBayar = DB::table('detail_nota_jalan')->where('no_rawat', $noRawat)->get();

        return view('rinciankasir.rincian', compact(
            'header',
            'kamar',
            'statusTambahan',
            'administrasi',
            'konsultasi',
            'sampel',
            'tindakanDokter',
            'tindakanPerawat',
            'lab',
            'radiologi',
            'operasi',
            'obat',
            'tambahanBiaya',
            'isRalan',
            'sepInfo',
            'eklaimTarif',
            'eklaimCbg',
            'eklaimDesc',
            'akunPiutangBpjs',
            'akunPiutangCobList',
            'akunBayarList',
            'currentPiutang',
            'currentDetailPiutang',
            'currentBayar',
            'mappingPenjamin'
        ));
    }

    /**
     * Ambil tarif E-Klaim INACBG berdasarkan nomor SEP
     */
    public function getTarifEklaim($noSep)
    {
        try {
            $bridging = new \App\Http\Controllers\Bpjs\bridginginacbg2();
            $refMethod = new \ReflectionMethod($bridging, 'requestInacbg');
            $refMethod->setAccessible(true);
            $res = $refMethod->invoke($bridging, [
                'metadata' => ['method' => 'get_claim_data'],
                'data'     => ['nomor_sep' => $noSep]
            ]);

            if (isset($res['metadata']['code']) && $res['metadata']['code'] == 200 && isset($res['response']['data'])) {
                $claimData = $res['response']['data'];
                $tariff = 0;
                $cbg = null;
                $desc = null;

                if (isset($claimData['grouper']['response_inacbg']['tariff'])) {
                    $tariff = (float) $claimData['grouper']['response_inacbg']['tariff'];
                    $cbg = $claimData['grouper']['response_inacbg']['cbg']['code'] ?? null;
                    $desc = $claimData['grouper']['response_inacbg']['cbg']['description'] ?? null;
                } elseif (isset($claimData['grouper']['response_idrg']['total_tarif'])) {
                    $tariff = (float) $claimData['grouper']['response_idrg']['total_tarif'];
                    $cbg = $claimData['grouper']['response_idrg']['drg_code'] ?? null;
                    $desc = $claimData['grouper']['response_idrg']['drg_description'] ?? null;
                }

                return [
                    'success'   => true,
                    'tarif'     => $tariff,
                    'cbg'       => $cbg,
                    'deskripsi' => $desc,
                    'data'      => $claimData
                ];
            }

            return [
                'success' => false,
                'tarif'   => 0,
                'message' => $res['metadata']['message'] ?? 'Data E-Klaim tidak ditemukan'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'tarif'   => 0,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * AJAX endpoint untuk cek / refresh tarif E-Klaim
     */
    public function cekEklaim($noRawat)
    {
        $noRawatDecoded = urldecode($noRawat);
        $sep = DB::table('bridging_sep')->where('no_rawat', $noRawatDecoded)->first();

        if (!$sep || empty($sep->no_sep)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor SEP untuk No. Rawat ini tidak ditemukan di bridging_sep'
            ], 404);
        }

        $result = $this->getTarifEklaim($sep->no_sep);
        $result['no_sep'] = $sep->no_sep;
        return response()->json($result);
    }

    /**
     * Simpan Pembayaran Kasir (Piutang BPJS / Piutang Asuransi / COB / Bayar Langsung)
     */
    public function simpanPembayaranCob(Request $request)
    {
        $noRawat = $request->input('no_rawat');
        $totalTagihan = (float) $request->input('total_tagihan_real', $request->input('total_tagihan', 0));
        
        $pasien = DB::table('reg_periksa')->where('no_rawat', $noRawat)->first();
        if (!$pasien) {
            return redirect()->back()->with('error', 'Data registrasi tidak ditemukan');
        }

        $kdPj = $pasien->kd_pj;
        $mapping = DB::table('bw_mapping_penjamin_kasir')->where('kd_pj', $kdPj)->first();
        $kategoriMapping = $mapping ? $mapping->kategori : 'Asuransi';

        $piutangBpjs = (float) $request->input('piutang_bpjs', 0);
        $selisih = (float) $request->input('selisih_cob', 0);
        $bayarLangsung = (float) $request->input('besar_bayar_langsung', 0);
        $namaAkunBayar = $request->input('nama_akun_bayar');

        $tglHariIni = date('Y-m-d');
        $jatuhTempo = $request->input('jatuh_tempo', $tglHariIni);

        DB::beginTransaction();
        try {
            // Bersihkan data piutang lama untuk no_rawat ini agar sinkron bersih
            DB::table('detail_piutang_pasien')->where('no_rawat', $noRawat)->delete();

            $totalPiutangDisimpan = 0;

            // 1. Jika ada Piutang BPJS
            if ($piutangBpjs > 0) {
                $akunBpjs = DB::table('akun_piutang')->where('nama_bayar', 'like', '2. Piutang BPJS%')->first();
                $namaBayarBpjs = $akunBpjs ? $akunBpjs->nama_bayar : '2. Piutang BPJS';
                $kdPjBpjs = $akunBpjs ? $akunBpjs->kd_pj : 'BPJ';

                DB::table('detail_piutang_pasien')->insert([
                    'no_rawat'     => $noRawat,
                    'nama_bayar'   => $namaBayarBpjs,
                    'kd_pj'        => $kdPjBpjs,
                    'totalpiutang' => $piutangBpjs,
                    'sisapiutang'  => $piutangBpjs,
                    'tgltempo'     => $jatuhTempo
                ]);
                $totalPiutangDisimpan += $piutangBpjs;
            }

            // 2. Jika ada Piutang Perusahaan / Asuransi (COB atau Asuransi Murni)
            if ($selisih > 0) {
                $inputNamaCob = $request->input('nama_bayar_cob');
                if (empty($inputNamaCob) && $mapping && !empty($mapping->nama_akun_piutang)) {
                    $inputNamaCob = $mapping->nama_akun_piutang;
                }
                $akunCob = DB::table('akun_piutang')->where('nama_bayar', $inputNamaCob)->first();
                $namaBayarCob = $akunCob ? $akunCob->nama_bayar : $inputNamaCob;
                $kdPjCob = $akunCob ? $akunCob->kd_pj : ($mapping->kd_pj ?? $kdPj);

                DB::table('detail_piutang_pasien')->insert([
                    'no_rawat'     => $noRawat,
                    'nama_bayar'   => $namaBayarCob,
                    'kd_pj'        => $kdPjCob,
                    'totalpiutang' => $selisih,
                    'sisapiutang'  => $selisih,
                    'tgltempo'     => $jatuhTempo
                ]);
                $totalPiutangDisimpan += $selisih;
            }

            // 3. Update / Insert Master Piutang Pasien jika ada piutang
            if ($totalPiutangDisimpan > 0) {
                DB::table('piutang_pasien')->updateOrInsert(
                    ['no_rawat' => $noRawat],
                    [
                        'tgl_piutang'  => $tglHariIni,
                        'no_rkm_medis' => $pasien->no_rkm_medis,
                        'status'       => 'Belum Lunas',
                        'totalpiutang' => $totalPiutangDisimpan,
                        'uangmuka'     => 0,
                        'sisapiutang'  => $totalPiutangDisimpan,
                        'tgltempo'     => $jatuhTempo
                    ]
                );
            } else {
                DB::table('piutang_pasien')->where('no_rawat', $noRawat)->delete();
            }

            // 4. Catat Pembayaran Langsung jika Ralan (detail_nota_jalan)
            if ($pasien->status_lanjut === 'Ralan' && $bayarLangsung > 0 && !empty($namaAkunBayar)) {
                DB::table('detail_nota_jalan')->where('no_rawat', $noRawat)->delete();
                DB::table('detail_nota_jalan')->insert([
                    'no_rawat'   => $noRawat,
                    'nama_bayar' => $namaAkunBayar,
                    'besar_ppn'  => 0,
                    'besar_bayar'=> $bayarLangsung
                ]);
            }

            // Pastikan status bayar di reg_periksa terupdate 'Sudah Bayar'
            DB::table('reg_periksa')->where('no_rawat', $noRawat)->update(['status_bayar' => 'Sudah Bayar']);

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran kasir berhasil disimpan! Total Piutang: Rp ' . number_format($totalPiutangDisimpan, 0, ',', '.') . ($bayarLangsung > 0 ? ', Bayar Tunai: Rp ' . number_format($bayarLangsung, 0, ',', '.') : ''));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }

    // ============================================================================
    // 1. HALAMAN UTAMA (INDEX)
    // ============================================================================

    public function getInvoices($filter)
    {
        $statusLanjut = $filter['status_lanjut'] ?? 'Ranap';
        $filterBy = $filter['filter_by'] ?? '';
        $tgl1 = $filter['tgl1'] ?? null;
        $tgl2 = $filter['tgl2'] ?? null;
        $cariNomor = $filter['cari_nomor'] ?? '';
        $jenisBayar = $filter['jenis_bayar'] ?? '';
        $kdPoli = $filter['kd_poli'] ?? '';
        $page = $filter['page'] ?? 1;
        $limit = $filter['limit'] ?? 10;

        $query = DB::table('reg_periksa')
            ->select('reg_periksa.*', 'pasien.nm_pasien', 'poliklinik.nm_poli', 'penjab.png_jawab')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('reg_periksa.status_lanjut', $statusLanjut);

        if ($statusLanjut === 'Ranap') {
            if ($filterBy === 'pulang') {
                $query->join('kamar_inap', function($join) {
                    $join->on('reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                         ->where('kamar_inap.stts_pulang', '!=', 'Pindah Kamar');
                })
                ->whereBetween('kamar_inap.tgl_keluar', [$tgl1, $tgl2]);
            } elseif ($filterBy === 'opname') {
                $query->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                      ->where('kamar_inap.stts_pulang', '-');
            } else {
                if ($tgl1 && $tgl2) {
                    $query->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2]);
                }
            }
        } else {
            if ($tgl1 && $tgl2) {
                $query->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2]);
            }
        }

        if (!empty($cariNomor)) {
            $query->where(function($q) use ($cariNomor) {
                $cari = '%' . $cariNomor . '%';
                $q->where('reg_periksa.no_rawat', 'like', $cari)
                  ->orWhere('reg_periksa.no_rkm_medis', 'like', $cari)
                  ->orWhere('pasien.nm_pasien', 'like', $cari);
            });
        }

        if (!empty($jenisBayar)) {
            if ($jenisBayar === 'BPJS') {
                $query->where(function($q) {
                    $q->where('penjab.png_jawab', 'like', '%BPJS%')
                      ->orWhere('penjab.png_jawab', 'like', '%COB%')
                      ->orWhereIn('penjab.kd_pj', ['BPJ', 'IAK', 'CKI']);
                });
            } elseif ($jenisBayar === 'Umum') {
                $query->where(function($q) {
                    $q->where('penjab.png_jawab', 'like', '%UMUM%')
                      ->orWhere('reg_periksa.kd_pj', '-')
                      ->orWhere('reg_periksa.kd_pj', 'UMU');
                });
            } elseif ($jenisBayar === 'Asuransi') {
                $query->where('penjab.png_jawab', 'not like', '%BPJS%')
                      ->where('penjab.png_jawab', 'not like', '%COB%')
                      ->where('penjab.png_jawab', 'not like', '%UMUM%')
                      ->whereNotIn('reg_periksa.kd_pj', ['-', 'UMU', 'BPJ', 'IAK', 'CKI']);
            }
        }

        if (!empty($kdPoli)) {
            $query->where('poliklinik.nm_poli', 'like', '%' . $kdPoli . '%');
        }

        $total = $query->count();

        $offset = ($page - 1) * $limit;
        $invoices = $query->limit($limit)->offset($offset)->get();

        return [
            'invoices' => $invoices,
            'total' => $total
        ];
    }

    public function checkStatusTambahan($noRawat)
    {
        $results = [];

        // 1. Cek Piutang
        $results['has_piutang'] = DB::table('piutang_pasien')
            ->where('no_rawat', $noRawat)
            ->where('sisapiutang', '>', 0)
            ->exists();

        // 2. Cek Opname
        $results['is_opname'] = DB::table('kamar_inap')
            ->where('no_rawat', $noRawat)
            ->where('stts_pulang', '-')
            ->exists();

        // 3. Cek Batal
        $results['is_batal'] = DB::table('reg_periksa')
            ->where('no_rawat', $noRawat)
            ->where('stts', 'Batal')
            ->exists();

        // 4. Cek Billing
        $results['has_billing'] = DB::table('nota_inap')
            ->where('no_rawat', $noRawat)
            ->exists();

        return $results;
    }

    public function checkStatusTambahanRalan($noRawat)
    {
        $results = [];

        // 1. Cek Piutang
        $results['has_piutang'] = DB::table('piutang_pasien')
            ->where('no_rawat', $noRawat)
            ->where('sisapiutang', '>', 0)
            ->exists();

        // 2. Tidak ada opname untuk ralan
        $results['is_opname'] = false;

        // 3. Cek Batal
        $results['is_batal'] = DB::table('reg_periksa')
            ->where('no_rawat', $noRawat)
            ->where('stts', 'Batal')
            ->exists();

        // 4. Cek Billing
        $results['has_billing'] = DB::table('nota_jalan')
            ->where('no_rawat', $noRawat)
            ->exists();

        return $results;
    }

    // ============================================================================
    // 2. INVOICE HEADER
    // ============================================================================

    public function getInvoiceHeaderRalan($noRawat)
    {
        return DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                DB::raw("COALESCE(nota_jalan.no_nota, '-') as no_nota"),
                DB::raw("'' as kd_kamar"),
                DB::raw("'' as kd_bangsal"),
                'poliklinik.nm_poli as nm_bangsal',
                DB::raw("'' as kelas"),
                'reg_periksa.tgl_registrasi as tgl_masuk',
                'reg_periksa.tgl_registrasi as tgl_keluar',
                DB::raw("0 as lama"),
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.alamat',
                DB::raw("COALESCE(dokter.kd_dokter, reg_periksa.kd_dokter) as kd_dokter"),
                DB::raw("COALESCE(dokter.nm_dokter, '') as nm_dokter"),
                'reg_periksa.biaya_reg',
                DB::raw("0 as ttl_biaya"),
                'reg_periksa.kd_pj',
                'penjab.png_jawab',
                'reg_periksa.status_bayar'
            )
            ->leftJoin('nota_jalan', 'reg_periksa.no_rawat', '=', 'nota_jalan.no_rawat')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('reg_periksa.no_rawat', $noRawat)
            ->first();
    }

    public function getPasienByNoRawat($noRawat)
    {
        return DB::table('reg_periksa')
            ->select('reg_periksa.*', 'pasien.nm_pasien', 'pasien.alamat', 'pasien.tgl_lahir', 'penjab.png_jawab')
            ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('reg_periksa.no_rawat', $noRawat)
            ->first();
    }

    public function getInvoiceHeader($noRawat)
    {
        return DB::table('reg_periksa')
            ->select(
                'reg_periksa.no_rawat',
                DB::raw("COALESCE(nota_inap.no_nota, '-') as no_nota"),
                'kamar_inap.kd_kamar',
                'kamar.kd_bangsal',
                'bangsal.nm_bangsal',
                'kamar.kelas',
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.lama',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.alamat',
                DB::raw("COALESCE(rawat_inap_dr.kd_dokter, rawat_jl_dr.kd_dokter, reg_periksa.kd_dokter) as kd_dokter"),
                DB::raw("COALESCE(dokter.nm_dokter, '') as nm_dokter"),
                'reg_periksa.biaya_reg',
                'kamar_inap.ttl_biaya',
                'reg_periksa.kd_pj',
                'penjab.png_jawab',
                'reg_periksa.status_bayar'
            )
            ->leftJoin('nota_inap', 'reg_periksa.no_rawat', '=', 'nota_inap.no_rawat')
            ->leftJoin('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('kamar_inap', function($join) {
                $join->on('reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                     ->where('kamar_inap.stts_pulang', '!=', 'Pindah Kamar');
            })
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('rawat_inap_dr', 'reg_periksa.no_rawat', '=', 'rawat_inap_dr.no_rawat')
            ->leftJoin('rawat_jl_dr', 'reg_periksa.no_rawat', '=', 'rawat_jl_dr.no_rawat')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', DB::raw("COALESCE(rawat_inap_dr.kd_dokter, rawat_jl_dr.kd_dokter, reg_periksa.kd_dokter)"))
            ->where('reg_periksa.no_rawat', $noRawat)
            ->orderBy('reg_periksa.no_rawat')
            ->first();
    }

    public function getAllKamarInap($noRawat)
    {
        return DB::table('kamar_inap')
            ->select(
                'kamar_inap.kd_kamar',
                'kamar.kd_bangsal',
                'bangsal.nm_bangsal',
                'kamar.kelas',
                'kamar_inap.tgl_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.lama',
                'kamar_inap.ttl_biaya',
                'kamar_inap.stts_pulang'
            )
            ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where('kamar_inap.no_rawat', $noRawat)
            ->orderBy('kamar_inap.tgl_masuk', 'asc')
            ->orderBy('kamar_inap.jam_masuk', 'asc')
            ->get();
    }

    // ============================================================================
    // 3. RINCIAN BIAYA
    // ============================================================================

    public function getAdministrasi($noRawat)
    {
        $itemsJL = DB::table('rawat_jl_pr')
            ->select(
                DB::raw("'Administrasi' as kategori"),
                'rawat_jl_pr.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'jns_perawatan.total_byrpr as biaya',
                DB::raw("1 as jumlah"),
                'rawat_jl_pr.material',
                'jns_perawatan.total_byrpr as total_biaya',
                'rawat_jl_pr.tgl_perawatan',
                DB::raw("'' as kd_dokter"),
                DB::raw("'' as nm_dokter"),
                'petugas.nama as nm_petugas'
            )
            ->leftJoin('jns_perawatan', 'rawat_jl_pr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->leftJoin('petugas', 'rawat_jl_pr.nip', '=', 'petugas.nip')
            ->where('rawat_jl_pr.no_rawat', $noRawat)
            ->where(function($q) {
                $q->where('jns_perawatan.nm_perawatan', 'like', '%Administrasi%')
                  ->orWhere('jns_perawatan.nm_perawatan', 'like', '%Materai%')
                  ->orWhere('jns_perawatan.nm_perawatan', 'like', '%Portir%')
                  ->orWhere('jns_perawatan.nm_perawatan', 'like', '%Sewa Ruang%');
            })
            ->get()
            ->toArray();

        $itemsRI = DB::table('rawat_inap_pr')
            ->select(
                DB::raw("'Administrasi' as kategori"),
                'rawat_inap_pr.kd_jenis_prw',
                'jns_perawatan_inap.nm_perawatan',
                'jns_perawatan_inap.total_byrpr as biaya',
                DB::raw("1 as jumlah"),
                'rawat_inap_pr.material',
                'jns_perawatan_inap.total_byrpr as total_biaya',
                'rawat_inap_pr.tgl_perawatan',
                DB::raw("'' as kd_dokter"),
                DB::raw("'' as nm_dokter"),
                'petugas.nama as nm_petugas'
            )
            ->leftJoin('jns_perawatan_inap', 'rawat_inap_pr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->leftJoin('petugas', 'rawat_inap_pr.nip', '=', 'petugas.nip')
            ->where('rawat_inap_pr.no_rawat', $noRawat)
            ->where('jns_perawatan_inap.nm_perawatan', 'like', '%Administrasi%')
            ->get()
            ->toArray();

        $merged = array_merge($itemsJL, $itemsRI);

        usort($merged, function($a, $b) {
            return strcmp($a->tgl_perawatan, $b->tgl_perawatan);
        });

        foreach ($merged as $item) {
            $formattedDate = strlen($item->tgl_perawatan) >= 10 ? substr($item->tgl_perawatan, 0, 10) : $item->tgl_perawatan;
            $pelaksanaStr = $item->nm_petugas;
            $item->tgl_perawatan = ($pelaksanaStr ? $pelaksanaStr . ' ' : '') . '(' . \Carbon\Carbon::parse($formattedDate)->format('d-m-Y') . ')';
        }

        return $merged;
    }

    public function getKonsultasi($noRawat)
    {
        return DB::table('rawat_jl_dr')
            ->select(
                DB::raw("'Konsultasi' as kategori"),
                'rawat_jl_dr.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'jns_perawatan.total_byrdr as biaya',
                DB::raw("1 as jumlah"),
                'rawat_jl_dr.material',
                'jns_perawatan.total_byrdr as total_biaya',
                'rawat_jl_dr.tgl_perawatan',
                'rawat_jl_dr.kd_dokter',
                'dokter.nm_dokter'
            )
            ->leftJoin('jns_perawatan', 'rawat_jl_dr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->leftJoin('dokter', 'rawat_jl_dr.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('rawat_jl_dr.no_rawat', $noRawat)
            ->where('jns_perawatan.nm_perawatan', 'like', '%Konsultasi%')
            ->get();
    }

    public function getPengambilanSampel($noRawat)
    {
        $rawItemsJL = DB::table('rawat_jl_pr')
            ->select(
                DB::raw("'Pengambilan Sampel' as kategori"),
                'rawat_jl_pr.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'jns_perawatan.total_byrpr as biaya',
                DB::raw("1 as jumlah"),
                'rawat_jl_pr.material',
                'jns_perawatan.total_byrpr as total_biaya',
                'rawat_jl_pr.tgl_perawatan',
                DB::raw("'' as kd_dokter"),
                DB::raw("'' as nm_dokter"),
                'petugas.nama as nm_petugas'
            )
            ->leftJoin('jns_perawatan', 'rawat_jl_pr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->leftJoin('petugas', 'rawat_jl_pr.nip', '=', 'petugas.nip')
            ->where('rawat_jl_pr.no_rawat', $noRawat)
            ->where(function($q) {
                $q->where('jns_perawatan.nm_perawatan', 'like', '%Sampel%')
                  ->orWhere('jns_perawatan.nm_perawatan', 'like', '%Tindakan Dewasa (LAB)%')
                  ->orWhere('jns_perawatan.nm_perawatan', 'like', '%Tindakan Anak (LAB)%')
                  ->orWhere('rawat_jl_pr.kd_jenis_prw', 'like', '%LAB%')
                  ->orWhere('rawat_jl_pr.kd_jenis_prw', 'like', '%BPLAB%');
            })
            ->get()
            ->toArray();

        $rawItemsRI = DB::table('rawat_inap_pr')
            ->select(
                DB::raw("'Pengambilan Sampel' as kategori"),
                'rawat_inap_pr.kd_jenis_prw',
                'jns_perawatan_inap.nm_perawatan',
                'rawat_inap_pr.biaya_rawat as biaya',
                DB::raw("1 as jumlah"),
                'rawat_inap_pr.material',
                'rawat_inap_pr.biaya_rawat as total_biaya',
                'rawat_inap_pr.tgl_perawatan',
                DB::raw("'' as kd_dokter"),
                DB::raw("'' as nm_dokter"),
                'petugas.nama as nm_petugas'
            )
            ->leftJoin('jns_perawatan_inap', 'rawat_inap_pr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->leftJoin('petugas', 'rawat_inap_pr.nip', '=', 'petugas.nip')
            ->where('rawat_inap_pr.no_rawat', $noRawat)
            ->where(function($q) {
                $q->where('jns_perawatan_inap.nm_perawatan', 'like', '%Sampel%')
                  ->orWhere('jns_perawatan_inap.nm_perawatan', 'like', '%Tindakan Dewasa (LAB)%')
                  ->orWhere('jns_perawatan_inap.nm_perawatan', 'like', '%Tindakan Anak (LAB)%')
                  ->orWhere('rawat_inap_pr.kd_jenis_prw', 'like', '%LAB%')
                  ->orWhere('rawat_inap_pr.kd_jenis_prw', 'like', '%BPLAB%');
            })
            ->get()
            ->toArray();

        $rawItems = array_merge($rawItemsJL, $rawItemsRI);

        usort($rawItems, function($a, $b) {
            return strcmp($a->tgl_perawatan, $b->tgl_perawatan);
        });

        foreach ($rawItems as $item) {
            $formattedDate = strlen($item->tgl_perawatan) >= 10 ? substr($item->tgl_perawatan, 0, 10) : $item->tgl_perawatan;
            $pelaksanaStr = $item->nm_petugas;
            $item->tgl_perawatan = ($pelaksanaStr ? $pelaksanaStr . ' ' : '') . '(' . \Carbon\Carbon::parse($formattedDate)->format('d-m-Y') . ')';
        }

        return $rawItems;
    }

    public function getTindakanDokter($noRawat)
    {
        $formatDate = function($dateStr) {
            return strlen($dateStr) >= 10 ? substr($dateStr, 0, 10) : $dateStr;
        };

        $itemsJL = DB::table('rawat_jl_dr')
            ->select(
                DB::raw("'Tindakan Dokter' as kategori"),
                'rawat_jl_dr.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'jns_perawatan.total_byrdr as biaya',
                DB::raw("1 as jumlah"),
                'rawat_jl_dr.material',
                'jns_perawatan.total_byrdr as total_biaya',
                'rawat_jl_dr.tgl_perawatan',
                'rawat_jl_dr.kd_dokter',
                'dokter.nm_dokter',
                DB::raw("'' as nm_petugas")
            )
            ->leftJoin('jns_perawatan', 'rawat_jl_dr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->leftJoin('dokter', 'rawat_jl_dr.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('rawat_jl_dr.no_rawat', $noRawat)
            ->where('jns_perawatan.nm_perawatan', 'not like', '%Konsultasi%')
            ->get()
            ->toArray();

        $itemsJLDrPr = DB::table('rawat_jl_drpr')
            ->select(
                DB::raw("'Tindakan Dokter' as kategori"),
                'rawat_jl_drpr.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'rawat_jl_drpr.biaya_rawat as biaya',
                DB::raw("1 as jumlah"),
                'rawat_jl_drpr.material',
                'rawat_jl_drpr.biaya_rawat as total_biaya',
                'rawat_jl_drpr.tgl_perawatan',
                'rawat_jl_drpr.kd_dokter',
                'dokter.nm_dokter',
                'petugas.nama as nm_petugas'
            )
            ->leftJoin('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->leftJoin('dokter', 'rawat_jl_drpr.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('petugas', 'rawat_jl_drpr.nip', '=', 'petugas.nip')
            ->where('rawat_jl_drpr.no_rawat', $noRawat)
            ->get()
            ->toArray();

        $itemsRI = DB::table('rawat_inap_dr')
            ->select(
                DB::raw("'Tindakan Dokter' as kategori"),
                'rawat_inap_dr.kd_jenis_prw',
                'jns_perawatan_inap.nm_perawatan',
                'jns_perawatan_inap.total_byrdr as biaya',
                DB::raw("1 as jumlah"),
                'rawat_inap_dr.material',
                'jns_perawatan_inap.total_byrdr as total_biaya',
                'rawat_inap_dr.tgl_perawatan',
                'rawat_inap_dr.kd_dokter',
                'dokter.nm_dokter',
                DB::raw("'' as nm_petugas")
            )
            ->leftJoin('jns_perawatan_inap', 'rawat_inap_dr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->leftJoin('dokter', 'rawat_inap_dr.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('rawat_inap_dr.no_rawat', $noRawat)
            ->get()
            ->toArray();

        $itemsRIDrPr = DB::table('rawat_inap_drpr')
            ->select(
                DB::raw("'Tindakan Dokter' as kategori"),
                'rawat_inap_drpr.kd_jenis_prw',
                'jns_perawatan_inap.nm_perawatan',
                'rawat_inap_drpr.biaya_rawat as biaya',
                DB::raw("1 as jumlah"),
                'rawat_inap_drpr.material',
                'rawat_inap_drpr.biaya_rawat as total_biaya',
                'rawat_inap_drpr.tgl_perawatan',
                'rawat_inap_drpr.kd_dokter',
                'dokter.nm_dokter',
                'petugas.nama as nm_petugas'
            )
            ->leftJoin('jns_perawatan_inap', 'rawat_inap_drpr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->leftJoin('dokter', 'rawat_inap_drpr.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('petugas', 'rawat_inap_drpr.nip', '=', 'petugas.nip')
            ->where('rawat_inap_drpr.no_rawat', $noRawat)
            ->get()
            ->toArray();

        $rawItems = array_merge($itemsJL, $itemsJLDrPr, $itemsRI, $itemsRIDrPr);

        usort($rawItems, function($a, $b) {
            $cmpName = strcmp($a->nm_perawatan, $b->nm_perawatan);
            if ($cmpName !== 0) return $cmpName;
            return strcmp($a->tgl_perawatan, $b->tgl_perawatan);
        });

        foreach ($rawItems as $item) {
            $formattedDate = $formatDate($item->tgl_perawatan);
            $pelaksanaStr = $item->nm_dokter;
            if (!empty($item->nm_petugas)) {
                $pelaksanaStr = $pelaksanaStr ? $pelaksanaStr . " & " . $item->nm_petugas : $item->nm_petugas;
            }
            $item->tgl_perawatan = "{$pelaksanaStr} (" . \Carbon\Carbon::parse($formattedDate)->format('d-m-Y') . ")";
        }

        return $rawItems;
    }

    public function getTindakanPerawat($noRawat)
    {
        $formatDate = function($dateStr) {
            return strlen($dateStr) >= 10 ? substr($dateStr, 0, 10) : $dateStr;
        };

        $itemsJL = DB::table('rawat_jl_pr')
            ->select(
                DB::raw("'Tindakan Keperawatan' as kategori"),
                'rawat_jl_pr.kd_jenis_prw',
                'jns_perawatan.nm_perawatan',
                'rawat_jl_pr.biaya_rawat as biaya',
                DB::raw("1 as jumlah"),
                'rawat_jl_pr.material',
                'rawat_jl_pr.biaya_rawat as total_biaya',
                'rawat_jl_pr.tgl_perawatan',
                DB::raw("'' as kd_dokter"),
                DB::raw("'' as nm_dokter"),
                'petugas.nama as nm_petugas'
            )
            ->leftJoin('jns_perawatan', 'rawat_jl_pr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
            ->leftJoin('petugas', 'rawat_jl_pr.nip', '=', 'petugas.nip')
            ->where('rawat_jl_pr.no_rawat', $noRawat)
            ->where(function($q) {
                $q->where('jns_perawatan.nm_perawatan', 'not like', '%Administrasi%')
                  ->where('jns_perawatan.nm_perawatan', 'not like', '%Materai%')
                  ->where('jns_perawatan.nm_perawatan', 'not like', '%Portir%')
                  ->where('jns_perawatan.nm_perawatan', 'not like', '%Sewa Ruang%')
                  ->where('jns_perawatan.nm_perawatan', 'not like', '%Sampel%')
                  ->where('jns_perawatan.nm_perawatan', 'not like', '%Tindakan Dewasa (LAB)%')
                  ->where('jns_perawatan.nm_perawatan', 'not like', '%Tindakan Anak (LAB)%');
            })
            ->get()
            ->toArray();

        $itemsRI = DB::table('rawat_inap_pr')
            ->select(
                DB::raw("'Tindakan Keperawatan' as kategori"),
                'rawat_inap_pr.kd_jenis_prw',
                'jns_perawatan_inap.nm_perawatan',
                'rawat_inap_pr.biaya_rawat as biaya',
                DB::raw("1 as jumlah"),
                'rawat_inap_pr.material',
                'rawat_inap_pr.biaya_rawat as total_biaya',
                'rawat_inap_pr.tgl_perawatan',
                DB::raw("'' as kd_dokter"),
                DB::raw("'' as nm_dokter"),
                'petugas.nama as nm_petugas'
            )
            ->leftJoin('jns_perawatan_inap', 'rawat_inap_pr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->leftJoin('petugas', 'rawat_inap_pr.nip', '=', 'petugas.nip')
            ->where('rawat_inap_pr.no_rawat', $noRawat)
            ->where(function($q) {
                $q->where('jns_perawatan_inap.nm_perawatan', 'not like', '%Administrasi%')
                  ->where('jns_perawatan_inap.nm_perawatan', 'not like', '%Sampel%')
                  ->where('jns_perawatan_inap.nm_perawatan', 'not like', '%Tindakan Dewasa (LAB)%')
                  ->where('jns_perawatan_inap.nm_perawatan', 'not like', '%Tindakan Anak (LAB)%');
            })
            ->get()
            ->toArray();

        $rawItems = array_merge($itemsJL, $itemsRI);

        usort($rawItems, function($a, $b) {
            $cmpName = strcmp($a->nm_perawatan, $b->nm_perawatan);
            if ($cmpName !== 0) return $cmpName;
            return strcmp($a->tgl_perawatan, $b->tgl_perawatan);
        });

        foreach ($rawItems as $item) {
            $formattedDate = $formatDate($item->tgl_perawatan);
            $pelaksanaStr = $item->nm_petugas;
            $item->tgl_perawatan = ($pelaksanaStr ? $pelaksanaStr . ' ' : '') . '(' . \Carbon\Carbon::parse($formattedDate)->format('d-m-Y') . ')';
        }

        return $rawItems;
    }

    public function getRadiologi($noRawat)
    {
        return DB::table('periksa_radiologi')
            ->select(
                DB::raw("'Radiologi' as kategori"),
                'periksa_radiologi.kd_jenis_prw',
                'jns_perawatan_radiologi.nm_perawatan',
                'periksa_radiologi.biaya as biaya',
                DB::raw("1 as jumlah"),
                DB::raw("0 as material"),
                'periksa_radiologi.biaya as total_biaya',
                'periksa_radiologi.tgl_periksa as tgl_perawatan',
                'periksa_radiologi.kd_dokter',
                'dokter.nm_dokter',
                'petugas.nama as nm_petugas'
            )
            ->leftJoin('jns_perawatan_radiologi', 'periksa_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
            ->leftJoin('dokter', 'periksa_radiologi.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('petugas', 'periksa_radiologi.nip', '=', 'petugas.nip')
            ->where('periksa_radiologi.no_rawat', $noRawat)
            ->get();
    }

    public function getPemeriksaanLab($noRawat)
    {
        return DB::table('periksa_lab')
            ->select(
                DB::raw("'Pemeriksaan Lab' as kategori"),
                'periksa_lab.kd_jenis_prw',
                'jns_perawatan_lab.nm_perawatan',
                'periksa_lab.biaya as biaya',
                DB::raw("1 as jumlah"),
                DB::raw("0 as material"),
                'periksa_lab.biaya as total_biaya',
                'periksa_lab.tgl_periksa as tgl_perawatan',
                'periksa_lab.kd_dokter',
                'dokter.nm_dokter',
                'petugas.nama as nm_petugas'
            )
            ->leftJoin('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->leftJoin('dokter', 'periksa_lab.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('petugas', 'periksa_lab.nip', '=', 'petugas.nip')
            ->where('periksa_lab.no_rawat', $noRawat)
            ->get();
    }

    public function getOperasi($noRawat)
    {
        $items = [];

        $rawData = DB::table('operasi')
            ->select(
                'operasi.kode_paket',
                'paket_operasi.nm_perawatan',
                'operasi.tgl_operasi',
                DB::raw("COALESCE(operasi.biayaoperator1, 0) as biaya_operator1"),
                DB::raw("COALESCE(operasi.biayaasisten_operator1, 0) as biaya_asisten_op1"),
                DB::raw("COALESCE(operasi.biayadokter_anestesi, 0) as biaya_dr_anestesi"),
                DB::raw("COALESCE(operasi.biayaasisten_anestesi, 0) as biaya_asisten_anest"),
                DB::raw("COALESCE(operasi.biayaalat, 0) as biaya_alat"),
                DB::raw("COALESCE(operasi.biayasewaok, 0) as biaya_sewa_ok"),
                DB::raw("COALESCE(operasi.biaya_dokter_umum, 0) as biaya_dokter_umum"),
                DB::raw("COALESCE(operasi.biayadokter_anak, 0) as biaya_dokter_anak")
            )
            ->leftJoin('paket_operasi', 'operasi.kode_paket', '=', 'paket_operasi.kode_paket')
            ->where('operasi.no_rawat', $noRawat)
            ->get();

        foreach ($rawData as $row) {
            $tglStr = Carbon::parse($row->tgl_operasi)->format('Y-m-d H:i:s');
            
            $totalPaket = $row->biaya_operator1 + $row->biaya_asisten_op1 + $row->biaya_dr_anestesi +
                          $row->biaya_asisten_anest + $row->biaya_alat + $row->biaya_sewa_ok +
                          $row->biaya_dokter_umum + $row->biaya_dokter_anak;

            $items[] = [
                'kategori' => 'Operasi',
                'kd_jenis_prw' => $row->kode_paket,
                'nm_perawatan' => $row->nm_perawatan,
                'biaya' => 0,
                'jumlah' => 0,
                'material' => 0,
                'total_biaya' => 0,
                'tgl_perawatan' => $tglStr,
                'kd_dokter' => '',
                'nm_dokter' => ''
            ];

            $appendItem = function($namaDetail, $biaya) use ($row, &$items) {
                if ($biaya > 0) {
                    $items[] = [
                        'kategori' => 'Operasi',
                        'kd_jenis_prw' => $row->kode_paket,
                        'nm_perawatan' => '- ' . $namaDetail,
                        'biaya' => $biaya,
                        'jumlah' => 1,
                        'material' => 0,
                        'total_biaya' => 0,
                        'tgl_perawatan' => '',
                        'kd_dokter' => '',
                        'nm_dokter' => ''
                    ];
                }
            };

            $appendItem('Biaya Operator', $row->biaya_operator1);
            $appendItem('Biaya Asisten Operator', $row->biaya_asisten_op1);
            $appendItem('Biaya Dokter Anestesi', $row->biaya_dr_anestesi);
            $appendItem('Biaya Asisten Anestesi', $row->biaya_asisten_anest);
            $appendItem('Biaya Dokter Umum', $row->biaya_dokter_umum);
            $appendItem('Biaya Dokter Anak', $row->biaya_dokter_anak);
            $appendItem('Biaya Alat', $row->biaya_alat);
            $appendItem('Biaya Sewa OK', $row->biaya_sewa_ok);

            $items[] = [
                'kategori' => 'Operasi',
                'kd_jenis_prw' => $row->kode_paket,
                'nm_perawatan' => 'Total Operasi',
                'biaya' => 0,
                'jumlah' => 1,
                'material' => 0,
                'total_biaya' => $totalPaket,
                'tgl_perawatan' => '',
                'kd_dokter' => '',
                'nm_dokter' => ''
            ];
        }

        $sewaKamar = DB::table('rawat_inap_pr')
            ->select(
                DB::raw("'Operasi' as kategori"),
                'rawat_inap_pr.kd_jenis_prw',
                'jns_perawatan_inap.nm_perawatan',
                'rawat_inap_pr.biaya_rawat as biaya',
                DB::raw("1 as jumlah"),
                'rawat_inap_pr.material',
                'rawat_inap_pr.biaya_rawat as total_biaya',
                'rawat_inap_pr.tgl_perawatan',
                DB::raw("'' as kd_dokter"),
                DB::raw("'' as nm_dokter")
            )
            ->leftJoin('jns_perawatan_inap', 'rawat_inap_pr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
            ->where('rawat_inap_pr.no_rawat', $noRawat)
            ->where('jns_perawatan_inap.nm_perawatan', 'like', '%Sewa Kamar Operasi%')
            ->get()
            ->toArray();

        if (count($sewaKamar) > 0) {
            $items = array_merge($items, $sewaKamar);
        }

        return $items;
    }

    public function getObatBHP($noRawat)
    {
        $items = [];

        // 1. detail_pemberian_obat
        $pemberian = DB::table('detail_pemberian_obat')
            ->select(
                'detail_pemberian_obat.kode_brng',
                'databarang.nama_brng',
                DB::raw("COALESCE(detail_pemberian_obat.biaya_obat, 0) as harga_satuan"),
                DB::raw("COALESCE(detail_pemberian_obat.jml, 0) as jumlah"),
                DB::raw("COALESCE(detail_pemberian_obat.total, 0) as total"),
                'detail_pemberian_obat.tgl_perawatan',
                'detail_pemberian_obat.kd_bangsal'
            )
            ->leftJoin('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
            ->where('detail_pemberian_obat.no_rawat', $noRawat)
            ->get();

        foreach ($pemberian as $p) {
            $items[] = $this->mapToObatBHP((array)$p, 'pemberian');
        }

        // 2. resep_pulang
        $resepPulang = DB::table('resep_pulang')
            ->select(
                'resep_pulang.kode_brng',
                'databarang.nama_brng',
                DB::raw("COALESCE(resep_pulang.harga, 0) as harga_satuan"),
                DB::raw("COALESCE(resep_pulang.jml_barang, 0) as jumlah"),
                DB::raw("COALESCE(resep_pulang.total, 0) as total"),
                'resep_pulang.tanggal as tgl_perawatan',
                'resep_pulang.kd_bangsal'
            )
            ->leftJoin('databarang', 'resep_pulang.kode_brng', '=', 'databarang.kode_brng')
            ->where('resep_pulang.no_rawat', $noRawat)
            ->get();

        foreach ($resepPulang as $p) {
            $items[] = $this->mapToObatBHP((array)$p, 'resep_pulang');
        }

        // 3. retur
        $noRawatHyphen = str_replace('/', '-', $noRawat);
        $retur = DB::table('detreturjual')
            ->select(
                'detreturjual.kode_brng',
                'databarang.nama_brng',
                DB::raw("COALESCE(detreturjual.h_retur, 0) as harga_satuan"),
                DB::raw("COALESCE(detreturjual.jml_retur, 0) as jumlah"),
                DB::raw("COALESCE(detreturjual.subtotal, 0) as total"),
                'returjual.tgl_retur as tgl_perawatan',
                'returjual.kd_bangsal'
            )
            ->leftJoin('returjual', 'detreturjual.no_retur_jual', '=', 'returjual.no_retur_jual')
            ->leftJoin('databarang', 'detreturjual.kode_brng', '=', 'databarang.kode_brng')
            ->where(function($q) use ($noRawat, $noRawatHyphen) {
                $q->where('detreturjual.no_retur_jual', 'like', $noRawat . '%')
                  ->orWhere('detreturjual.no_retur_jual', 'like', $noRawatHyphen . '%');
            })
            ->get();

        foreach ($retur as $p) {
            $items[] = $this->mapToObatBHP((array)$p, 'retur');
        }

        return $items;
    }

    private function mapToObatBHP($data, $sourceType)
    {
        $kodeBrng = trim($data['kode_brng'] ?? '');
        $namaBrng = $data['nama_brng'] ?? '';
        $hargaSatuan = (float)($data['harga_satuan'] ?? 0);
        $jumlah = (float)($data['jumlah'] ?? 0);
        $total = (float)($data['total'] ?? 0);
        
        $tglPerawatan = $data['tgl_perawatan'] ?? '';
        if ($tglPerawatan instanceof \DateTime || $tglPerawatan instanceof Carbon) {
            $tglPerawatan = $tglPerawatan->format('Y-m-d');
        } elseif (is_string($tglPerawatan) && strlen($tglPerawatan) >= 10) {
            $tglPerawatan = substr($tglPerawatan, 0, 10);
        }

        $kdBangsal = $data['kd_bangsal'] ?? '';

        $item = [
            'kode_brng' => $kodeBrng,
            'nama_brng' => $namaBrng,
            'biaya' => $hargaSatuan,
            'kd_bangsal' => $kdBangsal,
            'tgl_pemberian' => $tglPerawatan,
        ];

        if ($sourceType === 'retur') {
            $item['jumlah'] = 0;
            $item['total'] = 0;
            $item['jumlah_retur'] = $jumlah;
            $item['total_retur'] = $total;
            $item['total_bersih'] = -$total;
            $item['tgl_retur'] = $tglPerawatan;
            $item['nama_brng'] = $namaBrng . ' (RETUR)';
        } else {
            $item['jumlah'] = $jumlah;
            $item['total'] = $total;
            $item['jumlah_retur'] = 0;
            $item['total_retur'] = 0;
            $item['total_bersih'] = $total;
        }

        return $item;
    }

    public function getTambahanBiaya($noRawat)
    {
        $tambahan = DB::table('tambahan_biaya')
            ->select('no_rawat', 'nama_biaya', DB::raw("COALESCE(besar_biaya, 0) as besar"))
            ->where('no_rawat', $noRawat)
            ->get()
            ->map(function($item) {
                $item->tipe_biaya = 'tambahan';
                return $item;
            })
            ->toArray();

        $pengurangan = DB::table('pengurangan_biaya')
            ->select('no_rawat', 'nama_pengurangan as nama_biaya', DB::raw("COALESCE(besar_pengurangan, 0) as besar"))
            ->where('no_rawat', $noRawat)
            ->get()
            ->map(function($item) {
                $item->tipe_biaya = 'pengurangan';
                return $item;
            })
            ->toArray();

        return [
            'tambahan' => $tambahan,
            'pengurangan' => $pengurangan
        ];
    }
}
