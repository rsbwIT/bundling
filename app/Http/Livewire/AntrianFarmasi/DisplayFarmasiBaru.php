<?php

namespace App\Http\Livewire\AntrianFarmasi;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class DisplayFarmasiBaru extends Component
{
    public $tanggal;
    public $antrians;

    public function mount()
    {
        $this->tanggal = now()->toDateString();
        $this->loadData();
    }

    public function loadData()
    {
        $now = now();
        $this->antrians = DB::table('antrian')
            ->select('nomor_antrian', 'nama_pasien', 'keterangan', 'status', 'created_at', 'updated_at', 'waktu_validasi')
            ->whereDate('tanggal', $this->tanggal)
            ->whereIn('keterangan', ['RACIKAN', 'NON RACIK'])
            ->get()
            ->map(function ($item) use ($now) {
                $createdAt = $item->created_at ? \Carbon\Carbon::parse($item->created_at) : null;
                $updatedAt = $item->updated_at ? \Carbon\Carbon::parse($item->updated_at) : null;
                $validasiAt = $item->waktu_validasi ? \Carbon\Carbon::parse($item->waktu_validasi) : null;

                $item->jam_ambil = $createdAt ? $createdAt->format('H:i') : '-';
                $item->jam_validasi = $validasiAt ? $validasiAt->format('H:i') : '-';

                // Target hitung mundur: Non-Racik = 45 menit, Racikan = 60 menit
                $isRacikan = (stripos($item->keterangan, 'RACIK') !== false && stripos($item->keterangan, 'NON') === false);
                $targetDurasiMenit = $isRacikan ? 60 : 45;
                $item->target_durasi_menit = $targetDurasiMenit;

                if ($createdAt) {
                    if ($item->status === 'DIPANGGIL' && $updatedAt && $updatedAt->greaterThan($createdAt)) {
                        $menit = $createdAt->diffInMinutes($updatedAt);
                    } else {
                        $menit = $createdAt->diffInMinutes($now);
                    }

                    if ($menit < 60) {
                        $item->lama_tunggu = $menit . ' Menit';
                        $item->lama_tunggu_singkat = $menit . ' mnt';
                    } else {
                        $jam = floor($menit / 60);
                        $sisa = $menit % 60;
                        $item->lama_tunggu = $jam . ' Jam ' . ($sisa > 0 ? $sisa . ' Menit' : '');
                        $item->lama_tunggu_singkat = $jam . 'j ' . $sisa . 'm';
                    }
                    $item->menit_tunggu = $menit;
                } else {
                    $item->lama_tunggu = '-';
                    $item->lama_tunggu_singkat = '-';
                    $item->menit_tunggu = 0;
                }

                // Hitung Mundur (Countdown Timer) berdasarkan waktu validasi
                if ($validasiAt) {
                    $item->sudah_validasi = true;
                    $targetWaktu = $validasiAt->copy()->addMinutes($targetDurasiMenit);
                    $item->target_timestamp = $targetWaktu->timestamp * 1000;

                    // Jika sudah dipanggil, sisa waktu dihitung hingga waktu dipanggil
                    $hitungKe = ($item->status === 'DIPANGGIL' && $updatedAt && $updatedAt->greaterThan($validasiAt))
                        ? $updatedAt
                        : $now;

                    $sisaDetik = (int) $hitungKe->diffInSeconds($targetWaktu, false);
                    $item->sisa_detik = $sisaDetik;

                    if ($sisaDetik > 0) {
                        $m = floor($sisaDetik / 60);
                        $s = $sisaDetik % 60;
                        $item->sisa_waktu = sprintf('%02d:%02d', $m, $s);
                        $item->sisa_waktu_singkat = sprintf('%02d:%02d', $m, $s);
                        $item->countdown_status = ($m <= 10) ? 'warning' : 'normal';
                    } else {
                        $item->sisa_waktu = '00:00';
                        $item->sisa_waktu_singkat = '00:00';
                        $item->countdown_status = 'expired';
                    }
                    $item->countdown_text = $item->sisa_waktu;
                } else {
                    $item->sudah_validasi = false;
                    $item->target_timestamp = null;
                    $item->sisa_detik = null;
                    $item->sisa_waktu = 'Menunggu Validasi';
                    $item->sisa_waktu_singkat = 'Menunggu';
                    $item->countdown_text = 'Menunggu Validasi';
                    $item->countdown_status = 'unvalidated';
                }

                return $item;
            });
    }

    public function render()
    {
        return view('livewire.antrian-farmasi.displayfarmasibaru', [
            'antrians' => $this->antrians
        ]);
    }
}