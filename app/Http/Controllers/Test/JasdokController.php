<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Http\Controllers\JM\JMUmumController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JasdokController extends Controller
{
    protected $jmUmumController;

    public function __construct(JMUmumController $jmUmumController)
    {
        $this->jmUmumController = $jmUmumController;
    }

    public function index(Request $request)
    {
        // Parameter Periode Bulan & Tahun
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $namaBulan = [
            '01' => 'JANUARI',
            '02' => 'FEBRUARI',
            '03' => 'MARET',
            '04' => 'APRIL',
            '05' => 'MEI',
            '06' => 'JUNI',
            '07' => 'JULI',
            '08' => 'AGUSTUS',
            '09' => 'SEPTEMBER',
            '10' => 'OKTOBER',
            '11' => 'NOVEMBER',
            '12' => 'DESEMBER',
        ];

        $bulanPad = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $periodeLabel = ($namaBulan[$bulanPad] ?? 'SEPTEMBER') . ' ' . $tahun;

        // Ambil data template spesialis (kode berawalan SP)
        $rawTemplate = collect($this->jmUmumController->templateJM);
        $spesialisTemplate = $rawTemplate->filter(function ($item) {
            return str_starts_with($item['kode'], 'SP');
        })->values();

        // Ambil nama dokter terbaru dari tabel dokter di Khanza
        $idKhanzas = $spesialisTemplate->pluck('id_khanza')->filter()->values();
        $namaDokterMap = DB::table('dokter')
            ->whereIn('kd_dokter', $idKhanzas)
            ->pluck('nm_dokter', 'kd_dokter');

        // Daftar kode dokter yang memiliki highlight warna pink seperti di Excel
        $highlightKodes = ['SP6', 'SP9', 'SP11', 'SP17'];

        // Format data baris dokter spesialis
        $no = 1;
        $listDokterSpesialis = $spesialisTemplate->map(function ($item) use (&$no, $namaDokterMap, $highlightKodes) {
            $id = $item['id_khanza'];
            $nama = $namaDokterMap[$id] ?? $item['nama'];

            $isHighlighted = in_array($item['kode'], $highlightKodes);

            return (object) [
                'no' => $no++,
                'kode' => $item['kode'],
                'id_khanza' => $id,
                'nama' => $nama,
                'is_highlight' => $isHighlighted,
                'umum' => null,
                'asuransi' => null,
                'bpjs' => null,
                'inhealth' => null,
                'kemenkes' => null,
                'total1' => null,
                'poli_umum' => null,
                'total2' => null,
            ];
        });

        return view('test.jasdok', [
            'listDokterSpesialis' => $listDokterSpesialis,
            'bulan' => $bulanPad,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'periodeLabel' => $periodeLabel,
            'activeTab' => 'spesialis',
        ]);
    }
}
