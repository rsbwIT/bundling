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
        $kdDokterInput = $request->input('kd_dokter');
        $kdDokter = is_array($kdDokterInput) ? ($kdDokterInput[0] ?? null) : $kdDokterInput;
        $tanggl1 = $request->tgl1 ?? date('Y-m-01');
        $tanggl2 = $request->tgl2 ?? date('Y-m-t');

        if ($request->has('filter_submitted')) {
            $selectedJenis = (array) $request->input('jenis', []);
        } else {
            $selectedJenis = (array) $request->input('jenis', ['umum', 'asuransi', 'inhealth']);
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
        $detailsRalanInhealth = collect();
        $detailsRanapInhealth = collect();
        $nmDokter = '';

        $labelParts = [];
        if (in_array('umum', $selectedJenis)) $labelParts[] = 'Umum';
        if (in_array('asuransi', $selectedJenis)) $labelParts[] = 'Asuransi';
        if (in_array('inhealth', $selectedJenis)) $labelParts[] = 'Inhealth';
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

            // 2 & 3. Data Asuransi & Inhealth
            if (in_array('asuransi', $selectedJenis) || in_array('inhealth', $selectedJenis)) {
                $dataAsuransi = $this->jmAsuransiController->getDetailData($kdDokter, $tanggl1, $tanggl2);
                
                if (isset($dataAsuransi['detailsRalan'])) {
                    if (in_array('asuransi', $selectedJenis)) {
                        $detailsRalanAsuransi = $dataAsuransi['detailsRalan']->reject(function($item) {
                            return stripos($item->penjamin, 'inhealth') !== false;
                        });
                    }
                    if (in_array('inhealth', $selectedJenis)) {
                        $detailsRalanInhealth = $dataAsuransi['detailsRalan']->filter(function($item) {
                            return stripos($item->penjamin, 'inhealth') !== false;
                        });
                    }
                }
                
                if (isset($dataAsuransi['detailsRanap'])) {
                    if (in_array('asuransi', $selectedJenis)) {
                        $detailsRanapAsuransi = $dataAsuransi['detailsRanap']->reject(function($item) {
                            return stripos($item->penjamin, 'inhealth') !== false;
                        });
                    }
                    if (in_array('inhealth', $selectedJenis)) {
                        $detailsRanapInhealth = $dataAsuransi['detailsRanap']->filter(function($item) {
                            return stripos($item->penjamin, 'inhealth') !== false;
                        });
                    }
                }
            }

            // Hitung total-total
            $totalRalanUmum = $detailsRalanUmum->sum('tarif');
            $totalRanapUmum = $detailsRanapUmum->sum('tarif');
            $totalUmum = $totalRalanUmum + $totalRanapUmum;

            $totalRalanAsuransi = $detailsRalanAsuransi->sum('tarif');
            $totalRanapAsuransi = $detailsRanapAsuransi->sum('tarif');
            $totalAsuransi = $totalRalanAsuransi + $totalRanapAsuransi;

            $totalRalanInhealth = $detailsRalanInhealth->sum('tarif');
            $totalRanapInhealth = $detailsRanapInhealth->sum('tarif');
            $totalInhealth = $totalRalanInhealth + $totalRanapInhealth;

            $totalRalan = $totalRalanUmum + $totalRalanAsuransi + $totalRalanInhealth;
            $totalRanap = $totalRanapUmum + $totalRanapAsuransi + $totalRanapInhealth;
            $grandTotal = $totalUmum + $totalAsuransi + $totalInhealth;

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
                'detailsRalanInhealth' => $detailsRalanInhealth,
                'detailsRanapInhealth' => $detailsRanapInhealth,
                'totalRalanUmum' => $totalRalanUmum,
                'totalRanapUmum' => $totalRanapUmum,
                'totalUmum' => $totalUmum,
                'totalRalanAsuransi' => $totalRalanAsuransi,
                'totalRanapAsuransi' => $totalRanapAsuransi,
                'totalAsuransi' => $totalAsuransi,
                'totalRalanInhealth' => $totalRalanInhealth,
                'totalRanapInhealth' => $totalRanapInhealth,
                'totalInhealth' => $totalInhealth,
                'totalRalan' => $totalRalan,
                'totalRanap' => $totalRanap,
                'grandTotal' => $grandTotal,
            ];

            if ($request->export === 'pdf' || $request->has('pdf')) {
                $viewData['getSetting'] = DB::table('setting')->first();
                $pdf = \PDF::loadView('test.pdf-tindakan-export', $viewData);
                $pdf->setPaper('a4', 'portrait');

                $filename = 'Detail_Tindakan_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $nmDokter) . '_' . $tanggl1 . '_sd_' . $tanggl2 . '.pdf';
                
                if ($request->action === 'download') {
                    return $pdf->download($filename);
                }
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
            'detailsRalanInhealth' => $detailsRalanInhealth,
            'detailsRanapInhealth' => $detailsRanapInhealth,
            'totalRalanUmum' => 0,
            'totalRanapUmum' => 0,
            'totalUmum' => 0,
            'totalRalanAsuransi' => 0,
            'totalRanapAsuransi' => 0,
            'totalAsuransi' => 0,
            'totalRalanInhealth' => 0,
            'totalRanapInhealth' => 0,
            'totalInhealth' => 0,
            'totalRalan' => 0,
            'totalRanap' => 0,
            'grandTotal' => 0,
        ]);
    }
}
