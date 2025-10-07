<?php

namespace App\Http\Livewire\AntrianFarmasi;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PanggilanFarmasiBaru extends Component
{
    public $tanggal;

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
    }

    public function render()
    {
        $query = DB::table('antrian')
            ->select(
                'antrian.nomor_antrian',
                'antrian.nama_pasien',
                'antrian.tanggal',
                'antrian.keterangan',
                'antrian.status',
                'dokter.nm_dokter'
            )
            ->join('reg_periksa', 'antrian.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('reg_periksa.tgl_registrasi', $this->tanggal);

        if (request()->filled('dokter')) {
            $query->where('dokter.nm_dokter', request('dokter'));
        }

        if (request()->filled('keterangan')) {
            $query->where('antrian.keterangan', request('keterangan'));
        }

        $data = $query->get();

        $dokters = DB::table('dokter')->select('nm_dokter')->orderBy('nm_dokter')->get();

        $keterangans = DB::table('antrian')
            ->select('keterangan')
            ->distinct()
            ->whereNotNull('keterangan')
            ->orderBy('keterangan')
            ->get();

        return view('livewire.antrian-farmasi.panggilanfarmasibaru', [
            'antrians'    => $data,
            'tanggal'     => $this->tanggal,
            'dokters'     => $dokters,
            'keterangans' => $keterangans
        ]);
    }

    // ✅ Update status langsung di sini
    public function updateStatus(Request $request, $nomor)
        {
            DB::table('antrian')
                ->where('nomor_antrian', $nomor)
                ->whereDate('tanggal', $request->tanggal) // ✅ gunakan tanggal dari form
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            return back()->with(
                'success',
                "Status antrian $nomor pada tanggal {$request->tanggal} berhasil diubah ke {$request->status}"
            );
        }

     public function panggil($nomor)
        {
            $antrian = DB::table('antrian')->where('nomor_antrian', $nomor)->first();

            if($antrian) {
                DB::table('antrian')
                    ->where('nomor_antrian', $nomor)
                    ->update([
                        'status' => 'DIPANGGIL',
                        'updated_at' => now()
                    ]);

                // Emit event Livewire untuk JS suara
                $this->emit('panggilSuara', $antrian->nomor_antrian, $antrian->nama_pasien, $antrian->nm_dokter, $antrian->keterangan);
            }
        }


}