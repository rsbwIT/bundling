<?php

namespace App\Http\Livewire\Bpjs;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SettingBpjs extends Component
{
    public $loadDataCasemix = [];
    public $getSeting = [];
    public $cariNomor = '';

    public function mount()
    {
        $this->loadSetting();
        $this->loadCasemix();
    }

    public function render()
    {
        return view('livewire.bpjs.setting-bpjs', [
            'getDataListCasemix' => $this->loadDataCasemix,
        ]);
    }

    /**
     * Cari Data Casemix
     */
    public function loadCasemix()
    {
        $query = DB::table('file_casemix')
            ->select(
                'id',
                'no_rkm_medis',
                'no_rawat',
                'nama_pasein',
                'jenis_berkas',
                'file'
            );

        if (!empty($this->cariNomor)) {
            $query->where(function ($q) {
                $q->where('no_rkm_medis', $this->cariNomor)
                    ->orWhere('no_rawat', $this->cariNomor)
                    ->orWhere('nama_pasein', 'like', '%' . $this->cariNomor . '%');
            });
        }

        $this->loadDataCasemix = $query
            ->orderByDesc('id')
            ->limit(500)
            ->get();
    }

    /**
     * Delete File
     */
    public function deleteDataFile($id, $jenis_berkas, $file)
    {
        try {

            DB::table('file_casemix')
                ->where('id', $id)
                ->delete();

            switch ($jenis_berkas) {

                case 'INACBG':
                    Storage::disk('public')->delete('file_inacbg/' . $file);
                    break;

                case 'SCAN':
                    Storage::disk('public')->delete('file_scan/' . $file);
                    break;

                case 'RESUMEDLL':
                    Storage::disk('public')->delete('resume_dll/' . $file);
                    break;

                case 'HASIL':

                    $path = public_path('hasil_pdf/' . $file);

                    if (file_exists($path)) {
                        unlink($path);
                    }

                    break;
            }

            $this->loadCasemix();

            $this->flashMessage(
                'Data berhasil dihapus',
                'warning',
                'check'
            );

        } catch (\Exception $e) {

            $this->flashMessage(
                'Terjadi kesalahan saat menghapus data',
                'danger',
                'ban'
            );
        }
    }

    /**
     * Flash Message
     */
    private function flashMessage($message, $color, $icon)
    {
        Session::flash('message', $message);
        Session::flash('color', $color);
        Session::flash('icon', $icon);
    }

    /**
     * Load Setting Bundling
     */
    public function loadSetting()
    {
        $this->getSeting = DB::table('bw_setting_bundling')
            ->select(
                'id',
                'nama_berkas',
                'status',
                'urutan'
            )
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Update Status
     */
    public function updateStatus($id, $value)
    {
        DB::table('bw_setting_bundling')
            ->where('id', $id)
            ->update([
                'status' => $value
            ]);

        $this->loadSetting();

        $this->flashMessage(
            'Status berhasil diperbarui',
            'success',
            'check'
        );
    }

    /**
     * Update Urutan Drag & Drop
     */
    public function updateOrder($items)
    {
        DB::transaction(function () use ($items) {

            foreach ($items as $index => $id) {

                DB::table('bw_setting_bundling')
                    ->where('id', $id)
                    ->update([
                        'urutan' => $index + 1
                    ]);
            }
        });

        $this->loadSetting();

        $this->flashMessage(
            'Urutan berhasil diperbarui',
            'success',
            'check'
        );
    }
}