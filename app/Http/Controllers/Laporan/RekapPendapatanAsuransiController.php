<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use DatePeriod;
use DateTime;
use DateInterval;

class RekapPendapatanAsuransiController extends Controller
{
    public function index(Request $request)
    {
        $tgl1 = $request->tgl1 ?? date('Y-m-01');
        $tgl2 = $request->tgl2 ?? date('Y-m-t');

        // $notaSource groups by status_lanjut AND the first day of the month of the NOTA
        $notaSource = "(
            SELECT DISTINCT 
                bp.no_rawat, 
                rp.status_lanjut,
                DATE_FORMAT(COALESCE(ni.tanggal, nj.tanggal), '%Y-%m-01') as tanggal
            FROM bayar_piutang bp
            JOIN reg_periksa rp ON bp.no_rawat = rp.no_rawat
            LEFT JOIN nota_inap ni ON bp.no_rawat = ni.no_rawat
            LEFT JOIN nota_jalan nj ON bp.no_rawat = nj.no_rawat
            WHERE bp.tgl_bayar BETWEEN '{$tgl1}' AND '{$tgl2}'
            AND COALESCE(ni.tanggal, nj.tanggal) IS NOT NULL
        )";

        $nipDokterKhusus = [
            '12041999', '0518010327', '518010327', '1802081010970003', '1802081010970000',
            '10104020181', '0817010312', '817010312', '05084010153', '5084010153',
            '0106010608', '106010608', '19960223', '0224010675', '224010675',
            '1802086108980001', '1802086108980000', '1802054204000003', '1802054204000000',
            '09964020055', '9964020055', '88888', '0214010227', '214010227', '0512010199', '512010199', '1204020129'
        ];
        $nipIn = "'" . implode("','", array_unique($nipDokterKhusus)) . "'";

        // 1. REGISTRASI
        $regQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Registrasi');

        $regPerTgl = $regQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_reg'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_reg];
            });

        // 2. PAKET TINDAKAN
        $sql = "
            SELECT 
                combined.status_lanjut,
                combined.tanggal,
                SUM(combined.material) as js,
                SUM(combined.bhp) as bhp,
                SUM(combined.jm_dr) as jm_dr,
                SUM(combined.pr) as pr,
                SUM(combined.kso) as kso
            FROM (
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, t.tarif_tindakandr as jm_dr, 0 as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_jl_dr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                UNION ALL
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, (CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_jl_pr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                UNION ALL
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, (t.tarif_tindakandr + CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_jl_drpr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                UNION ALL
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, t.tarif_tindakandr as jm_dr, 0 as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_inap_dr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                UNION ALL
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, (CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_inap_pr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
                UNION ALL
                SELECT ni.status_lanjut, ni.tanggal, t.material, t.bhp, (t.tarif_tindakandr + CASE WHEN t.nip IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as jm_dr, (CASE WHEN t.nip NOT IN ($nipIn) THEN t.tarif_tindakanpr ELSE 0 END) as pr, COALESCE(t.kso, 0) as kso
                FROM rawat_inap_drpr t JOIN {$notaSource} ni ON ni.no_rawat = t.no_rawat
            ) as combined
            GROUP BY combined.status_lanjut, combined.tanggal
        ";
        $tindakanPerTgl = collect(DB::select($sql))->keyBy(function($item) {
            return $item->status_lanjut . '|' . $item->tanggal;
        });

        // 3. OBAT
        $obatQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Obat');
        $obatPerTgl = $obatQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_obat'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_obat];
            });

        // 4. RETUR OBAT
        $returQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Retur Obat');
        $returPerTgl = $returQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_retur'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_retur];
            });

        // 5. LAB
        $labQuery = DB::table('periksa_lab as pl')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'pl.no_rawat');
        $labPerTgl = $labQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(pl.bagian_rs) as js'), DB::raw('SUM(pl.bhp) as bhp'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->keyBy(function($item) {
                return $item->status_lanjut . '|' . $item->tanggal;
            });

        // 6. RO
        $roQuery = DB::table('periksa_radiologi as pr')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'pr.no_rawat');
        $roPerTgl = $roQuery->select(
            'ni.status_lanjut', 'ni.tanggal',
            DB::raw("SUM(pr.bagian_rs + CASE WHEN pr.dokter_perujuk = 'D0000091' THEN pr.tarif_perujuk ELSE 0 END) as js"),
            DB::raw('SUM(pr.bhp) as bhp'),
            DB::raw('SUM(pr.tarif_tindakan_dokter) as jm_pj'),
            DB::raw('SUM(pr.tarif_tindakan_petugas) as petugas'),
            DB::raw("SUM(CASE WHEN pr.dokter_perujuk = 'D0000091' THEN 0 ELSE pr.tarif_perujuk END) as perujuk")
        )->groupBy('ni.status_lanjut', 'ni.tanggal')
        ->get()->keyBy(function($item) {
            return $item->status_lanjut . '|' . $item->tanggal;
        });

        // 7. POTONGAN (POT)
        $potQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Potongan');
        $potPerTgl = $potQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_pot'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_pot];
            });

        // 8. TAMBAHAN (TBM)
        $tbmQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Tambahan');
        $tbmPerTgl = $tbmQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_tbm'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_tbm];
            });

        // 9. KAMAR
        $kmrQuery = DB::table('billing as b')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'b.no_rawat')
            ->where('b.status', 'Kamar');
        $kmrPerTgl = $kmrQuery->select('ni.status_lanjut', 'ni.tanggal', DB::raw('SUM(b.totalbiaya) as total_kmr'))
            ->groupBy('ni.status_lanjut', 'ni.tanggal')
            ->get()->mapWithKeys(function ($item) {
                return [$item->status_lanjut . '|' . $item->tanggal => $item->total_kmr];
            });

        // 10. OK / OPERASI
        $okQuery = DB::table('operasi as o')
            ->join(DB::raw("{$notaSource} as ni"), 'ni.no_rawat', '=', 'o.no_rawat');
        $okPerTgl = $okQuery->select(
            'ni.status_lanjut', 'ni.tanggal',
            DB::raw("SUM(
                o.biayaoperator1 + o.biayaoperator2 + o.biayaoperator3 + o.biayadokter_anak + o.biayadokter_anestesi + o.biaya_dokter_pjanak + o.biaya_dokter_umum
                + CASE WHEN o.asisten_operator1 IN ($nipIn) THEN o.biayaasisten_operator1 ELSE 0 END
                + CASE WHEN o.asisten_operator2 IN ($nipIn) THEN o.biayaasisten_operator2 ELSE 0 END
                + CASE WHEN o.asisten_operator3 IN ($nipIn) THEN o.biayaasisten_operator3 ELSE 0 END
                + CASE WHEN o.asisten_anestesi IN ($nipIn) THEN o.biayaasisten_anestesi ELSE 0 END
                + CASE WHEN o.asisten_anestesi2 IN ($nipIn) THEN o.biayaasisten_anestesi2 ELSE 0 END
                + CASE WHEN o.bidan IN ($nipIn) THEN o.biayabidan ELSE 0 END
                + CASE WHEN o.bidan2 IN ($nipIn) THEN o.biayabidan2 ELSE 0 END
                + CASE WHEN o.bidan3 IN ($nipIn) THEN o.biayabidan3 ELSE 0 END
                + CASE WHEN o.instrumen IN ($nipIn) THEN o.biayainstrumen ELSE 0 END
                + CASE WHEN o.perawaat_resusitas IN ($nipIn) THEN o.biayaperawaat_resusitas ELSE 0 END
                + CASE WHEN o.perawat_luar IN ($nipIn) THEN o.biayaperawat_luar ELSE 0 END
                + CASE WHEN o.omloop IN ($nipIn) THEN o.biaya_omloop ELSE 0 END
                + CASE WHEN o.omloop2 IN ($nipIn) THEN o.biaya_omloop2 ELSE 0 END
                + CASE WHEN o.omloop3 IN ($nipIn) THEN o.biaya_omloop3 ELSE 0 END
                + CASE WHEN o.omloop4 IN ($nipIn) THEN o.biaya_omloop4 ELSE 0 END
                + CASE WHEN o.omloop5 IN ($nipIn) THEN o.biaya_omloop5 ELSE 0 END
            ) as jm_dr"),
            DB::raw("SUM(
                CASE WHEN o.asisten_operator1 NOT IN ($nipIn) THEN o.biayaasisten_operator1 ELSE 0 END
                + CASE WHEN o.asisten_operator2 NOT IN ($nipIn) THEN o.biayaasisten_operator2 ELSE 0 END
                + CASE WHEN o.asisten_operator3 NOT IN ($nipIn) THEN o.biayaasisten_operator3 ELSE 0 END
                + CASE WHEN o.asisten_anestesi NOT IN ($nipIn) THEN o.biayaasisten_anestesi ELSE 0 END
                + CASE WHEN o.asisten_anestesi2 NOT IN ($nipIn) THEN o.biayaasisten_anestesi2 ELSE 0 END
                + CASE WHEN o.bidan NOT IN ($nipIn) THEN o.biayabidan ELSE 0 END
                + CASE WHEN o.bidan2 NOT IN ($nipIn) THEN o.biayabidan2 ELSE 0 END
                + CASE WHEN o.bidan3 NOT IN ($nipIn) THEN o.biayabidan3 ELSE 0 END
                + CASE WHEN o.instrumen NOT IN ($nipIn) THEN o.biayainstrumen ELSE 0 END
                + CASE WHEN o.perawaat_resusitas NOT IN ($nipIn) THEN o.biayaperawaat_resusitas ELSE 0 END
                + CASE WHEN o.perawat_luar NOT IN ($nipIn) THEN o.biayaperawat_luar ELSE 0 END
                + CASE WHEN o.omloop NOT IN ($nipIn) THEN o.biaya_omloop ELSE 0 END
                + CASE WHEN o.omloop2 NOT IN ($nipIn) THEN o.biaya_omloop2 ELSE 0 END
                + CASE WHEN o.omloop3 NOT IN ($nipIn) THEN o.biaya_omloop3 ELSE 0 END
                + CASE WHEN o.omloop4 NOT IN ($nipIn) THEN o.biaya_omloop4 ELSE 0 END
                + CASE WHEN o.omloop5 NOT IN ($nipIn) THEN o.biaya_omloop5 ELSE 0 END
            ) as jm_pr"),
            DB::raw('SUM(o.biayaalat + o.biayasewaok + o.akomodasi + o.bagian_rs + o.biayasarpras) as js')
        )->groupBy('ni.status_lanjut', 'ni.tanggal')
        ->get()->keyBy(function($item) {
            return $item->status_lanjut . '|' . $item->tanggal;
        });

        // GABUNGKAN SEMUA KEY (status_lanjut|tanggal) YANG ADA
        $allKeys = collect(array_keys($tindakanPerTgl->toArray()))
            ->merge(array_keys($regPerTgl->toArray()))
            ->merge(array_keys($obatPerTgl->toArray()))
            ->merge(array_keys($returPerTgl->toArray()))
            ->merge(array_keys($labPerTgl->toArray()))
            ->merge(array_keys($roPerTgl->toArray()))
            ->merge(array_keys($potPerTgl->toArray()))
            ->merge(array_keys($tbmPerTgl->toArray()))
            ->merge(array_keys($kmrPerTgl->toArray()))
            ->merge(array_keys($okPerTgl->toArray()))
            ->filter()
            ->unique()
            ->sort() // sorts alphabetically e.g. "Ralan|2025-01-01", "Ranap|2025-01-01"
            ->values();

        $namaBulan = [
            1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 5 => 'MEI', 6 => 'JUNI',
            7 => 'JULI', 8 => 'AGT', 9 => 'SEPT', 10 => 'OKT', 11 => 'NOV', 12 => 'DES'
        ];

        $dataRekap = collect();
        foreach ($allKeys as $keyStr) {
            // $keyStr is format "Ralan|2025-01-01"
            list($statusLanjut, $tglStr) = explode('|', $keyStr);
            
            $dt = new DateTime($tglStr);
            $monthNum = (int)$dt->format('n');
            $year = $dt->format('Y');
            
            $statusPrefix = ($statusLanjut === 'Ralan') ? 'RJ' : 'RI';

            $tindakan = $tindakanPerTgl->get($keyStr);
            $regVal = (float) $regPerTgl->get($keyStr, 0);
            $jsVal   = $tindakan ? (float)$tindakan->js : 0;
            $bhpVal  = $tindakan ? (float)$tindakan->bhp : 0;
            $jmDrVal = $tindakan ? (float)$tindakan->jm_dr : 0;
            $prVal   = $tindakan ? (float)$tindakan->pr : 0;
            $ksoVal  = $tindakan ? (float)$tindakan->kso : 0;

            $obatVal  = (float) $obatPerTgl->get($keyStr, 0);
            $returVal = (float) $returPerTgl->get($keyStr, 0);

            $labItem = $labPerTgl->get($keyStr);
            $labJsVal  = $labItem ? (float)$labItem->js : 0;
            $labBhpVal = $labItem ? (float)$labItem->bhp : 0;
            $labJmPjVal = 0; // belum ada logic

            $roItem = $roPerTgl->get($keyStr);
            $roJsVal      = $roItem ? (float)$roItem->js : 0;
            $roBhpVal     = $roItem ? (float)$roItem->bhp : 0;
            $roJmPjVal    = $roItem ? (float)$roItem->jm_pj : 0;
            $roPetugasVal = $roItem ? (float)$roItem->petugas : 0;
            $roPerujukVal = $roItem ? (float)$roItem->perujuk : 0;

            $potVal = (float) $potPerTgl->get($keyStr, 0);
            $tbmVal = (float) $tbmPerTgl->get($keyStr, 0);
            $kmrVal = (float) $kmrPerTgl->get($keyStr, 0);

            $okItem    = $okPerTgl->get($keyStr);
            $okJmDrVal = $okItem ? (float)$okItem->jm_dr : 0;
            $okJmPrVal = $okItem ? (float)$okItem->jm_pr : 0;
            $okJsVal   = $okItem ? (float)$okItem->js : 0;

            $rowTotal = $regVal + $jsVal + $bhpVal + $jmDrVal + $prVal + $ksoVal
                + $obatVal + $returVal
                + $labJsVal + $labBhpVal
                + $roJsVal + $roBhpVal + $roJmPjVal + $roPetugasVal + $roPerujukVal
                + $potVal + $tbmVal + $kmrVal
                + $okJmDrVal + $okJmPrVal + $okJsVal;

            $dataRekap->push((object)[
                'group_key'  => $statusPrefix . '.' . $year,
                'nama_bulan' => $namaBulan[$monthNum],
                'reg'        => $regVal,
                'js'         => $jsVal,
                'bhp'        => $bhpVal,
                'jm_dr'      => $jmDrVal,
                'pr'         => $prVal,
                'kso'        => $ksoVal,
                'obat'       => $obatVal,
                'retur'      => $returVal,
                'lab_js'     => $labJsVal,
                'lab_bhp'    => $labBhpVal,
                'lab_jm_pj'  => $labJmPjVal,
                'ro_js'      => $roJsVal,
                'ro_bhp'     => $roBhpVal,
                'ro_jm_pj'   => $roJmPjVal,
                'ro_petugas' => $roPetugasVal,
                'ro_perujuk' => $roPerujukVal,
                'pot'        => $potVal,
                'tbm'        => $tbmVal,
                'kamar'      => $kmrVal,
                'ok_jm_dr'   => $okJmDrVal,
                'ok_jm_pr'   => $okJmPrVal,
                'ok_js'      => $okJsVal,
                'total'      => $rowTotal,
                'dp_ekses'   => 0,
                'ekses'      => 0,
                'sudah_bayar'=> 0,
                'piutang_obat'=> 0,
                'cob'        => 0,
                'tmb_bayar'  => 0,
                'pot_bayar'  => 0,
                'grand_total'=> 0
            ]);
        }

        return view('laporan.rekappendapatanasuransi', compact('tgl1', 'tgl2', 'dataRekap'));
    }
}
