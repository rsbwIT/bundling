<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateKamarBPJSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $kd_kelas_bpjs;
    protected $nm_ruangan_bpjs;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($kd_kelas_bpjs, $nm_ruangan_bpjs)
    {
        $this->kd_kelas_bpjs = $kd_kelas_bpjs;
        $this->nm_ruangan_bpjs = $nm_ruangan_bpjs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $referensi = new \App\Services\Bpjs\ReferensiBPJS;
            
            $udapteKamar = \Illuminate\Support\Facades\DB::table('bw_display_bad')
                ->select(
                    'bw_display_bad.ruangan',
                    'bw_display_bad.nm_ruangan_bpjs',
                    'bw_display_bad.kd_ruang',
                    'bw_display_bad.kd_kelas_bpjs',
                    \Illuminate\Support\Facades\DB::raw('COUNT(bw_display_bad.status) AS kapasitas'),
                    \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN bw_display_bad.status = 0 THEN 0 END) AS tersedia'),
                    \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN bw_display_bad.status = 0 THEN 0 END) AS tersedia_wanita'),
                    \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN bw_display_bad.status = 0 THEN 0 END) AS tersedia_pria_wanita')
                )
                ->where('bw_display_bad.kd_kelas_bpjs', $this->kd_kelas_bpjs)
                ->where('bw_display_bad.nm_ruangan_bpjs', $this->nm_ruangan_bpjs)
                ->groupBy('bw_display_bad.kd_kelas_bpjs')
                ->first();

            if ($udapteKamar) {
                $data = [
                    'kodekelas' =>   $udapteKamar->kd_kelas_bpjs,
                    'koderuang' =>   $udapteKamar->kd_ruang,
                    'namaruang' => $udapteKamar->nm_ruangan_bpjs,
                    'kapasitas' => $udapteKamar->kapasitas,
                    'tersedia' => $udapteKamar->tersedia,
                    'tersediapria' => 0,
                    'tersediawanita' => 0,
                    'tersediapriawanita' => $udapteKamar->tersedia,
                ];

                $referensi->updateRuangan(json_encode($data));
            }
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('Gagal update kamar ke BPJS: ' . $th->getMessage());
        }
    }
}
