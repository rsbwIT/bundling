<?php

namespace App\Http\Controllers\RM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KodinganRmController extends Controller
{
    public function index(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai', Carbon::now()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', Carbon::now()->format('Y-m-d'));
        $searchTerm = $request->get('search', '');
        $filterStatus = $request->get('filter_status', 'semua');
        $perPage = $request->get('per_page', 10000);

        $query = DB::table('reg_periksa as rp')
            ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('poliklinik as pol', 'rp.kd_poli', '=', 'pol.kd_poli')
            ->leftJoin('kodingan_versi_rm as krm', 'rp.no_rawat', '=', 'krm.no_rawat');

        $query->whereBetween('rp.tgl_registrasi', [$tanggalMulai, $tanggalSelesai]);

        if ($filterStatus != 'semua') {
            if ($filterStatus == 'ranap') {
                $query->where('rp.status_lanjut', 'Ranap');
            } elseif ($filterStatus == 'ralan') {
                $query->where('rp.status_lanjut', 'Ralan');
            }
        }

        if (!empty($searchTerm)) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('rp.no_rawat', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('rp.no_rkm_medis', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('p.nm_pasien', 'LIKE', "%{$searchTerm}%");
            });
        }

        $query->select([
            'rp.no_rawat',
            'rp.no_rkm_medis',
            'p.nm_pasien',
            'rp.status_lanjut',
            'rp.tgl_registrasi',
            'pol.nm_poli',
            'rp.stts',
            'krm.icd10',
            'krm.icd9'
        ]);

        $dataPasien = $query->orderBy('rp.tgl_registrasi', 'DESC')->paginate($perPage);

        return view('rm.kodingan-rm', compact(
            'dataPasien', 'tanggalMulai', 'tanggalSelesai', 'searchTerm', 'filterStatus', 'perPage'
        ));
    }

    public function cariIcd10(Request $request)
    {
        $search = $request->get('q', '');
        $query = DB::table('penyakit')
            ->select('kd_penyakit', 'nm_penyakit');
            
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('kd_penyakit', 'like', "%{$search}%")
                  ->orWhere('nm_penyakit', 'like', "%{$search}%");
            });
        }
        $data = $query->limit(50)->get();
        return response()->json($data);
    }

    public function cariIcd9(Request $request)
    {
        $search = $request->get('q', '');
        $query = DB::table('icd9')
            ->select('kode', 'deskripsi_panjang', 'deskripsi_pendek');
            
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('deskripsi_panjang', 'like', "%{$search}%")
                  ->orWhere('deskripsi_pendek', 'like', "%{$search}%");
            });
        }
        $data = $query->limit(50)->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $no_rawat = $request->input('no_rawat');
        $icd10 = $request->input('icd10');
        $icd9 = $request->input('icd9');

        if (empty($no_rawat)) {
            return response()->json(['success' => false, 'message' => 'No rawat tidak valid.']);
        }

        $exists = DB::table('kodingan_versi_rm')->where('no_rawat', $no_rawat)->exists();

        if ($exists) {
            DB::table('kodingan_versi_rm')->where('no_rawat', $no_rawat)->update([
                'icd10' => $icd10,
                'icd9' => $icd9,
                'updated_at' => Carbon::now()
            ]);
        } else {
            DB::table('kodingan_versi_rm')->insert([
                'no_rawat' => $no_rawat,
                'icd10' => $icd10,
                'icd9' => $icd9,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Data kodingan berhasil disimpan.']);
    }
}
