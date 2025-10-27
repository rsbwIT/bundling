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
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Set default values - DEFAULT FILTER IS 'semua' (All Patients)
        $tanggal = $request->get('tanggal', Carbon::now()->format('Y-m-d'));
        $tanggalMulai = $request->get('tanggal_mulai', '');
        $tanggalSelesai = $request->get('tanggal_selesai', '');
        $searchTerm = $request->get('search', '');
        $filterStatus = $request->get('filter_status', '');
        $filterType = $request->get('filter_type', 'semua');
        $perPage = $request->get('per_page', 100);

        // Ambil statistik
        $statistik = $this->getStatistikData($tanggal, $tanggalMulai, $tanggalSelesai);

        // Ambil daftar pasien berdasarkan filter
        $daftarPasienBelumNota = $this->getDaftarPasienData($tanggal, $searchTerm, $filterStatus, $filterType, $perPage);

        return view('regperiksa.kroscek-pasien', compact(
            'statistik',
            'daftarPasienBelumNota',
            'tanggal',
            'tanggalMulai',
            'tanggalSelesai',
            'searchTerm',
            'filterStatus',
            'filterType',
            'perPage'
        ));
    }

    /**
     * Mendapatkan data statistik dengan detail IGD dan exclude pasien batal dari progress
     */
    private function getStatistikData($tanggal, $tanggalMulai = null, $tanggalSelesai = null)
    {
        try {
            $query = DB::table('reg_periksa as rp')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->leftJoin('nota_inap as ni', 'rp.no_rawat', '=', 'ni.no_rawat')
                ->leftJoin('nota_jalan as nj', 'rp.no_rawat', '=', 'nj.no_rawat');

            // Filter tanggal
            if ($tanggalMulai && $tanggalSelesai) {
                $query->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);
            } else {
                $query->where('rp.tgl_registrasi', $tanggal);
            }

            $result = $query->selectRaw('
                COUNT(DISTINCT rp.no_rawat) as total_pasien,

                -- Rawat Jalan (status_lanjut = Ralan dan bukan IGD)
                SUM(CASE WHEN rp.status_lanjut = "Ralan" AND rp.kd_poli != "IGDK" THEN 1 ELSE 0 END) as total_ralan,

                -- Total IGD (semua yang kd_poli = IGDK)
                SUM(CASE WHEN rp.kd_poli = "IGDK" THEN 1 ELSE 0 END) as total_igd,

                -- Rawat Inap lewat IGD (status_lanjut = Ranap AND kd_poli = IGDK)
                SUM(CASE WHEN rp.status_lanjut = "Ranap" AND rp.kd_poli = "IGDK" THEN 1 ELSE 0 END) as total_ranap_igd,

                -- Rawat Inap lewat Poli (status_lanjut = Ranap AND kd_poli != IGDK)
                SUM(CASE WHEN rp.status_lanjut = "Ranap" AND rp.kd_poli != "IGDK" THEN 1 ELSE 0 END) as total_ranap_poli,

                -- Batal
                SUM(CASE WHEN rp.stts = "Batal" THEN 1 ELSE 0 END) as total_batal,

                -- Belum ada nota RAWAT JALAN (tidak batal, rawat jalan, bukan IGD, belum ada nota)
                SUM(CASE
                    WHEN rp.stts <> "Batal"
                         AND rp.status_lanjut = "Ralan"
                         AND rp.kd_poli != "IGDK"
                         AND ni.no_nota IS NULL
                         AND nj.no_nota IS NULL
                    THEN 1 ELSE 0
                END) as total_belum_nota,

                -- Sudah ada nota (tidak batal dan sudah ada nota - untuk progress)
                SUM(CASE
                    WHEN rp.stts <> "Batal"
                         AND (ni.no_nota IS NOT NULL OR nj.no_nota IS NOT NULL)
                    THEN 1 ELSE 0
                END) as total_sudah_nota,

                -- Total pasien yang tidak batal (untuk perhitungan progress)
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
     * Filter "Belum Nota" hanya menampilkan pasien rawat jalan
     */
    private function getDaftarPasienData($tanggal, $searchTerm = '', $filterStatus = '', $filterType = 'semua', $perPage = 100)
    {
        try {
            $query = DB::table('reg_periksa as rp')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->leftJoin('nota_inap as ni', 'rp.no_rawat', '=', 'ni.no_rawat')
                ->leftJoin('nota_jalan as nj', 'rp.no_rawat', '=', 'nj.no_rawat')
                ->leftJoin('poliklinik as pol', 'rp.kd_poli', '=', 'pol.kd_poli')
                ->where('rp.tgl_registrasi', $tanggal)
                ->select([
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
                    // Tampilkan semua reg pasien (tidak ada filter tambahan) - DEFAULT
                    break;
                case 'batal':
                    // Hanya yang batal
                    $query->where('rp.stts', 'Batal');
                    break;
                case 'igd':
                    // Pasien ranap IGD (ranap yang lewat IGD)
                    $query->where('rp.status_lanjut', 'Ranap')
                          ->where('rp.kd_poli', 'IGDK');
                    break;
                case 'ralan':
                    // Pasien khusus rawat jalan (bukan IGD)
                    $query->where('rp.status_lanjut', 'Ralan')
                          ->where('rp.kd_poli', '!=', 'IGDK');
                    break;
                case 'ranap_poli':
                    // Pasien ranap poli (ranap yang tidak lewat IGD)
                    $query->where('rp.status_lanjut', 'Ranap')
                          ->where('rp.kd_poli', '!=', 'IGDK');
                    break;
                case 'belum_nota':
                    // CHANGED: Yang belum nota KHUSUS RAWAT JALAN saja
                    $query->where('rp.stts', '<>', 'Batal')
                          ->where('rp.status_lanjut', 'Ralan') // Tambah filter rawat jalan
                          ->where('rp.kd_poli', '!=', 'IGDK') // Exclude IGD
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
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistikPasien(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|date_format:Y-m-d'
        ]);

        $tanggal = $request->input('tanggal');
        $statistik = $this->getStatistikData($tanggal);

        return response()->json([
            'success' => true,
            'message' => 'Data kroscek pasien berhasil diambil',
            'data' => [
                'tanggal' => $tanggal,
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
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistikHariIni()
    {
        $tanggalHariIni = Carbon::now()->format('Y-m-d');
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
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistikRentangTanggal(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date|date_format:Y-m-d',
            'tanggal_selesai' => 'required|date|date_format:Y-m-d|after_or_equal:tanggal_mulai'
        ]);

        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $statistik = $this->getStatistikData(null, $tanggalMulai, $tanggalSelesai);

        return response()->json([
            'success' => true,
            'message' => 'Data kroscek pasien dalam rentang tanggal berhasil diambil',
            'data' => [
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
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
     * Filter "Belum Nota" hanya menampilkan pasien rawat jalan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDaftarPasienBelumNota(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|date_format:Y-m-d',
            'search' => 'nullable|string|max:100',
            'filter_status' => 'nullable|string|in:Ranap,Ralan,IGD,Sudah_Nota,Belum_Nota',
            'filter_type' => 'nullable|string|in:semua,batal,igd,ralan,ranap_poli,belum_nota',
            'per_page' => 'nullable|integer|min:10|max:500'
        ]);

        $tanggal = $request->input('tanggal');
        $searchTerm = $request->input('search', '');
        $filterStatus = $request->input('filter_status', '');
        $filterType = $request->input('filter_type', 'semua');
        $perPage = $request->input('per_page', 100);

        try {
            $query = DB::table('reg_periksa as rp')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->leftJoin('nota_inap as ni', 'rp.no_rawat', '=', 'ni.no_rawat')
                ->leftJoin('nota_jalan as nj', 'rp.no_rawat', '=', 'nj.no_rawat')
                ->leftJoin('poliklinik as pol', 'rp.kd_poli', '=', 'pol.kd_poli')
                ->where('rp.tgl_registrasi', $tanggal)
                ->select([
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

            // Apply filter type with 'semua' as default
            switch ($filterType) {
                case 'semua':
                    // Tampilkan semua - DEFAULT
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
                    // CHANGED: Yang belum nota KHUSUS RAWAT JALAN saja
                    $query->where('rp.stts', '<>', 'Batal')
                          ->where('rp.status_lanjut', 'Ralan') // Tambah filter rawat jalan
                          ->where('rp.kd_poli', '!=', 'IGDK') // Exclude IGD
                          ->whereNull('ni.no_nota')
                          ->whereNull('nj.no_nota');
                    break;
            }

            // Apply search and other filters
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('rp.no_rawat', 'like', "%{$searchTerm}%")
                      ->orWhere('p.nm_pasien', 'like', "%{$searchTerm}%")
                      ->orWhere('rp.no_rkm_medis', 'like', "%{$searchTerm}%")
                      ->orWhere('pol.nm_poli', 'like', "%{$searchTerm}%");
                });
            }

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

            // Sort by no_rawat ascending (natural sorting)
            $result = $query->orderByRaw('CAST(rp.no_rawat AS UNSIGNED) ASC, rp.no_rawat ASC')->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar pasien berhasil diambil',
                'data' => [
                    'tanggal' => $tanggal,
                    'filter_type' => $filterType,
                    'filter_status' => $filterStatus,
                    'search_term' => $searchTerm,
                    'per_page' => $perPage,
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
