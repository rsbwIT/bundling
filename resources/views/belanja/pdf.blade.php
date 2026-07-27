<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rencana Belanja</title>
    <style>
        @page { margin: 10px 15px; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5px;
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
            padding: 4px 5px;
            word-wrap: break-word;
            vertical-align: middle;
        }
        th {
            background-color: #e2e8f0;
            color: #334155;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9.5px;
        }
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .badge-danger { color: #ef4444; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <table style="border: none; width: 100%; margin-bottom: 5px;">
            <tr style="border: none;">
                <td style="border: none; width: 100px; text-align: center;">
                    @if($getSetting->logo)
                        <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" width="70" height="70">
                    @endif
                </td>
                <td style="border: none; text-align: center;">
                    <h2>{{ $getSetting->nama_instansi }}</h2>
                    <p style="margin: 3px 0;">{{ $getSetting->alamat_instansi }}, {{ $getSetting->kabupaten }}, {{ $getSetting->propinsi }}</p>
                    <p style="margin: 0;">{{ $getSetting->kontak }} | {{ $getSetting->email }}</p>
                </td>
                <td style="border: none; width: 100px;"></td>
            </tr>
        </table>
        <hr style="border: 1px solid #333; margin-bottom: 20px;">

        <h2>LAPORAN RENCANA BELANJA FARMASI</h2>
        <p>Periode: {{ date('d-m-Y', strtotime($tanggal_awal)) }} s/d {{ date('d-m-Y', strtotime($tanggal_akhir)) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Dasar</th>
                <th>Jual</th>
                <th>Diskon</th>
                <th>Besar<br>Diskon</th>
                <th>Satuan</th>
                <th>Beli</th>
                <th>Stok</th>
                <th>Sblm</th>
                <th>Keluar</th>
                <th>Butuh</th>
                @foreach($selectedBangsal ?? [] as $b)
                    <th>{{ $b->kd_bangsal }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows ?? [] as $i => $row)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $row['kode_brng'] }}</td>
                    <td>{{ $row['nama_brng'] }}</td>
                    <td>{{ $row['kategori_nama'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format($row['harga_beli'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['ralan'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['dis'] ?? 0, 0, ',', '.') }}%</td>
                    <td class="text-right">{{ number_format($row['besardis'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $row['kode_sat'] }}</td>
                    <td class="text-right">{{ number_format($row['jumlah_beli'], 0, ',', '.') }}</td>
                    <td class="text-right text-bold">{{ number_format($row['stok'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['stok_sebelumnya'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['pengeluaran'], 0, ',', '.') }}</td>
                    <td class="text-right text-bold" style="color: {{ $row['kebutuhan'] > 0 ? '#ef4444' : '#333' }}">
                        {{ number_format($row['kebutuhan'], 0, ',', '.') }}
                    </td>
                    @foreach($selectedBangsal ?? [] as $b)
                        <td class="text-right">{{ number_format($stokPerBangsalMap[$row['kode_brng']][$b->kd_bangsal] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="20" class="text-center">Tidak ada data untuk filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
