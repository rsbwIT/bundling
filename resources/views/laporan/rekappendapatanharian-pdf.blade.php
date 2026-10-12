<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Pendapatan Harian - {{ $statusLanjut == 'Ralan' ? 'Rawat Jalan' : ($statusLanjut == 'SEMUA' ? 'Rawat Inap & Jalan' : 'Rawat Inap') }}</title>
    <style>
        @page {
            @if(($kertas ?? 'letter') === 'legal')
                size: 14in 8.5in;
            @elseif(($kertas ?? 'letter') === 'folio')
                size: 330mm 215mm;
            @else
                size: 11in 8.8in; /* Kertas 8.8 x 11 in - Landscape Full */
            @endif
            margin: 4mm 3mm 4mm 3mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 5.8pt;
            color: #111;
            margin: 0;
            padding: 0;
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.5pt solid #000;
            margin-bottom: 5px;
            padding-bottom: 3px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 1px;
        }

        .title-box {
            text-align: center;
            margin-bottom: 6px;
        }
        .title-box h3 {
            margin: 0;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-box p {
            margin: 1px 0 0 0;
            font-size: 7.2pt;
            color: #333;
        }

        .section-title {
            font-size: 8pt;
            font-weight: bold;
            margin: 6px 0 2px 0;
            color: #111;
            text-transform: uppercase;
        }

        table.rekap-pdf-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 3px;
            font-size: 5.5pt;
        }
        table.rekap-pdf-table th, table.rekap-pdf-table td {
            border: 0.5pt solid #333;
            padding: 2.0pt 0.8pt;
            vertical-align: middle;
        }
        table.rekap-pdf-table thead {
            display: table-header-group;
        }
        table.rekap-pdf-table th {
            background-color: #eaedf0;
            text-align: center;
            font-weight: bold;
            font-size: 4.8pt;
            line-height: 1.1;
            color: #000;
        }
        table.rekap-pdf-table td {
            text-align: right;
            white-space: nowrap;
        }
        table.rekap-pdf-table td.text-center {
            text-align: center;
        }
        table.rekap-pdf-table td.col-total, table.rekap-pdf-table th.col-th-total {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        table.rekap-pdf-table td.col-grand, table.rekap-pdf-table th.col-th-grand {
            background-color: #cfe2ff;
            font-weight: bold;
        }
        table.rekap-pdf-table tfoot {
            font-weight: bold;
            font-size: 4.8pt;
        }
        table.rekap-pdf-table tfoot tr.row-footer-total {
            background-color: #fff3cd;
        }
        table.rekap-pdf-table tfoot tr.row-footer-grand {
            background-color: #cff4fc;
        }
        table.rekap-pdf-table tfoot tr.row-footer-all {
            background-color: #d1e7dd;
            font-weight: bold;
        }
        table.rekap-pdf-table tfoot td.col-grand-all {
            background-color: #a3cfbb;
            font-weight: bold;
            text-align: right;
        }

        .summary-wrapper {
            margin-top: 10px;
            page-break-inside: avoid;
        }
        table.summary-pdf-table {
            width: 320pt;
            border-collapse: collapse;
            font-size: 6.8pt;
        }
        table.summary-pdf-table th, table.summary-pdf-table td {
            border: 0.5pt solid #333;
            padding: 2.5pt 4pt;
        }
        table.summary-pdf-table th {
            background-color: #dbe2e8;
            text-align: center;
            font-weight: bold;
            font-size: 7.5pt;
        }
        table.summary-pdf-table td {
            text-align: right;
        }
        table.summary-pdf-table td.text-left {
            text-align: left;
        }

        .page-break {
            page-break-before: always;
        }
        
        .footer-note {
            margin-top: 5px;
            font-size: 5.8pt;
            color: #666;
            text-align: right;
        }
    </style>
</head>
<body>
    @php
        $bulanIndo = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $bln = date('m', strtotime($tgl1));
        $thn = date('Y', strtotime($tgl1));
        $namaBulan = $bulanIndo[$bln] ?? '';
    @endphp

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            @if($setting && $setting->logo)
            <td style="width: 45pt; text-align: center;">
                <img src="data:image/jpeg;base64,{{ base64_encode($setting->logo) }}" style="height: 38pt;" alt="Logo">
            </td>
            @endif
            <td style="text-align: center;">
                <strong style="font-size: 11pt; letter-spacing: 0.5px;">{{ $setting->nama_instansi ?? 'RUMAH SAKIT' }}</strong><br/>
                <span style="font-size: 7pt;">{{ $setting->alamat_instansi ?? '' }}, {{ $setting->kabupaten ?? '' }}, {{ $setting->propinsi ?? '' }}</span><br/>
                <span style="font-size: 6.8pt; color: #333;">Kontak: {{ $setting->kontak ?? '-' }} | Email: {{ $setting->email ?? '-' }}</span>
            </td>
        </tr>
    </table>

    <div class="title-box">
        <h3>REKAP PENDAPATAN BULAN {{ strtoupper($namaBulan) }} {{ $thn }}</h3>
        <p>
            Jenis Pelayanan: <strong>{{ $statusLanjut == 'Ralan' ? 'Rawat Jalan' : ($statusLanjut == 'SEMUA' ? 'Rawat Inap & Jalan' : 'Rawat Inap') }}</strong>
            | Penjamin: <strong>{{ $penjaminLabel }}</strong>
            | Periode: <strong>{{ date('d/m/Y', strtotime($tgl1)) }} s/d {{ date('d/m/Y', strtotime($tgl2)) }}</strong>
        </p>
    </div>

    {{-- TABEL DATA --}}
    @if($statusLanjut == 'SEMUA')
        <div class="section-title">1. RAWAT INAP (RANAP)</div>
        @include('laporan.partials.table_rekap_pdf', ['dataRekapLocal' => $dataRekapRanap, 'statusLanjutLocal' => 'Ranap'])
        
        <div class="page-break"></div>

        {{-- Header ulang di halaman Rawat Jalan --}}
        <table class="header-table">
            <tr>
                @if($setting && $setting->logo)
                <td style="width: 45pt; text-align: center;">
                    <img src="data:image/jpeg;base64,{{ base64_encode($setting->logo) }}" style="height: 38pt;" alt="Logo">
                </td>
                @endif
                <td style="text-align: center;">
                    <strong style="font-size: 11pt; letter-spacing: 0.5px;">{{ $setting->nama_instansi ?? 'RUMAH SAKIT' }}</strong><br/>
                    <span style="font-size: 7pt;">{{ $setting->alamat_instansi ?? '' }}, {{ $setting->kabupaten ?? '' }}, {{ $setting->propinsi ?? '' }}</span><br/>
                    <span style="font-size: 6.8pt; color: #333;">Kontak: {{ $setting->kontak ?? '-' }} | Email: {{ $setting->email ?? '-' }}</span>
                </td>
            </tr>
        </table>

        <div class="title-box">
            <h3>REKAP PENDAPATAN BULAN {{ strtoupper($namaBulan) }} {{ $thn }}</h3>
            <p>
                Jenis Pelayanan: <strong>Rawat Jalan</strong>
                | Penjamin: <strong>{{ $penjaminLabel }}</strong>
                | Periode: <strong>{{ date('d/m/Y', strtotime($tgl1)) }} s/d {{ date('d/m/Y', strtotime($tgl2)) }}</strong>
            </p>
        </div>

        <div class="section-title">2. RAWAT JALAN (RALAN)</div>
        @php
            $totalSemua = $dataRekapRanap->sum('grand_total') + $dataRekapRalan->sum('grand_total');
        @endphp
        @include('laporan.partials.table_rekap_pdf', [
            'dataRekapLocal' => $dataRekapRalan, 
            'statusLanjutLocal' => 'Ralan',
            'showTotalKeseluruhan' => true,
            'totalKeseluruhan' => $totalSemua
        ])
    @else
        <div class="section-title">{{ $statusLanjut == 'Ralan' ? 'RAWAT JALAN (RALAN)' : 'RAWAT INAP (RANAP)' }}</div>
        @include('laporan.partials.table_rekap_pdf', ['dataRekapLocal' => $dataRekap, 'statusLanjutLocal' => $statusLanjut])
    @endif

    {{-- SUMMARY TABLE --}}
    @php
        $sum_ranap = isset($dataRekapRanap) ? $dataRekapRanap->sum('total') : ($statusLanjut == 'Ranap' ? $dataRekap->sum('total') : 0);
        $sum_ralan = isset($dataRekapRalan) ? $dataRekapRalan->sum('total') : ($statusLanjut == 'Ralan' ? $dataRekap->sum('total') : 0);
        $sum_pj = isset($dataRekapRalan) ? $dataRekapRalan->sum('pj') : ($statusLanjut != 'Ranap' ? $dataRekap->sum('pj') : 0);
        $sum_ekses_ri = isset($dataRekapRanap) ? $dataRekapRanap->sum('ekses') : ($statusLanjut == 'Ranap' ? $dataRekap->sum('ekses') : 0);
        $sum_ekses_rj = isset($dataRekapRalan) ? $dataRekapRalan->sum('ekses') : ($statusLanjut == 'Ralan' ? $dataRekap->sum('ekses') : 0);
        
        $total_pendapatan = $sum_ranap + $sum_ralan + $sum_pj + $sum_ekses_ri + $sum_ekses_rj;
    @endphp

    <div class="summary-wrapper">
        <table class="summary-pdf-table">
            <thead>
                <tr>
                    <th colspan="2">Pendapatan {{ $namaBulan }} {{ $thn }} ({{ $penjaminLabel }})</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-left" style="width: 58%;">Ranap</td>
                    <td>{{ $sum_ranap == 0 ? '-' : number_format($sum_ranap, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-left">Ralan</td>
                    <td>{{ $sum_ralan == 0 ? '-' : number_format($sum_ralan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-left">Pend. Lain</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-left">PJ</td>
                    <td>{{ $sum_pj == 0 ? '-' : number_format($sum_pj, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-left">Ekses RI</td>
                    <td>{{ $sum_ekses_ri == 0 ? '-' : number_format($sum_ekses_ri, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-left">Ekses RJ</td>
                    <td>{{ $sum_ekses_rj == 0 ? '-' : number_format($sum_ekses_rj, 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #ffc107; font-weight: bold;">
                    <td class="text-left">Total Pendapatan {{ $namaBulan }} {{ $thn }} ({{ $penjaminLabel }})</td>
                    <td>{{ number_format($total_pendapatan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-left">Tambahan (+)</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="text-left">(-)</td>
                    <td>-</td>
                </tr>
                <tr style="background-color: #cfe2ff; font-weight: bold;">
                    <td class="text-left">Rekening Bank</td>
                    <td>{{ number_format($total_pendapatan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        <div class="footer-note">
            Dicetak pada: {{ date('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
