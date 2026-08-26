<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Gangguan Koneksi BPJS</title>
    <style>
        @page { margin: 10px 15px; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px;
            word-wrap: break-word;
            vertical-align: middle;
        }
        th {
            background-color: #e2e8f0;
            color: #334155;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 10px;
        }
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-danger { color: #ef4444; font-weight: bold; }
        .text-success { color: #22c55e; font-weight: bold; }
        .text-warning { color: #f59e0b; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <table style="border: none; width: 100%; margin-bottom: 5px;">
            <tr style="border: none;">
                <td style="border: none; width: 100px; text-align: center;">
                    @if(isset($getSetting) && $getSetting->logo)
                        <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" width="70" height="70">
                    @endif
                </td>
                <td style="border: none; text-align: center;">
                    <h2>{{ $getSetting->nama_instansi ?? 'NAMA INSTANSI' }}</h2>
                    <p style="margin: 3px 0;">{{ $getSetting->alamat_instansi ?? '' }}, {{ $getSetting->kabupaten ?? '' }}, {{ $getSetting->propinsi ?? '' }}</p>
                    <p style="margin: 0;">{{ $getSetting->kontak ?? '' }} | {{ $getSetting->email ?? '' }}</p>
                </td>
                <td style="border: none; width: 100px;"></td>
            </tr>
        </table>
        <hr style="border: 1px solid #333; margin-bottom: 15px;">

        <h2>LAPORAN RIWAYAT GANGGUAN KONEKSI (DOWNTIME) BPJS DAN SISTEM RUMAH SAKIT</h2>
        <p>Periode: {{ date('d-m-Y', strtotime($tanggal_awal)) }} s/d {{ date('d-m-Y', strtotime($tanggal_akhir)) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Waktu Gangguan</th>
                <th width="15%">Waktu Normal</th>
                <th width="25%">Layanan</th>
                <th width="20%">URL / Host</th>
                <th width="20%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs ?? [] as $i => $log)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center text-danger">{{ $log->waktu_gangguan }}</td>
                    <td class="text-center {!! $log->waktu_normal ? 'text-success' : 'text-warning' !!}">
                        {{ $log->waktu_normal ? $log->waktu_normal : 'Belum Normal' }}
                    </td>
                    <td>{{ $log->service_name }}</td>
                    <td>{{ $log->url }}</td>
                    <td class="text-center">{{ $log->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada catatan gangguan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
