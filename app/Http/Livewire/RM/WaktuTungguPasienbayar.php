<?php

namespace App\Http\Livewire\RM;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class WaktuTungguPasienbayar extends Component
{

    public $tanggal1;
    public $tanggal2;
    public $status_pulang;
    public $carinomor;

    public function mount()
    {
        $this->tanggal1 = date('Y-m-d');
        $this->tanggal2 = date('Y-m-d');
        $this->status_pulang = 'blm_pulang';
        $this->getListPasienRanap();
    }

    public function render()
    {
        $this->getListPasienRanap();
        return view('livewire.r-m.waktu-tunggu-pasienbayar');
    }

    public $getPasien;
    function getListPasienRanap()
    {
        $cariKode = $this->carinomor;
        $sts_pulang = $this->status_pulang;
        $this->getPasien =  DB::table('reg_periksa')
            ->select(
                'pasien.nm_pasien',
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.status_lanjut',
                'dokter.nm_dokter',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.jam_keluar',
                'kamar_inap.stts_pulang',
                'kamar_inap.kd_kamar',
                'bangsal.nm_bangsal'
            )
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->where(function ($query) use ($sts_pulang) {
                if ($sts_pulang == 'blm_pulang') {
                    $query->where('kamar_inap.stts_pulang', '-');
                } elseif ($sts_pulang == 'tgl_masuk') {
                    $query->whereBetween('kamar_inap.tgl_masuk', [$this->tanggal1, $this->tanggal2]);
                } elseif ($sts_pulang == 'tgl_keluar') {
                    $query->whereBetween('kamar_inap.tgl_keluar', [$this->tanggal1, $this->tanggal2]);
                }
            })
            ->where(function ($query) use ($cariKode) {

                $query->orwhere('reg_periksa.no_rkm_medis', 'LIKE', "%$cariKode%")
                    ->orwhere('pasien.nm_pasien', 'LIKE', "%$cariKode%")
                    ->orwhere('reg_periksa.no_rawat', 'LIKE', "%$cariKode%");
            })
            ->where('reg_periksa.status_lanjut', '=', 'Ranap')
            ->where('reg_periksa.kd_pj', '=', 'UMU')
            ->groupBy('kamar_inap.no_rawat')
            ->get();
        $this->getPasien->map(function ($item) {
            $item->waktu_tunggu = DB::table('pemeriksaan_ranap')
                ->select(
                    'pemeriksaan_ranap.no_rawat',
                    'reg_periksa.kd_pj',
                    'pemeriksaan_ranap.tgl_perawatan',
                    'pemeriksaan_ranap.jam_rawat',
                    'pasien.nm_pasien',
                    'dokter.nm_dokter',
                    'kamar_inap.tgl_masuk',
                    'kamar_inap.jam_masuk',
                    'kamar_inap.tgl_keluar',
                    'kamar_inap.jam_keluar',
                    'nota_inap.tanggal as tanggal_nota',
                    'nota_inap.jam as jam_nota',
                    DB::raw("
                CONCAT(
                    TIMESTAMPDIFF(DAY, CONCAT(pemeriksaan_ranap.tgl_perawatan, ' ', pemeriksaan_ranap.jam_rawat), CONCAT(nota_inap.tanggal, ' ', nota_inap.jam)), ' hari, ',
                    MOD(TIMESTAMPDIFF(HOUR, CONCAT(pemeriksaan_ranap.tgl_perawatan, ' ', pemeriksaan_ranap.jam_rawat), CONCAT(nota_inap.tanggal, ' ', nota_inap.jam)), 24), ' jam, ',
                    MOD(TIMESTAMPDIFF(MINUTE, CONCAT(pemeriksaan_ranap.tgl_perawatan, ' ', pemeriksaan_ranap.jam_rawat), CONCAT(nota_inap.tanggal, ' ', nota_inap.jam)), 60), ' menit'
                ) as time_difference_cppt
            "),
                    DB::raw("
                CONCAT(
                    TIMESTAMPDIFF(DAY, CONCAT(kamar_inap.tgl_keluar, ' ', kamar_inap.jam_keluar), CONCAT(nota_inap.tanggal, ' ', nota_inap.jam)), ' hari, ',
                    MOD(TIMESTAMPDIFF(HOUR, CONCAT(kamar_inap.tgl_keluar, ' ', kamar_inap.jam_keluar), CONCAT(nota_inap.tanggal, ' ', nota_inap.jam)), 24), ' jam, ',
                    MOD(TIMESTAMPDIFF(MINUTE, CONCAT(kamar_inap.tgl_keluar, ' ', kamar_inap.jam_keluar), CONCAT(nota_inap.tanggal, ' ', nota_inap.jam)), 60), ' menit'
                ) as time_difference_set_pulang
            ")
                )
                ->join('reg_periksa', 'pemeriksaan_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('dokter', 'pemeriksaan_ranap.nip', '=', 'dokter.kd_dokter')
                ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('nota_inap', 'nota_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->where('pemeriksaan_ranap.no_rawat', '=', $item->no_rawat)
                ->orderBy('pemeriksaan_ranap.tgl_perawatan', 'desc')
                ->orderBy('pemeriksaan_ranap.jam_rawat', 'desc')
                ->limit(1)
                ->get();
            $item->waktu_tunggu_cppt_terakhir = DB::table('pemeriksaan_ranap')
                ->select(
                    'pemeriksaan_ranap.no_rawat',
                    'reg_periksa.kd_pj',
                    'pemeriksaan_ranap.tgl_perawatan',
                    'pemeriksaan_ranap.jam_rawat',
                    'pasien.nm_pasien',
                    'kamar_inap.tgl_masuk',
                    'kamar_inap.jam_masuk',
                    'kamar_inap.tgl_keluar',
                    'kamar_inap.jam_keluar',
                    'nota_inap.tanggal as tanggal_nota',
                    'nota_inap.jam as jam_nota',
                    DB::raw("
                CONCAT(
                    TIMESTAMPDIFF(DAY, CONCAT(pemeriksaan_ranap.tgl_perawatan, ' ', pemeriksaan_ranap.jam_rawat), CONCAT(nota_inap.tanggal, ' ', nota_inap.jam)), ' hari, ',
                    MOD(TIMESTAMPDIFF(HOUR, CONCAT(pemeriksaan_ranap.tgl_perawatan, ' ', pemeriksaan_ranap.jam_rawat), CONCAT(nota_inap.tanggal, ' ', nota_inap.jam)), 24), ' jam, ',
                    MOD(TIMESTAMPDIFF(MINUTE, CONCAT(pemeriksaan_ranap.tgl_perawatan, ' ', pemeriksaan_ranap.jam_rawat), CONCAT(nota_inap.tanggal, ' ', nota_inap.jam)), 60), ' menit'
                ) as time_difference_cppt_terakhir
            ")
                )
                ->join('reg_periksa', 'pemeriksaan_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->leftJoin('nota_inap', 'nota_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->where('pemeriksaan_ranap.no_rawat', '=', $item->no_rawat)
                ->orderBy('pemeriksaan_ranap.tgl_perawatan', 'desc')
                ->orderBy('pemeriksaan_ranap.jam_rawat', 'desc')
                ->limit(1)
                ->get();
        });
    }
}
