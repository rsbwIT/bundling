<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 0.5pt solid #ccc;
            padding: 6px;
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        th {
            background-color: #1d7969;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1e293b;
        }
        .meta {
            font-size: 10pt;
            color: #64748b;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="title">Laporan Monitoring PKPA Penggunaan Antibiotik</div>
    <div class="meta">
        <strong>Periode:</strong> {{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('d F Y') }} s.d {{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('d F Y') }}<br>
        <strong>Ruangan:</strong> {{ $bangsal }}<br>
        <strong>Tanggal Export:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="background-color: #1d7969; color: #ffffff;">RM</th>
                <th style="background-color: #1d7969; color: #ffffff;">NAMA</th>
                <th style="background-color: #1d7969; color: #ffffff;">ALAMAT</th>
                <th style="background-color: #1d7969; color: #ffffff;">DIAGNOSIS</th>
                <th style="background-color: #1d7969; color: #ffffff;">DPJP</th>
                <th style="background-color: #1d7969; color: #ffffff;">Ruangan</th>
                <th style="background-color: #1d7969; color: #ffffff;">Nama Antibiotik</th>
                <th style="background-color: #1d7969; color: #ffffff;">Regimen Dosis</th>
                <th style="background-color: #1d7969; color: #ffffff;">Dosis per-hari</th>
                <th style="background-color: #1d7969; color: #ffffff;">Kode</th>
                <th style="background-color: #1d7969; color: #ffffff;">Lama Terapi AB</th>
                <th style="background-color: #1d7969; color: #ffffff;">Total Dosis</th>
                <th style="background-color: #1d7969; color: #ffffff;">Kode DDD</th>
                <th style="background-color: #1d7969; color: #ffffff;">DDD</th>
                <th style="background-color: #1d7969; color: #ffffff;">No Rawat</th>
                <th style="background-color: #1d7969; color: #ffffff;">Kategori Pasien</th>
                <th style="background-color: #1d7969; color: #ffffff;">Lama Rawat Inap</th>
                <th style="background-color: #1d7969; color: #ffffff;">Tanggal Pemberian Obat</th>
                <th style="background-color: #1d7969; color: #ffffff;">Status Resep</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $row)
                <tr>
                    <td style="mso-number-format:'\@';">{{ $row->RM }}</td>
                    <td>{{ $row->NAMA }}</td>
                    <td>{{ $row->ALAMAT }}</td>
                    <td>{{ $row->DIAGNOSIS }}</td>
                    <td>{{ $row->DPJP }}</td>
                    <td>{{ $row->Ruangan }}</td>
                    <td>{{ $row->{"Nama Antibiotik"} }}</td>
                    <td>{{ $row->{"Regimen Dosis"} }}</td>
                    <td class="text-right" style="mso-number-format:'#\,##0';">{{ $row->{"Dosis per-hari"} }}</td>
                    <td style="mso-number-format:'\@';">{{ $row->Kode }}</td>
                    <td class="text-center" style="mso-number-format:'#\,##0';">{{ $row->{"Lama Terapi AB"} }}</td>
                    <td class="text-right" style="mso-number-format:'#\,##0\.000';">{{ $row->{"Total Dosis"} }}</td>
                    <td style="mso-number-format:'\@';">{{ $row->{"Kode DDD"} }}</td>
                    <td class="text-right" style="mso-number-format:'#\,##0\.000';">{{ $row->DDD }}</td>
                    <td style="mso-number-format:'\@';">{{ $row->{"No Rawat"} }}</td>
                    <td>{{ $row->{"Kategori Pasien"} }}</td>
                    <td class="text-center" style="mso-number-format:'#\,##0';">{{ $row->{"Lama Rawat Inap"} }}</td>
                    <td style="mso-number-format:'\@';">{{ $row->{"Tanggal Pemberian Obat"} }}</td>
                    <td>{{ $row->{"Status Resep"} }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
