<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Http\Controllers\JM\JMUmumController;
use App\Http\Controllers\JM\JMAsuransiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PdfTindakanController extends Controller
{
    protected $jmUmumController;
    protected $jmAsuransiController;

    public function __construct(JMUmumController $jmUmumController, JMAsuransiController $jmAsuransiController)
    {
        $this->jmUmumController = $jmUmumController;
        $this->jmAsuransiController = $jmAsuransiController;
    }

    public function index(Request $request)
    {
        $kdDokter = $request->kd_dokter;
        $tanggl1 = $request->tgl1 ?? date('Y-m-01');
        $tanggl2 = $request->tgl2 ?? date('Y-m-t');

        // Pilihan jenis tindakan: default keduanya (umum dan asuransi)
        if ($request->has('filter_submitted')) {
            $selectedJenis = (array) $request->input('jenis', []);
        } else {
            $selectedJenis = (array) $request->input('jenis', ['umum', 'asuransi']);
        }

        // Gabungkan daftar dokter/petugas dari templateJM
        $templateUmum = collect($this->jmUmumController->templateJM);
        $templateAsuransi = collect($this->jmAsuransiController->templateJM);
        $listDokter = $templateUmum->merge($templateAsuransi)
            ->unique('id_khanza')
            ->filter(function ($item) {
                return !empty($item['id_khanza']);
            })
            ->sortBy('nama')
            ->values();

        $detailsRalanUmum = collect();
        $detailsRanapUmum = collect();
        $detailsRalanAsuransi = collect();
        $detailsRanapAsuransi = collect();
        $nmDokter = '';

        $labelParts = [];
        if (in_array('umum', $selectedJenis)) $labelParts[] = 'Umum';
        if (in_array('asuransi', $selectedJenis)) $labelParts[] = 'Asuransi';
        $labelJenis = !empty($labelParts) ? implode(' & ', $labelParts) : '-';

        if ($kdDokter) {
            $nmDokter = DB::table('dokter')->where('kd_dokter', $kdDokter)->value('nm_dokter')
                ?? DB::table('petugas')->where('nip', $kdDokter)->value('nama')
                ?? $kdDokter;

            // 1. Data Umum
            if (in_array('umum', $selectedJenis)) {
                $dataUmum = $this->jmUmumController->getDetailData($kdDokter, $tanggl1, $tanggl2);
                if (isset($dataUmum['detailsRalan'])) {
                    $detailsRalanUmum = $dataUmum['detailsRalan'];
                }
                if (isset($dataUmum['detailsRanap'])) {
                    $detailsRanapUmum = $dataUmum['detailsRanap'];
                }
            }

            // 2. Data Asuransi
            if (in_array('asuransi', $selectedJenis)) {
                $dataAsuransi = $this->jmAsuransiController->getDetailData($kdDokter, $tanggl1, $tanggl2);
                if (isset($dataAsuransi['detailsRalan'])) {
                    $detailsRalanAsuransi = $dataAsuransi['detailsRalan'];
                }
                if (isset($dataAsuransi['detailsRanap'])) {
                    $detailsRanapAsuransi = $dataAsuransi['detailsRanap'];
                }
            }

            // Hitung total-total
            $totalRalanUmum = $detailsRalanUmum->sum('tarif');
            $totalRanapUmum = $detailsRanapUmum->sum('tarif');
            $totalUmum = $totalRalanUmum + $totalRanapUmum;

            $totalRalanAsuransi = $detailsRalanAsuransi->sum('tarif');
            $totalRanapAsuransi = $detailsRanapAsuransi->sum('tarif');
            $totalAsuransi = $totalRalanAsuransi + $totalRanapAsuransi;

            $totalRalan = $totalRalanUmum + $totalRalanAsuransi;
            $totalRanap = $totalRanapUmum + $totalRanapAsuransi;
            $grandTotal = $totalUmum + $totalAsuransi;

            $viewData = [
                'listDokter' => $listDokter,
                'kdDokter' => $kdDokter,
                'nmDokter' => $nmDokter,
                'tanggl1' => $tanggl1,
                'tanggl2' => $tanggl2,
                'selectedJenis' => $selectedJenis,
                'labelJenis' => $labelJenis,
                'detailsRalanUmum' => $detailsRalanUmum,
                'detailsRanapUmum' => $detailsRanapUmum,
                'detailsRalanAsuransi' => $detailsRalanAsuransi,
                'detailsRanapAsuransi' => $detailsRanapAsuransi,
                'totalRalanUmum' => $totalRalanUmum,
                'totalRanapUmum' => $totalRanapUmum,
                'totalUmum' => $totalUmum,
                'totalRalanAsuransi' => $totalRalanAsuransi,
                'totalRanapAsuransi' => $totalRanapAsuransi,
                'totalAsuransi' => $totalAsuransi,
                'totalRalan' => $totalRalan,
                'totalRanap' => $totalRanap,
                'grandTotal' => $grandTotal,
            ];

            if ($request->export === 'pdf' || $request->has('pdf')) {
                $viewData['getSetting'] = DB::table('setting')->first();
                $pdf = \PDF::loadView('test.pdf-tindakan-export', $viewData);
                $pdf->setPaper('a4', 'portrait');

                $filename = 'Detail_Tindakan_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $nmDokter) . '_' . $tanggl1 . '_sd_' . $tanggl2 . '.pdf';
                return $pdf->stream($filename);
            }

            return view('test.pdf-tindakan', $viewData);
        }

        return view('test.pdf-tindakan', [
            'listDokter' => $listDokter,
            'kdDokter' => $kdDokter,
            'nmDokter' => $nmDokter,
            'tanggl1' => $tanggl1,
            'tanggl2' => $tanggl2,
            'selectedJenis' => $selectedJenis,
            'labelJenis' => $labelJenis,
            'detailsRalanUmum' => $detailsRalanUmum,
            'detailsRanapUmum' => $detailsRanapUmum,
            'detailsRalanAsuransi' => $detailsRalanAsuransi,
            'detailsRanapAsuransi' => $detailsRanapAsuransi,
            'totalRalanUmum' => 0,
            'totalRanapUmum' => 0,
            'totalUmum' => 0,
            'totalRalanAsuransi' => 0,
            'totalRanapAsuransi' => 0,
            'totalAsuransi' => 0,
            'totalRalan' => 0,
            'totalRanap' => 0,
            'grandTotal' => 0,
        ]);
    }
}
