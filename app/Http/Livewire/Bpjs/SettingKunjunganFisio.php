<?php

namespace App\Http\Livewire\Bpjs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\SettingFisioService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class SettingKunjunganFisio extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $suhuTubuhFisio = [];
    public $customSuhuFisio = '';
    public $searchTracker = '';

    public function mount()
    {
        $this->loadSetting();
    }

    public function updatingSearchTracker()
    {
        $this->resetPage();
    }

    public function render()
    {
        $currentUserId = (string) (session('auth')['id_user'] ?? (session('user')['id_user'] ?? ''));
        $canViewTracker = ($currentUserId === '01091999');

        $logs = [];
        if ($canViewTracker && Schema::hasTable('bw_setting_fisio_log')) {
            $query = DB::table('bw_setting_fisio_log')
                ->orderBy('created_at', 'desc');

            if (!empty($this->searchTracker)) {
                $search = '%' . trim($this->searchTracker) . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('nama_user', 'like', $search)
                      ->orWhere('id_user', 'like', $search)
                      ->orWhere('keterangan', 'like', $search)
                      ->orWhere('ip_address', 'like', $search);
                });
            }

            $logs = $query->paginate(10);
        }

        return view('livewire.bpjs.setting-kunjungan-fisio', [
            'suhuTubuhFisio' => $this->suhuTubuhFisio,
            'logs' => $logs,
            'canViewTracker' => $canViewTracker,
        ]);
    }

    public function loadSetting()
    {
        $this->suhuTubuhFisio = SettingFisioService::getSuhuTubuhFisio();
        $this->customSuhuFisio = implode(', ', $this->suhuTubuhFisio);
    }

    public function saveSetting()
    {
        $items = array_map('trim', explode(',', (string) $this->customSuhuFisio));
        $items = array_filter($items, function ($val) {
            return $val !== '';
        });

        $res = SettingFisioService::setSuhuTubuhFisio(array_values($items));
        $this->loadSetting();

        if ($res) {
            $this->flashMessage(
                'Setting nilai kunjungan Fisio berhasil disimpan: [' . implode(', ', $this->suhuTubuhFisio) . ']',
                'success',
                'check'
            );
        } else {
            $this->flashMessage(
                'Gagal menyimpan setting ke database. Silakan periksa koneksi/izin database.',
                'danger',
                'exclamation-triangle'
            );
        }
    }

    public function toggleCheckboxSuhu($angka)
    {
        $angka = (string) $angka;
        if (in_array($angka, $this->suhuTubuhFisio, true)) {
            $this->suhuTubuhFisio = array_values(array_diff($this->suhuTubuhFisio, [$angka]));
        } else {
            $this->suhuTubuhFisio[] = $angka;
        }

        sort($this->suhuTubuhFisio, SORT_NATURAL);
        $this->customSuhuFisio = implode(', ', $this->suhuTubuhFisio);

        $res = SettingFisioService::setSuhuTubuhFisio($this->suhuTubuhFisio);
        $this->loadSetting();

        if ($res) {
            $this->flashMessage(
                'Setting kunjungan Fisio diperbarui: [' . implode(', ', $this->suhuTubuhFisio) . ']',
                'success',
                'check'
            );
        } else {
            $this->flashMessage(
                'Gagal memperbarui setting ke database. Silakan periksa koneksi/izin database.',
                'danger',
                'exclamation-triangle'
            );
        }
    }

    private function flashMessage($message, $color, $icon)
    {
        Session::flash('message', $message);
        Session::flash('color', $color);
        Session::flash('icon', $icon);
    }
}
