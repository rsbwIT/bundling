<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class waktutunggufarmasi extends Controller
{
    public function index(Request $request)
    {
        // Default range: awal bulan ini s.d hari ini
        $tgl1 = $request->input('tgl1', date('Y-m-01'));
        $tgl2 = $request->input('tgl2', date('Y-m-d'));

        // Jalankan raw query yang diinstruksikan oleh user
        $data = DB::select("
            SELECT
                antrian.nama_pasien,
                antrian.rekam_medik,
                antrian.tanggal,
                antrian.keterangan,
                antrian.created_at AS waktu_daftar,
                antrian.updated_at AS waktu_selesai,
                antrian.racik_non_racik,
                antrian.status,

                -- Hitung menit selisih waktu
                TIMESTAMPDIFF(MINUTE, antrian.created_at, antrian.updated_at) AS waktu_tunggu_menit,

                -- Format Jam & Menit
                CONCAT(
                    FLOOR(TIMESTAMPDIFF(MINUTE, antrian.created_at, antrian.updated_at) / 60), ' Jam ',
                    MOD(TIMESTAMPDIFF(MINUTE, antrian.created_at, antrian.updated_at), 60), ' Menit'
                ) AS waktu_tunggu_jam_menit

            FROM antrian
            WHERE antrian.tanggal BETWEEN ? AND ?
            ORDER BY antrian.created_at ASC
        ", [$tgl1, $tgl2]);

        return view('farmasi.waktutunggufarmasi', compact('data', 'tgl1', 'tgl2'));
    }

    public function export(Request $request)
    {
        $tgl1 = $request->input('tgl1', date('Y-m-01'));
        $tgl2 = $request->input('tgl2', date('Y-m-d'));

        $data = DB::select("
            SELECT
                antrian.nama_pasien,
                antrian.rekam_medik,
                antrian.tanggal,
                antrian.keterangan,
                antrian.created_at AS waktu_daftar,
                antrian.updated_at AS waktu_selesai,
                antrian.racik_non_racik,
                antrian.status,
                TIMESTAMPDIFF(MINUTE, antrian.created_at, antrian.updated_at) AS waktu_tunggu_menit,
                CONCAT(
                    FLOOR(TIMESTAMPDIFF(MINUTE, antrian.created_at, antrian.updated_at) / 60), ' Jam ',
                    MOD(TIMESTAMPDIFF(MINUTE, antrian.created_at, antrian.updated_at), 60), ' Menit'
                ) AS waktu_tunggu_jam_menit
            FROM antrian
            WHERE antrian.tanggal BETWEEN ? AND ?
            ORDER BY antrian.created_at ASC
        ", [$tgl1, $tgl2]);

        $filename = "Waktu_Tunggu_Farmasi_" . date('Ymd_His') . ".xls";

        return response()->streamDownload(function () use ($data) {
            // UTF-8 BOM
            echo "\xEF\xBB\xBF";

            echo "<table border='1'>";
            echo "<tr>
                <th>No</th>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Racik/Non-Racik</th>
                <th>Waktu Daftar</th>
                <th>Waktu Selesai</th>
                <th>Status</th>
                <th>Waktu Tunggu (Menit)</th>
                <th>Waktu Tunggu (Jam & Menit)</th>
            </tr>";

            foreach ($data as $i => $row) {
                $statusText = (strtolower($row->status) === 'selesai' || $row->status == '3') ? 'Selesai' : $row->status;
                echo "<tr>
                    <td>" . ($i + 1) . "</td>
                    <td style='mso-number-format:\"\\@\"'>{$row->rekam_medik}</td>
                    <td>{$row->nama_pasien}</td>
                    <td>{$row->tanggal}</td>
                    <td>{$row->keterangan}</td>
                    <td>{$row->racik_non_racik}</td>
                    <td>{$row->waktu_daftar}</td>
                    <td>{$row->waktu_selesai}</td>
                    <td>{$statusText}</td>
                    <td style='mso-number-format:\"0\"'>{$row->waktu_tunggu_menit}</td>
                    <td>{$row->waktu_tunggu_jam_menit}</td>
                </tr>";
            }
            echo "</table>";
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Cache-Control' => 'max-age=0'
        ]);
    }
}
