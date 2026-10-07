<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Pendapatan Harian</title>
    <style>
        @page { margin: 15px 20px; }
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
        .header img { float: left; width: 70px; height: 70px; margin-right: -70px; }
        .header-content { text-align: center; }
        .header h3, .header p { margin: 2px 0; }
        .table-rekap { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; white-space: nowrap; }
        .table-rekap th, .table-rekap td { border: 1px solid #000; padding: 4px; vertical-align: middle; }
        .table-rekap th { background-color: #f4f6f9; text-align: center; font-weight: bold; text-transform: uppercase; height: 25px; }
        .table-rekap td { text-align: right; }
        .table-rekap td:nth-child(1) { text-align: center; }
        .judul { text-align: center; margin: 15px 0; font-size: 14px; font-weight: bold; }
        .text-right { text-align: right; }
        .mt-3 { margin-top: 15px; }
        .font-weight-bold { font-weight: bold; }
        .table-warning { background-color: #ffeeba; }
        .table-info { background-color: #bee5eb; }
        .table-light { background-color: #fdfdfe; }
        .table-primary { background-color: #b8daff; }
        .text-dark { color: #343a40; }
        .text-center { text-align: center !important; }
    </style>
</head>
<body>
    <div class="header">
        @if($setting->logo)
            <img src="data:image/jpeg;base64,{{ base64_encode($setting->logo) }}" alt="Logo">
        @endif
        <div class="header-content">
            <h3 style="font-size: 16px;">{{ $setting->nama_instansi }}</h3>
            <p style="font-size: 11px;">{{ $setting->alamat_instansi }}, {{ $setting->kabupaten }}, {{ $setting->propinsi }}</p>
            <p style="font-size: 11px;">Kontak: {{ $setting->kontak }} | Email: {{ $setting->email }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    @php
        // Translasi nama bulan ke Bahasa Indonesia
        $bulanIndo = array(
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        );
        $bln = date('m', strtotime($tgl1));
        $thn = date('Y', strtotime($tgl1));
        $namaBulan = $bulanIndo[$bln] ?? '';
    @endphp

    <div class="judul">
        REKAP BULAN {{ strtoupper($namaBulan) }} {{ $thn }}<br>
        <span style="font-weight: normal; font-size: 11px;">Jenis Pelayanan: {{ $statusLanjut == 'Ralan' ? 'Rawat Jalan' : ($statusLanjut == 'SEMUA' ? 'Rawat Inap & Jalan' : 'Rawat Inap') }} | Penjamin: {{ $penjaminLabel }}</span>
    </div>

    @if($statusLanjut == 'SEMUA')
        <h4 style="margin-bottom: 5px;">RAWAT INAP (RANAP)</h4>
        @include('laporan.partials.table_rekap', ['dataRekapLocal' => $dataRekapRanap, 'statusLanjutLocal' => 'Ranap'])
        
        <h4 style="margin-top: 20px; margin-bottom: 5px;">RAWAT JALAN (RALAN)</h4>
        @php
            $totalSemua = $dataRekapRanap->sum('grand_total') + $dataRekapRalan->sum('grand_total');
        @endphp
        @include('laporan.partials.table_rekap', [
            'dataRekapLocal' => $dataRekapRalan, 
            'statusLanjutLocal' => 'Ralan',
            'showTotalKeseluruhan' => true,
            'totalKeseluruhan' => $totalSemua
        ])
    @else
        @include('laporan.partials.table_rekap', ['dataRekapLocal' => $dataRekap, 'statusLanjutLocal' => $statusLanjut])
    @endif
    
    @include('laporan.partials.summary_table')
</body>
</html>
