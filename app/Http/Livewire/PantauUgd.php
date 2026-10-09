<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PantauUgd extends Component
{
    use \Livewire\WithPagination;

    public $tgl_awal;
    public $tgl_akhir;
    public $jenis_bayar = '';
    public $search = '';
    
    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'tanggalDiubah' => 'updateTanggal'
    ];

    public function updateTanggal($field, $value)
    {
        $this->$field = $value;
        $this->resetPage();
    }

    public function mount()
    {
        $this->tgl_awal = date('Y-m-d');
        $this->tgl_akhir = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTglAwal()
    {
        $this->resetPage();
    }

    public function updatingTglAkhir()
    {
        $this->resetPage();
    }

    public function updatingJenisBayar()
    {
        $this->resetPage();
    }

    public function cari()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tglAwal = $this->tgl_awal ?: date('Y-m-d');
        $tglAkhir = $this->tgl_akhir ?: date('Y-m-d');

        $query = \Illuminate\Support\Facades\DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select(
                'reg_periksa.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'poliklinik.nm_poli',
                'reg_periksa.kd_pj',
                'penjab.png_jawab as jenis_bayar',
                'reg_periksa.stts',
                'reg_periksa.status_lanjut'
            )
            ->whereBetween('reg_periksa.tgl_registrasi', [$tglAwal, $tglAkhir])
            ->where(function ($q) {
                $q->where('poliklinik.nm_poli', 'like', '%IGD%')
                  ->orWhere('poliklinik.nm_poli', 'like', '%UGD%');
            });

        if (!empty($this->jenis_bayar)) {
            $query->where('reg_periksa.kd_pj', $this->jenis_bayar);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('pasien.nm_pasien', 'like', '%' . $this->search . '%')
                  ->orWhere('reg_periksa.no_rawat', 'like', '%' . $this->search . '%')
                  ->orWhere('pasien.no_rkm_medis', 'like', '%' . $this->search . '%');
            });
        }

        // ===== REKAP (mengikuti filter yang sama) =====
        $rekapPenjab = (clone $query)
            ->select(
                'reg_periksa.kd_pj',
                'penjab.png_jawab as jenis_bayar',
                \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN reg_periksa.status_lanjut = 'Ralan' THEN 1 ELSE 0 END) as ralan"),
                \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN reg_periksa.status_lanjut = 'Ranap' THEN 1 ELSE 0 END) as ranap"),
                \Illuminate\Support\Facades\DB::raw("COUNT(*) as total")
            )
            ->groupBy('reg_periksa.kd_pj', 'penjab.png_jawab')
            ->orderByDesc('total')
            ->get();

        $totalRalan = (int) $rekapPenjab->sum('ralan');
        $totalRanap = (int) $rekapPenjab->sum('ranap');
        $totalSemua = (int) $rekapPenjab->sum('total');

        $query->orderBy('reg_periksa.tgl_registrasi', 'DESC')
              ->orderBy('reg_periksa.jam_reg', 'DESC');

        $pasienUgd = $query->paginate(15);
        $listPenjab = \Illuminate\Support\Facades\DB::table('penjab')->where('status', '1')->get();

        return view('livewire.pantau-ugd', [
            'pasienUgd' => $pasienUgd,
            'listPenjab' => $listPenjab,
            'rekapPenjab' => $rekapPenjab,
            'totalRalan' => $totalRalan,
            'totalRanap' => $totalRanap,
            'totalSemua' => $totalSemua,
        ]);
    }
}
