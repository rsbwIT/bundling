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

        // Ambil dokter/petugas terurut sesuai urutan master sheet dari atas sampai bawah
        $masterDoctors = $this->getOrderedMasterDoctors();
        $categoryDefs = $this->getCategoryDefs();
        $categories = [];
        foreach ($categoryDefs as $p => $def) {
            $categories[$p] = $def;
            $categories[$p]['items'] = $masterDoctors->where('prefix', $p)->values();
        }

        $otherDocs = $masterDoctors->where('prefix', 'LAINNYA')->values();
        if ($otherDocs->count() > 0) {
            $categories['LAINNYA'] = [
                'key' => 'lainnya',
                'nama' => 'Lainnya',
                'badge' => 'Lain',
                'color' => 'secondary',
                'icon' => 'fas fa-user',
                'folder' => '8. Lainnya',
                'items' => $otherDocs,
            ];
        }

        $listDokterSpesialis = $categories['SP']['items'] ?? collect();
        $listDokterUmum = $categories['U']['items'] ?? collect();
        $listPetugasOK = $categories['OK']['items'] ?? collect();
        $listPetugasHD = $categories['HD']['items'] ?? collect();
        $listFisioterapi = $categories['FG']['items'] ?? collect();
        $listESWL = $categories['SWL']['items'] ?? collect();
        $listAdministrasi = $categories['AD']['items'] ?? collect();
        $listPetugas = $listPetugasOK->merge($listPetugasHD)->merge($listFisioterapi)->merge($listESWL)->merge($listAdministrasi);

        $listDokter = $masterDoctors; // master ordered for select dropdown


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
            $templateData = collect($this->jmUmumController->templateJM)->firstWhere('id_khanza', $kdDokter);
            $nmDokterFromTemplate = $templateData ? $templateData['nama'] : null;

            $nmDokter = DB::table('dokter')->where('kd_dokter', $kdDokter)->value('nm_dokter')
                ?? DB::table('petugas')->where('nip', $kdDokter)->value('nama')
                ?? $nmDokterFromTemplate 
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
                'categories' => $categories,
                'listDokter' => $listDokter,
                'listDokterUmum' => $listDokterUmum,
                'listDokterSpesialis' => $listDokterSpesialis,
                'listPetugasOK' => $listPetugasOK,
                'listPetugasHD' => $listPetugasHD,
                'listFisioterapi' => $listFisioterapi,
                'listESWL' => $listESWL,
                'listAdministrasi' => $listAdministrasi,
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

                $bulanTahun = \Carbon\Carbon::parse($tanggl1)->locale('id')->translatedFormat('F Y');
                $jenisStr = (count($selectedJenis) === 1) ? ' ' . strtolower($selectedJenis[0]) : ' jm';
                $safeNmDokter = preg_replace('/[\/\\\\:*?"<>|]/', '', $nmDokter);
                $filename = $safeNmDokter . ' slip' . $jenisStr . ' ' . $bulanTahun . '.pdf';
                
                if ($request->action === 'download') {
                    return $pdf->download($filename);
                }
                return $pdf->stream($filename);
            }

            return view('test.pdf-tindakan', $viewData);
        }

        return view('test.pdf-tindakan', [
            'categories' => $categories,
            'listDokter' => $listDokter,
            'listDokterUmum' => $listDokterUmum,
            'listDokterSpesialis' => $listDokterSpesialis,
            'listPetugasOK' => $listPetugasOK,
            'listPetugasHD' => $listPetugasHD,
            'listFisioterapi' => $listFisioterapi,
            'listESWL' => $listESWL,
            'listAdministrasi' => $listAdministrasi,
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

        $masterDoctors = $this->getOrderedMasterDoctors();
        $masterMap = $masterDoctors->keyBy('id_khanza');

        $categoryDefs = $this->getCategoryDefs();
        $catOrder = array_flip(array_keys($categoryDefs));

        // Urutkan kdDokterInput sesuai urutan master dari atas sampai bawah
        usort($kdDokterInput, function ($a, $b) use ($masterMap, $catOrder) {
            $docA = $masterMap->get($a);
            $docB = $masterMap->get($b);

            $catAOrder = isset($docA['prefix']) && isset($catOrder[$docA['prefix']]) ? $catOrder[$docA['prefix']] : 999;
            $catBOrder = isset($docB['prefix']) && isset($catOrder[$docB['prefix']]) ? $catOrder[$docB['prefix']] : 999;

            if ($catAOrder !== $catBOrder) {
                return $catAOrder <=> $catBOrder;
            }

            $noA = $docA['no_urut'] ?? 9999;
            $noB = $docB['no_urut'] ?? 9999;

            return $noA <=> $noB;
        });

        $addedFilesCount = 0;

        foreach ($kdDokterInput as $kdDokter) {
            if (!$kdDokter) continue;

            $docInfo = $masterMap->get($kdDokter);
            $nmDokter = $docInfo['nama'] ?? (
                \Illuminate\Support\Facades\DB::table('dokter')->where('kd_dokter', $kdDokter)->value('nm_dokter')
                ?? \Illuminate\Support\Facades\DB::table('pegawai')->where('nik', $kdDokter)->value('nama')
                ?? \Illuminate\Support\Facades\DB::table('petugas')->where('nip', $kdDokter)->value('nama')
                ?? $kdDokter
            );

            // Tentukan folder kategori untuk file di dalam ZIP
            $categoryFolder = $docInfo['category_folder'] ?? ($categoryDefs[$docInfo['prefix'] ?? 'LAINNYA']['folder'] ?? '8. Lainnya');

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

            // Jika pada filter tanggal tersebut tidak ada jasa/gaji (nominal 0 atau tidak ada tindakan), jangan masukkan ke ZIP
            if ($grandTotal <= 0) {
                continue;
            }

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

            $bulanTahun = \Carbon\Carbon::parse($tanggl1)->locale('id')->translatedFormat('F Y');
            $jenisStr = (count($selectedJenis) === 1) ? ' ' . strtolower($selectedJenis[0]) : ' jm';
            $safeNmDokter = preg_replace('/[\/\\\\:*?"<>|]/', '', $nmDokter);

            $noUrut = $docInfo['no_urut'] ?? null;
            $kodePrefix = $docInfo['kode'] ?? '';
            // Nama file memakai nama dari template (sesuai lembar master)
            $safeNmTemplate = preg_replace('/[\/\\\\:*?"<>|]/', '', $docInfo['nama_template'] ?? $nmDokter);

            if ($noUrut && $kodePrefix) {
                $pdfFilename = sprintf('%02d. %s - %s slip%s %s.pdf', $noUrut, $kodePrefix, $safeNmTemplate, $jenisStr, $bulanTahun);
            } elseif ($kodePrefix) {
                $pdfFilename = sprintf('%s - %s slip%s %s.pdf', $kodePrefix, $safeNmTemplate, $jenisStr, $bulanTahun);
            } else {
                $pdfFilename = $safeNmTemplate . ' slip' . $jenisStr . ' ' . $bulanTahun . '.pdf';
            }
            
            // Masukkan PDF yang di-generate ke dalam ZIP (disusun per folder kategori)
            $zip->addFromString($categoryFolder . '/' . $pdfFilename, $pdf->output());
            $addedFilesCount++;
        }

        if ($addedFilesCount === 0) {
            $zip->close();
            if (file_exists($zipFilePath)) {
                @unlink($zipFilePath);
            }
            return back()->with('error', 'Tidak ada dokter/petugas yang memiliki nominal jasa/gaji pada filter tanggal tersebut.');
        }

        $zip->close();

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    public function getCategoryDefs()
    {
        return [
            'SP' => [
                'key' => 'sp',
                'nama' => 'Dokter Spesialis',
                'badge' => 'SP',
                'color' => 'primary',
                'icon' => 'fas fa-user-md',
                'folder' => '1. Dokter Spesialis',
            ],
            'U' => [
                'key' => 'u',
                'nama' => 'Dokter Umum',
                'badge' => 'U',
                'color' => 'success',
                'icon' => 'fas fa-stethoscope',
                'folder' => '2. Dokter Umum',
            ],
            'OK' => [
                'key' => 'ok',
                'nama' => 'Tim Operasi (OK)',
                'badge' => 'OK',
                'color' => 'warning',
                'icon' => 'fas fa-procedures',
                'folder' => '3. Tim OK',
            ],
            'HD' => [
                'key' => 'hd',
                'nama' => 'Tim Hemodialisa (HD)',
                'badge' => 'HD',
                'color' => 'info',
                'icon' => 'fas fa-tint',
                'folder' => '4. Tim HD',
            ],
            'FG' => [
                'key' => 'fg',
                'nama' => 'Fisioterapi (FG)',
                'badge' => 'FG',
                'color' => 'purple',
                'icon' => 'fas fa-walking',
                'folder' => '5. Fisioterapi',
            ],
            'SWL' => [
                'key' => 'swl',
                'nama' => 'ESWL (SWL)',
                'badge' => 'SWL',
                'color' => 'danger',
                'icon' => 'fas fa-wave-square',
                'folder' => '6. ESWL',
            ],
            'AD' => [
                'key' => 'ad',
                'nama' => 'Administrasi (AD)',
                'badge' => 'AD',
                'color' => 'secondary',
                'icon' => 'fas fa-user-cog',
                'folder' => '7. Administrasi',
            ],
        ];
    }

    public function getOrderedMasterDoctors()
    {
        // Ambil template asli terurut (129 baris) dari jmBpjsController
        $master = collect($this->jmBpjsController->templateJM)
            ->filter(function ($item) {
                return !empty($item['id_khanza']);
            })
            ->values();

        $idKhanzas = $master->pluck('id_khanza')->filter()->toArray();
        $dbDokterNames = DB::table('dokter')->whereIn('kd_dokter', $idKhanzas)->pluck('nm_dokter', 'kd_dokter');
        $dbPetugasNames = DB::table('petugas')->whereIn('nip', $idKhanzas)->pluck('nama', 'nip');
        $dbPegawaiNames = DB::table('pegawai')->whereIn('nik', $idKhanzas)->pluck('nama', 'nik');

        $categoryDefs = $this->getCategoryDefs();
        $counters = [];

        return $master->map(function ($item) use ($dbDokterNames, $dbPetugasNames, $dbPegawaiNames, $categoryDefs, &$counters) {
            $id = $item['id_khanza'];
            $nama = $item['nama'];
            if ($dbDokterNames->has($id)) {
                $nama = $dbDokterNames[$id];
            } elseif ($dbPetugasNames->has($id)) {
                $nama = $dbPetugasNames[$id];
            } elseif ($dbPegawaiNames->has($id)) {
                $nama = $dbPegawaiNames[$id];
            }

            $kode = strtoupper($item['kode'] ?? '');
            preg_match('/^([A-Z]+)/', $kode, $m);
            $prefix = $m[1] ?? 'LAINNYA';

            if (!isset($categoryDefs[$prefix])) {
                $prefix = 'LAINNYA';
            }

            if (!isset($counters[$prefix])) {
                $counters[$prefix] = 0;
            }
            $counters[$prefix]++;
            $noUrut = $counters[$prefix];

            return [
                'no_urut' => $noUrut,
                'kode' => $kode,
                'nama' => $nama,
                'nama_template' => $item['nama'],
                'id_khanza' => $id,
                'prefix' => $prefix,
                'kategori_key' => $categoryDefs[$prefix]['key'] ?? 'lainnya',
                'kategori_nama' => $categoryDefs[$prefix]['nama'] ?? 'Lainnya',
                'kategori_badge' => $categoryDefs[$prefix]['badge'] ?? 'Lain',
                'kategori_color' => $categoryDefs[$prefix]['color'] ?? 'secondary',
                'kategori_icon' => $categoryDefs[$prefix]['icon'] ?? 'fas fa-user',
                'category_folder' => $categoryDefs[$prefix]['folder'] ?? '8. Lainnya',
            ];
        });
    }
}