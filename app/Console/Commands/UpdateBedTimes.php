<?php

namespace App\Console\Commands;

use App\Services\Bpjs\ReferensiBPJS;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateBedTimes extends Command
{
    protected $signature = 'bed:update-times';
    protected $description = 'Update times_update and sync bed availability to MJKN every 5 minutes';

    public function handle()
    {
        $updated = DB::table('bw_display_bad')
            ->update(['times_update' => now()]);

        $this->info("Updated {$updated} bed record(s).");

        $referensi = new ReferensiBPJS();
        $rooms = DB::table('bw_display_bad')
            ->select('kd_kelas_bpjs', 'nm_ruangan_bpjs')
            ->whereNotNull('kd_kelas_bpjs')
            ->whereNotNull('nm_ruangan_bpjs')
            ->groupBy('kd_kelas_bpjs', 'nm_ruangan_bpjs')
            ->get();

        foreach ($rooms as $room) {
            $roomData = DB::table('bw_display_bad')
                ->select(
                    'ruangan',
                    'nm_ruangan_bpjs',
                    'kd_ruang',
                    'kd_kelas_bpjs',
                    DB::raw('COUNT(status) AS kapasitas'),
                    DB::raw('COUNT(CASE WHEN status = 0 THEN 0 END) AS tersedia')
                )
                ->where('kd_kelas_bpjs', $room->kd_kelas_bpjs)
                ->where('nm_ruangan_bpjs', $room->nm_ruangan_bpjs)
                ->groupBy('kd_kelas_bpjs')
                ->first();

            if (!$roomData) {
                continue;
            }

            $data = [
                'kodekelas' => $roomData->kd_kelas_bpjs,
                'koderuang' => $roomData->kd_ruang,
                'namaruang' => 'R ' . $roomData->nm_ruangan_bpjs,
                'kapasitas' => $roomData->kapasitas,
                'tersedia' => $roomData->tersedia,
                'tersediapria' => 0,
                'tersediawanita' => 0,
                'tersediapriawanita' => $roomData->tersedia,
            ];

            try {
                $response = $referensi->updateRuangan(json_encode($data));
                $this->info('Synced room: ' . ($roomData->nm_ruangan_bpjs ?? $room->nm_ruangan_bpjs));
            } catch (\Throwable $th) {
                $this->warn('Failed to sync room: ' . ($roomData->nm_ruangan_bpjs ?? $room->nm_ruangan_bpjs) . ' - ' . $th->getMessage());
            }
        }

        return 0;
    }
}
