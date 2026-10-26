<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PiutangBelumLunasController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $penjab = $this->cacheService->getPenjab();
        $cari = $request->cari;
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $kd_pj = $request->kd_pj;
        $no_tagihan = $request->no_tagihan;
        $statusLunas = $request->statusLunas ?: 'Belum Lunas';

        // Master Akun Bayar & Pengaturan Akun
        $akunBayar = DB::table('akun_bayar')->orderBy('nama_bayar')->get();
        $setAkun = DB::table('set_akun')->first();

        // Daftar Penagihan Piutang terakhir untuk filter no_tagihan
        $penagihanList = DB::table('penagihan_piutang')
            ->select('no_tagihan', 'tanggal', 'kd_pj', 'catatan')
            ->orderByDesc('tanggal')
            ->limit(50)
            ->get();

        // Detail Tagihan terpilih jika ada filter no_tagihan
        $tagihanDetail = null;
        if (!empty($no_tagihan)) {
            $tagihanDetail = DB::table('penagihan_piutang')
                ->leftJoin('akun_piutang', 'penagihan_piutang.kd_pj', '=', 'akun_piutang.kd_pj')
                ->leftJoin('akun_bayar', 'penagihan_piutang.kd_rek', '=', 'akun_bayar.kd_rek')
                ->where('penagihan_piutang.no_tagihan', $no_tagihan)
                ->select(
                    'penagihan_piutang.no_tagihan',
                    'penagihan_piutang.kd_pj',
                    'akun_piutang.kd_rek as kd_rek_kontra',
                    'akun_piutang.nama_bayar as nama_bayar_piutang',
                    'akun_bayar.nama_bayar as nama_akun_bayar',
                    'akun_bayar.kd_rek as kd_rek_bayar'
                )
                ->first();
        }

        // Query Utama Piutang
        $query = DB::table('piutang_pasien')
            ->select(
                'piutang_pasien.no_rawat',
                'piutang_pasien.tgl_piutang',
                'piutang_pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'piutang_pasien.status',
                'piutang_pasien.totalpiutang',
                'piutang_pasien.uangmuka',
                'piutang_pasien.sisapiutang',
                'piutang_pasien.tgltempo',
                'penjab.png_jawab',
                'penjab.kd_pj',
                'reg_periksa.status_lanjut',
                'akun_piutang.kd_rek as kd_rek_kontra',
                'akun_piutang.nama_bayar as nama_bayar_piutang'
            )
            ->join('pasien', 'piutang_pasien.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('reg_periksa', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin(DB::raw('(SELECT kd_pj, MIN(kd_rek) as kd_rek, MIN(nama_bayar) as nama_bayar FROM akun_piutang GROUP BY kd_pj) as akun_piutang'), 'penjab.kd_pj', '=', 'akun_piutang.kd_pj');

        // Filter Daftar No. Rawat Khusus (misal dari Import Excel / Paste)
        $noRawatList = $request->no_rawat_list;
        $rawatArr = [];
        if (!empty($noRawatList)) {
            if (is_array($noRawatList)) {
                $rawatArr = $noRawatList;
            } else {
                $rawatArr = preg_split('/[\r\n,]+/', $noRawatList);
            }
            $cleanRawats = [];
            foreach ($rawatArr as $r) {
                $item = trim(str_replace(['"', "'"], '', $r));
                if (!empty($item)) {
                    $cleanRawats[] = $item;
                    $cleanRawats[] = str_replace('-', '/', $item);
                }
            }
            $rawatArr = array_values(array_unique(array_filter($cleanRawats)));
            if (!empty($rawatArr)) {
                $query->whereIn('piutang_pasien.no_rawat', $rawatArr);
                // Reset tanggal agar tidak membatasi No. Rawat dari file Excel
                $tgl1 = null;
                $tgl2 = null;
            }
        }

        // KUNCI HANYA STATUS 'Belum Lunas' (Sesuai instruksi: data yang sudah lunas jangan dimunculkan lagi)
        $query->where('piutang_pasien.status', 'Belum Lunas');

        // Filter Penagihan Piutang (Khanza tampiltagihan logic)
        if (empty($rawatArr) && !empty($no_tagihan)) {
            $query->whereIn('piutang_pasien.no_rawat', function ($sub) use ($no_tagihan) {
                $sub->select('detail_penagihan_piutang.no_rawat')
                    ->from('detail_penagihan_piutang')
                    ->where('detail_penagihan_piutang.no_tagihan', $no_tagihan);
            });
        }

        // Filter Cara Bayar / Penjamin (hanya jika tidak ada filter no_rawat khusus)
        if (empty($rawatArr) && !empty($kd_pj)) {
            $query->where('penjab.kd_pj', $kd_pj);
        }

        // Filter Tanggal Piutang (hanya jika tidak ada filter no_rawat khusus)
        if (empty($rawatArr)) {
            if (!empty($tgl1) && !empty($tgl2)) {
                $query->whereBetween('piutang_pasien.tgl_piutang', [$tgl1, $tgl2]);
            } elseif (!empty($tgl1)) {
                $query->where('piutang_pasien.tgl_piutang', '>=', $tgl1);
            } elseif (!empty($tgl2)) {
                $query->where('piutang_pasien.tgl_piutang', '<=', $tgl2);
            }
        }

        // Filter Pencarian Teks (No.Rawat / RM / Nama Pasien)
        if (!empty($cari)) {
            $query->where(function ($q) use ($cari) {
                $q->where('piutang_pasien.no_rawat', 'like', "%{$cari}%")
                    ->orWhere('piutang_pasien.no_rkm_medis', 'like', "%{$cari}%")
                    ->orWhere('pasien.nm_pasien', 'like', "%{$cari}%");
            });
        }

        $query->orderBy('piutang_pasien.tgl_piutang', 'asc');
        $limit = !empty($rawatArr) ? 2000 : 500;
        $piutangData = $query->limit($limit)->get();

        $noRawats = $piutangData->pluck('no_rawat')->toArray();

        // Ambil cicilan yang sudah dibayar sebelumnya dari bayar_piutang
        $sudahBayar = collect();
        $bridgingSep = collect();
        if (!empty($noRawats)) {
            $sudahBayar = DB::table('bayar_piutang')
                ->whereIn('no_rawat', $noRawats)
                ->select(
                    'no_rawat',
                    DB::raw('IFNULL(SUM(besar_cicilan),0) as sum_cicilan'),
                    DB::raw('IFNULL(SUM(diskon_piutang),0) as sum_diskon'),
                    DB::raw('IFNULL(SUM(tidak_terbayar),0) as sum_tidak_terbayar'),
                    DB::raw('IFNULL(SUM(besar_cicilan + diskon_piutang + tidak_terbayar),0) as total_sudah_bayar')
                )
                ->groupBy('no_rawat')
                ->get()
                ->keyBy('no_rawat');

            // Ambil Nomor SEP untuk pencocokan umbal CSV
            $bridgingSep = DB::table('bridging_sep')
                ->whereIn('no_rawat', $noRawats)
                ->select('no_rawat', 'no_sep')
                ->get()
                ->groupBy('no_rawat');
        }

        // Map data dengan cicilan dan sisa piutang aktual
        $piutangData->transform(function ($item) use ($sudahBayar, $bridgingSep) {
            $bayarInfo = $sudahBayar->get($item->no_rawat);
            $item->sudah_dibayar = $bayarInfo ? (float)$bayarInfo->total_sudah_bayar : 0.0;
            $item->sum_cicilan = $bayarInfo ? (float)$bayarInfo->sum_cicilan : 0.0;
            $item->sum_diskon = $bayarInfo ? (float)$bayarInfo->sum_diskon : 0.0;
            $item->sum_tidak_terbayar = $bayarInfo ? (float)$bayarInfo->sum_tidak_terbayar : 0.0;

            // Sisa piutang aktual = Total Piutang - Uang Muka - Sudah Dibayar Sebelumnya
            $sisaAktual = (float)$item->totalpiutang - (float)$item->uangmuka - $item->sudah_dibayar;
            $item->sisa_piutang_aktual = max(0, $sisaAktual);

            // Nomor SEP
            $seps = $bridgingSep->get($item->no_rawat);
            $item->no_sep = $seps && $seps->isNotEmpty() ? $seps->first()->no_sep : '';

            return $item;
        });

        // Saring hanya data yang benar-benar belum lunas (sisa piutang > 0)
        $piutangData = $piutangData->filter(function ($item) {
            return $item->status === 'Belum Lunas' && $item->sisa_piutang_aktual > 0;
        })->values();

        // Ambil daftar petugas aktif untuk petugas pelunasan
        $petugasList = DB::table('petugas')
            ->where('status', '1')
            ->select('nip', 'nama')
            ->orderBy('nama', 'asc')
            ->get();

        // Tentukan default NIP petugas pelunasan
        $user = Auth::user();
        $defaultNip = '';
        if ($user) {
            $match = DB::table('petugas')->where('nip', $user->username)->first();
            if (!$match && !empty($user->name)) {
                $match = DB::table('petugas')->where('nama', 'like', '%' . $user->name . '%')->first();
            }
            if ($match) {
                $defaultNip = $match->nip;
            }
        }
        if (empty($defaultNip)) {
            $latestCatatan = DB::table('bayar_piutang')
                ->where('catatan', 'like', 'diverifikasi oleh %')
                ->orderBy('tgl_bayar', 'desc')
                ->value('catatan');
            if ($latestCatatan) {
                $defaultNip = trim(str_replace('diverifikasi oleh', '', $latestCatatan));
            }
        }

        // Hitung transaksi pelunasan yang dibuat hari ini untuk opsi pembatalan/rollback
        $todayBayarCount = DB::table('bayar_piutang')
            ->where('tgl_bayar', date('Y-m-d'))
            ->count();

        return view('keuangan.piutangBelumLunas', [
            'piutangData'     => $piutangData,
            'penjab'          => $penjab,
            'akunBayar'       => $akunBayar,
            'petugasList'     => $petugasList,
            'defaultNip'      => $defaultNip,
            'todayBayarCount' => $todayBayarCount,
            'setAkun'         => $setAkun,
            'penagihanList'   => $penagihanList,
            'tagihanDetail'   => $tagihanDetail,
            'cari'            => $cari,
            'tgl1'            => $tgl1,
            'tgl2'            => $tgl2,
            'kd_pj'           => $kd_pj,
            'no_tagihan'      => $no_tagihan,
            'statusLunas'     => $statusLunas,
            'no_rawat_list'   => is_array($noRawatList) ? implode("\n", $noRawatList) : $noRawatList,
        ]);
    }

    /**
     * Simpan Pelunasan Piutang Pasien (Sesuai Logika Java BtnBayarActionPerformed)
     */
    public function simpanBayar(Request $request)
    {
        $request->validate([
            'tgl_bayar'   => 'required|date',
            'kd_rek'      => 'required|string',
            'nip_petugas' => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.no_rawat'      => 'required|string',
            'items.*.no_rkm_medis'  => 'required|string',
            'items.*.bayar'         => 'required|numeric|min:0',
            'items.*.diskon'        => 'nullable|numeric|min:0',
            'items.*.tidak_terbayar'=> 'nullable|numeric|min:0',
        ]);

        $tglBayar = $request->tgl_bayar;
        $kdRekBayar = $request->kd_rek;
        $nipPetugas = $request->nip_petugas;
        $items = $request->items;

        $setAkun = DB::table('set_akun')->first();
        $diskonPiutangRek = $setAkun->Diskon_Piutang ?? '';
        $tidakTerbayarRek = $setAkun->Piutang_Tidak_Terbayar ?? '';

        // Tentukan identitas petugas pelunasan untuk kolom catatan (sesuai Khanza: diverifikasi oleh <NIP>)
        $petugasRow = !empty($nipPetugas) ? DB::table('petugas')->where('nip', $nipPetugas)->first() : null;
        $kodePetugas = $petugasRow ? $petugasRow->nip : (Auth::user()->username ?? 'Admin');
        $catatan = 'diverifikasi oleh ' . $kodePetugas;

        $suksesCount = 0;
        $totalBayarSemua = 0;

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $noRawat = $item['no_rawat'];
                $noRkmMedis = $item['no_rkm_medis'];
                $besarCicilan = (float)($item['bayar'] ?? 0);
                $diskonPiutang = (float)($item['diskon'] ?? 0);
                $tidakTerbayar = (float)($item['tidak_terbayar'] ?? 0);
                $kdRekKontra = $item['kd_rek_kontra'] ?? '';

                $totalPelunasanBaris = $besarCicilan + $diskonPiutang + $tidakTerbayar;
                if ($totalPelunasanBaris <= 0) {
                    continue;
                }

                // Ambil data piutang awal untuk validasi
                $piutangRow = DB::table('piutang_pasien')->where('no_rawat', $noRawat)->first();
                if (!$piutangRow) {
                    continue;
                }

                // Hitung total yang sudah dibayar sebelum transaksi ini
                $sudahDibayarSebelumnya = (float)DB::table('bayar_piutang')
                    ->where('no_rawat', $noRawat)
                    ->sum(DB::raw('besar_cicilan + diskon_piutang + tidak_terbayar'));

                $sisaSebelumBayar = (float)$piutangRow->totalpiutang - (float)$piutangRow->uangmuka - $sudahDibayarSebelumnya;

                if ($totalPelunasanBaris > ($sisaSebelumBayar + 1)) {
                    throw new \Exception("Nilai pelunasan untuk No. Rawat {$noRawat} ({$totalPelunasanBaris}) melebihi sisa piutang ({$sisaSebelumBayar})!");
                }

                // 1. Simpan ke tabel bayar_piutang
                DB::table('bayar_piutang')->insert([
                    'tgl_bayar'             => $tglBayar,
                    'no_rkm_medis'          => $noRkmMedis,
                    'besar_cicilan'         => $besarCicilan,
                    'catatan'               => $catatan,
                    'no_rawat'              => $noRawat,
                    'kd_rek'                => $kdRekBayar,
                    'kd_rek_kontra'         => $kdRekKontra,
                    'diskon_piutang'        => $diskonPiutang,
                    'kd_rek_diskon_piutang' => $diskonPiutangRek,
                    'tidak_terbayar'        => $tidakTerbayar,
                    'kd_rek_tidak_terbayar' => $tidakTerbayarRek,
                ]);

                // 2. Hitung sisa akhir piutang
                $sisaAkhir = $sisaSebelumBayar - $totalPelunasanBaris;

                // 3. Update status piutang_pasien jika sudah lunas (sisa <= 1)
                $updateData = [
                    'sisapiutang' => max(0, $sisaAkhir)
                ];
                if ($sisaAkhir <= 1) {
                    $updateData['status'] = 'Lunas';
                }
                DB::table('piutang_pasien')->where('no_rawat', $noRawat)->update($updateData);

                // 4. Update detail_piutang_pasien jika ada record
                if (DB::getSchemaBuilder()->hasTable('detail_piutang_pasien')) {
                    DB::table('detail_piutang_pasien')
                        ->where('no_rawat', $noRawat)
                        ->update([
                            'sisapiutang' => DB::raw("GREATEST(0, sisapiutang - {$totalPelunasanBaris})")
                        ]);
                }

                $suksesCount++;
                $totalBayarSemua += $totalPelunasanBaris;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil memproses pembayaran untuk {$suksesCount} data piutang dengan total Rp " . number_format($totalBayarSemua, 0, ',', '.'),
                'sukses_count' => $suksesCount,
                'total_bayar' => $totalBayarSemua,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Simpan Pelunasan Piutang: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Batalkan Transaksi Pembayaran yang dibuat Hari Ini (Rollback ke Belum Lunas)
     */
    public function batalBayarHariIni(Request $request)
    {
        $tglBayar = $request->tgl_bayar ?? date('Y-m-d');
        $todayBayars = DB::table('bayar_piutang')->where('tgl_bayar', $tglBayar)->get();

        if ($todayBayars->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan transaksi pembayaran pada tanggal ' . $tglBayar . ' untuk dibatalkan.'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $rawatList = $todayBayars->pluck('no_rawat')->unique();

            // 1. Hapus transaksi di bayar_piutang pada tanggal tersebut
            DB::table('bayar_piutang')->where('tgl_bayar', $tglBayar)->delete();

            // 2. Kembalikan status & sisapiutang di piutang_pasien
            $countUpdated = 0;
            foreach ($rawatList as $noRawat) {
                $piutang = DB::table('piutang_pasien')->where('no_rawat', $noRawat)->first();
                if (!$piutang) continue;

                // Hitung sisa pembayaran di luar tanggal yang dibatalkan
                $sudahBayarLain = (float)DB::table('bayar_piutang')
                    ->where('no_rawat', $noRawat)
                    ->sum(DB::raw('besar_cicilan + diskon_piutang + tidak_terbayar'));

                $sisaAktual = (float)$piutang->totalpiutang - (float)$piutang->uangmuka - $sudahBayarLain;
                $status = ($sisaAktual <= 1) ? 'Lunas' : 'Belum Lunas';

                DB::table('piutang_pasien')->where('no_rawat', $noRawat)->update([
                    'sisapiutang' => max(0, $sisaAktual),
                    'status'      => $status,
                ]);

                if (DB::getSchemaBuilder()->hasTable('detail_piutang_pasien')) {
                    DB::table('detail_piutang_pasien')->where('no_rawat', $noRawat)->update([
                        'sisapiutang' => max(0, $sisaAktual),
                    ]);
                }

                $countUpdated++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil membatalkan {$todayBayars->count()} transaksi pembayaran ({$countUpdated} pasien). Status piutang telah dikembalikan ke Belum Lunas!"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Batal Pelunasan Piutang: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
}
