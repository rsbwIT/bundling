<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MappingLisController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->input('cari');

        $query = DB::table('bw_mapping_lis_softmedik')
            ->leftJoin('jns_perawatan_lab', 'bw_mapping_lis_softmedik.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->leftJoin('template_laboratorium', 'bw_mapping_lis_softmedik.id_template_khanza', '=', 'template_laboratorium.id_template')
            ->select(
                'bw_mapping_lis_softmedik.*',
                'jns_perawatan_lab.nm_perawatan as nama_tindakan_terpilih',
                'template_laboratorium.Pemeriksaan as nama_template_terpilih'
            );

        if (!empty($cari)) {
            $query->where(function($q) use ($cari) {
                $q->where('bw_mapping_lis_softmedik.kode_softmedik', 'like', "%$cari%")
                  ->orWhere('bw_mapping_lis_softmedik.nama_test_softmedik', 'like', "%$cari%")
                  ->orWhere('bw_mapping_lis_softmedik.kd_jenis_prw', 'like', "%$cari%")
                  ->orWhere('jns_perawatan_lab.nm_perawatan', 'like', "%$cari%");
            });
        }

        $mappings = $query->orderBy('bw_mapping_lis_softmedik.nama_test_softmedik', 'asc')->paginate(20)->withQueryString();

        return view('lab.mapping-lis', compact('mappings', 'cari'));
    }

    public function searchTindakan(Request $request)
    {
        $term = $request->input('q');
        $data = DB::table('jns_perawatan_lab')
            ->where('status', '1')
            ->where(function($q) use ($term) {
                if (!empty($term)) {
                    $q->where('kd_jenis_prw', 'like', "%$term%")
                      ->orWhere('nm_perawatan', 'like', "%$term%");
                }
            })
            ->select('kd_jenis_prw', 'nm_perawatan', 'total_byr')
            ->orderBy('nm_perawatan', 'asc')
            ->limit(50)
            ->get();

        return response()->json($data);
    }

    public function searchTemplate(Request $request)
    {
        $term = $request->input('q');
        $kd_jenis_prw = $request->input('kd_jenis_prw');

        $query = DB::table('template_laboratorium')
            ->join('jns_perawatan_lab', 'template_laboratorium.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw');

        if (!empty($kd_jenis_prw)) {
            $query->where('template_laboratorium.kd_jenis_prw', $kd_jenis_prw);
        }

        if (!empty($term)) {
            $query->where(function($q) use ($term) {
                $q->where('template_laboratorium.Pemeriksaan', 'like', "%$term%")
                  ->orWhere('template_laboratorium.id_template', 'like', "%$term%")
                  ->orWhere('template_laboratorium.kd_jenis_prw', 'like', "%$term%")
                  ->orWhere('jns_perawatan_lab.nm_perawatan', 'like', "%$term%");
            });
        }

        $data = $query->select(
                'template_laboratorium.id_template',
                'template_laboratorium.kd_jenis_prw',
                'template_laboratorium.Pemeriksaan',
                'template_laboratorium.satuan',
                'jns_perawatan_lab.nm_perawatan'
            )
            ->orderBy('template_laboratorium.Pemeriksaan', 'asc')
            ->limit(50)
            ->get();

        return response()->json($data);
    }

    public function getTemplate(Request $request)
    {
        $kd_jenis_prw = $request->input('kd_jenis_prw');
        if (empty($kd_jenis_prw)) {
            return response()->json([]);
        }

        $data = DB::table('template_laboratorium')
            ->where('kd_jenis_prw', $kd_jenis_prw)
            ->select('id_template', 'Pemeriksaan', 'satuan', 'nilai_rujukan_ld')
            ->orderBy('Pemeriksaan', 'asc')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_softmedik'      => 'required|string|max:50',
            'nama_test_softmedik' => 'required|string|max:150',
        ]);

        $kdJenisPrw = $request->input('kd_jenis_prw');
        $idTemplate = $request->input('id_template_khanza');

        $template = null;
        if ($idTemplate) {
            $template = DB::table('template_laboratorium')->where('id_template', $idTemplate)->first();
        }

        $tindakan = null;
        if ($kdJenisPrw) {
            $tindakan = DB::table('jns_perawatan_lab')->where('kd_jenis_prw', $kdJenisPrw)->first();
        }

        DB::table('bw_mapping_lis_softmedik')->updateOrInsert(
            ['kode_softmedik' => $request->input('kode_softmedik')],
            [
                'nama_test_softmedik'     => $request->input('nama_test_softmedik'),
                'kd_jenis_prw'            => $kdJenisPrw ?: ($template->kd_jenis_prw ?? null),
                'id_template_khanza'      => $idTemplate ?: null,
                'nama_pemeriksaan_khanza' => $template->Pemeriksaan ?? ($tindakan->nm_perawatan ?? null),
                'satuan'                  => $template->satuan ?? '',
                'nilai_rujukan'           => $template->nilai_rujukan_ld ?? ($template->nilai_rujukan_la ?? ''),
                'updated_at'              => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Mapping berhasil disimpan!']);
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $kdJenisPrw = $request->input('kd_jenis_prw');
        $idTemplate = $request->input('id_template_khanza');

        $template = null;
        if ($idTemplate) {
            $template = DB::table('template_laboratorium')->where('id_template', $idTemplate)->first();
        }

        $tindakan = null;
        if ($kdJenisPrw) {
            $tindakan = DB::table('jns_perawatan_lab')->where('kd_jenis_prw', $kdJenisPrw)->first();
        }

        DB::table('bw_mapping_lis_softmedik')->where('id', $id)->update([
            'kd_jenis_prw'            => $kdJenisPrw ?: ($template->kd_jenis_prw ?? null),
            'id_template_khanza'      => $idTemplate ?: null,
            'nama_pemeriksaan_khanza' => $template->Pemeriksaan ?? ($tindakan->nm_perawatan ?? null),
            'satuan'                  => $template->satuan ?? '',
            'nilai_rujukan'           => $template->nilai_rujukan_ld ?? '',
            'updated_at'              => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Mapping berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        DB::table('bw_mapping_lis_softmedik')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data mapping berhasil dihapus.');
    }
}
