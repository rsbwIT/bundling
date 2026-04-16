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

        return view('detail-tindakan-umum.test', [
            'actionCari'=> $actionCari,
            'dokter'=> $dokter,
            'dataCombined' => $dataCombined,
            'tanggl1' => $tanggl1,
            'tanggl2' => $tanggl2
        ]);
    }
}
