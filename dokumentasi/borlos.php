<?php
 //     $Ruangan = DB::table('bw_borlos')
        //     ->select('bw_borlos.ruangan', 'bw_borlos.jml_bed')
        //     ->get();
        // $year = 2024;
        // $borResults = [];
        // for ($month = 1; $month <= 12; $month++) {
        //     $total_bed_perbulan[$month] = 0;
        //     $total_hari_perbulan[$month] = 0;
        // }
        // foreach ($Ruangan as $room) {
        //     $kamar = $room->ruangan;
        //     $jumlah_tempat_tidur = $room->jml_bed;
        //     for ($month = 1; $month <= 12; $month++) {
        //         $start_Date = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        //         $end_Date = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        //         $jumlah_hari = DB::table('reg_periksa')
        //             ->select(DB::raw('SUM(DATEDIFF(kamar_inap.tgl_keluar, kamar_inap.tgl_masuk)) as total_jumlah_hari'))
        //             ->join('kamar_inap', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
        //             ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
        //             ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
        //             ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        //             ->whereBetween('reg_periksa.tgl_registrasi', [$start_Date, $end_Date])
        //             ->where('bangsal.nm_bangsal', 'like', '%' . $kamar . '%')
        //             ->first();
        //         $jumlahhari = (int) ($jumlah_hari->total_jumlah_hari ?? 0);
        //         $periode_hari = Carbon::create($year, $month, 1)->daysInMonth;

        //         $bor = ($jumlahhari / ($jumlah_tempat_tidur * $periode_hari)) * 100;

        //         $borResults[$kamar][$month] = [
        //             'jumlahhari' => $jumlahhari,
        //             'jumlah_tempat_tidur' => $jumlah_tempat_tidur,
        //             'periode_hari' => $periode_hari,
        //             'bor' => $bor
        //         ];
        //         $total_bed_perbulan[$month] += $jumlah_tempat_tidur;
        //         $total_hari_perbulan[$month] += $jumlahhari;
        //     }
        // }
        // $TotalBor = [];
        // foreach ($total_bed_perbulan as $month => $totalBeds) {
        //     $periode_hari = Carbon::create($year, $month, 1)->daysInMonth;
        //     $hitung_total_bor = ($total_hari_perbulan[$month] / ($totalBeds * $periode_hari)) * 100;

        //     $TotalBor[$month] = [
        //         'total_hari_perbulan' => $total_hari_perbulan[$month],
        //         'total_beds' => $totalBeds,
        //         'periode_hari' => $periode_hari,
        //         'overall_bor' => $hitung_total_bor
        //     ];
        // }
        // $borResults['SEMUA_RUANGAN'] = $TotalBor;

        // $this->BOR = $borResults;
