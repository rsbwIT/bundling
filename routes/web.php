<?php

use App\Http\Controllers\RM\Borlos;
use App\Http\Controllers\RM\BerkasRM;
use App\Http\Controllers\RM\PasienRawatJalan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Bpjs\DataInacbg;
use App\Http\Controllers\Bpjs\HomeCasemix;
use App\Http\Controllers\Bpjs\SettingBpjs;
use App\Http\Controllers\Bpjs\GabungBerkas;
use App\Http\Controllers\Laporan\BayarUmum;
use App\Http\Controllers\Laporan\CobHarian;
use App\Http\Controllers\Bpjs\BpjsController;
use App\Http\Controllers\InfoKamar\InfoKamar;
use App\Http\Controllers\Test\TestController;
use App\Http\Controllers\Bpjs\ListPasienRalan;
use App\Http\Controllers\Bpjs\ListPasienRanap;
use App\Http\Controllers\Laporan\BayarPiutang;
use App\Http\Controllers\Laporan\PiutangRalan;
use App\Http\Controllers\Laporan\PiutangRanap;
use App\Http\Controllers\Bpjs\CesmikController;
use App\Http\Controllers\Bpjs\ListPasienRalan2;
use App\Http\Controllers\Regperiksa\Listpasien;
use App\Http\Controllers\Lab\BridgingalatlatLis;
use App\Http\Controllers\AntrianPoli\AntrianPoli;
use App\Http\Controllers\Farmasi\BundlingFarmasi;
use App\Http\Controllers\Laporan\CobBayarPiutang;
use App\Http\Controllers\Laporan\InvoiceAsuransi;
use App\Http\Controllers\Laporan\PembayaranRalan;
use App\Http\Controllers\BriggingBpjs\KirimTaskId;
use App\Http\Controllers\Laporan\PasienController;
use App\Http\Controllers\AntrianPoli\BwJadwaldokter;
use App\Http\Controllers\Bpjs\PrintCesmikController;
use App\Http\Controllers\DetailTindakan\RalanDokter;
use App\Http\Controllers\DetailTindakanBulanan\RalanDokter2;
use App\Http\Controllers\DetailTindakan\RanapDokter;
use App\Http\Controllers\DetailTindakanBulanan\RanapDokter4;
use App\Http\Controllers\Farmasi\BundlingResepobat2;
use App\Http\Controllers\Farmasi\SepResepController;
use App\Http\Controllers\Keperawatan\LaporanLogBook;
use App\Http\Controllers\Laporan\BayarPiutangKhanza;
use App\Http\Controllers\Regperiksa\AnjunganMandiri;
use App\Http\Controllers\DetailTindakan\OperasiAndVK;
use App\Http\Controllers\DetailTindakanBulanan\OperasiAndVK1;
use App\Http\Controllers\DetailTindakan\RanapDokter2;
use App\Http\Controllers\DetailTindakan\RanapDokter3;
use App\Http\Controllers\Keperawatan\HomeKeperawatan;
use App\Http\Controllers\Keperawatan\LaporanLogBook2;
use App\Http\Controllers\Laporan\BayarPiutangKaryawan;
use App\Http\Controllers\DetailTindakan\RalanParamedis;
use App\Http\Controllers\DetailTindakanBulanan\RalanParamedis2;
use App\Http\Controllers\DetailTindakan\RanapParamedis;
use App\Http\Controllers\DetailTindakanBulanan\RanapParamedis2;
use App\Http\Controllers\Farmasi\MinimalStokController;
use App\Http\Controllers\Keperawatan\LaporanLogbokKaru;
use App\Http\Controllers\Returobat\ReturObatController;
use App\Http\Controllers\Farmasi\ViewSepResepController;
use App\Http\Controllers\DetailTindakan\PeriksaRadiologi;
use App\Http\Controllers\DetailTindakanBulanan\PeriksaRadiologi2;
use App\Http\Controllers\Farmasi\ViewSepResepController2;
use App\Http\Controllers\Keperawatan\PengawasKeperawatan;
use App\Http\Controllers\DetailTindakanUmum\RalanDokterUm;
use App\Http\Controllers\DetailTindakanUmum\RanapDokterUm;
use App\Http\Controllers\DetailTindakanUmum\OperasiAndVKUm;
use App\Http\Controllers\DetailTindakan\RalanDokterParamedis;
use App\Http\Controllers\DetailTindakanBulanan\RalanDokterParamedis2;
use App\Http\Controllers\DetailTindakan\RanapDokterParamedis;
use App\Http\Controllers\DetailTindakanBulanan\RanapDokterParamedis2;
use App\Http\Controllers\DetailTindakanUmum\RalanParamedisUm;
use App\Http\Controllers\DetailTindakanUmum\RanapParamedisUm;
use App\Http\Controllers\AntrianPendaftaran\AntrianPendaftaran;
use App\Http\Controllers\DetailTindakanUmum\PeriksaRadiologiUm;
use App\Http\Controllers\DetailTindakanUmum\RalanDokterParamedisUm;
use App\Http\Controllers\DetailTindakanUmum\RanapDokterParamedisUm;
use App\Http\Controllers\RM\KunjunganRalan;
use App\Http\Controllers\RM\PasienPulangRanap;
use App\Http\Controllers\RM\StatusDataRm;
use App\Http\Controllers\RM\JumlahPasien;
use App\Http\Controllers\RM\PasienPerEpisode;
use App\Http\Controllers\RM\PasienRanapIgd;
use App\Http\Controllers\RM\PasienMeninggal;
use App\Http\Controllers\RM\TabulasiIGD;
// use App\Http\Controllers\AntrianFarmasi\AntrianFarmasiController;
use App\Http\Controllers\AntrianFarmasi\DisplayController;
use App\Http\Controllers\AntrianFarmasi\AntrianFarmasiController;
use App\Http\Controllers\AntrianFarmasi\PanggilanAntrianController;
use App\Http\Controllers\AntrianFarmasi\PanggilPasien;
use App\Http\Controllers\AntrianFarmasi\AntrianFarmasi1;
use App\Http\Controllers\AI\ChatController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\AI\AIChat;
use App\Http\Controllers\AI\AIChatController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/update', [AuthController::class, 'Maintance']);
Route::group(['middleware' => 'default'], function () {
    Route::get('/login', [AuthController::class, 'Login'])->name('login');
    Route::post('/mesinlogin', [AuthController::class, 'mesinLogin']);

    Route::group(['middleware' => 'auth-rsbw'], function () {
        Route::get('/test', [TestController::class, 'Test']);
        Route::get('/test-delte', [TestController::class, 'TestDelete']);
        Route::get('/test-cari', [TestController::class, 'TestCari']);
        Route::get('/logout', [AuthController::class, 'Logout'])->name('logout');
        Route::get('/laporan-pasien', [PasienController::class, 'Pasien']);

        // LIST PASIEN
        Route::get('/', [Listpasien::class, 'Listpasien']);

        // OBAT
        Route::get('/returObat', [ReturObatController::class, 'Obat'])->middleware('permision-rsbw:penyakit');
        Route::get('/cariNorm', [ReturObatController::class, 'Obat']);
        Route::get('/print/{id}', [ReturObatController::class, 'Print']);

        // CASEMIX
        Route::get('/list-pasein-ralan', [ListPasienRalan::class, 'lisPaseinRalan']);
        Route::get('/cari-list-pasein-ralan', [ListPasienRalan::class, 'cariListPaseinRalan']);
        Route::get('/list-pasein-ralan2', [ListPasienRalan2::class, 'lisPaseinRalan2']);
        Route::get('/list-pasein-ranap', [ListPasienRanap::class, 'lisPaseinRanap']);
        Route::get('/cari-list-pasein-ranap', [ListPasienRanap::class, 'cariListPaseinRanap']);
        Route::get('/casemix-home', [HomeCasemix::class, 'casemixHome']);
        Route::get('/casemix-home-cari', [HomeCasemix::class, 'casemixHomeCari']);
        Route::get('/cariNorawat-ClaimBpjs', [BpjsController::class, 'claimBpjs']);
        Route::post('/upload-berkas', [BpjsController::class, 'inputClaimBpjs']);
        Route::get('/carinorawat-casemix', [CesmikController::class, 'Casemix']);
        Route::get('/print-casemix', [PrintCesmikController::class, 'printCasemix']);
        Route::get('/gabung-berkas-casemix', [GabungBerkas::class, 'gabungBerkas']);
        Route::get('/data-inacbg', [DataInacbg::class, 'Inacbg']);
        Route::get('/setting-bpjs-casemix', [SettingBpjs::class, 'settingBpjsCasemix']);
        Route::get('/croscheck-coding', [HomeCasemix::class, 'crosCheckCoding']);

        // FARMASI
        Route::get('/list-pasien-farmasi', [SepResepController::class, 'ListPasienFarmasi']);
        Route::get('/cari-list-pasien-farmasi', [SepResepController::class, 'CariListPasienFarmasi']);
        Route::get('/view-sep-resep', [ViewSepResepController::class, 'ViewBerkasSepResep']);
        Route::post('/upload-berkas-farmasi', [ViewSepResepController::class, 'UploadBerkasFarmasi']);
        Route::get('/download-sepresep-farmasi', [ViewSepResepController::class, 'DonwloadSEPResep']);
        Route::get('/download-hasilgabungberks', [ViewSepResepController::class, 'DonwloadHasilGabung']);
        Route::get('/print-sep-resep', [BundlingFarmasi::class, 'PrintBerkasSepResep']);
        Route::get('/gabung-berkas-farmasi', [BundlingFarmasi::class, 'GabungBergkas']);
        Route::get('/minimal-stok-obat', [MinimalStokController::class, 'MinimalStokObat']);
        Route::get('/list-pasien-farmasi2', [BundlingResepobat2::class, 'Listpasien2']);
        Route::get('/view-sep-resep2', [ViewSepResepController2::class, 'ViewSepResepController2']);

        // AIChat
        Route::get('/chat', [ChatController::class, 'index']);
        Route::post('/chat', [ChatController::class, 'send']);


        // LAPORAN / KEUANGAN
        Route::get('/pembayaran-ralan', [PembayaranRalan::class, 'PembayaranRanal']);
        Route::get('/cari-pembayaran-ralan', [PembayaranRalan::class, 'CariPembayaranRanal']);
        Route::get('/cari-piutang-ralan', [PiutangRalan::class, 'CariPiutangRalan']);
        Route::get('/cari-piutang-ranap', [PiutangRanap::class, 'CariPiutangRanap']);
        Route::get('/cari-bayar-piutang', [BayarPiutang::class, 'CariBayarPiutang']);
        Route::get('/bayar-piutang-khanza', [BayarPiutangKhanza::class, 'BayarPiutangKhanza']);
        Route::get('/bayar-piutang-karyawan', [BayarPiutangKaryawan::class, 'bayarPiutangKaryawan']);
        Route::get('/cari-cob-bayar-piutang', [CobBayarPiutang::class, 'CobBayarPiutang']);
        Route::get('/cari-bayar-umum', [BayarUmum::class, 'CariBayarUmum']);
        Route::get('/invoice-asuransi', [InvoiceAsuransi::class, 'InvoiceAsuransi']);
        Route::get('/simpan-invoice-asuransi', [InvoiceAsuransi::class, 'simpanNomor']);
        Route::get('/cetak-invoice-asuransi/{nomor_tagihan}/{template}', [InvoiceAsuransi::class, 'cetakInvoice']);
        Route::get('/cob-harian', [CobHarian::class, 'CobHarian']);

        // DETAIL TINDAKAN Asuransi
        Route::get('/ralan-dokter', [RalanDokter::class, 'RalanDokter']);
        Route::get('/ralan-dokter2', [RalanDokter2::class, 'RalanDokter2']);
        Route::get('/ralan-paramedis', [RalanParamedis::class, 'RalanParamedis']);
        Route::get('/ralan-paramedis2', [RalanParamedis2::class, 'RalanParamedis2']);
        Route::get('/ralan-dokter-paramedis', [RalanDokterParamedis::class, 'RalanDokterParamedis']);
        Route::get('/ralan-dokter-paramedis2', [RalanDokterParamedis2::class, 'RalanDokterParamedis2']);
        Route::get('/operasi-and-vk', [OperasiAndVK::class, 'OperasiAndVK']);
        Route::get('/operasi-and-vk1', [OperasiAndVK1::class, 'OperasiAndVK1']);
        Route::get('/ranap-dokter', [RanapDokter::class, 'RanapDokter']);
        Route::get('/ranap-dokter4', [RanapDokter4::class, 'RanapDokter4']);
        Route::get('/ranap-dokter2', [RanapDokter2::class, 'RanapDokter2']);
        Route::get('/ranap-dokter3', [RanapDokter3::class, 'RanapDokter3']);
        Route::get('/ranap-paramedis', [RanapParamedis::class, 'RanapParamedis']);
        Route::get('/ranap-paramedis2', [RanapParamedis2::class, 'RanapParamedis2']);
        Route::get('/ranap-dokter-paramedis', [RanapDokterParamedis::class, 'RanapDokterParamedis']);
        Route::get('/ranap-dokter-paramedis2', [RanapDokterParamedis2::class, 'RanapDokterParamedis2']);
        Route::get('/periksa-radiologi', [PeriksaRadiologi::class, 'PeriksaRadiologi']);
        Route::get('/periksa-radiologi2', [PeriksaRadiologi2::class, 'PeriksaRadiologi2']);

        // DETAIL TINDAKAN Umum
        Route::get('/ralan-dokter-umum', [RalanDokterUm::class, 'RalanDokterUm']);
        Route::get('/ralan-paramedis-umum', [RalanParamedisUm::class, 'RalanParamedisUm']);
        Route::get('/ralan-dokter-paramedis-umum', [RalanDokterParamedisUm::class, 'RalanDokterParamedisUm']);
        Route::get('/operasi-and-vk-umum', [OperasiAndVKUm::class, 'OperasiAndVKUm']);
        Route::get('/ranap-dokter-umum', [RanapDokterUm::class, 'RanapDokterUm']);
        Route::get('/ranap-paramedis-umum', [RanapParamedisUm::class, 'RanapParamedisUm']);
        Route::get('/ranap-dokter-paramedis-umum', [RanapDokterParamedisUm::class, 'RanapDokterParamedisUm']);
        Route::get('/periksa-radiologi-umum', [PeriksaRadiologiUm::class, 'PeriksaRadiologiUm']);

        // ANTRIAN PENDAFTARAN
        Route::get('/antrian-pendaftaran', [AntrianPendaftaran::class, 'AntrianPendaftaran']);
        Route::get('/cari-loket', [AntrianPendaftaran::class, 'DisplayAntrian']);
        Route::get('/setting-antrian', [AntrianPendaftaran::class, 'SetingAntrian']);

        // ANTRIAN POLI
        Route::get('/antrian-poli', [AntrianPoli::class, 'AntrianPoli']);
        Route::get('/antrian-poli-download', [AntrianPoli::class, 'downloadAutorun']);
        Route::get('/panggil-poli', [AntrianPoli::class, 'panggilpoli']);
        Route::get('/setting-antrian-poli', [AntrianPoli::class, 'settingPoli']);
        Route::get('/jadwal-dokter', [BwJadwaldokter::class, 'BwJadwaldokter']);

        //tes farmasi
        Route::get('/antrian-farmasi1', [AntrianFarmasi1::class, 'AntrianFarmasi1']);
        Route::get('/antrian-farmasi-download', [AntrianFarmasi1::class, 'downloadAutorunfarmasi']);
        Route::get('/panggil-farmasi1', [AntrianFarmasi1::class, 'panggilfarmasi']);

        Route::get('/tes', [AntrianFarmasi1::class, 'PanggilFarmasi1']);
        //DISPLAY
        Route::get('/info-kamar-ruangan', [InfoKamar::class, 'InfoKamarRuangan']);


        // RM
        Route::get('/berkas-rm', [BerkasRM::class, 'BerkasRM']);
        Route::get('/waktu-tunggu-pasien-bayar', [BerkasRM::class, 'WaktuTungguPasienBayar']);
        Route::get('/laporan-borlosetc', [Borlos::class, 'Borlosetc']);
        Route::get('/laporan-bto', [Borlos::class, 'Bto']);
        Route::get('/anjungan-mandiri', [AnjunganMandiri::class, 'Anjungan'])->middleware('permision-rsbw:registrasi');
        Route::get('/anjungan-mandiri-print/{noRawat}', [AnjunganMandiri::class, 'Print'])->middleware('permision-rsbw:registrasi');
        Route::get('/rawat-jalan', [PasienRawatJalan::class, 'PasienRawatJalan']);
        Route::get('/kunjungan-ralan', [KunjunganRalan::class, 'KunjunganRalan']);
        Route::get('/status-data-rm', [StatusDataRm::class, 'StatusDataRm']);
        Route::get('/pasien-pulang-ranap', [PasienPulangRanap::class, 'PasienPulangRanap']);
        Route::get('/jumlah-pasien', [JumlahPasien::class, 'JumlahPasien']);
        Route::get('/pasien-ranap-igd', [PasienRanapIgd::class, 'PasienRanapIgd']);
        Route::get('/pasien-per-episode', [PasienPerEpisode::class, 'PasienPerEpisode']);
        Route::get('/pasien-meninggal', [PasienMeninggal::class, 'PasienMeninggal']);
        Route::get('/tabulasi-igd', [TabulasiIGD::class, 'TabulasiIGD']);

        //AntrianFarmasi

        Route::get('/antrian-farmasi', [AntrianFarmasiController::class, 'index'])->name('antrian-farmasi.index');
        Route::post('/antrian-farmasi/ambil', [AntrianFarmasiController::class, 'ambilAntrian'])->name('antrian-farmasi.ambilAntrian');
        Route::patch('/antrian-farmasi/update/{id}', [AntrianFarmasiController::class, 'updateStatus'])->name('antrian-farmasi.updateStatus');
        Route::get('/antrian-farmasi/pasien/{no_rkm_medis}', [AntrianFarmasiController::class, 'getPasien'])->name('antrian-farmasi.getPasien');
        Route::get('/antrian-farmasi/cetak/{nomorAntrian}', [AntrianFarmasiController::class, 'cetakAntrian'])->name('antrian-farmasi.cetak');

        //tess
        Route::get('/display-farmasi', [PanggilanAntrianController::class, 'panggilanDisplay'])->name('antrian.view'); //tampilan farmasi
        Route::post('/antrian-farmasi/panggil', [PanggilanAntrianController::class, 'panggilAntrian'])->name('antrian-farmasi.antrian');



        // KEPERAWATAN
        Route::get('/home-keperawatan', [HomeKeperawatan::class, 'HomeKeperawatan']);
        Route::get('/logbook-keperawatan', [PengawasKeperawatan::class, 'PengawasKeperawatan']);
        Route::get('/laporan-logbook-keperawatan', [LaporanLogBook::class, 'getLookBook']);
        Route::get('/laporan-logbook-keperawatan2', [LaporanLogBook2::class, 'getLookBook']);
        Route::get('/input-kegiatan-keperawatan-lain', [PengawasKeperawatan::class, 'InputKegiatanLain']);
        Route::get('/input-kegiatan-karu', [PengawasKeperawatan::class, 'InputKegiatankaru']);
        Route::get('/laporan-kegiatan-karu', [LaporanLogbokKaru::class, 'LaporanLogbokKaru']);

        // BRIDGING BPJS
        Route::get('/kirim-taskid-bpjs', [KirimTaskId::class, 'KirimTaskId']);
        Route::get('/kirim-taskid-bpjs2', [KirimTaskId::class, 'KirimTaskId2']);
        Route::get('/sep-vclaim', [KirimTaskId::class, 'CariSepVclaim']);
        Route::get('/update-jadwal-dokter', [KirimTaskId::class, 'UpdateJadwalHfis']);
        Route::get('/icare', [KirimTaskId::class, 'Icare']);

        // LAB
        Route::get('/bridging-lis-lab', [BridgingalatlatLis::class, 'BridgingalatlatLis']);
    });
    // diplay
    Route::get('/display', [AntrianPoli::class, 'display']);
    Route::get('/display-petugas', [AntrianPendaftaran::class, 'DisplayPetugas']);
    Route::get('/info-kamar', [InfoKamar::class, 'InfoKamar']);
    Route::get('/info-kamar2', [InfoKamar::class, 'InfoKamar2']);
    Route::get('/info-kamar3', [InfoKamar::class, 'InfoKamar3']);
});
