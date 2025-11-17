<?php

namespace App\Http\Controllers\Regperiksa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KroscekPasien extends Controller
{
    /**
     * Menampilkan halaman view kroscek pasien
     */
    public function index(Request $request)
    {
        // 1. Ambil semua input
        $tanggal = $request->get('tanggal', '');
        $tanggalMulai = $request->get('tanggal_mulai', '');
        $tanggalSelesai = $request->get('tanggal_selesai', '');
        $searchTerm = $request->get('search', '');
        $filterStatus = $request->get('filter_status', '');
        $filterType = $request->get('filter_type', 'semua');
        $perPage = $request->get('per_page', 100);
        $excludedPoli = $request->get('excluded_poli', []); // <-- [UBAH] Ganti nama variabel

        // 2. Tentukan Mode Tanggal Aktif & Set Default
        $isRangeActive = !empty($tanggalMulai) && !empty($tanggalSelesai);

        if ($isRangeActive) {
            // Jika mode range aktif, kosongkan tanggal tunggal
            $tanggal = '';
        } else {
            // Jika mode range tidak aktif, kosongkan tanggal range
            $tanggalMulai = '';
            $tanggalSelesai = '';

            // Jika tanggal tunggal kosong, set default hari ini
            if (empty($tanggal)) {
                $tanggal = Carbon::now()->format('Y-m-d');
            }
        }

        // Ambil daftar semua poli untuk view
        $allPoli = DB::table('poliklinik')
                        ->select('kd_poli', 'nm_poli')
                        ->where('status', '1') // Asumsi '1' = aktif
                        ->orderBy('nm_poli', 'asc')
                        ->get();

        // 3. Ambil data dengan mode tanggal yang sudah disinkronkan
        // [UBAH] Teruskan $excludedPoli
        $statistik = $this->getStatistikData($tanggal, $tanggalMulai, $tanggalSelesai, $excludedPoli);
        $daftarPasienBelumNota = $this->getDaftarPasienData($tanggal, $tanggalMulai, $tanggalSelesai, $searchTerm, $filterStatus, $filterType, $perPage, $excludedPoli);

        return view('regperiksa.kroscek-pasien', compact(
            'statistik',
            'daftarPasienBelumNota',
            'tanggal',
            'tanggalMulai',
            'tanggalSelesai',
            'searchTerm',
            'filterStatus',
            'filterType',
            'perPage',
            'allPoli',
            'excludedPoli'  // <-- [UBAH] Kirim ke view
        ));
    }

    /**
     * Mendapatkan data statistik dengan detail IGD dan exclude pasien batal dari progress
     */
    // [UBAH] Tambahkan parameter $excludedPoli
    private function getStatistikData($tanggal, $tanggalMulai = null, $tanggalSelesai = null, $excludedPoli = [])
    {
        try {
            $query = DB::table('reg_periksa as rp')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->leftJoin('nota_inap as ni', 'rp.no_rawat', '=', 'ni.no_rawat')
                ->leftJoin('nota_jalan as nj', 'rp.no_rawat', '=', 'nj.no_rawat');

            // Filter tanggal: Prioritaskan Rentang Tanggal
            if ($tanggalMulai && $tanggalSelesai) {
                $query->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);
            } else {
                $query->where('rp.tgl_registrasi', $tanggal);
            }

            // [UBAH] Terapkan filter pengecualian poli
            if (!empty($excludedPoli)) {
                $query->whereNotIn('rp.kd_poli', $excludedPoli);
            }

            $result = $query->selectRaw('
                COUNT(DISTINCT rp.no_rawat) as total_pasien,
                SUM(CASE WHEN rp.status_lanjut = "Ralan" AND rp.kd_poli != "IGDK" THEN 1 ELSE 0 END) as total_ralan,
                SUM(CASE WHEN rp.kd_poli = "IGDK" THEN 1 ELSE 0 END) as total_igd,
                SUM(CASE WHEN rp.status_lanjut = "Ranap" AND rp.kd_poli = "IGDK" THEN 1 ELSE 0 END) as total_ranap_igd,
                SUM(CASE WHEN rp.status_lanjut = "Ranap" AND rp.kd_poli != "IGDK" THEN 1 ELSE 0 END) as total_ranap_poli,
                SUM(CASE WHEN rp.stts = "Batal" THEN 1 ELSE 0 END) as total_batal,
                SUM(CASE
                    WHEN rp.stts <> "Batal"
                        AND rp.status_lanjut = "Ralan"
                        AND rp.kd_poli != "IGDK"
                        AND ni.no_nota IS NULL
                        AND nj.no_nota IS NULL
                    THEN 1 ELSE 0
                END) as total_belum_nota,
                SUM(CASE
                    WHEN rp.stts <> "Batal"
                        AND (ni.no_nota IS NOT NULL OR nj.no_nota IS NOT NULL)
                    THEN 1 ELSE 0
                END) as total_sudah_nota,
                SUM(CASE WHEN rp.stts <> "Batal" THEN 1 ELSE 0 END) as total_pasien_aktif
            ')->first();

            return $result;
        } catch (\Exception $e) {
            // Return default values if error
            return (object) [
                'total_pasien' => 0,
                'total_ralan' => 0,
                'total_igd' => 0,
                'total_ranap_igd' => 0,
                'total_ranap_poli' => 0,
                'total_batal' => 0,
                'total_belum_nota' => 0,
                'total_sudah_nota' => 0,
                'total_pasien_aktif' => 0
            ];
        }
    }

    /**
     * Mendapatkan daftar pasien berdasarkan filter dengan sorting berdasarkan no_rawat ASC
     */
    // [UBAH] Tambahkan parameter $excludedPoli
    private function getDaftarPasienData($tanggal, $tanggalMulai = null, $tanggalSelesai = null, $searchTerm = '', $filterStatus = '', $filterType = 'semua', $perPage = 100, $excludedPoli = [])
    {
        try {
            $query = DB::table('reg_periksa as rp')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->leftJoin('nota_inap as ni', 'rp.no_rawat', '=', 'ni.no_rawat')
                ->leftJoin('nota_jalan as nj', 'rp.no_rawat', '=', 'nj.no_rawat')
                ->leftJoin('poliklinik as pol', 'rp.kd_poli', '=', 'pol.kd_poli');

            // Filter tanggal: Prioritaskan Rentang Tanggal
            if ($tanggalMulai && $tanggalSelesai) {
                $query->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);
            } else {
                $query->where('rp.tgl_registrasi', $tanggal);
            }

            // [UBAH] Terapkan filter pengecualian poli
            if (!empty($excludedPoli)) {
                $query->whereNotIn('rp.kd_poli', $excludedPoli);
            }

            $query->select([
                'rp.no_rawat',
                'rp.no_rkm_medis',
                'p.nm_pasien',
                'rp.status_lanjut',
                'rp.kd_poli',
                'pol.nm_poli',
                'rp.tgl_registrasi',
                'rp.jam_reg',
                'rp.stts',
                DB::raw('CASE
                    WHEN ni.no_nota IS NOT NULL OR nj.no_nota IS NOT NULL THEN "Sudah Nota"
                    WHEN rp.stts = "Batal" THEN "Batal"
                    ELSE "Belum Nota"
                END as status_nota')
            ]);

            // Filter berdasarkan filter type
            switch ($filterType) {
                case 'semua':
                    break;
                case 'batal':
                    $query->where('rp.stts', 'Batal');
                    break;
                case 'igd':
                    $query->where('rp.status_lanjut', 'Ranap')
                        ->where('rp.kd_poli', 'IGDK');
                    break;
                case 'ralan':
                    $query->where('rp.status_lanjut', 'Ralan')
                        ->where('rp.kd_poli', '!=', 'IGDK');
                    break;
                case 'ranap_poli':
                    $query->where('rp.status_lanjut', 'Ranap')
                        ->where('rp.kd_poli', '!=', 'IGDK');
                    break;
                case 'belum_nota':
                    $query->where('rp.stts', '<>', 'Batal')
                        ->where('rp.status_lanjut', 'Ralan')
                        ->where('rp.kd_poli', '!=', 'IGDK')
                        ->whereNull('ni.no_nota')
                        ->whereNull('nj.no_nota');
                    break;
            }

            // Apply search filter
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('rp.no_rawat', 'like', "%{$searchTerm}%")
                        ->orWhere('p.nm_pasien', 'like', "%{$searchTerm}%")
                        ->orWhere('rp.no_rkm_medis', 'like', "%{$searchTerm}%")
                        ->orWhere('pol.nm_poli', 'like', "%{$searchTerm}%");
                });
            }

            // Apply status filter (filter tambahan di dalam kategori yang dipilih)
            if ($filterStatus) {
                switch ($filterStatus) {
                    case 'Ranap':
                        $query->where('rp.status_lanjut', 'Ranap');
                        break;
                    case 'Ralan':
                        $query->where('rp.status_lanjut', 'Ralan');
                        break;
                    case 'IGD':
                        $query->where('rp.kd_poli', 'IGDK');
                        break;
                    case 'Sudah_Nota':
                        $query->where(function($q) {
                            $q->whereNotNull('ni.no_nota')
                                ->orWhereNotNull('nj.no_nota');
                        });
                        break;
                    case 'Belum_Nota':
                        $query->where('rp.stts', '<>', 'Batal')
                            ->whereNull('ni.no_nota')
                            ->whereNull('nj.no_nota');
                        break;
                }
            }

            // Sort by no_rawat ascending (natural sorting for varchar with numbers)
            return $query->orderByRaw('CAST(rp.no_rawat AS UNSIGNED) ASC, rp.no_rawat ASC')
                ->paginate($perPage);

        } catch (\Exception $e) {
            // Return empty paginator if error
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, $perPage, 1, ['path' => request()->url()]
            );
        }
    }

    /**
     * Mendapatkan statistik kroscek pasien berdasarkan tanggal registrasi
     */
    public function getStatistikPasien(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable|date|date_format:Y-m-d',
            'tanggal_mulai' => 'nullable|date|date_format:Y-m-d',
            'tanggal_selesai' => 'nullable|date|date_format:Y-m-d|after_or_equal:tanggal_mulai',
            'excluded_poli' => 'nullable|array' // <-- [UBAH] Validasi
        ]);

        $tanggal = $request->input('tanggal', Carbon::now()->format('Y-m-d'));
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $excludedPoli = $request->input('excluded_poli', []); // <-- [UBAH] Ambil input

        // Sinkronisasi data filter: jika ada range, abaikan tanggal tunggal.
        if (!empty($tanggalMulai) && !empty($tanggalSelesai)) {
            $tanggal = null;
        } else {
            $tanggalMulai = null;
            $tanggalSelesai = null;
        }

        // [UBAH] Teruskan $excludedPoli
        $statistik = $this->getStatistikData($tanggal, $tanggalMulai, $tanggalSelesai, $excludedPoli);

        return response()->json([
            'success' => true,
            'message' => 'Data kroscek pasien berhasil diambil',
            'data' => [
                'tanggal' => $tanggal,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'excluded_poli' => $excludedPoli, // <-- [UBAH]
                'statistik' => $statistik,
                'progress' => [
                    'total_pasien_aktif' => $statistik->total_pasien_aktif,
                    'sudah_nota' => $statistik->total_sudah_nota,
                    'belum_nota' => $statistik->total_belum_nota,
                    'batal' => $statistik->total_batal,
                    'percentage' => $statistik->total_pasien_aktif > 0 ?
                        ($statistik->total_sudah_nota / $statistik->total_pasien_aktif) * 100 : 0
                ]
            ]
        ], 200);
    }

    /**
     * Mendapatkan statistik kroscek pasien untuk hari ini
     */
    public function getStatistikHariIni()
    {
        $tanggalHariIni = Carbon::now()->format('Y-m-d');
        // Panggil helper dengan $excludedPoli default (array kosong)
        $statistik = $this->getStatistikData($tanggalHariIni);

        return response()->json([
            'success' => true,
            'message' => 'Data kroscek pasien hari ini berhasil diambil',
            'data' => [
                'tanggal' => $tanggalHariIni,
                'statistik' => $statistik,
                'progress' => [
                    'total_pasien_aktif' => $statistik->total_pasien_aktif,
                    'sudah_nota' => $statistik->total_sudah_nota,
                    'belum_nota' => $statistik->total_belum_nota,
                    'batal' => $statistik->total_batal,
                    'percentage' => $statistik->total_pasien_aktif > 0 ?
                        ($statistik->total_sudah_nota / $statistik->total_pasien_aktif) * 100 : 0
                ]
            ]
        ], 200);
    }

    /**
     * Mendapatkan statistik kroscek pasien dalam rentang tanggal
     */
    public function getStatistikRentangTanggal(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date|date_format:Y-m-d',
            'tanggal_selesai' => 'required|date|date_format:Y-m-d|after_or_equal:tanggal_mulai',
            'excluded_poli' => 'nullable|array' // <-- [UBAH] Validasi
        ]);

        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $excludedPoli = $request->input('excluded_poli', []); // <-- [UBAH] Ambil input

        // Panggil helper dengan tanggal tunggal diset null
        // [UBAH] Teruskan $excludedPoli
        $statistik = $this->getStatistikData(null, $tanggalMulai, $tanggalSelesai, $excludedPoli);

        return response()->json([
            'success' => true,
            'message' => 'Data kroscek pasien dalam rentang tanggal berhasil diambil',
            'data' => [
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'excluded_poli' => $excludedPoli, // <-- [UBAH]
                'statistik' => $statistik,
                'progress' => [
                    'total_pasien_aktif' => $statistik->total_pasien_aktif,
                    'sudah_nota' => $statistik->total_sudah_nota,
                    'belum_nota' => $statistik->total_belum_nota,
                    'batal' => $statistik->total_batal,
                    'percentage' => $statistik->total_pasien_aktif > 0 ?
                        ($statistik->total_sudah_nota / $statistik->total_pasien_aktif) * 100 : 0
                ]
            ]
        ], 200);
    }

    /**
     * Mendapatkan detail daftar pasien berdasarkan filter dengan sorting no_rawat ASC
     */
    public function getDaftarPasienBelumNota(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable|date|date_format:Y-m-d',
            'tanggal_mulai' => 'nullable|date|date_format:Y-m-d',
            'tanggal_selesai' => 'nullable|date|date_format:Y-m-d|after_or_equal:tanggal_mulai',
            'search' => 'nullable|string|max:100',
            'filter_status' => 'nullable|string|in:Ranap,Ralan,IGD,Sudah_Nota,Belum_Nota',
            'filter_type' => 'nullable|string|in:semua,batal,igd,ralan,ranap_poli,belum_nota',
            'per_page' => 'nullable|integer|min:10|max:500',
            'excluded_poli' => 'nullable|array' // <-- [UBAH] Validasi
        ]);

        $tanggal = $request->input('tanggal', Carbon::now()->format('Y-m-d'));
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $searchTerm = $request->input('search', '');
        $filterStatus = $request->input('filter_status', '');
        $filterType = $request->input('filter_type', 'semua');
        $perPage = $request->input('per_page', 100);
        $excludedPoli = $request->input('excluded_poli', []); // <-- [UBAH] Ambil input

        // Sinkronisasi data filter: jika ada range, abaikan tanggal tunggal.
        if (!empty($tanggalMulai) && !empty($tanggalSelesai)) {
            $tanggal = null;
        } else {
            $tanggalMulai = null;
            $tanggalSelesai = null;
        }
        // Jika mode tunggal, pastikan $tanggal ada nilainya (minimal hari ini)
        if (!$tanggalMulai && !$tanggalSelesai && empty($tanggal)) {
            $tanggal = Carbon::now()->format('Y-m-d');
        }

        try {
            $query = DB::table('reg_periksa as rp')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->leftJoin('nota_inap as ni', 'rp.no_rawat', '=', 'ni.no_rawat')
                ->leftJoin('nota_jalan as nj', 'rp.no_rawat', '=', 'nj.no_rawat')
                ->leftJoin('poliklinik as pol', 'rp.kd_poli', '=', 'pol.kd_poli');

            // Filter tanggal: Prioritaskan Rentang Tanggal
            if ($tanggalMulai && $tanggalSelesai) {
                $query->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);
            } else {
                $query->where('rp.tgl_registrasi', $tanggal);
            }

            // [UBAH] Terapkan filter pengecualian poli
            if (!empty($excludedPoli)) {
                $query->whereNotIn('rp.kd_poli', $excludedPoli);
            }

            $query->select([
                'rp.no_rawat',
                'rp.no_rkm_medis',
                'p.nm_pasien',
                'rp.status_lanjut',
                'rp.kd_poli',
                'pol.nm_poli',
                'rp.tgl_registrasi',
                'rp.jam_reg',
                'rp.stts',
                DB::raw('CASE
                    WHEN ni.no_nota IS NOT NULL OR nj.no_nota IS NOT NULL THEN "Sudah Nota"
                    WHEN rp.stts = "Batal" THEN "Batal"
                    ELSE "Belum Nota"
                END as status_nota')
            ]);

            // Filter berdasarkan filter type
            switch ($filterType) {
                case 'semua':
                    break;
                case 'batal':
                    $query->where('rp.stts', 'Batal');
                    break;
                case 'igd':
                    $query->where('rp.status_lanjut', 'Ranap')
                        ->where('rp.kd_poli', 'IGDK');
                    break;
                case 'ralan':
                    $query->where('rp.status_lanjut', 'Ralan')
                        ->where('rp.kd_poli', '!=', 'IGDK');
                    break;
                case 'ranap_poli':
                    $query->where('rp.status_lanjut', 'Ranap')
                        ->where('rp.kd_poli', '!=', 'IGDK');
                    break;
                case 'belum_nota':
                    $query->where('rp.stts', '<>', 'Batal')
                        ->where('rp.status_lanjut', 'Ralan')
                        ->where('rp.kd_poli', '!=', 'IGDK')
                        ->whereNull('ni.no_nota')
                        ->whereNull('nj.no_nota');
                    break;
            }

            // Apply search filter
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('rp.no_rawat', 'like', "%{$searchTerm}%")
                        ->orWhere('p.nm_pasien', 'like', "%{$searchTerm}%")
                        ->orWhere('rp.no_rkm_medis', 'like', "%{$searchTerm}%")
                        ->orWhere('pol.nm_poli', 'like', "%{$searchTerm}%");
                });
            }

            // Apply status filter (filter tambahan di dalam kategori yang dipilih)
            if ($filterStatus) {
                switch ($filterStatus) {
                    case 'Ranap':
                        $query->where('rp.status_lanjut', 'Ranap');
                        break;
                    case 'Ralan':
                        $query->where('rp.status_lanjut', 'Ralan');
                        break;
                    case 'IGD':
                        $query->where('rp.kd_poli', 'IGDK');
                        break;
                    case 'Sudah_Nota':
                        $query->where(function($q) {
                            $q->whereNotNull('ni.no_nota')
                                ->orWhereNotNull('nj.no_nota');
                        });
                        break;
                    case 'Belum_Nota':
                        $query->where('rp.stts', '<>', 'Batal')
                            ->whereNull('ni.no_nota')
                            ->whereNull('nj.no_nota');
                        break;
                }
            }

            // Sort by no_rawat ascending (natural sorting for varchar with numbers)
            $result = $query->orderByRaw('CAST(rp.no_rawat AS UNSIGNED) ASC, rp.no_rawat ASC')->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar pasien berhasil diambil',
                'data' => [
                    'tanggal' => $tanggal,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                    'filter_type' => $filterType,
                    'filter_status' => $filterStatus,
                    'search_term' => $searchTerm,
                    'per_page' => $perPage,
                    'excluded_poli' => $excludedPoli, // <-- [UBAH]
                    'total' => $result->count(),
                    'pasien' => $result
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil daftar pasien',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
