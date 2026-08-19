<?php

namespace App\Http\Controllers\Gizi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiziController extends Controller
{
    public function index(Request $request)
    {
        $tgl1 = $request->input('tgl1', date('Y-m-01'));
        $tgl2 = $request->input('tgl2', date('Y-m-d'));
        $search = $request->input('search', '');
        $selectedKdDiet = $request->input('kd_diet', '');
        $selectedWaktu = $request->input('waktu', '');

        $dataGizi = collect();
        $listDiet = collect();
        $listWaktu = collect();

        try {
            $tablesToTry = ['detail_beri_diet', 'pasien_diet', 'pemberian_diet', 'diet_pasien'];
            $table = null;

            foreach ($tablesToTry as $t) {
                if (DB::getSchemaBuilder()->hasTable($t)) {
                    $table = $t;
                    break;
                }
            }

            if ($table) {
                $cols = DB::getSchemaBuilder()->getColumnListing($table);
                $hasKdKamar = in_array('kd_kamar', $cols);
                $hasWaktu = in_array('waktu', $cols);
                $hasJam = in_array('jam', $cols);
                $hasKdDiet = in_array('kd_diet', $cols);
                $dateCol = in_array('tgl_diberi', $cols) ? 'tgl_diberi' : (in_array('tanggal', $cols) ? 'tanggal' : 'tgl_diberi');
                $shiftCol = $hasWaktu ? 'waktu' : ($hasJam ? 'jam' : null);

                // Fetch distinct shifts/waktu
                if ($shiftCol) {
                    $listWaktu = DB::table($table)->whereNotNull($shiftCol)->where($shiftCol, '!=', '')->select($shiftCol)->distinct()->pluck($shiftCol);
                }

                // Fetch diet master list
                if (DB::getSchemaBuilder()->hasTable('diet')) {
                    $listDiet = DB::table('diet')->select('kd_diet', 'nama_diet')->orderBy('nama_diet', 'asc')->get();
                }

                $buildQuery = function ($applyDateFilter = true) use ($table, $cols, $hasKdKamar, $shiftCol, $hasKdDiet, $dateCol, $tgl1, $tgl2, $search, $selectedKdDiet, $selectedWaktu) {
                    $query = DB::table($table)
                        ->select(
                            "{$table}.no_rawat",
                            "{$table}.{$dateCol} as tgl_diberi",
                            $shiftCol ? "{$table}.{$shiftCol} as jam" : DB::raw("'' as jam"),
                            'pasien.nm_pasien',
                            'pasien.no_rkm_medis',
                            $hasKdDiet ? 'diet.nama_diet' : DB::raw("'' as nama_diet"),
                            DB::raw("COALESCE(bangsal.nm_bangsal, kamar.kd_kamar, 'Rawat Inap') as nm_bangsal")
                        )
                        ->leftJoin('reg_periksa', "{$table}.no_rawat", '=', 'reg_periksa.no_rawat')
                        ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis');

                    if ($hasKdDiet) {
                        $query->leftJoin('diet', "{$table}.kd_diet", '=', 'diet.kd_diet');
                    }

                    if ($hasKdKamar) {
                        $query->leftJoin('kamar', "{$table}.kd_kamar", '=', 'kamar.kd_kamar')
                              ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal');
                    } else {
                        $query->leftJoin('kamar_inap', function($join) use ($table) {
                            $join->on("{$table}.no_rawat", '=', 'kamar_inap.no_rawat');
                        })
                        ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                        ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal');
                    }

                    if ($applyDateFilter && $tgl1 && $tgl2) {
                        $query->whereBetween("{$table}.{$dateCol}", [$tgl1, $tgl2]);
                    }

                    if ($hasKdDiet && $selectedKdDiet) {
                        $query->where("{$table}.kd_diet", $selectedKdDiet);
                    }

                    if ($shiftCol && $selectedWaktu) {
                        $query->where("{$table}.{$shiftCol}", $selectedWaktu);
                    }

                    if ($search) {
                        $query->where(function($q) use ($table, $search, $hasKdDiet) {
                            $q->where("{$table}.no_rawat", 'like', "%{$search}%")
                              ->orWhere('pasien.nm_pasien', 'like', "%{$search}%")
                              ->orWhere('pasien.no_rkm_medis', 'like', "%{$search}%");
                            if ($hasKdDiet) {
                                $q->orWhere('diet.nama_diet', 'like', "%{$search}%");
                            }
                        });
                    }

                    return $query->orderBy("{$table}.{$dateCol}", 'desc')->distinct();
                };

                $result = $buildQuery(true)->paginate(25);

                // Fallback: If 0 results returned for default date filter and no search input, load latest records automatically
                if ($result->total() == 0 && !$request->has('tgl1') && !$search && !$selectedKdDiet && !$selectedWaktu) {
                    $result = $buildQuery(false)->paginate(25);
                    if ($result->count() > 0) {
                        $tgl2 = $result->first()->tgl_diberi ?? $tgl2;
                        $tgl1 = $result->last()->tgl_diberi ?? $tgl1;
                    }
                }

                $dataGizi = $result;
            }
        } catch (\Exception $e) {
            $dataGizi = collect();
        }

        return view('gizi.index', [
            'dataGizi' => $dataGizi,
            'listDiet' => $listDiet,
            'listWaktu' => $listWaktu,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'search' => $search,
            'selectedKdDiet' => $selectedKdDiet,
            'selectedWaktu' => $selectedWaktu,
        ]);
    }

    public function printLabel(Request $request)
    {
        $no_rawat = $request->input('no_rawat');
        $tgl = $request->input('tgl');
        $waktu = $request->input('waktu');
        $all = $request->input('all');
        $tgl1 = $request->input('tgl1', date('Y-m-01'));
        $tgl2 = $request->input('tgl2', date('Y-m-d'));
        $search = $request->input('search', '');
        $selectedKdDiet = $request->input('kd_diet', '');

        $items = collect();

        try {
            $table = DB::getSchemaBuilder()->hasTable('detail_beri_diet') ? 'detail_beri_diet' : 'pasien_diet';
            $cols = DB::getSchemaBuilder()->getColumnListing($table);
            $dateCol = in_array('tgl_diberi', $cols) ? 'tgl_diberi' : (in_array('tanggal', $cols) ? 'tanggal' : 'tgl_diberi');
            $shiftCol = in_array('waktu', $cols) ? 'waktu' : (in_array('jam', $cols) ? 'jam' : null);
            $hasKdDiet = in_array('kd_diet', $cols);
            $hasKdKamar = in_array('kd_kamar', $cols);

            $query = DB::table($table)
                ->select(
                    "{$table}.no_rawat",
                    "{$table}.{$dateCol} as tgl_diberi",
                    $shiftCol ? "{$table}.{$shiftCol} as jam" : DB::raw("'' as jam"),
                    'pasien.nm_pasien',
                    'pasien.no_rkm_medis',
                    $hasKdDiet ? 'diet.nama_diet' : DB::raw("'' as nama_diet"),
                    DB::raw("COALESCE(bangsal.nm_bangsal, kamar.kd_kamar, 'Rawat Inap') as nm_bangsal")
                )
                ->leftJoin('reg_periksa', "{$table}.no_rawat", '=', 'reg_periksa.no_rawat')
                ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis');

            if ($hasKdDiet) {
                $query->leftJoin('diet', "{$table}.kd_diet", '=', 'diet.kd_diet');
            }

            if ($hasKdKamar) {
                $query->leftJoin('kamar', "{$table}.kd_kamar", '=', 'kamar.kd_kamar')
                      ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal');
            } else {
                $query->leftJoin('kamar_inap', function($join) use ($table) {
                    $join->on("{$table}.no_rawat", '=', 'kamar_inap.no_rawat');
                })
                ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal');
            }

            if ($no_rawat) {
                $query->where("{$table}.no_rawat", $no_rawat);
                if ($tgl) {
                    $query->where("{$table}.{$dateCol}", $tgl);
                }
                if ($waktu && $shiftCol) {
                    $query->where("{$table}.{$shiftCol}", $waktu);
                }
                $items = $query->take(1)->get();
            } else {
                if ($tgl1 && $tgl2) {
                    $query->whereBetween("{$table}.{$dateCol}", [$tgl1, $tgl2]);
                }
                if ($hasKdDiet && $selectedKdDiet) {
                    $query->where("{$table}.kd_diet", $selectedKdDiet);
                }
                if ($shiftCol && $waktu) {
                    $query->where("{$table}.{$shiftCol}", $waktu);
                }
                if ($search) {
                    $query->where(function($q) use ($table, $search, $hasKdDiet) {
                        $q->where("{$table}.no_rawat", 'like', "%{$search}%")
                          ->orWhere('pasien.nm_pasien', 'like', "%{$search}%")
                          ->orWhere('pasien.no_rkm_medis', 'like', "%{$search}%");
                        if ($hasKdDiet) {
                            $q->orWhere('diet.nama_diet', 'like', "%{$search}%");
                        }
                    });
                }
                $items = $query->orderBy("{$table}.{$dateCol}", 'desc')->distinct()->get();
            }
        } catch (\Exception $e) {
            $items = collect();
        }

        return view('gizi.print-label', ['items' => $items]);
    }
}

