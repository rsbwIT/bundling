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
    protected $jmBpjsController;

    public function __construct(JMUmumController $jmUmumController, JMAsuransiController $jmAsuransiController, \App\Http\Controllers\JM\JMBpjsController $jmBpjsController)
    {
        $this->jmUmumController = $jmUmumController;
        $this->jmAsuransiController = $jmAsuransiController;
        $this->jmBpjsController = $jmBpjsController;
    }

    public function index(Request $request)
    {
        $kdDokterInput = $request->input('kd_dokter');
        $kdDokter = is_array($kdDokterInput) ? ($kdDokterInput[0] ?? null) : $kdDokterInput;
        $tanggl1 = $request->tgl1 ?? date('Y-m-01');
        $tanggl2 = $request->tgl2 ?? date('Y-m-t');

        if ($request->export === 'zip') {
            return $this->exportZip($request);
        }

        if ($request->has('filter_submitted')) {
            $selectedJenis = (array) $request->input('jenis', []);
        } else {
            $selectedJenis = (array) $request->input('jenis', ['umum', 'asuransi', 'inhealth', 'bpjs']);
        }

        // Gabungkan daftar dokter/petugas dari templateJM
        $templateUmum = collect($this->jmUmumController->templateJM);
        $templateAsuransi = collect($this->jmAsuransiController->templateJM);
        $templateBpjs = collect($this->jmBpjsController->templateJM);
        $listDokterRaw = $templateUmum->merge($templateAsuransi)->merge($templateBpjs)
            ->unique('id_khanza')
            ->filter(function ($item) {
                return !empty($item['id_khanza']);
            })
            ->sortBy('nama')
            ->values();

        // Categorize
        // Fetch correct names from DB based on kd_dokter (id_khanza)
        $idKhanzas = $listDokterRaw->pluck('id_khanza')->filter()->toArray();
        $dbDokterNames = \Illuminate\Support\Facades\DB::table('dokter')->whereIn('kd_dokter', $idKhanzas)->pluck('nm_dokter', 'kd_dokter');
        $dbPetugasNames = \Illuminate\Support\Facades\DB::table('petugas')->whereIn('nip', $idKhanzas)->pluck('nama', 'nip');

        $listDokterUmum = collect();
        $listDokterSpesialis = collect();
        $listPetugas = collect();

        foreach ($listDokterRaw as $doc) {
            $id = $doc['id_khanza'];
            if (empty($id)) continue;
            
            // Override nama with DB name
            if ($dbDokterNames->has($id)) {
                $doc['nama'] = $dbDokterNames[$id];
            } elseif ($dbPetugasNames->has($id)) {
                $doc['nama'] = $dbPetugasNames[$id];
            }
            
            $kode = strtoupper($doc['kode'] ?? '');
            if (str_starts_with($kode, 'SP')) {
                $listDokterSpesialis->push($doc);
            } elseif (str_starts_with($kode, 'U')) {
                $listDokterUmum->push($doc);
            } else {
                $listPetugas->push($doc);
            }
        }
        
        // Sort by the newly fetched DB names
        $listDokterUmum = $listDokterUmum->sortBy('nama')->values();
        $listDokterSpesialis = $listDokterSpesialis->sortBy('nama')->values();
        $listPetugas = $listPetugas->sortBy('nama')->values();

        $listDokter = $listDokterRaw; // keep original for select dropdown


        $detailsRalanUmum = collect();
        $detailsRanapUmum = collect();
        $detailsRalanAsuransi = collect();
        $detailsRanapAsuransi = collect();
        $detailsRalanInhealth = collect();
        $detailsRanapInhealth = collect();
        $detailsRalanBpjs = collect();
        $detailsRanapBpjs = collect();
        $nmDokter = '';

        $labelParts = [];
        if (in_array('umum', $selectedJenis)) $labelParts[] = 'Umum';
        if (in_array('asuransi', $selectedJenis)) $labelParts[] = 'Asuransi';
        if (in_array('inhealth', $selectedJenis)) $labelParts[] = 'Inhealth';
        if (in_array('bpjs', $selectedJenis)) $labelParts[] = 'BPJS';
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

            // 4. Data BPJS
            if (in_array('bpjs', $selectedJenis)) {
                // Gunakan request dummy untuk mengirim parameter ke JMBpjsController
                $bpjsRequest = new \Illuminate\Http\Request();
                $bpjsRequest->merge([
                    'kd_dokter' => $kdDokter,
                    'tgl1' => $tanggl1,
                    'tgl2' => $tanggl2,
                ]);
                
                $dataBpjs = collect($this->jmBpjsController->detail($bpjsRequest, true));
                
                // Filter hanya yang mendapat tarif (> 0)
                $dataBpjs = $dataBpjs->filter(function($item) {
                    return $item->tarif > 0;
                });
                
                $detailsRalanBpjs = $dataBpjs->filter(function($item) {
                    return stripos($item->status, 'Ralan') !== false;
                });
                
                $detailsRanapBpjs = $dataBpjs->filter(function($item) {
                    return stripos($item->status, 'Ranap') !== false;
                });
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

            $totalRalanBpjs = $detailsRalanBpjs->sum('tarif');
            $totalRanapBpjs = $detailsRanapBpjs->sum('tarif');
            $totalBpjs = $totalRalanBpjs + $totalRanapBpjs;

            $totalRalan = $totalRalanUmum + $totalRalanAsuransi + $totalRalanInhealth + $totalRalanBpjs;
            $totalRanap = $totalRanapUmum + $totalRanapAsuransi + $totalRanapInhealth + $totalRanapBpjs;
            $grandTotal = $totalUmum + $totalAsuransi + $totalInhealth + $totalBpjs;

            $viewData = [
                'listDokter' => $listDokter,
            'listDokterUmum' => $listDokterUmum,
            'listDokterSpesialis' => $listDokterSpesialis,
            'listPetugas' => $listPetugas,
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
                'detailsRalanBpjs' => $detailsRalanBpjs,
                'detailsRanapBpjs' => $detailsRanapBpjs,
                'totalRalanUmum' => $totalRalanUmum,
                'totalRanapUmum' => $totalRanapUmum,
                'totalUmum' => $totalUmum,
                'totalRalanAsuransi' => $totalRalanAsuransi,
                'totalRanapAsuransi' => $totalRanapAsuransi,
                'totalAsuransi' => $totalAsuransi,
                'totalRalanInhealth' => $totalRalanInhealth,
                'totalRanapInhealth' => $totalRanapInhealth,
                'totalInhealth' => $totalInhealth,
                'totalRalanBpjs' => $totalRalanBpjs,
                'totalRanapBpjs' => $totalRanapBpjs,
                'totalBpjs' => $totalBpjs,
                'totalRalan' => $totalRalan,
                'totalRanap' => $totalRanap,
                'grandTotal' => $grandTotal,
            ];

            if ($request->export === 'pdf' || $request->has('pdf')) {
                $viewData['getSetting'] = DB::table('setting')->first();
                $pdf = \PDF::loadView('test.pdf-tindakan-export', $viewData);
                $pdf->setPaper('a4', 'landscape');

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
            'listDokterUmum' => $listDokterUmum,
            'listDokterSpesialis' => $listDokterSpesialis,
            'listPetugas' => $listPetugas,
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
            'detailsRalanBpjs' => $detailsRalanBpjs,
            'detailsRanapBpjs' => $detailsRanapBpjs,
            'totalRalanUmum' => 0,
            'totalRanapUmum' => 0,
            'totalUmum' => 0,
            'totalRalanAsuransi' => 0,
            'totalRanapAsuransi' => 0,
            'totalAsuransi' => 0,
            'totalRalanInhealth' => 0,
            'totalRanapInhealth' => 0,
            'totalInhealth' => 0,
            'totalRalanBpjs' => 0,
            'totalRanapBpjs' => 0,
            'totalBpjs' => 0,
            'totalRalan' => 0,
            'totalRanap' => 0,
            'grandTotal' => 0,
        ]);
    }

    public function exportZip(\Illuminate\Http\Request $request)
    {
        set_time_limit(0); // Biarkan proses berjalan lama
        
        $kdDokterInput = $request->input('kd_dokter', []);
        if (!is_array($kdDokterInput)) {
            $kdDokterInput = explode(',', $kdDokterInput);
        }
        
        if (empty($kdDokterInput)) {
            return back()->with('error', 'Pilih minimal 1 dokter/petugas.');
        }

        $tanggl1 = $request->tgl1 ?? date('Y-m-01');
        $tanggl2 = $request->tgl2 ?? date('Y-m-t');
        $selectedJenis = (array) $request->input('jenis', ['umum', 'asuransi', 'inhealth', 'bpjs']);

        $labelParts = [];
        if (in_array('umum', $selectedJenis)) $labelParts[] = 'Umum';
        if (in_array('asuransi', $selectedJenis)) $labelParts[] = 'Asuransi';
        if (in_array('inhealth', $selectedJenis)) $labelParts[] = 'Inhealth';
        if (in_array('bpjs', $selectedJenis)) $labelParts[] = 'BPJS';
        $labelJenis = !empty($labelParts) ? implode(' & ', $labelParts) : '-';

        $getSetting = \Illuminate\Support\Facades\DB::table('setting')->first();

        // Siapkan Zip
        $zip = new \ZipArchive();
        $zipFileName = 'Tindakan_' . date('Ymd_His') . '.zip';
        $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;

        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return back()->with('error', 'Gagal membuat file zip.');
        }

        foreach ($kdDokterInput as $kdDokter) {
            if (!$kdDokter) continue;

            $nmDokter = \Illuminate\Support\Facades\DB::table('dokter')->where('kd_dokter', $kdDokter)->value('nm_dokter')
                ?? \Illuminate\Support\Facades\DB::table('petugas')->where('nip', $kdDokter)->value('nama')
                ?? $kdDokter;

            $detailsRalanUmum = collect();
            $detailsRanapUmum = collect();
            $detailsRalanAsuransi = collect();
            $detailsRanapAsuransi = collect();
            $detailsRalanInhealth = collect();
            $detailsRanapInhealth = collect();
            $detailsRalanBpjs = collect();
            $detailsRanapBpjs = collect();

            // 1. Data Umum
            if (in_array('umum', $selectedJenis)) {
                $dataUmum = $this->jmUmumController->getDetailData($kdDokter, $tanggl1, $tanggl2);
                if (isset($dataUmum['detailsRalan'])) $detailsRalanUmum = $dataUmum['detailsRalan'];
                if (isset($dataUmum['detailsRanap'])) $detailsRanapUmum = $dataUmum['detailsRanap'];
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

            // 4. Data BPJS
            if (in_array('bpjs', $selectedJenis)) {
                $bpjsRequest = new \Illuminate\Http\Request();
                $bpjsRequest->replace([
                    'kd_dokter' => $kdDokter,
                    'tgl1' => $tanggl1,
                    'tgl2' => $tanggl2,
                ]);
                
                $dataBpjs = collect($this->jmBpjsController->detail($bpjsRequest, true));
                
                // Filter hanya yang mendapat tarif (> 0)
                $dataBpjs = $dataBpjs->filter(function($item) {
                    return $item->tarif > 0;
                });
                
                $detailsRalanBpjs = $dataBpjs->filter(function($item) {
                    return stripos($item->status, 'Ralan') !== false;
                });
                
                $detailsRanapBpjs = $dataBpjs->filter(function($item) {
                    return stripos($item->status, 'Ranap') !== false;
                });
            }

            $totalRalanUmum = $detailsRalanUmum->sum('tarif');
            $totalRanapUmum = $detailsRanapUmum->sum('tarif');
            $totalUmum = $totalRalanUmum + $totalRanapUmum;

            $totalRalanAsuransi = $detailsRalanAsuransi->sum('tarif');
            $totalRanapAsuransi = $detailsRanapAsuransi->sum('tarif');
            $totalAsuransi = $totalRalanAsuransi + $totalRanapAsuransi;

            $totalRalanInhealth = $detailsRalanInhealth->sum('tarif');
            $totalRanapInhealth = $detailsRanapInhealth->sum('tarif');
            $totalInhealth = $totalRalanInhealth + $totalRanapInhealth;

            $totalRalanBpjs = $detailsRalanBpjs->sum('tarif');
            $totalRanapBpjs = $detailsRanapBpjs->sum('tarif');
            $totalBpjs = $totalRalanBpjs + $totalRanapBpjs;

            $grandTotal = $totalUmum + $totalAsuransi + $totalInhealth + $totalBpjs;

            $viewData = [
                'kdDokter' => $kdDokter,
                'totalRalanUmum' => $totalRalanUmum,
                'totalRanapUmum' => $totalRanapUmum,
                'totalUmum' => $totalUmum,
                'totalRalanAsuransi' => $totalRalanAsuransi,
                'totalRanapAsuransi' => $totalRanapAsuransi,
                'totalAsuransi' => $totalAsuransi,
                'totalRalanInhealth' => $totalRalanInhealth,
                'totalRanapInhealth' => $totalRanapInhealth,
                'totalInhealth' => $totalInhealth,
                'totalRalanBpjs' => $totalRalanBpjs,
                'totalRanapBpjs' => $totalRanapBpjs,
                'totalBpjs' => $totalBpjs,
                'grandTotal' => $grandTotal,
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
                'detailsRalanBpjs' => $detailsRalanBpjs,
                'detailsRanapBpjs' => $detailsRanapBpjs,
                'getSetting' => $getSetting
            ];

            $pdf = \PDF::loadView('test.pdf-tindakan-export', $viewData);
            $pdf->setPaper('a4', 'landscape');

            $pdfFilename = 'Detail_Tindakan_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $nmDokter) . '_' . $tanggl1 . '_sd_' . $tanggl2 . '.pdf';
            
            // Masukkan PDF yang di-generate ke dalam ZIP
            $zip->addFromString($pdfFilename, $pdf->output());
        }

        $zip->close();

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}