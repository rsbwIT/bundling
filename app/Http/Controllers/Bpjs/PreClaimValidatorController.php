<?php

namespace App\Http\Controllers\Bpjs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreClaimValidatorController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $jenisRawat = $request->input('jenis_rawat', 'Semua');
        
        $data = DB::table('reg_periksa')
            ->selectRaw("
                reg_periksa.no_rawat,
                reg_periksa.no_rkm_medis,
                reg_periksa.status_lanjut,
                pasien.nm_pasien as nama_pasien,
                poliklinik.nm_poli as poliklinik,
                dokter.nm_dokter as nama_dokter,
                dokter.no_telp as no_telp,
                IF(COUNT(resume_pasien.no_rawat) > 0 OR COUNT(resume_pasien_ranap.no_rawat) > 0, 1, 0) as resume_medis,
                IF(COUNT(operasi.no_rawat) > 0, IF(COUNT(laporan_operasi.no_rawat) > 0, 1, 0), NULL) as laporan_operasi,
                IF(COUNT(diagnosa_pasien.no_rawat) > 0, 1, 0) as koding_inacbg
            ")
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('resume_pasien', 'reg_periksa.no_rawat', '=', 'resume_pasien.no_rawat')
            ->leftJoin('resume_pasien_ranap', 'reg_periksa.no_rawat', '=', 'resume_pasien_ranap.no_rawat')
            ->leftJoin('operasi', 'reg_periksa.no_rawat', '=', 'operasi.no_rawat')
            ->leftJoin('laporan_operasi', 'reg_periksa.no_rawat', '=', 'laporan_operasi.no_rawat')
            ->leftJoin('diagnosa_pasien', 'reg_periksa.no_rawat', '=', 'diagnosa_pasien.no_rawat')
            ->where('reg_periksa.tgl_registrasi', $tanggal)
            ->when($jenisRawat !== 'Semua', function($q) use ($jenisRawat) {
                return $q->where('reg_periksa.status_lanjut', $jenisRawat);
            })
            ->groupBy(
                'reg_periksa.no_rawat', 
                'reg_periksa.no_rkm_medis', 
                'reg_periksa.status_lanjut',
                'pasien.nm_pasien', 
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
                'dokter.no_telp'
            )
            ->get();

        // Calculate aggregates for dashboard metric cards
        $totalPasien = $data->count();
        $siapKlaim = 0;
        $bermasalah = 0;
        $sepBermasalah = 0;

        // Process final status for each row
        foreach ($data as $row) {
            // --- FETCH INACBG TARIFF & SEP ---
            $row->status_sep = 0;
            $row->no_sep = null;
            $row->tarif_inacbg = null;
            $row->code_cbg = null;

            // Ambil semua SEP milik pasien ini, PRIORITASKAN SEP yang sesuai dengan status_lanjut
            $targetJnsPelayanan = ($row->status_lanjut == 'Ranap') ? '1' : '2'; // 1 = Ranap, 2 = Ralan
            
            $seps = DB::table('bridging_sep')
                ->where('no_rawat', $row->no_rawat)
                ->orderByRaw("jnspelayanan = '{$targetJnsPelayanan}' DESC") // Prioritaskan jenis pelayanan yang cocok
                ->orderBy('tglsep', 'desc')
                ->get();

            if ($seps->count() > 0) {
                $row->status_sep = 1;
                
                // Cari SEP mana yang SUDAH dikoding/digrouping (yang punya tarif)
                foreach ($seps as $s) {
                    $cleanSep = trim($s->no_sep);
                    
                    // Cek stage 12 dulu, kalau tidak ada cek stage 1
                    $inacbg = DB::table('inacbg_grouping_stage12')->where('no_sep', $cleanSep)->first();
                    if (!$inacbg) {
                        $inacbg = DB::table('inacbg_grouping_stage1')->where('no_sep', $cleanSep)->first();
                    }

                    if ($inacbg) {
                        $row->no_sep = $cleanSep;
                        $row->tarif_inacbg = $inacbg->tarif;
                        $row->code_cbg = $inacbg->code_cbg;
                        $row->koding_inacbg = 1; // Anggap sudah dikoding jika tarif sudah ada
                        break; // Berhenti mencari jika sudah ketemu SEP yang bertarif
                    }
                }
                
                // Jika pasien punya SEP tapi belum ada satupun yang dikoding, gunakan SEP terbaru
                if (!$row->no_sep) {
                    $row->no_sep = trim($seps->first()->no_sep);
                }
            }

            // Evaluasi status klaim:
            // Klaim siap jika: punya SEP, punya Resume, dan Koding INACBG selesai.
            // Laporan operasi opsional, tapi jika dia punya Operasi, maka laporannya harus ada.
            $isOperasiValid = true;
            if ($row->laporan_operasi === 0) {
                // Ada tagihan operasi tapi belum ada laporan
                $isOperasiValid = false;
            }

            if ($row->status_sep == 1 && $row->resume_medis == 1 && $row->koding_inacbg == 1 && $isOperasiValid) {
                $row->status_klaim = 'LENGKAP';
                $siapKlaim++;
            } else {
                $row->status_klaim = 'PENDING';
                $bermasalah++;
                
                if ($row->status_sep == 0) {
                    $sepBermasalah++;
                }
            }

            // --- SUPER CODING OPTIMIZER ---
            $row->saran_optimasi = "";
            
            // 1. Ambil semua diagnosa pasien sekaligus (Efisiensi Query)
            $diagnosaPasien = DB::table('diagnosa_pasien')
                ->where('no_rawat', $row->no_rawat)
                ->pluck('kd_penyakit')
                ->toArray();
            
            // 2. Ambil semua hasil lab pasien sekaligus
            $labPasien = DB::table('detail_periksa_lab')
                ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template')
                ->where('detail_periksa_lab.no_rawat', $row->no_rawat)
                ->select('template_laboratorium.Pemeriksaan', 'detail_periksa_lab.nilai')
                ->get();

            // 3. Ambil semua riwayat obat pasien sekaligus
            $obatPasien = DB::table('detail_pemberian_obat')
                ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
                ->where('detail_pemberian_obat.no_rawat', $row->no_rawat)
                ->pluck('databarang.nama_brng')
                ->toArray();

            // 4. Ambil semua prosedur ICD-9 pasien
            $prosedurPasien = DB::table('prosedur_pasien')
                ->where('no_rawat', $row->no_rawat)
                ->pluck('kode')
                ->toArray();

            // Fungsi Helper untuk cek diagnosa awalan tertentu
            $hasDiagnosa = function($prefix) use ($diagnosaPasien) {
                foreach ($diagnosaPasien as $d) {
                    if (str_starts_with($d, $prefix)) return true;
                }
                return false;
            };

            // Fungsi Helper untuk cek prosedur awalan tertentu
            $hasProsedur = function($prefix) use ($prosedurPasien) {
                foreach ($prosedurPasien as $p) {
                    if (str_starts_with($p, $prefix)) return true;
                }
                return false;
            };

            $saranArray = [];

            // A. Evaluasi Hasil Laboratorium Terhadap Aturan Klaim
            foreach ($labPasien as $lab) {
                $namaLab = strtolower($lab->Pemeriksaan);
                // Ubah koma jadi titik agar bisa dikonversi ke desimal
                $nilai = floatval(trim(str_replace(',', '.', $lab->nilai)));
                
                if ($nilai <= 0) continue; 

                // 1. Anemia (Hb < 8 -> D64)
                if (str_contains($namaLab, 'hb') || str_contains($namaLab, 'hemoglobin')) {
                    if ($nilai < 8 && !$hasDiagnosa('D64')) {
                        $saranArray[] = "🩸 <b>Anemia:</b> Pasien memiliki riwayat Hb rendah ($lab->nilai). Tambahkan kode komorbid <b>D64 (Anemia)</b> untuk berpotensi menaikkan <b>Severity Level</b> klaim.";
                    }
                }
                
                // 2. Hipokalemia (Kalium < 3.5 -> E87.6)
                if (str_contains($namaLab, 'kalium')) {
                    if ($nilai < 3.5 && !$hasDiagnosa('E87.6')) {
                        $saranArray[] = "🧪 <b>Hipokalemia:</b> Pasien memiliki Kalium rendah ($lab->nilai). Tambahkan kode komorbid <b>E87.6 (Hipokalemia)</b> untuk mendongkrak <b>Severity Level</b>.";
                    }
                }

                // 3. Hiponatremia (Natrium < 135 -> E87.1)
                if (str_contains($namaLab, 'natrium')) {
                    if ($nilai < 135 && !$hasDiagnosa('E87.1')) {
                        $saranArray[] = "🧂 <b>Hiponatremia:</b> Pasien memiliki Natrium rendah ($lab->nilai). Tambahkan kode komorbid <b>E87.1 (Hiponatremia)</b> untuk mendongkrak <b>Severity Level</b>.";
                    }
                }

                // 4. Diabetes (Gula Darah > 200 -> E11)
                if (str_contains($namaLab, 'gula darah') || str_contains($namaLab, 'glukosa')) {
                    if ($nilai > 200 && !$hasDiagnosa('E11')) {
                        $saranArray[] = "🍬 <b>Diabetes:</b> Pasien memiliki Gula Darah tinggi ($lab->nilai). Jika benar menderita DM, masukkan kode <b>E11</b> sebagai sekunder untuk menaikkan <b>Severity Level</b> klaim.";
                    }
                }
                
                // 5. Sepsis (Leukosit > 12000 or < 4000 -> A41.9)
                if (str_contains($namaLab, 'leukosit')) {
                    if (($nilai > 12000 || ($nilai < 4000 && $nilai > 0)) && !$hasDiagnosa('A41')) {
                        $saranArray[] = "🦠 <b>Indikasi Sepsis (Billion Dollar Code):</b> Leukosit pasien sangat abnormal ($lab->nilai). Telusuri gejala klinis (SIRS), jika memenuhi kriteria, konfirmasi ke DPJP untuk menegakkan diagnosa <b>Sepsis (A41.9)</b> untuk melipatgandakan Severity Level!";
                    }
                }
            }

            // B. Evaluasi Diagnosa Khusus
            // 5. Kontrol Ortopedi / Bedah (Z09 -> butuh S/T/M code)
            if ($hasDiagnosa('Z09')) {
                $hasPenyakitUtama = false;
                foreach ($diagnosaPasien as $d) {
                    // Cek apakah ada cedera (S/T) atau muskuloskeletal (M)
                    if (str_starts_with($d, 'S') || str_starts_with($d, 'T') || str_starts_with($d, 'M')) {
                        $hasPenyakitUtama = true; 
                        break;
                    }
                }
                if (!$hasPenyakitUtama) {
                    $saranArray[] = "🦴 <b>Kontrol / Follow-up:</b> Pasien ini menggunakan kode kontrol (Z09). Pastikan Anda memasukkan kode penyakit utamanya (contoh: <b>S52.50</b> untuk Fraktur) sebagai sekunder agar <b>Severity Level & Tarif</b> maksimal.";
                }
            }

            // C. Evaluasi Riwayat Obat (Farmasi)
            $hasObat = function($keywords) use ($obatPasien) {
                foreach ($obatPasien as $obat) {
                    $namaObat = strtolower($obat);
                    foreach ((array)$keywords as $kw) {
                        if (str_contains($namaObat, strtolower($kw))) return true;
                    }
                }
                return false;
            };

            // 6. Gagal Jantung (Furosemide / Lasix -> butuh I50)
            if ($hasObat(['furosemid', 'lasix', 'farsix', 'impugan'])) {
                if (!$hasDiagnosa('I50')) {
                    $saranArray[] = "💊 <b>Gagal Jantung:</b> Pasien diberikan obat Diuretik (contoh: Furosemide). Cek apakah pasien mengalami Gagal Jantung, jika iya masukkan kode <b>I50</b> sebagai komorbid untuk menaikkan <b>Severity Level</b>.";
                }
            }

            // 7. PPOK / Asma (Combivent / Ventolin / Symbicort / Salbutamol -> J44 / J45)
            if ($hasObat(['combivent', 'ventolin', 'symbicort', 'salbutamol', 'pulmicort', 'fartolin'])) {
                if (!$hasDiagnosa('J44') && !$hasDiagnosa('J45')) {
                    $saranArray[] = "💨 <b>PPOK / Asma:</b> Pasien diberikan obat Inhaler / Nebulizer. Cek apakah ada indikasi PPOK (<b>J44</b>) atau Asma (<b>J45</b>) untuk diinput sebagai sekunder penambah <b>Severity Level</b>.";
                }
                if (!$hasProsedur('93.9')) {
                    $saranArray[] = "💨 <b>Tindakan Siluman (Nebulizer):</b> Pasien mendapat terapi pernapasan. Pastikan Kode Prosedur Terapi Nebulizer (ICD-9: <b>93.94</b>) ikut dimasukkan.";
                }
            }

            // 8. Transfusi Darah (PRC / WB / Darah -> butuh prosedur 99.04)
            if ($hasObat(['prc', 'wb', 'darah ', 'kantong darah', 'transfusi'])) {
                if (!$hasProsedur('99.0')) {
                    $saranArray[] = "🩸 <b>Tindakan Siluman (Transfusi):</b> Terdapat tagihan Kantong Darah, namun Kode Prosedur Transfusi (ICD-9: <b>99.04</b>) belum di-input. Jangan sampai terlewat karena berdampak besar pada Grouping!";
                }
            }

            // D. Evaluasi Berkas Penunjang, Operasi, & Status Pulang
            // 9. Anti-Fraud Laporan Operasi
            if ($row->laporan_operasi === 0) {
                $saranArray[] = "<div class='alert alert-danger mb-0 p-2 mt-2'>🚨 <b>FRAUD ALERT:</b> Terdapat tagihan Kamar Operasi, namun <b>Dokter Bedah belum mengetikkan Laporan Operasi!</b> Klaim terkunci mutlak dan dilarang dikirim ke BPJS.</div>";
            }
            // 8. Radiologi Kosong
            $rad = DB::table('periksa_radiologi')->where('no_rawat', $row->no_rawat)->exists();
            if ($rad) {
                $hasilRad = DB::table('hasil_radiologi')->where('no_rawat', $row->no_rawat)->exists();
                if (!$hasilRad) {
                    $saranArray[] = "🩻 <b>Radiologi Kosong:</b> Terdapat tagihan Rontgen, namun Hasil Ekspertise belum di-input! Berkas wajib dilengkapi agar tidak dikembalikan BPJS.";
                    $row->status_klaim = 'PENDING';
                }
            }

            // 9. Pasien Meninggal
            $kamar = DB::table('kamar_inap')->where('no_rawat', $row->no_rawat)->orderBy('tgl_keluar', 'desc')->first();
            if ($kamar && stripos($kamar->stts_pulang, 'Meninggal') !== false) {
                $saranArray[] = "💀 <b>Pasien Meninggal:</b> Pasien tercatat Meninggal di Rawat Inap. Pastikan Cara Pulang di E-Klaim di-set ke Kematian agar klaim tidak ditolak!";
            }

            // Gabungkan semua saran
            if (count($saranArray) > 0) {
                $row->saran_optimasi = implode("<hr class='my-2'>", $saranArray);
            }
        }

        // Feature: Excel Export
        if ($request->input('export') === 'excel') {
            return response(view('bpjs.pre_claim_excel', [
                'tanggal' => $tanggal,
                'jenisRawat' => $jenisRawat,
                'data' => $data,
            ]))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="Rekap_Klaim_BPJS_'.$tanggal.'.xls"');
        }

        return view('bpjs.pre_claim_validator', [
            'tanggal' => $tanggal,
            'jenisRawat' => $jenisRawat,
            'data' => $data,
            'metrics' => [
                'total' => $totalPasien,
                'siap' => $siapKlaim,
                'pending' => $bermasalah,
                'sep_error' => $sepBermasalah
            ]
        ]);
    }
}
