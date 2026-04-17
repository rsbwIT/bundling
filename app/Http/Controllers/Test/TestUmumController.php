<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\CacheService;

class TestUmumController extends Controller
{
    protected $cacheService;
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $actionCari = '/test-umum';
        $dokter = $this->cacheService->getDokter();

        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1 ?? date('Y-m-01');
        $tanggl2 = $request->tgl2 ?? date('Y-m-t');
        $kdDokter = ($request->input('kdDokter')  == null) ? "" : explode(',', $request->input('kdDokter'));

        // 1. Query rawat_jl_dr (Dokter Saja)
        $queryDr = DB::table('pasien')
        ->select(
            'rawat_jl_dr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(rawat_jl_dr.tarif_tindakandr) as total_ralan")
        )
        ->join('reg_periksa','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
        ->join('rawat_jl_dr','reg_periksa.no_rawat','=','rawat_jl_dr.no_rawat')
        ->join('dokter','rawat_jl_dr.kd_dokter','=','dokter.kd_dokter')
        ->join('jns_perawatan','rawat_jl_dr.kd_jenis_prw','=','jns_perawatan.kd_jenis_prw')
        ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
        ->join('penjab','reg_periksa.kd_pj','=','penjab.kd_pj')
        ->join('billing','billing.no_rawat','=','reg_periksa.no_rawat')
        ->where('billing.no','=','No.Nota')
        ->where('penjab.kd_pj','UMU')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ( $kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_jl_dr.kd_dokter', $kdDokter);
            }
        })
        ->where(function($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_dr.kd_dokter', 'dokter.nm_dokter');

        // 2. Query rawat_jl_drpr (Dokter & Paramedis) - Ambil tarif dokternya saja
        $queryDrPr = DB::table('pasien')
        ->select(
            'rawat_jl_drpr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(rawat_jl_drpr.tarif_tindakandr) as total_ralan")
        )
        ->join('reg_periksa','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
        ->join('rawat_jl_drpr','reg_periksa.no_rawat','=','rawat_jl_drpr.no_rawat')
        ->join('jns_perawatan','rawat_jl_drpr.kd_jenis_prw','=','jns_perawatan.kd_jenis_prw')
        ->join('dokter','rawat_jl_drpr.kd_dokter','=','dokter.kd_dokter')
        ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
        ->join('penjab','reg_periksa.kd_pj','=','penjab.kd_pj')
        ->join('petugas','rawat_jl_drpr.nip','=','petugas.nip')
        ->join('billing','billing.no_rawat','=','reg_periksa.no_rawat')
        ->where('billing.no','=','No.Nota')
        ->where('penjab.kd_pj','UMU')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ( $kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_jl_drpr.kd_dokter', $kdDokter);
            }
        })
        ->where(function($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_drpr.kd_dokter', 'dokter.nm_dokter');

        // 2b. Query periksa_radiologi Ralan - JM Perujuk (tarif_perujuk)
        $queryRadPerujukRalan = DB::table('periksa_radiologi')
        ->select(
            'periksa_radiologi.dokter_perujuk as kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(periksa_radiologi.tarif_perujuk) as total_ralan")
        )
        ->join('reg_periksa', 'periksa_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'periksa_radiologi.dokter_perujuk', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('periksa_radiologi.dokter_perujuk', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('periksa_radiologi.dokter_perujuk', 'dokter.nm_dokter');

        // 2c. Query periksa_radiologi Ralan - JM PJ Rad (tarif_tindakan_dokter)
        $queryRadDokterRalan = DB::table('periksa_radiologi')
        ->select(
            'periksa_radiologi.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(periksa_radiologi.tarif_tindakan_dokter) as total_ralan")
        )
        ->join('reg_periksa', 'periksa_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'periksa_radiologi.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('periksa_radiologi.kd_dokter', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('periksa_radiologi.kd_dokter', 'dokter.nm_dokter');

        // Gabungkan semua query ralan (Union), ambil datanya, dan jumlahkan lagi di level Collection
        $results = $queryDr
            ->unionAll($queryDrPr)
            ->unionAll($queryRadPerujukRalan)
            ->unionAll($queryRadDokterRalan)
            ->get();

        $dataRalan = $results->groupBy('kd_dokter')->map(function ($row) {
            return (object) [
                'kd_dokter' => $row->first()->kd_dokter,
                'nm_dokter' => $row->first()->nm_dokter,
                'total_ralan' => $row->sum('total_ralan'),
            ];
        })->values();

        // 3. Query rawat_inap_dr (Ranap Dokter Saja)
        $queryRanapDr = DB::table('pasien')
        ->select(
            'rawat_inap_dr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(rawat_inap_dr.tarif_tindakandr) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_inap_dr', 'rawat_inap_dr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan_inap', 'rawat_inap_dr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
        ->join('dokter', 'rawat_inap_dr.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_inap_dr.kd_dokter', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_inap_dr.kd_dokter', 'dokter.nm_dokter');

        // 4. Query rawat_inap_drpr (Ranap Dokter & Paramedis) - Ambil tarif dokternya saja
        $queryRanapDrPr = DB::table('pasien')
        ->select(
            'rawat_inap_drpr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(rawat_inap_drpr.tarif_tindakandr) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_inap_drpr', 'rawat_inap_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan_inap', 'rawat_inap_drpr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
        ->join('dokter', 'rawat_inap_drpr.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('petugas', 'rawat_inap_drpr.nip', '=', 'petugas.nip')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_inap_drpr.kd_dokter', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_inap_drpr.kd_dokter', 'dokter.nm_dokter');

        // 5. Query OPERASI (operator1)
        $queryOperasi = DB::table('operasi')
        ->select(
            'operasi.operator1 as kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(operasi.biayaoperator1) as total_ranap")
        )
        ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'operasi.operator1', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('operasi.operator1', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('operasi.operator1', 'dokter.nm_dokter');

        // 6. Query rawat_jl_dr pada pasien Ranap (tindakan ralan pada pasien ranap)
        $queryRanapJlDr = DB::table('pasien')
        ->select(
            'rawat_jl_dr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(rawat_jl_dr.tarif_tindakandr) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_dr', 'reg_periksa.no_rawat', '=', 'rawat_jl_dr.no_rawat')
        ->join('dokter', 'rawat_jl_dr.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('jns_perawatan', 'rawat_jl_dr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_jl_dr.kd_dokter', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_dr.kd_dokter', 'dokter.nm_dokter');

        // 7. Query rawat_jl_drpr pada pasien Ranap (tindakan ralan dr+pr pada pasien ranap)
        $queryRanapJlDrPr = DB::table('pasien')
        ->select(
            'rawat_jl_drpr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(rawat_jl_drpr.tarif_tindakandr) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_drpr', 'rawat_jl_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('dokter', 'rawat_jl_drpr.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('petugas', 'rawat_jl_drpr.nip', '=', 'petugas.nip')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_jl_drpr.kd_dokter', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_drpr.kd_dokter', 'dokter.nm_dokter');

        // 8. Query periksa_radiologi - JM Perujuk (tarif_perujuk grouped by dokter_perujuk)
        $queryRadiologiPerujuk = DB::table('periksa_radiologi')
        ->select(
            'periksa_radiologi.dokter_perujuk as kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(periksa_radiologi.tarif_perujuk) as total_ranap")
        )
        ->join('reg_periksa', 'periksa_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'periksa_radiologi.dokter_perujuk', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('periksa_radiologi.dokter_perujuk', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('periksa_radiologi.dokter_perujuk', 'dokter.nm_dokter');

        // 9. Query periksa_radiologi - JM PJ Rad (tarif_tindakan_dokter grouped by kd_dokter)
        $queryRadiologiDokter = DB::table('periksa_radiologi')
        ->select(
            'periksa_radiologi.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(periksa_radiologi.tarif_tindakan_dokter) as total_ranap")
        )
        ->join('reg_periksa', 'periksa_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'periksa_radiologi.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('periksa_radiologi.kd_dokter', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('periksa_radiologi.kd_dokter', 'dokter.nm_dokter');

        // Gabungkan semua query ranap (Union), ambil datanya, dan jumlahkan lagi di level Collection
        $resultsRanap = $queryRanapDr
            ->unionAll($queryRanapDrPr)
            ->unionAll($queryOperasi)
            ->unionAll($queryRanapJlDr)
            ->unionAll($queryRanapJlDrPr)
            ->unionAll($queryRadiologiPerujuk)
            ->unionAll($queryRadiologiDokter)
            ->get();

        $dataRanap = $resultsRanap->groupBy('kd_dokter')->map(function ($row) {
            return (object) [
                'kd_dokter' => $row->first()->kd_dokter,
                'nm_dokter' => $row->first()->nm_dokter,
                'total_ranap' => $row->sum('total_ranap'),
            ];
        })->values();

        // Gabungkan ralan + ranap ke satu collection berdasarkan kd_dokter
        $allDokterKeys = $dataRalan->pluck('kd_dokter')
            ->merge($dataRanap->pluck('kd_dokter'))
            ->unique();

        $dataCombined = $allDokterKeys->map(function ($kd) use ($dataRalan, $dataRanap) {
            $ralan  = $dataRalan->firstWhere('kd_dokter', $kd);
            $ranap  = $dataRanap->firstWhere('kd_dokter', $kd);

            $totalRalan = $ralan->total_ralan ?? 0;
            $totalRanap = $ranap->total_ranap ?? 0;
            $nmDokter   = $ralan->nm_dokter ?? $ranap->nm_dokter ?? '-';

            return (object) [
                'kd_dokter'   => $kd,
                'nm_dokter'   => $nmDokter,
                'total_ranap' => $totalRanap,
                'total_ralan' => $totalRalan,
                'total_igd'   => 0,
                'grand_total' => $totalRanap + $totalRalan,
            ];
        })->sortByDesc('grand_total')->values();

        // ========================================
        // PARAMEDIS (Fisioterapis saja)
        // ========================================

        // P1. rawat_jl_pr Ralan (Paramedis Ralan)
        $queryPrRalan = DB::table('pasien')
        ->select(
            'rawat_jl_pr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(rawat_jl_pr.tarif_tindakanpr) as total_ralan")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_pr', 'rawat_jl_pr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan', 'rawat_jl_pr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('petugas', 'rawat_jl_pr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->where('petugas.nama', 'not like', '(NS)%')
        ->where('petugas.nama', 'not like', '(LAB)%')
        ->where('petugas.nama', 'not like', '(PS)%')
        ->where('petugas.nama', 'not like', '(PR)%')
        ->where('petugas.nama', '!=', 'Dahyar')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_pr.nip', 'petugas.nama');

        // P1b. rawat_jl_drpr Ralan - tarif paramedis (Ralan DrPr paramedis fee)
        $queryPrRalanDrPr = DB::table('pasien')
        ->select(
            'rawat_jl_drpr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(rawat_jl_drpr.tarif_tindakanpr) as total_ralan")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_drpr', 'rawat_jl_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('petugas', 'rawat_jl_drpr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->where('petugas.nama', 'not like', '(PR)%')
        ->where('petugas.nama', 'not like', '(NS)%')
        ->where('petugas.nama', 'not like', '(LAB)%')
        ->where('petugas.nama', 'not like', '(PS)%')
        ->where('petugas.nama', '!=', 'Dahyar')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_drpr.nip', 'petugas.nama');

        // P2. rawat_jl_pr Ranap (tindakan ralan pada pasien ranap)
        $queryPrRanapJl = DB::table('pasien')
        ->select(
            'rawat_jl_pr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(rawat_jl_pr.tarif_tindakanpr) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_pr', 'rawat_jl_pr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan', 'rawat_jl_pr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('petugas', 'rawat_jl_pr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where('petugas.nama', 'not like', '(PR)%')
        ->where('petugas.nama', 'not like', '(NS)%')
        ->where('petugas.nama', 'not like', '(LAB)%')
        ->where('petugas.nama', 'not like', '(PS)%')
        ->where('petugas.nama', '!=', 'Dahyar')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_pr.nip', 'petugas.nama');

        // P2b. rawat_jl_drpr Ranap - tarif paramedis (tindakan dr+pr ralan pada pasien ranap)
        $queryPrRanapJlDrPr = DB::table('pasien')
        ->select(
            'rawat_jl_drpr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(rawat_jl_drpr.tarif_tindakanpr) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_drpr', 'rawat_jl_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('petugas', 'rawat_jl_drpr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where('petugas.nama', 'not like', '(PR)%')
        ->where('petugas.nama', 'not like', '(NS)%')
        ->where('petugas.nama', 'not like', '(LAB)%')
        ->where('petugas.nama', 'not like', '(PS)%')
        ->where('petugas.nama', '!=', 'Dahyar')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_drpr.nip', 'petugas.nama');

        // P3. rawat_inap_pr (Ranap Paramedis)
        $queryPrRanap = DB::table('pasien')
        ->select(
            'rawat_inap_pr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(rawat_inap_pr.tarif_tindakanpr) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_inap_pr', 'rawat_inap_pr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan_inap', 'rawat_inap_pr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
        ->join('petugas', 'rawat_inap_pr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('petugas.nama', 'not like', '(PR)%')
        ->where('petugas.nama', 'not like', '(NS)%')
        ->where('petugas.nama', 'not like', '(LAB)%')
        ->where('petugas.nama', 'not like', '(PS)%')
        ->where('petugas.nama', '!=', 'Dahyar')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_inap_pr.nip', 'petugas.nama');

        // P4. rawat_inap_drpr - tarif paramedis (Ranap DrPr paramedis fee)
        $queryPrRanapDrPr = DB::table('pasien')
        ->select(
            'rawat_inap_drpr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(rawat_inap_drpr.tarif_tindakanpr) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_inap_drpr', 'rawat_inap_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan_inap', 'rawat_inap_drpr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
        ->join('petugas', 'rawat_inap_drpr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->where('petugas.nama', 'not like', '(PR)%')
        ->where('petugas.nama', 'not like', '(NS)%')
        ->where('petugas.nama', 'not like', '(LAB)%')
        ->where('petugas.nama', 'not like', '(PS)%')
        ->where('petugas.nama', '!=', 'Dahyar')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_inap_drpr.nip', 'petugas.nama');

        // P5. Operasi - Asisten Operator 1
        $queryPrOkAsistenOp1 = DB::table('operasi')
        ->select(
            'operasi.asisten_operator1 as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(operasi.biayaasisten_operator1) as total_ranap")
        )
        ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('petugas', 'operasi.asisten_operator1', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('operasi.asisten_operator1', 'petugas.nama');

        // P6. Operasi - Asisten Anestesi
        $queryPrOkAsistenAnestesi = DB::table('operasi')
        ->select(
            'operasi.asisten_anestesi as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(operasi.biayaasisten_anestesi) as total_ranap")
        )
        ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('petugas', 'operasi.asisten_anestesi', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('billing', 'billing.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('billing.no', '=', 'No.Nota')
        ->where('penjab.kd_pj', 'UMU')
        ->whereBetween('billing.tgl_byr', [$tanggl1, $tanggl2])
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('operasi.asisten_anestesi', 'petugas.nama');

        // Gabungkan ralan paramedis (P1 + P1b)
        $resultsPrRalan = $queryPrRalan
            ->unionAll($queryPrRalanDrPr)
            ->get();

        $dataPrRalan = $resultsPrRalan->groupBy('kd_petugas')->map(function ($row) {
            return (object) [
                'kd_petugas' => $row->first()->kd_petugas,
                'nm_petugas' => $row->first()->nm_petugas,
                'total_ralan' => $row->sum('total_ralan'),
            ];
        })->values();

        // Gabungkan ranap paramedis (P2 + P3 + P4 + P5 + P6)
        $resultsPrRanap = $queryPrRanapJl
            ->unionAll($queryPrRanap)
            ->unionAll($queryPrRanapDrPr)
            ->unionAll($queryPrOkAsistenOp1)
            ->unionAll($queryPrOkAsistenAnestesi)
            ->get();

        $dataPrRanap = $resultsPrRanap->groupBy('kd_petugas')->map(function ($row) {
            return (object) [
                'kd_petugas' => $row->first()->kd_petugas,
                'nm_petugas' => $row->first()->nm_petugas,
                'total_ranap' => $row->sum('total_ranap'),
            ];
        })->values();

        // Gabungkan ralan + ranap paramedis
        $allPetugasKeys = $dataPrRalan->pluck('kd_petugas')
            ->merge($dataPrRanap->pluck('kd_petugas'))
            ->unique();

        $dataParamedis = $allPetugasKeys->map(function ($nip) use ($dataPrRalan, $dataPrRanap) {
            $ralan = $dataPrRalan->firstWhere('kd_petugas', $nip);
            $ranap = $dataPrRanap->firstWhere('kd_petugas', $nip);

            $totalRalan = $ralan->total_ralan ?? 0;
            $totalRanap = $ranap->total_ranap ?? 0;
            $nmPetugas  = $ralan->nm_petugas ?? $ranap->nm_petugas ?? '-';

            return (object) [
                'kd_dokter'   => $nip,
                'nm_dokter'   => $nmPetugas,
                'total_ranap' => $totalRanap,
                'total_ralan' => $totalRalan,
                'total_igd'   => 0,
                'grand_total' => $totalRanap + $totalRalan,
            ];
        })->sortByDesc('grand_total')->values();

        return view('detail-tindakan-umum.test', [
            'actionCari'=> $actionCari,
            'dokter'=> $dokter,
            'dataCombined' => $dataCombined,
            'dataParamedis' => $dataParamedis,
            'tanggl1' => $tanggl1,
            'tanggl2' => $tanggl2
        ]);
    }
}
