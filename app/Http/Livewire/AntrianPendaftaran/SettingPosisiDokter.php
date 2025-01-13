<?php

namespace App\Http\Livewire\AntrianPendaftaran;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SettingPosisiDokter extends Component
{
    public $getListDokter;
    public $getLoket;
    protected $listeners = ['mout'];  // Menerima Triger Dari SettingAntrianLoket

    public function mout()
    {
        $this->getListDokter();
        $this->getLoket();
    }
    public function render()
    {
        $this->getListDokter();
        $this->getLoket();
        return view('livewire.antrian-pendaftaran.setting-posisi-dokter');
    }
    private function getLoket()
    {
        $this->getLoket = DB::table('loket')
            ->select('loket.kd_loket', 'loket.nama_loket')
            ->get();
    }

    // GET LIST DOKTER
    private function getListDokter()
    {
        $this->getListDokter = DB::table('dokter')
            ->select('dokter.kd_dokter', 'dokter.nm_dokter', 'list_dokter.kd_loket', 'list_dokter.foto', DB::raw('IFNULL(kuota_tambahan, 0) as kuota_tambahan'))
            ->leftJoin('list_dokter', 'dokter.kd_dokter', '=', 'list_dokter.kd_dokter')
            ->where('dokter.status', '=', '1')
            ->orderBy('dokter.kd_dokter', 'ASC')
            ->get();
    }

    // EDIT LOKET DOKTER
    public function editLoket($kd_dokter, $nm_dokter, $kd_loket)
    {
        try {
            DB::table('list_dokter')->updateOrInsert(
                ['kd_dokter' => $kd_dokter],
                ['nama_dokter' => $nm_dokter, 'kd_loket' => $kd_loket]
            );
            $this->flashMessage('Posisi Dokter Dipindahkan Ke ' . $kd_loket, 'success', 'check');
        } catch (\Exception $e) {
            $this->flashMessage('Terjadi kesalahan saat menghapus pendaftaran.', 'danger', 'ban');
        }
    }

    // ALERT
    private function flashMessage($message, $color, $icon)
    {
        Session::flash('message', $message);
        Session::flash('color', $color);
        Session::flash('icon', $icon);
    }

    // UPLOAD FOTO
    public $keyModal;
    public $nm_dokter;
    public $kd_dokter;
    public $kd_loket;
    public function SetmodalInacbg($key, $kd_loket)
    {
        $this->keyModal = $key;
        $this->nm_dokter = $this->getListDokter[$key]['nm_dokter'];
        $this->kd_dokter = $this->getListDokter[$key]['kd_dokter'];
        $this->kd_loket = $kd_loket;
    }

    public $foto_dokter = [];
    use WithFileUploads;
    public function UploadFoto($key, $kd_dokter)
    {
        try {
            $this->validate([
                'foto_dokter.' . $key => 'image'
            ]);
            $file_name = $kd_dokter . '.' . $this->foto_dokter[$key]->getClientOriginalExtension();
            $this->foto_dokter[$key]->storeAs('public/foto_dokter', $file_name);
            $livewire_tmp_file = 'livewire-tmp/' . $this->foto_dokter[$key]->getFileName();
            Storage::delete($livewire_tmp_file);
            DB::table('list_dokter')
                ->where('kd_dokter', $kd_dokter)
                ->update(['foto' => $file_name]);
        } catch (\Throwable $th) {
        }
        $this->getListDokter();
    }

    // TAMBAH KUOTA
    public $kuota_tambahan;
    function updateKuotaDokter($key, $kd_dokter)
    {
        try {
            DB::table('list_dokter')
                ->where('kd_dokter', $kd_dokter) // Specify the doctor using kd_dokter
                ->update([
                    'kuota_tambahan' => $this->getListDokter[$key]['kuota_tambahan']
                ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
