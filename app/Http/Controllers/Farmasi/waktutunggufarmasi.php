<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class waktutunggufarmasi extends Controller
{
    public function index(Request $request)
    {
        // Default range: hari ini s.d hari ini
        $tgl1 = $request->input('tgl1', date('Y-m-d'));
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

        // Pisahkan data racik dan non racik berdasarkan kolom keterangan (atau kolom racik_non_racik sebagai cadangan)
        $racik = [];
        $nonRacik = [];
        foreach ($data as $row) {
            $ket = strtolower($row->keterangan ?? '');
            if (strpos($ket, 'racik') !== false) {
                if (strpos($ket, 'non') !== false) {
                    $nonRacik[] = $row;
                } else {
                    $racik[] = $row;
                }
            } else {
                if (strtolower($row->racik_non_racik) === 'racik') {
                    $racik[] = $row;
                } else {
                    $nonRacik[] = $row;
                }
            }
        }

        return view('farmasi.waktutunggufarmasi', compact('racik', 'nonRacik', 'tgl1', 'tgl2'));
    }

    public function export(Request $request)
    {
        $tgl1 = $request->input('tgl1', date('Y-m-d'));
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

        $racik = [];
        $nonRacik = [];
        foreach ($data as $row) {
            $ket = strtolower($row->keterangan ?? '');
            if (strpos($ket, 'racik') !== false) {
                if (strpos($ket, 'non') !== false) {
                    $nonRacik[] = $row;
                } else {
                    $racik[] = $row;
                }
            } else {
                if (strtolower($row->racik_non_racik) === 'racik') {
                    $racik[] = $row;
                } else {
                    $nonRacik[] = $row;
                }
            }
        }

        $filename = "Waktu_Tunggu_Farmasi_" . date('Ymd_His') . ".xls";

        return response()->streamDownload(function () use ($racik, $nonRacik) {
            // Gunakan XML SpreadsheetML untuk mendukung multi-sheet (Sheet) secara native di Excel
            echo '<?xml version="1.0"?>' . "\n";
            echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            echo ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
            echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
            echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            echo ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
            
            // Definisikan Style
            echo '  <Styles>' . "\n";
            echo '    <Style ss:ID="HeaderStyle">' . "\n";
            echo '      <Font ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
            echo '      <Interior ss:Color="#1e3a8a" ss:Pattern="Solid"/>' . "\n";
            echo '      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n";
            echo '    </Style>' . "\n";
            echo '    <Style ss:ID="TextStyle">' . "\n";
            echo '      <NumberFormat ss:Format="@"/>' . "\n";
            echo '    </Style>' . "\n";
            echo '  </Styles>' . "\n";

            // ================= SHEET 1: OBAT NON-RACIK =================
            echo '  <Worksheet ss:Name="Obat Non-Racik">' . "\n";
            echo '    <Table>' . "\n";
            // Header
            echo '      <Row ss:Height="22">' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">No</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">No. RM</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Nama Pasien</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Tanggal</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Keterangan</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Waktu Daftar</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Waktu Selesai</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Status</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Waktu Tunggu (Menit)</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Waktu Tunggu (Jam &amp; Menit)</Data></Cell>' . "\n";
            echo '      </Row>' . "\n";

            foreach ($nonRacik as $i => $row) {
                $statusText = (strtolower($row->status) === 'selesai' || $row->status == '3') ? 'Selesai' : $row->status;
                echo '      <Row>' . "\n";
                echo '        <Cell><Data ss:Type="Number">' . ($i + 1) . '</Data></Cell>' . "\n";
                echo '        <Cell ss:StyleID="TextStyle"><Data ss:Type="String">' . htmlspecialchars($row->rekam_medik) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->nama_pasien) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->tanggal) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->keterangan ?? '') . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->waktu_daftar ?? '') . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->waktu_selesai ?? '') . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($statusText) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="Number">' . (int)$row->waktu_tunggu_menit . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->waktu_tunggu_jam_menit ?? '') . '</Data></Cell>' . "\n";
                echo '      </Row>' . "\n";
            }
            echo '    </Table>' . "\n";
            echo '  </Worksheet>' . "\n";

            // ================= SHEET 2: OBAT RACIKAN =================
            echo '  <Worksheet ss:Name="Obat Racikan">' . "\n";
            echo '    <Table>' . "\n";
            // Header
            echo '      <Row ss:Height="22">' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">No</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">No. RM</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Nama Pasien</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Tanggal</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Keterangan</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Waktu Daftar</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Waktu Selesai</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Status</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Waktu Tunggu (Menit)</Data></Cell>' . "\n";
            echo '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">Waktu Tunggu (Jam &amp; Menit)</Data></Cell>' . "\n";
            echo '      </Row>' . "\n";

            foreach ($racik as $i => $row) {
                $statusText = (strtolower($row->status) === 'selesai' || $row->status == '3') ? 'Selesai' : $row->status;
                echo '      <Row>' . "\n";
                echo '        <Cell><Data ss:Type="Number">' . ($i + 1) . '</Data></Cell>' . "\n";
                echo '        <Cell ss:StyleID="TextStyle"><Data ss:Type="String">' . htmlspecialchars($row->rekam_medik) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->nama_pasien) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->tanggal) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->keterangan ?? '') . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->waktu_daftar ?? '') . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->waktu_selesai ?? '') . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($statusText) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="Number">' . (int)$row->waktu_tunggu_menit . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($row->waktu_tunggu_jam_menit ?? '') . '</Data></Cell>' . "\n";
                echo '      </Row>' . "\n";
            }
            echo '    </Table>' . "\n";
            echo '  </Worksheet>' . "\n";

            echo '</Workbook>' . "\n";

        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Cache-Control' => 'max-age=0'
        ]);
    }
}
