<?php

namespace App\Http\Controllers\JM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\CacheService;

class JMAsuransiController extends Controller
{
    protected $cacheService;

    // List Template dari Gambar (bisa ditambahkan 'id_khanza' nya nanti jika auto-match meleset)
    public $templateJM = [
        ['kode' => 'SP1', 'nama' => 'Achmad Gozali, dr, Sp.P', 'id_khanza' => 'D0000070'],
        ['kode' => 'FG1', 'nama' => 'Agung Rangga Dinata', 'id_khanza' => '0518010327'],
        ['kode' => 'U1', 'nama' => 'Mohamad Farhan, dr', 'id_khanza' => 'D0000113'],
        ['kode' => 'SP2', 'nama' => 'Aldilla Cinarasti, dr, Sp.A', 'id_khanza' => 'D0000077'],
        ['kode' => 'SP3', 'nama' => 'Ali Imran Yusuf, dr, Sp.PD, KGEH', 'id_khanza' => 'D0000012'],
        ['kode' => 'OK1', 'nama' => 'M. Amirudin', 'id_khanza' => '1212010202'],
        ['kode' => 'U2', 'nama' => 'Andi Nurlela Wulandari, dr', 'id_khanza' => 'D0000033'],
        ['kode' => 'U3', 'nama' => 'Arly Fadhilah Arief, dr', 'id_khanza' => 'D0000099'],
        ['kode' => 'SP4', 'nama' => 'Anisrulloh, dr, Sp.THT.KL M.Kes', 'id_khanza' => 'D0000043'],
        ['kode' => 'U4', 'nama' => 'Arief Yulizar, dr', 'id_khanza' => 'D0000005'],
        ['kode' => 'SP5', 'nama' => 'Arman Sanun, dr, SpOG', 'id_khanza' => 'D0000032'],
        ['kode' => 'U27', 'nama' => 'Arya Pandu Astaguna, dr', 'id_khanza' => 'D0000087'],
        ['kode' => 'SWL3', 'nama' => 'Astika Septiyani', 'id_khanza' => '512010199'],
        ['kode' => 'U5', 'nama' => 'Hendro Prasetiyo, dr', 'id_khanza' => 'D0000112'],
        ['kode' => 'U6', 'nama' => 'Azizha Risa Luthfia, dr', 'id_khanza' => 'D0000074'],
        ['kode' => 'AD1', 'nama' => 'Nisaa Qolbi', 'id_khanza' => '12041999'],
        ['kode' => 'SP6', 'nama' => 'Bobby Setiawan, dr, Sp.THT-KL', 'id_khanza' => 'D0000110'],
        ['kode' => 'SWL1', 'nama' => 'Dani', 'id_khanza' => '88888'],
        ['kode' => 'SP7', 'nama' => 'Dewi Arum Listyanti, dr, SpB', 'id_khanza' => 'D0000042'],
        ['kode' => 'U7', 'nama' => 'Sella Kintania Sari, dr', 'id_khanza' => 'D0000126'],
        ['kode' => 'OK2', 'nama' => 'Dony Marlindo, S.Kep', 'id_khanza' => '1212121'],
        ['kode' => 'SP8', 'nama' => 'Luciana Jeannette Ciptoyuwono, dr', 'id_khanza' => 'D0000132'],
        ['kode' => 'OK3', 'nama' => 'Dwi Oktariadi', 'id_khanza' => '8994010078'],
        ['kode' => 'SP9', 'nama' => 'Muhammad Aljazza Asmarantaka', 'id_khanza' => 'D0000130'],
        ['kode' => 'SP10', 'nama' => 'Eddy Marudut Sitompul, dr, SpOT', 'id_khanza' => 'D0000014'],
        ['kode' => 'SP11', 'nama' => 'Muhammad Aditya, dr, Sp.P.M.Epi', 'id_khanza' => 'D0000109'],
        ['kode' => 'SP12', 'nama' => 'Samuel Marco Halomoan, dr, SpB', 'id_khanza' => 'D0000106'],
        ['kode' => 'U8', 'nama' => 'Evi Febriana Lubis, dr', 'id_khanza' => 'D0000034'],
        ['kode' => 'SP13', 'nama' => 'Exsa Hadibrata, dr, Sp.U', 'id_khanza' => 'D0000054'],
        ['kode' => 'U9', 'nama' => 'Fitrinda Soniya, dr', 'id_khanza' => 'D0000134'],
        ['kode' => 'FG2', 'nama' => 'Farah, Amd', 'id_khanza' => '10104020181'],
        ['kode' => 'SP14', 'nama' => 'Farida Oktarina, dr, Sp.M', 'id_khanza' => 'D0000072'],
        ['kode' => 'SP15', 'nama' => 'Ghita Widya Murti, dr, SpP', 'id_khanza' => 'D0000105'],
        ['kode' => 'SP16', 'nama' => 'Indri Widiarti, dr, Sp.PA', 'id_khanza' => 'D0000133'],
        ['kode' => 'SP17', 'nama' => 'Shintia Putri Wulandari, dr, Sp.THT', 'id_khanza' => 'D0000111'],
        ['kode' => 'OK4', 'nama' => 'Harsono', 'id_khanza' => ''],
        ['kode' => 'U25', 'nama' => 'Senja Nurhayati, dr', 'id_khanza' => 'D0000121'],
        ['kode' => 'HD1', 'nama' => 'HD Andan', 'id_khanza' => ''],
        ['kode' => 'HD2', 'nama' => 'HD Bayu', 'id_khanza' => ''],
        ['kode' => 'HD3', 'nama' => 'HD Danu', 'id_khanza' => ''],
        ['kode' => 'HD4', 'nama' => 'HD Ferdian', 'id_khanza' => ''],
        ['kode' => 'HD5', 'nama' => 'HD Kus', 'id_khanza' => '09964020055'],
        ['kode' => 'HD6', 'nama' => 'HD Lili', 'id_khanza' => ''],
        ['kode' => 'HD7', 'nama' => 'HD M. Dwi', 'id_khanza' => ''],
        ['kode' => 'HD8', 'nama' => 'HD Mala', 'id_khanza' => ''],
        ['kode' => 'HD9', 'nama' => 'HD Ade Supriatna', 'id_khanza' => ''],
        ['kode' => 'HD10', 'nama' => 'HD Yopi', 'id_khanza' => ''],
        ['kode' => 'HD11', 'nama' => 'HD Ria', 'id_khanza' => ''],
        ['kode' => 'HD12', 'nama' => 'HD Ronal', 'id_khanza' => ''],
        ['kode' => 'HD13', 'nama' => 'HD Sabtina', 'id_khanza' => ''],
        ['kode' => 'HD14', 'nama' => 'HD Sumo', 'id_khanza' => ''],
        ['kode' => 'HD15', 'nama' => 'HD Sutriyanti', 'id_khanza' => ''],
        ['kode' => 'HD16', 'nama' => 'HD Vina', 'id_khanza' => ''],
        ['kode' => 'SP18', 'nama' => 'Horidokasa R, dr, SpOG', 'id_khanza' => 'D0000062'],
        ['kode' => 'SP19', 'nama' => 'Hotman Sijabat, dr, SpPD', 'id_khanza' => 'D0000038'],
        ['kode' => 'SP20', 'nama' => 'Lydia Theresia Tampubolon, dr, M.Kes', 'id_khanza' => 'D0000107'],
        ['kode' => 'U11', 'nama' => 'Ilham Wijaya Kusuma, dr', 'id_khanza' => 'D0000056'],
        ['kode' => 'SP21', 'nama' => 'Tresia Ivani Saskia, dr, Sp.M', 'id_khanza' => 'D0000128'],
        ['kode' => 'OK5', 'nama' => 'Nailul Istiqomah, A.Md An', 'id_khanza' => '1616161'],
        ['kode' => 'SP22', 'nama' => 'Karyanto, dr, Sp Rad', 'id_khanza' => 'D0000017'],
        ['kode' => 'SP23', 'nama' => 'Rizqi Adhalia, dr, SpAN', 'id_khanza' => 'D0000045'],
        ['kode' => 'SP61', 'nama' => 'Kristina Yuniarsih, dr, Sp.M', 'id_khanza' => 'D0000129'],
        ['kode' => 'SP24', 'nama' => 'Romi Saputra, dr, SpB', 'id_khanza' => 'D0000100'],
        ['kode' => 'SP25', 'nama' => 'Lily Hayati, dr, Sp. A', 'id_khanza' => 'D0000082'],
        ['kode' => 'SP26', 'nama' => 'Lukma Alinda Putri, Drg', 'id_khanza' => 'D0000061'],
        ['kode' => 'U12', 'nama' => 'Aditya Rustami, dr', 'id_khanza' => 'D0000127'],
        ['kode' => 'SWL2', 'nama' => 'Indah Lestriana', 'id_khanza' => '214010227'],
        ['kode' => 'SP27', 'nama' => 'Nanang Suhana, dr, Sp.THT-KL', 'id_khanza' => 'D0000051'],
        ['kode' => 'U26', 'nama' => 'Asyraf Vivaldi Wardoyo, dr', 'id_khanza' => 'D0000122'],
        ['kode' => 'SP28', 'nama' => 'Muhammad Nasrulloh, dr, SpOT', 'id_khanza' => 'D0000044'],
        ['kode' => 'OK6', 'nama' => 'Ndang', 'id_khanza' => '202020'],
        ['kode' => 'U13', 'nama' => 'Caesaria Sinta Zuya, dr', 'id_khanza' => 'D0000118'],
        ['kode' => 'SP29', 'nama' => 'Rahmiasari Mujitaba, dr, Sp.DV', 'id_khanza' => 'D0000114'],
        ['kode' => 'U14', 'nama' => 'Nona Fitria Tu, dr', 'id_khanza' => 'D0000048'],
        ['kode' => 'SP30', 'nama' => 'Nur Fahmi Fauziah, Drg', 'id_khanza' => 'D0000047'],
        ['kode' => 'SP31', 'nama' => 'Chairil Makky, dr, Sp.PD,FINASIM', 'id_khanza' => 'D0000115'],
        ['kode' => 'SP32', 'nama' => 'Tegar Dwi Prakoso N, dr, SpOG', 'id_khanza' => 'D0000125'],
        ['kode' => 'U15', 'nama' => 'Oktafany, dr', 'id_khanza' => 'D0000009'],
        ['kode' => 'OK7', 'nama' => 'Pebriyudin', 'id_khanza' => '511010185'],
        ['kode' => 'SP33', 'nama' => 'Pinna Hutaunik, dr, SpB FINACS', 'id_khanza' => 'D0000018'],
        ['kode' => 'AD3', 'nama' => 'Putu', 'id_khanza' => '5191004'],
        ['kode' => 'SP34', 'nama' => 'Radin Intan Edilla Sini, dr, SpAn.M', 'id_khanza' => 'D0000039'],
        ['kode' => 'FG3', 'nama' => 'Resi Resiana', 'id_khanza' => '0817010312'],
        ['kode' => 'U16', 'nama' => 'Rifkia Izza Maarits, dr', 'id_khanza' => 'D0000068'],
        ['kode' => 'SP35', 'nama' => 'Rina Dewi Yustiani, dr, Sp.PD.', 'id_khanza' => 'D0000028'],
        ['kode' => 'U17', 'nama' => 'Rino Yoga Okdiansyah, dr', 'id_khanza' => 'D0000065'],
        ['kode' => 'SP36', 'nama' => 'Riona Sari, dr. M.Sc., Sp.A', 'id_khanza' => 'D0000064'],
        ['kode' => 'U18', 'nama' => 'Rizky Madiyya Taqwin, dr', 'id_khanza' => 'D0000053'],
        ['kode' => 'SP37', 'nama' => 'Roeswir Achary, dr, SpS', 'id_khanza' => 'D0000019'],
        ['kode' => 'SP38', 'nama' => 'Rolis Anggi Wurllyanti, drg, Sp.P.M', 'id_khanza' => 'D0000073'],
        ['kode' => 'SP39', 'nama' => 'Rosdianti Diah Andhiani, dr, SpM', 'id_khanza' => 'D0000026'],
        ['kode' => 'SP40', 'nama' => 'Ruskandi Martaatmadja, dr, SpA', 'id_khanza' => 'D0000020'],
        ['kode' => 'U19', 'nama' => 'Sabdo Mulyawan, dr', 'id_khanza' => 'D0000008'],
        ['kode' => 'SP41', 'nama' => 'Sanjoto Santibudi, dr, Sp.KFR', 'id_khanza' => 'D0000030'],
        ['kode' => 'U20', 'nama' => 'Puji Indah Permatasari, dr', 'id_khanza' => 'D0000098'],
        ['kode' => 'SP42', 'nama' => 'Sariningsih, dr, Sp.S', 'id_khanza' => 'D0000071'],
        ['kode' => 'SP43', 'nama' => 'Soelistyowati I, dr, Sp.A', 'id_khanza' => 'D0000021'],
        ['kode' => 'SP44', 'nama' => 'Sofyan Solah, dr, SpOG', 'id_khanza' => 'D0000001'],
        ['kode' => 'OK8', 'nama' => 'Srie Wartono', 'id_khanza' => '1414141'],
        ['kode' => 'OK9', 'nama' => 'Sudrajat, SST', 'id_khanza' => '131313'],
        ['kode' => 'SP45', 'nama' => 'Sukarti, dr, Sp.P', 'id_khanza' => 'D0000046'],
        ['kode' => 'AD4', 'nama' => 'Sumaryono', 'id_khanza' => '406010145'],
        ['kode' => 'SP46', 'nama' => 'Arief Budiman, dr, SpOG', 'id_khanza' => 'D0000103'],
        ['kode' => 'HD17', 'nama' => 'Tarmono', 'id_khanza' => '394020030'],
        ['kode' => 'OK10', 'nama' => 'Suwarno', 'id_khanza' => '151515'],
        ['kode' => 'SP47', 'nama' => 'Syahril Syahsir, dr, Sp B', 'id_khanza' => 'D0000022'],
        ['kode' => 'U21', 'nama' => 'Syamsu Ramadhan, dr', 'id_khanza' => 'D0000007'],
        ['kode' => 'SP48', 'nama' => 'Tiara Annisa Navis, dr,Sp.PD', 'id_khanza' => 'D0000123'],
        ['kode' => 'AD5', 'nama' => 'Tomi', 'id_khanza' => '101010'],
        ['kode' => 'OK11', 'nama' => 'M Tri Muflihin', 'id_khanza' => '1209010171'],
        ['kode' => 'FG4', 'nama' => 'Waryanti', 'id_khanza' => '05084010153'],
        ['kode' => 'U22', 'nama' => 'Widi Prabanta Manungka, dr', 'id_khanza' => 'D0000011'],
        ['kode' => 'U23', 'nama' => 'Widya Emiliana, dr', 'id_khanza' => 'D0000049'],
        ['kode' => 'SP49', 'nama' => 'Yarfiliina, Drg', 'id_khanza' => 'D0000076'],
        ['kode' => 'U24', 'nama' => 'Syahrul Hamidi Nasution, dr, M.Ep', 'id_khanza' => 'D0000094'],
        ['kode' => 'SP50', 'nama' => 'M. Zulkarnain Hussein, dr, SpOG', 'id_khanza' => 'D0000016'],
        ['kode' => 'FG5', 'nama' => 'Rahma Idhanani', 'id_khanza' => '306010608'],
        ['kode' => 'SP52', 'nama' => 'Rahmi Ulfa, dr, Sp.N', 'id_khanza' => 'D0000117'],
        ['kode' => 'U28', 'nama' => 'Tazsya Fatimah Taufik, dr', 'id_khanza' => 'D0000090'],
        ['kode' => 'OK12', 'nama' => 'Verina SDA', 'id_khanza' => '914010246'],
        ['kode' => 'OK13', 'nama' => 'Yudi Efranto', 'id_khanza' => '516010265'],
        ['kode' => 'HD18', 'nama' => 'HD Sayu Putu', 'id_khanza' => '603010118'],
        ['kode' => 'SP53', 'nama' => 'Arief Rohman, dr.Sp.A', 'id_khanza' => 'D0000124'],
        ['kode' => 'SP54', 'nama' => 'Arini Patriharyanti, dr, Sp.KFR', 'id_khanza' => 'D0000119'],
        ['kode' => 'FG6', 'nama' => 'Tiara Feviantiha', 'id_khanza' => '19960223'],
        ['kode' => 'FG7', 'nama' => 'Vega Aurrillia Putri', 'id_khanza' => '224010675'],
        ['kode' => 'FG8', 'nama' => 'Aini Raymentan Bakhri', 'id_khanza' => '1802086108980001'],
        ['kode' => 'FG9', 'nama' => 'Utha Aprisa', 'id_khanza' => '1802054204000003'],
        ['kode' => 'FG10', 'nama' => 'Andri Oktavian', 'id_khanza' => '1802081010970003'],
    ];

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $actionCari = '/jm-asuransi';
        $dokter = $this->cacheService->getDokter();

        $cariNomor = $request->cariNomor;
        $tanggl1 = $request->tgl1 ?? date('Y-m-01');
        $tanggl2 = $request->tgl2 ?? date('Y-m-t');
        $kdDokter = ($request->input('kdDokter')  == null) ? "" : explode(',', $request->input('kdDokter'));
        $kdPenjamin = ($request->input('kdPenjamin') == null) ? "" : explode(',', $request->input('kdPenjamin'));

        // 1. Query rawat_jl_dr (Dokter Saja)
        $queryDr = DB::table('pasien')
        ->select(
            'rawat_jl_dr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(CASE WHEN rawat_jl_dr.kd_dokter IN ('D0000103', 'D0000032') AND jns_perawatan.nm_perawatan LIKE '%USG Kebidanan%' AND jns_perawatan.nm_perawatan NOT LIKE '%(RSBW)%' THEN rawat_jl_dr.tarif_tindakandr * 0.5 ELSE rawat_jl_dr.tarif_tindakandr END) as total_ralan")
        )
        ->join('reg_periksa','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
        ->join('rawat_jl_dr','reg_periksa.no_rawat','=','rawat_jl_dr.no_rawat')
        ->join('dokter','rawat_jl_dr.kd_dokter','=','dokter.kd_dokter')
        ->join('jns_perawatan','rawat_jl_dr.kd_jenis_prw','=','jns_perawatan.kd_jenis_prw')
        ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
        ->join('penjab','reg_periksa.kd_pj','=','penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ( $kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_jl_dr.kd_dokter', $kdDokter);
            }
        })
        ->where(function($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_dr.kd_dokter', 'dokter.nm_dokter');

        // 2. Query rawat_jl_drpr (Dokter & Paramedis) - Ambil tarif dokternya saja
        $queryDrPr = DB::table('pasien')
        ->select(
            'rawat_jl_drpr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(CASE WHEN rawat_jl_drpr.kd_dokter IN ('D0000103', 'D0000032') AND jns_perawatan.nm_perawatan LIKE '%USG Kebidanan%' AND jns_perawatan.nm_perawatan NOT LIKE '%(RSBW)%' THEN rawat_jl_drpr.tarif_tindakandr * 0.5 ELSE rawat_jl_drpr.tarif_tindakandr END) as total_ralan")
        )
        ->join('reg_periksa','reg_periksa.no_rkm_medis','=','pasien.no_rkm_medis')
        ->join('rawat_jl_drpr','reg_periksa.no_rawat','=','rawat_jl_drpr.no_rawat')
        ->join('jns_perawatan','rawat_jl_drpr.kd_jenis_prw','=','jns_perawatan.kd_jenis_prw')
        ->join('dokter','rawat_jl_drpr.kd_dokter','=','dokter.kd_dokter')
        ->join('poliklinik','reg_periksa.kd_poli','=','poliklinik.kd_poli')
        ->join('penjab','reg_periksa.kd_pj','=','penjab.kd_pj')
        ->join('petugas','rawat_jl_drpr.nip','=','petugas.nip')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ( $kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_jl_drpr.kd_dokter', $kdDokter);
            }
        })
        ->where(function($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_drpr.kd_dokter', 'dokter.nm_dokter');

        // 2b. Query periksa_radiologi Ralan (Gabungan)
        $queryRadRalan = DB::table('periksa_radiologi')
        ->select(
            'periksa_radiologi.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(periksa_radiologi.tarif_perujuk + periksa_radiologi.tarif_tindakan_dokter) as total_ralan")
        )
        ->join('reg_periksa', 'periksa_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'periksa_radiologi.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) $query->whereIn('periksa_radiologi.kd_dokter', $kdDokter);
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('periksa_radiologi.kd_dokter', 'dokter.nm_dokter');

        // 2d. Query periksa_lab Lab PA Ralan - JM Perujuk (tarif_perujuk)
        $queryLabPerujukRalan = DB::table('periksa_lab')
        ->select(
            'periksa_lab.dokter_perujuk as kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(periksa_lab.tarif_perujuk) as total_ralan")
        )
        ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'periksa_lab.dokter_perujuk', '=', 'dokter.kd_dokter')
        ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'periksa_lab.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'periksa_lab.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->where('jns_perawatan_lab.kategori', 'PA')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) $query->whereIn('periksa_lab.dokter_perujuk', $kdDokter);
        })
        ->groupBy('periksa_lab.dokter_perujuk', 'dokter.nm_dokter');

        // 2e. Query periksa_lab Lab PA Ralan - JM PJ Lab (tarif_tindakan_dokter)
        $queryLabDokterRalan = DB::table('periksa_lab')
        ->select(
            'periksa_lab.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(periksa_lab.tarif_tindakan_dokter) as total_ralan")
        )
        ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'periksa_lab.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'periksa_lab.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'periksa_lab.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->where('jns_perawatan_lab.kategori', 'PA')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) $query->whereIn('periksa_lab.kd_dokter', $kdDokter);
        })
        ->groupBy('periksa_lab.kd_dokter', 'dokter.nm_dokter');

        // Gabungkan semua query ralan (Union), ambil datanya, dan jumlahkan lagi di level Collection
        $results = $queryDr
            ->unionAll($queryDrPr)
            ->unionAll($queryRadRalan)
            ->unionAll($queryLabPerujukRalan)
            ->unionAll($queryLabDokterRalan)
            ->get();

        $dataRalan = $results->groupBy('kd_dokter')->map(function ($row) {
            return (object) [
                'kd_dokter' => $row->first()->kd_dokter,
                'nm_dokter' => $row->first()->nm_dokter,
                'total_ralan' => $row->sum('total_ralan'),
            ];
        })->values();

        // 3. Query rawat_inap_dr (Ranap Dokter Saja)
        $queryRanapDr = DB::table('pasien')
        ->select(
            'rawat_inap_dr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(CASE WHEN rawat_inap_dr.kd_dokter IN ('D0000103', 'D0000032') AND jns_perawatan_inap.nm_perawatan LIKE '%USG Kebidanan%' AND jns_perawatan_inap.nm_perawatan NOT LIKE '%(RSBW)%' THEN rawat_inap_dr.tarif_tindakandr * 0.5 ELSE rawat_inap_dr.tarif_tindakandr END) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_inap_dr', 'rawat_inap_dr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan_inap', 'rawat_inap_dr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
        ->join('dokter', 'rawat_inap_dr.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_inap_dr.kd_dokter', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_inap_dr.kd_dokter', 'dokter.nm_dokter');

        // 4. Query rawat_inap_drpr (Ranap Dokter & Paramedis) - Ambil tarif dokternya saja
        $queryRanapDrPr = DB::table('pasien')
        ->select(
            'rawat_inap_drpr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(CASE WHEN rawat_inap_drpr.kd_dokter IN ('D0000103', 'D0000032') AND jns_perawatan_inap.nm_perawatan LIKE '%USG Kebidanan%' AND jns_perawatan_inap.nm_perawatan NOT LIKE '%(RSBW)%' THEN rawat_inap_drpr.tarif_tindakandr * 0.5 ELSE rawat_inap_drpr.tarif_tindakandr END) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_inap_drpr', 'rawat_inap_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan_inap', 'rawat_inap_drpr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
        ->join('dokter', 'rawat_inap_drpr.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('petugas', 'rawat_inap_drpr.nip', '=', 'petugas.nip')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_inap_drpr.kd_dokter', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_inap_drpr.kd_dokter', 'dokter.nm_dokter');

        // 5. Query OPERASI (operator1)
        $queryOperasi = DB::table('operasi')
        ->select(
            'operasi.operator1 as kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(operasi.biayaoperator1) as total_ranap")
        )
        ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'operasi.operator1', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('operasi.operator1', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('operasi.operator1', 'dokter.nm_dokter');

        // 5b. Query OPERASI (dokter_anestesi)
        $queryOperasiAnestesi = DB::table('operasi')
        ->select(
            'operasi.dokter_anestesi as kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(operasi.biayadokter_anestesi) as total_ranap")
        )
        ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'operasi.dokter_anestesi', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('operasi.dokter_anestesi', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('operasi.dokter_anestesi', 'dokter.nm_dokter');

        // 5c. Query OPERASI (dokter_anak)
        $queryOperasiAnak = DB::table('operasi')
        ->select(
            'operasi.dokter_anak as kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(operasi.biayadokter_anak) as total_ranap")
        )
        ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'operasi.dokter_anak', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) $query->whereIn('operasi.dokter_anak', $kdDokter);
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('operasi.dokter_anak', 'dokter.nm_dokter');

        // 5d. Query OPERASI (dokter_umum)
        $queryOperasiUmum = DB::table('operasi')
        ->select(
            'operasi.dokter_umum as kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(operasi.biaya_dokter_umum) as total_ranap")
        )
        ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'operasi.dokter_umum', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) $query->whereIn('operasi.dokter_umum', $kdDokter);
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('operasi.dokter_umum', 'dokter.nm_dokter');

        // 6. Query rawat_jl_dr pada pasien Ranap (tindakan ralan pada pasien ranap)
        $queryRanapJlDr = DB::table('pasien')
        ->select(
            'rawat_jl_dr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(rawat_jl_dr.tarif_tindakandr) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_dr', 'reg_periksa.no_rawat', '=', 'rawat_jl_dr.no_rawat')
        ->join('dokter', 'rawat_jl_dr.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('jns_perawatan', 'rawat_jl_dr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_jl_dr.kd_dokter', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_dr.kd_dokter', 'dokter.nm_dokter');

        // 7. Query rawat_jl_drpr pada pasien Ranap (tindakan ralan dr+pr pada pasien ranap)
        $queryRanapJlDrPr = DB::table('pasien')
        ->select(
            'rawat_jl_drpr.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(rawat_jl_drpr.tarif_tindakandr) as total_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_drpr', 'rawat_jl_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('dokter', 'rawat_jl_drpr.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->join('petugas', 'rawat_jl_drpr.nip', '=', 'petugas.nip')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) {
                $query->whereIn('rawat_jl_drpr.kd_dokter', $kdDokter);
            }
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_drpr.kd_dokter', 'dokter.nm_dokter');

        // 8. Query periksa_radiologi Ranap (Gabungan)
        $queryRadiologiRanap = DB::table('periksa_radiologi')
        ->select(
            'periksa_radiologi.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(periksa_radiologi.tarif_perujuk + periksa_radiologi.tarif_tindakan_dokter) as total_ranap")
        )
        ->join('reg_periksa', 'periksa_radiologi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'periksa_radiologi.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) $query->whereIn('periksa_radiologi.kd_dokter', $kdDokter);
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('periksa_radiologi.kd_dokter', 'dokter.nm_dokter');

        // Gabungkan semua query ranap (Union), ambil datanya, dan jumlahkan lagi di level Collection
        $queryRanap = $queryRanapDr
            ->unionAll($queryRanapDrPr)
            ->unionAll($queryOperasi)
            ->unionAll($queryOperasiAnestesi)
            ->unionAll($queryOperasiAnak)
            ->unionAll($queryOperasiUmum)
            ->unionAll($queryRanapJlDr)
            ->unionAll($queryRanapJlDrPr)
            ->unionAll($queryRadiologiRanap);

        // 10. Query periksa_lab Lab PA Ranap - JM Perujuk
        $queryLabPerujukRanap = DB::table('periksa_lab')
        ->select(
            'periksa_lab.dokter_perujuk as kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(periksa_lab.tarif_perujuk) as total_ranap")
        )
        ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'periksa_lab.dokter_perujuk', '=', 'dokter.kd_dokter')
        ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'periksa_lab.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'periksa_lab.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where('jns_perawatan_lab.kategori', 'PA')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) $query->whereIn('periksa_lab.dokter_perujuk', $kdDokter);
        })
        ->groupBy('periksa_lab.dokter_perujuk', 'dokter.nm_dokter');

        // 11. Query periksa_lab Lab PA Ranap - JM PJ Lab
        $queryLabDokterRanap = DB::table('periksa_lab')
        ->select(
            'periksa_lab.kd_dokter',
            'dokter.nm_dokter',
            DB::raw("SUM(periksa_lab.tarif_tindakan_dokter) as total_ranap")
        )
        ->join('reg_periksa', 'periksa_lab.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('dokter', 'periksa_lab.kd_dokter', '=', 'dokter.kd_dokter')
        ->join('jns_perawatan_lab', 'periksa_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'periksa_lab.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'periksa_lab.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where('jns_perawatan_lab.kategori', 'PA')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function ($query) use ($kdDokter) {
            if ($kdDokter) $query->whereIn('periksa_lab.kd_dokter', $kdDokter);
        })
        ->groupBy('periksa_lab.kd_dokter', 'dokter.nm_dokter');

        $resultsRanap = $queryRanap
            ->unionAll($queryLabPerujukRanap)
            ->unionAll($queryLabDokterRanap)
            ->get();

        $dataRanap = $resultsRanap->groupBy('kd_dokter')->map(function ($row) {
            return (object) [
                'kd_dokter' => $row->first()->kd_dokter,
                'nm_dokter' => $row->first()->nm_dokter,
                'total_ranap' => $row->sum('total_ranap'),
            ];
        })->values();

        // Gabungkan ralan + ranap ke satu collection berdasarkan kd_dokter
        $allDokterKeys = $dataRalan->pluck('kd_dokter')
            ->merge($dataRanap->pluck('kd_dokter'))
            ->unique();

        $dataCombined = $allDokterKeys->map(function ($kd) use ($dataRalan, $dataRanap) {
            $ralan  = $dataRalan->firstWhere('kd_dokter', $kd);
            $ranap  = $dataRanap->firstWhere('kd_dokter', $kd);

            $totalRalan = $ralan->total_ralan ?? 0;
            $totalRanap = $ranap->total_ranap ?? 0;
            $nmDokter   = $ralan->nm_dokter ?? $ranap->nm_dokter ?? '-';

            return (object) [
                'kd_dokter'   => $kd,
                'nm_dokter'   => $nmDokter,
                'total_ranap' => $totalRanap,
                'total_ralan' => $totalRalan,
                'total_igd'   => 0,
                'grand_total' => $totalRanap + $totalRalan,
            ];
        })->sortByDesc('grand_total')->values();

        // ========================================
        // PARAMEDIS (Fisioterapis saja)
        // ========================================

        // P1. rawat_jl_pr Ralan (Paramedis Ralan)
        $queryPrRalan = DB::table('pasien')
        ->select(
            'rawat_jl_pr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(CASE WHEN jns_perawatan.nm_perawatan NOT LIKE '%jasa operator hd%' THEN rawat_jl_pr.tarif_tindakanpr ELSE 0 END) as total_ralan"),
            DB::raw("COUNT(DISTINCT CONCAT(rawat_jl_pr.no_rawat, rawat_jl_pr.kd_jenis_prw, rawat_jl_pr.jam_rawat)) as jml_tindakan"),
            DB::raw("COUNT(DISTINCT CASE WHEN jns_perawatan.nm_perawatan LIKE '%jasa operator hd%' THEN CONCAT(rawat_jl_pr.no_rawat, rawat_jl_pr.kd_jenis_prw, rawat_jl_pr.jam_rawat) END) as jml_tindakan_hd_ralan"),
            DB::raw("0 as jml_tindakan_hd_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_pr', 'rawat_jl_pr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan', 'rawat_jl_pr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('petugas', 'rawat_jl_pr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function($q) {
            $q->where(function($q2) {
                $q2->where('petugas.nama', '!=', 'Dahyar');
            })->orWhere('jns_perawatan.nm_perawatan', 'like', '%jasa operator hd%');
        })
        // ... (rest of where filters)
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_pr.nip', 'petugas.nama');

        // P1b. rawat_jl_drpr Ralan - tarif paramedis (Ralan DrPr paramedis fee)
        $queryPrRalanDrPr = DB::table('pasien')
        ->select(
            'rawat_jl_drpr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(CASE WHEN jns_perawatan.nm_perawatan NOT LIKE '%jasa operator hd%' THEN rawat_jl_drpr.tarif_tindakanpr ELSE 0 END) as total_ralan"),
            DB::raw("COUNT(DISTINCT CONCAT(rawat_jl_drpr.no_rawat, rawat_jl_drpr.kd_jenis_prw, rawat_jl_drpr.jam_rawat)) as jml_tindakan"),
            DB::raw("COUNT(DISTINCT CASE WHEN jns_perawatan.nm_perawatan LIKE '%jasa operator hd%' THEN CONCAT(rawat_jl_drpr.no_rawat, rawat_jl_drpr.kd_jenis_prw, rawat_jl_drpr.jam_rawat) END) as jml_tindakan_hd_ralan"),
            DB::raw("0 as jml_tindakan_hd_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_drpr', 'rawat_jl_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('petugas', 'rawat_jl_drpr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function($q) {
            $q->where(function($q2) {
                $q2->where('petugas.nama', '!=', 'Dahyar');
            })->orWhere('jns_perawatan.nm_perawatan', 'like', '%jasa operator hd%');
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_drpr.nip', 'petugas.nama');

        // P2. rawat_jl_pr Ranap (tindakan ralan pada pasien ranap)
        $queryPrRanapJl = DB::table('pasien')
        ->select(
            'rawat_jl_pr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(CASE WHEN jns_perawatan.nm_perawatan NOT LIKE '%jasa operator hd%' THEN rawat_jl_pr.tarif_tindakanpr ELSE 0 END) as total_ranap"),
            DB::raw("COUNT(DISTINCT CONCAT(rawat_jl_pr.no_rawat, rawat_jl_pr.kd_jenis_prw, rawat_jl_pr.jam_rawat)) as jml_tindakan"),
            DB::raw("0 as jml_tindakan_hd_ralan"),
            DB::raw("COUNT(DISTINCT CASE WHEN jns_perawatan.nm_perawatan LIKE '%jasa operator hd%' THEN CONCAT(rawat_jl_pr.no_rawat, rawat_jl_pr.kd_jenis_prw, rawat_jl_pr.jam_rawat) END) as jml_tindakan_hd_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_pr', 'rawat_jl_pr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan', 'rawat_jl_pr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('petugas', 'rawat_jl_pr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function($q) {
            $q->where(function($q2) {
                $q2->where('petugas.nama', '!=', 'Dahyar');
            })->orWhere('jns_perawatan.nm_perawatan', 'like', '%jasa operator hd%');
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_pr.nip', 'petugas.nama');

        // P2b. rawat_jl_drpr Ranap - tarif paramedis (tindakan dr+pr ralan pada pasien ranap)
        $queryPrRanapJlDrPr = DB::table('pasien')
        ->select(
            'rawat_jl_drpr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(CASE WHEN jns_perawatan.nm_perawatan NOT LIKE '%jasa operator hd%' THEN rawat_jl_drpr.tarif_tindakanpr ELSE 0 END) as total_ranap"),
            DB::raw("COUNT(DISTINCT CONCAT(rawat_jl_drpr.no_rawat, rawat_jl_drpr.kd_jenis_prw, rawat_jl_drpr.jam_rawat)) as jml_tindakan"),
            DB::raw("0 as jml_tindakan_hd_ralan"),
            DB::raw("COUNT(DISTINCT CASE WHEN jns_perawatan.nm_perawatan LIKE '%jasa operator hd%' THEN CONCAT(rawat_jl_drpr.no_rawat, rawat_jl_drpr.kd_jenis_prw, rawat_jl_drpr.jam_rawat) END) as jml_tindakan_hd_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_jl_drpr', 'rawat_jl_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan', 'rawat_jl_drpr.kd_jenis_prw', '=', 'jns_perawatan.kd_jenis_prw')
        ->join('petugas', 'rawat_jl_drpr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function($q) {
            $q->where(function($q2) {
                $q2->where('petugas.nama', '!=', 'Dahyar');
            })->orWhere('jns_perawatan.nm_perawatan', 'like', '%jasa operator hd%');
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_jl_drpr.nip', 'petugas.nama');

        // P3. rawat_inap_pr (Ranap Paramedis)
        $queryPrRanap = DB::table('pasien')
        ->select(
            'rawat_inap_pr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(CASE WHEN jns_perawatan_inap.nm_perawatan NOT LIKE '%jasa operator hd%' THEN rawat_inap_pr.tarif_tindakanpr ELSE 0 END) as total_ranap"),
            DB::raw("COUNT(DISTINCT CONCAT(rawat_inap_pr.no_rawat, rawat_inap_pr.kd_jenis_prw, rawat_inap_pr.jam_rawat)) as jml_tindakan"),
            DB::raw("0 as jml_tindakan_hd_ralan"),
            DB::raw("COUNT(DISTINCT CASE WHEN jns_perawatan_inap.nm_perawatan LIKE '%jasa operator hd%' THEN CONCAT(rawat_inap_pr.no_rawat, rawat_inap_pr.kd_jenis_prw, rawat_inap_pr.jam_rawat) END) as jml_tindakan_hd_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_inap_pr', 'rawat_inap_pr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan_inap', 'rawat_inap_pr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
        ->join('petugas', 'rawat_inap_pr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function($q) {
            $q->where(function($q2) {
                $q2->where('petugas.nama', '!=', 'Dahyar');
            })->orWhere('jns_perawatan_inap.nm_perawatan', 'like', '%jasa operator hd%');
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_inap_pr.nip', 'petugas.nama');

        // P4. rawat_inap_drpr - tarif paramedis (Ranap DrPr paramedis fee)
        $queryPrRanapDrPr = DB::table('pasien')
        ->select(
            'rawat_inap_drpr.nip as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(CASE WHEN jns_perawatan_inap.nm_perawatan NOT LIKE '%jasa operator hd%' THEN rawat_inap_drpr.tarif_tindakanpr ELSE 0 END) as total_ranap"),
            DB::raw("COUNT(DISTINCT CONCAT(rawat_inap_drpr.no_rawat, rawat_inap_drpr.kd_jenis_prw, rawat_inap_drpr.jam_rawat)) as jml_tindakan"),
            DB::raw("0 as jml_tindakan_hd_ralan"),
            DB::raw("COUNT(DISTINCT CASE WHEN jns_perawatan_inap.nm_perawatan LIKE '%jasa operator hd%' THEN CONCAT(rawat_inap_drpr.no_rawat, rawat_inap_drpr.kd_jenis_prw, rawat_inap_drpr.jam_rawat) END) as jml_tindakan_hd_ranap")
        )
        ->join('reg_periksa', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('rawat_inap_drpr', 'rawat_inap_drpr.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('jns_perawatan_inap', 'rawat_inap_drpr.kd_jenis_prw', '=', 'jns_perawatan_inap.kd_jenis_prw')
        ->join('petugas', 'rawat_inap_drpr.nip', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where(function($q) {
            $q->where(function($q2) {
                $q2->where('petugas.nama', '!=', 'Dahyar');
            })->orWhere('jns_perawatan_inap.nm_perawatan', 'like', '%jasa operator hd%');
        })
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('rawat_inap_drpr.nip', 'petugas.nama');

        // P5. Operasi - Asisten Operator 1
        $queryPrOkAsistenOp1 = DB::table('operasi')
        ->select(
            'operasi.asisten_operator1 as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(operasi.biayaasisten_operator1) as total_ranap"),
            DB::raw("COUNT(DISTINCT operasi.no_rawat) as jml_tindakan"),
            DB::raw("0 as jml_tindakan_hd_ralan"),
            DB::raw("0 as jml_tindakan_hd_ranap")
        )
        ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('petugas', 'operasi.asisten_operator1', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where('petugas.nama', '!=', 'Dahyar')
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('operasi.asisten_operator1', 'petugas.nama');

        // P6. Operasi - Asisten Anestesi
        $queryPrOkAsistenAnestesi = DB::table('operasi')
        ->select(
            'operasi.asisten_anestesi as kd_petugas',
            'petugas.nama as nm_petugas',
            DB::raw("SUM(operasi.biayaasisten_anestesi) as total_ranap"),
            DB::raw("COUNT(DISTINCT operasi.no_rawat) as jml_tindakan"),
            DB::raw("0 as jml_tindakan_hd_ralan"),
            DB::raw("0 as jml_tindakan_hd_ranap")
        )
        ->join('reg_periksa', 'operasi.no_rawat', '=', 'reg_periksa.no_rawat')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('petugas', 'operasi.asisten_anestesi', '=', 'petugas.nip')
        ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        ->leftJoin('bayar_piutang', 'reg_periksa.no_rawat', '=', 'bayar_piutang.no_rawat')
        ->leftJoin('piutang_pasien', 'piutang_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
        ->where(function ($query) use ($kdPenjamin, $tanggl1, $tanggl2) {
            if ($kdPenjamin) {
                $query->whereIn('penjab.kd_pj', $kdPenjamin);
            } else {
                $query->whereNotIn('penjab.kd_pj', ['UMU', 'BPJ', 'A09'])
                      ->where('penjab.png_jawab', 'not like', '%COB%');
            }
            $query->whereBetween('bayar_piutang.tgl_bayar', [$tanggl1, $tanggl2])
                  ->where('piutang_pasien.status', 'Lunas');
        })
        ->where('petugas.nama', '!=', 'Dahyar')
        ->where(function ($query) use ($cariNomor) {
            if ($cariNomor) {
                $query->where(function ($q) use ($cariNomor) {
                    $q->orWhere('reg_periksa.no_rawat', 'like', '%' . $cariNomor . '%')
                      ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $cariNomor . '%')
                      ->orWhere('pasien.nm_pasien', 'like', '%' . $cariNomor . '%');
                });
            }
        })
        ->groupBy('operasi.asisten_anestesi', 'petugas.nama');

        // Gabungkan ralan paramedis (P1 + P1b)
        $resultsPrRalan = $queryPrRalan
            ->unionAll($queryPrRalanDrPr)
            ->get();

        $dataPrRalan = $resultsPrRalan->groupBy('kd_petugas')->map(function ($row) {
            return (object) [
                'kd_petugas' => $row->first()->kd_petugas,
                'nm_petugas' => $row->first()->nm_petugas,
                'total_ralan' => $row->sum('total_ralan'),
                'jml_tindakan' => $row->sum('jml_tindakan'),
                'jml_tindakan_hd_ralan' => $row->sum('jml_tindakan_hd_ralan'),
                'jml_tindakan_hd_ranap' => $row->sum('jml_tindakan_hd_ranap'),
            ];
        })->values();

        // Gabungkan ranap paramedis (P2 + P3 + P4 + P5 + P6)
        $resultsPrRanap = $queryPrRanapJl
            ->unionAll($queryPrRanap)
            ->unionAll($queryPrRanapDrPr)
            ->unionAll($queryPrOkAsistenOp1)
            ->unionAll($queryPrOkAsistenAnestesi)
            ->get();

        $dataPrRanap = $resultsPrRanap->groupBy('kd_petugas')->map(function ($row) {
            return (object) [
                'kd_petugas' => $row->first()->kd_petugas,
                'nm_petugas' => $row->first()->nm_petugas,
                'total_ranap' => $row->sum('total_ranap'),
                'jml_tindakan' => $row->sum('jml_tindakan'),
                'jml_tindakan_hd_ralan' => $row->sum('jml_tindakan_hd_ralan'),
                'jml_tindakan_hd_ranap' => $row->sum('jml_tindakan_hd_ranap'),
            ];
        })->values();

        // Gabungkan ralan + ranap paramedis
        $allPetugasKeys = $dataPrRalan->pluck('kd_petugas')
            ->merge($dataPrRanap->pluck('kd_petugas'))
            ->unique();

        $dataParamedis = $allPetugasKeys->map(function ($nip) use ($dataPrRalan, $dataPrRanap) {
            $ralan = $dataPrRalan->firstWhere('kd_petugas', $nip);
            $ranap = $dataPrRanap->firstWhere('kd_petugas', $nip);
 
            $totalRalan = $ralan->total_ralan ?? 0;
            $totalRanap = $ranap->total_ranap ?? 0;
            $jmlTindakan = ($ralan->jml_tindakan ?? 0) + ($ranap->jml_tindakan ?? 0);
            $jmlTindakanHdRalan = ($ralan->jml_tindakan_hd_ralan ?? 0) + ($ranap->jml_tindakan_hd_ralan ?? 0);
            $jmlTindakanHdRanap = ($ralan->jml_tindakan_hd_ranap ?? 0) + ($ranap->jml_tindakan_hd_ranap ?? 0);
            $nmPetugas  = $ralan->nm_petugas ?? $ranap->nm_petugas ?? '-';
 
            return (object) [
                'kd_dokter'   => $nip,
                'nm_dokter'   => $nmPetugas,
                'total_ranap' => $totalRanap,
                'total_ralan' => $totalRalan,
                'total_igd'   => 0,
                'jml_tindakan' => $jmlTindakan,
                'jml_tindakan_hd_ralan' => $jmlTindakanHdRalan,
                'jml_tindakan_hd_ranap' => $jmlTindakanHdRanap,
                'jml_tindakan_hd' => $jmlTindakanHdRalan + $jmlTindakanHdRanap,
                'grand_total' => $totalRanap + $totalRalan,
            ];
        })->sortByDesc('grand_total')->values();

        // ========================================
        // PROSES MAPPING DATA KE TEMPLATE JM UMUM
        // ========================================
        
        // Gabungkan seluruh data ralan + ranap (Dokter & Paramedis)
        $semuaData = collect($dataCombined)->merge($dataParamedis);

        // Ambil Data Nama Asli dari Database Khanza secara LIVE (untuk menghindari nama hard-code)
        $idKhanzas = collect($this->templateJM)->pluck('id_khanza')->filter()->toArray();
        $pegawais = DB::table('pegawai')->whereIn('nik', $idKhanzas)->pluck('nama', 'nik');
        $dokters = DB::table('dokter')->whereIn('kd_dokter', $idKhanzas)->pluck('nm_dokter', 'kd_dokter');
        $petugass = DB::table('petugas')->whereIn('nip', $idKhanzas)->pluck('nama', 'nip');

        // Petakan ke array template yang telah didefinisikan (dari gambar)
        $mappedTemplate = collect($this->templateJM)->map(function($tpl) use ($semuaData, $pegawais, $dokters, $petugass) {
            $matched = null;
            $id = trim($tpl['id_khanza']);
            
            // 1. Coba cocokkan dengan ID Khanza jika ada di array template
            if (!empty($id)) {
                $matched = $semuaData->firstWhere('kd_dokter', $id);
            } 
            
            // 2. Tentukan Nama asli dari database
            $namaDb = $tpl['nama']; // Fallback menggunakan nama hardcode di atas
            if (!empty($id)) {
                if (isset($pegawais[$id])) {
                    $namaDb = $pegawais[$id];
                } elseif (isset($dokters[$id])) {
                    $namaDb = $dokters[$id];
                } elseif (isset($petugass[$id])) {
                    $namaDb = $petugass[$id];
                }
            }

            return (object) [
                'kode_template'  => $tpl['kode'],
                'nama_template'  => $namaDb, // Nama Live dari Pegawai/Dokter DB
                'kode_id_khanza' => $id, 
                'nama_khanza'    => $matched ? $matched->nm_dokter : $namaDb,
                'total_ranap'    => $matched ? $matched->total_ranap : 0,
                'total_ralan'    => $matched ? $matched->total_ralan : 0,
                'total_igd'      => $matched ? $matched->total_igd : 0,
                'jml_tindakan'   => $matched ? ($matched->jml_tindakan ?? 0) : 0,
                'jml_tindakan_hd' => $matched ? ($matched->jml_tindakan_hd ?? 0) : 0,
                'jml_tindakan_hd_ralan' => $matched ? ($matched->jml_tindakan_hd_ralan ?? 0) : 0,
                'jml_tindakan_hd_ranap' => $matched ? ($matched->jml_tindakan_hd_ranap ?? 0) : 0,
                'grand_total'    => $matched ? $matched->grand_total : 0,
            ];
        });

        // -------------------------------------------------------------
        // PEMBAGIAN KHUSUS JASA OPERATOR HD (PARAMEDIS 09964020055)
        // -------------------------------------------------------------
        $kodeKus = 'HD5'; // Kode HD Kus / id_khanza 09964020055
        $hdKus = $mappedTemplate->firstWhere('kode_template', $kodeKus);
        
        if ($hdKus && ($hdKus->jml_tindakan_hd > 0)) {
            $multiplier = $hdKus->jml_tindakan_hd; 
            
            // Hitung rasio ralan dan ranap dari JUMLAH TINDAKAN HD-nya
            // (Karena nominal nominal asuransi sudah di-filter/nol-can agar tidak masuk hitungan main report)
            $totalHdActions = $hdKus->jml_tindakan_hd_ralan + $hdKus->jml_tindakan_hd_ranap;
            $ratioRalan = $totalHdActions > 0 ? ($hdKus->jml_tindakan_hd_ralan / $totalHdActions) : 1;
            $ratioRanap = $totalHdActions > 0 ? ($hdKus->jml_tindakan_hd_ranap / $totalHdActions) : 0;

            // Aturan pembagian (dalam proporsi / per 1 tindakan nilai 50.500)
            $pembagianHD = [
                'HD5'  => 10000, // HD Kus
                'HD8'  => 8000,  // HD Mala
                'HD11' => 6000,  // HD Ria
                'HD3'  => 4000,  // HD Danu
                'HD12' => 4000,  // HD Ronal
                'HD14' => 4000,  // HD Sumo
                'HD13' => 4000,  // HD Sabtina
                'HD15' => 2000,  // HD Sutriyanti
                'HD6'  => 2000,  // HD Lili
                'HD16' => 2000,  // HD Vina
                'HD18' => 2000,  // HD Sayu Putu
                'HD9'  => 500,   // HD Ade Supriatna
                'HD10' => 2000,  // HD Yopi
            ];

            // Reset saldo Kus (akan diisi kembali sesuai properti HD5 di array distribusi)
            $hdKus->total_ralan = 0;
            $hdKus->total_ranap = 0;
            $hdKus->grand_total = 0;
            
            // Iterasi dan distribusikan ke tim
            foreach ($pembagianHD as $kodeTarget => $nilai) {
                // Menemukan object petugas berdasarkan kodenya (SP, HD, dst)
                $targetPetugas = $mappedTemplate->firstWhere('kode_template', $kodeTarget);
                if ($targetPetugas) {
                    $tambahan = $nilai * $multiplier;
                    $tambahanRalan = $tambahan * $ratioRalan;
                    $tambahanRanap = $tambahan * $ratioRanap;

                    $targetPetugas->total_ralan += $tambahanRalan;
                    $targetPetugas->total_ranap += $tambahanRanap;
                    $targetPetugas->grand_total += $tambahan;
                }
            }
        }

        // 3. Cari Data Dokter/Paramedis yang ada Jasanya, tetapi BELUM MASUK list Template di atas
        // (Sebagai daftar pengecekan di bawah tabel utama, biar ga miss)
        $unmatchedData = $semuaData->filter(function($item) use ($mappedTemplate) {
            // cek apa kd_dokter sudah masuk ke baris kode_id_khanza pada table mapping di atas
            return !$mappedTemplate->contains('kode_id_khanza', $item->kd_dokter);
        })->map(function($item) {
            return (object) [
                'kode_template'  => '-',
                'nama_template'  => 'TIDAK TERDAFTAR DI TEMPLATE',
                'kode_id_khanza' => $item->kd_dokter,
                'nama_khanza'    => $item->nm_dokter,
                'total_ranap'    => $item->total_ranap,
                'total_ralan'    => $item->total_ralan,
                'total_igd'      => $item->total_igd,
                'grand_total'    => $item->grand_total,
            ];
        })->values();

        return view('detail-tindakan-umum.jm-asuransi', [
            'actionCari'=> $actionCari,
            'dokter'=> $dokter,
            'mappedTemplate' => $mappedTemplate,
            'unmatchedData'  => $unmatchedData,
            'tanggl1' => $tanggl1,
            'tanggl2' => $tanggl2
        ]);
    }
}
