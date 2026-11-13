<?php

namespace App\Http\Controllers\Gizi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiziController extends Controller
{
    /**
     * Deteksi tabel diet & kolom-kolom yang tersedia.
     */
    private function detectSchema()
    {
        $tablesToTry = ['detail_beri_diet', 'pasien_diet', 'pemberian_diet', 'diet_pasien'];
        $table = null;

        foreach ($tablesToTry as $t) {
            if (DB::getSchemaBuilder()->hasTable($t)) {
                $table = $t;
                break;
            }
        }

        if (!$table) {
            return null;
        }

        $cols = DB::getSchemaBuilder()->getColumnListing($table);

        return (object) [
            'table'      => $table,
            'hasKdKamar' => in_array('kd_kamar', $cols),
            'hasKdDiet'  => in_array('kd_diet', $cols),
            'dateCol'    => in_array('tgl_diberi', $cols) ? 'tgl_diberi' : (in_array('tanggal', $cols) ? 'tanggal' : 'tgl_diberi'),
            'shiftCol'   => in_array('waktu', $cols) ? 'waktu' : (in_array('jam', $cols) ? 'jam' : null),
        ];
    }

    /**
     * Query dasar pemberian diet beserta info pasien, diet, bangsal, kelas & kamar.
     */
    private function baseQuery($s)
    {
        $table = $s->table;

        $query = DB::table($table)
            ->select(
                "{$table}.no_rawat",
                "{$table}.{$s->dateCol} as tgl_diberi",
                $s->shiftCol ? "{$table}.{$s->shiftCol} as jam" : DB::raw("'' as jam"),
                'pasien.nm_pasien',
                'pasien.no_rkm_medis',
                $s->hasKdDiet ? 'diet.nama_diet' : DB::raw("'' as nama_diet"),
                DB::raw("COALESCE(bangsal.nm_bangsal, 'Rawat Inap') as nm_bangsal"),
                'bangsal.kd_bangsal',
                'kamar.kd_kamar',
                'kamar.kelas'
            )
            ->leftJoin('reg_periksa', "{$table}.no_rawat", '=', 'reg_periksa.no_rawat')
            ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis');

        if ($s->hasKdDiet) {
            $query->leftJoin('diet', "{$table}.kd_diet", '=', 'diet.kd_diet');
        }

        if ($s->hasKdKamar) {
            $query->leftJoin('kamar', "{$table}.kd_kamar", '=', 'kamar.kd_kamar');
        } else {
            $query->leftJoin('kamar_inap', "{$table}.no_rawat", '=', 'kamar_inap.no_rawat')
                  ->leftJoin('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar');
        }

        $query->leftJoin('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal');

        return $query;
    }

    /**
     * Terapkan filter (tanggal, diet, shift, ruangan, pencarian bebas).
     */
    private function applyFilters($query, $s, $filters, $applyDateFilter = true)
    {
        $table = $s->table;

        if ($applyDateFilter && $filters['tgl1'] && $filters['tgl2']) {
            $query->whereBetween("{$table}.{$s->dateCol}", [$filters['tgl1'], $filters['tgl2']]);
        }

        if ($s->hasKdDiet && $filters['kd_diet']) {
            $query->where("{$table}.kd_diet", $filters['kd_diet']);
        }

        if ($s->shiftCol && $filters['waktu']) {
            $query->where("{$table}.{$s->shiftCol}", $filters['waktu']);
        }

        if ($filters['kd_bangsal']) {
            $query->where('kamar.kd_bangsal', $filters['kd_bangsal']);
        }

        // Pencarian bebas: dicari utuh sebagai satu frasa (tidak dipecah per kata)
        // agar "garuda 5" tidak ikut menampilkan "garuda 3".
        if ($filters['search']) {
            $kw = trim(preg_replace('/\s+/', ' ', $filters['search']));
            $query->where(function ($q) use ($table, $kw, $s) {
                $q->where("{$table}.no_rawat", 'like', "%{$kw}%")
                  ->orWhere('pasien.nm_pasien', 'like', "%{$kw}%")
                  ->orWhere('pasien.no_rkm_medis', 'like', "%{$kw}%")
                  ->orWhere('bangsal.nm_bangsal', 'like', "%{$kw}%")
                  ->orWhere('kamar.kd_kamar', 'like', "%{$kw}%")
                  ->orWhere('kamar.kelas', 'like', "%{$kw}%")
                  // gabungan ruangan + kelas + kamar, mis. "melati kelas 2"
                  ->orWhereRaw("CONCAT_WS(' ', bangsal.nm_bangsal, kamar.kelas, kamar.kd_kamar) LIKE ?", ["%{$kw}%"]);
                if ($s->hasKdDiet) {
                    $q->orWhere('diet.nama_diet', 'like', "%{$kw}%");
                }
                if ($s->shiftCol) {
                    $q->orWhere("{$table}.{$s->shiftCol}", 'like', "%{$kw}%");
                }
            });
        }

        return $query;
    }

    private function getFilters(Request $request)
    {
        return [
            'tgl1'       => $request->input('tgl1', date('Y-m-01')),
            'tgl2'       => $request->input('tgl2', date('Y-m-d')),
            'search'     => $request->input('search', ''),
            'kd_diet'    => $request->input('kd_diet', ''),
            'waktu'      => $request->input('waktu', ''),
            'kd_bangsal' => $request->input('kd_bangsal', ''),
        ];
    }

    public function index(Request $request)
    {
        $filters = $this->getFilters($request);
        $tgl1 = $filters['tgl1'];
        $tgl2 = $filters['tgl2'];

        $dataGizi = collect();
        $listDiet = collect();
        $listWaktu = collect();
        $listBangsal = collect();

        try {
            $s = $this->detectSchema();

            if ($s) {
                if ($s->shiftCol) {
                    $listWaktu = DB::table($s->table)->whereNotNull($s->shiftCol)->where($s->shiftCol, '!=', '')
                        ->select($s->shiftCol)->distinct()->pluck($s->shiftCol);
                }

                if (DB::getSchemaBuilder()->hasTable('diet')) {
                    $listDiet = DB::table('diet')->select('kd_diet', 'nama_diet')->orderBy('nama_diet', 'asc')->get();
                }

                // Daftar ruangan/bangsal yang memiliki kamar
                $listBangsal = DB::table('bangsal')
                    ->join('kamar', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                    ->select('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
                    ->distinct()
                    ->orderBy('bangsal.nm_bangsal')
                    ->get();

                $buildQuery = function ($applyDateFilter = true) use ($s, $filters) {
                    $query = $this->baseQuery($s);
                    $this->applyFilters($query, $s, $filters, $applyDateFilter);
                    return $query->orderBy("{$s->table}.{$s->dateCol}", 'desc')
                                 ->orderBy('bangsal.nm_bangsal')
                                 ->orderBy('kamar.kd_kamar')
                                 ->distinct();
                };

                $result = $buildQuery(true)->paginate(25);

                // Fallback: jika default tanggal kosong & tanpa filter, tampilkan data terbaru
                if ($result->total() == 0 && !$request->has('tgl1') && !$filters['search'] && !$filters['kd_diet'] && !$filters['waktu'] && !$filters['kd_bangsal']) {
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
            'dataGizi'           => $dataGizi,
            'listDiet'           => $listDiet,
            'listWaktu'          => $listWaktu,
            'listBangsal'        => $listBangsal,
            'tgl1'               => $tgl1,
            'tgl2'               => $tgl2,
            'search'             => $filters['search'],
            'selectedKdDiet'     => $filters['kd_diet'],
            'selectedWaktu'      => $filters['waktu'],
            'selectedKdBangsal'  => $filters['kd_bangsal'],
        ]);
    }

    public function printLabel(Request $request)
    {
        $no_rawat = $request->input('no_rawat');
        $tgl = $request->input('tgl');
        $waktu = $request->input('waktu');
        $filters = $this->getFilters($request);

        $items = collect();

        try {
            $s = $this->detectSchema();

            if ($s) {
                $query = $this->baseQuery($s);

                if ($no_rawat) {
                    $query->where("{$s->table}.no_rawat", $no_rawat);
                    if ($tgl) {
                        $query->where("{$s->table}.{$s->dateCol}", $tgl);
                    }
                    if ($waktu && $s->shiftCol) {
                        $query->where("{$s->table}.{$s->shiftCol}", $waktu);
                    }
                    $items = $query->take(1)->get();
                } else {
                    $this->applyFilters($query, $s, $filters, true);
                    $items = $query->orderBy("{$s->table}.{$s->dateCol}", 'desc')
                                   ->orderBy('bangsal.nm_bangsal')
                                   ->orderBy('kamar.kd_kamar')
                                   ->distinct()
                                   ->get();
                }
            }
        } catch (\Exception $e) {
            $items = collect();
        }

        return view('gizi.print-label', ['items' => $items]);
    }
}
