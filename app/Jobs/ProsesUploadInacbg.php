<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProsesUploadInacbg implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $file_name, $no_rawat, $no_rkm_medis;

    public function __construct($file_name, $no_rawat, $no_rkm_medis)
    {
        $this->file_name = $file_name;
        $this->no_rawat = $no_rawat;
        $this->no_rkm_medis = $no_rkm_medis;
    }

    public function handle()
    {
        $cekBerkas = DB::table('bw_file_casemix_inacbg')
            ->where('no_rawat', $this->no_rawat)
            ->exists();

        if (!$cekBerkas) {
            DB::table('bw_file_casemix_inacbg')->insert([
                'no_rkm_medis' => $this->no_rkm_medis,
                'no_rawat' => $this->no_rawat,
                'file' => $this->file_name,
            ]);
        }
    }
}
