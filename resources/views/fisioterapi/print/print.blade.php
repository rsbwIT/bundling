<!DOCTYPE html>
<html>
<head>
    <title>Print Fisioterapi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size:14px; margin:0; padding:20px; }
        h3, h4 { margin: 5px 0; }
        .header-box { border:1px solid #000; padding:15px; margin-bottom:15px; }
        table { width:100%; border-collapse: collapse; margin-top:10px; table-layout: fixed; }
        th, td { padding:6px; border:1px solid #000; text-align:left; vertical-align:top; word-wrap: break-word; }
        th { background-color: #f0f0f0; }
        .ttd-img { height:50px; width:auto; display:block; margin:auto; }
        .footer-dokter { margin-top:30px; text-align:right; font-size:14px; }
    </style>
</head>
<body>

{{-- KOP SURAT --}}
<div style="width:100%; display:flex; align-items:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:20px;">
    <div style="width:120px; text-align:left;">
        <img src="{{ public_path('img/bw2.png') }}" style="height:80px;">
    </div>
    <div style="flex:1; text-align:center;">
        <div style="font-size:20px; font-weight:bold;">
            {{ $setting->nama_instansi ?? 'NAMA INSTANSI' }}
        </div>
        <div style="font-size:13px; margin-top:2px;">
            {{ $setting->alamat_instansi ?? '' }} <br>
            {{ $setting->kabupaten ?? '' }}, {{ $setting->propinsi ?? '' }} <br>
            Telp: {{ $setting->kontak ?? '-' }} — Email: {{ $setting->email ?? '-' }}
        </div>
    </div>
</div>

{{-- DATA PASIEN --}}
<div class="header-box">
    <table>
        <tr><td><b>No. Rawat</b></td><td>{{ $first->no_rawat }}</td></tr>
        <tr><td><b>No RM</b></td><td>{{ $first->no_rkm_medis }}</td></tr>
        <tr><td><b>Nama Pasien</b></td><td>{{ $first->nm_pasien }}</td></tr>
        <tr><td><b>Lembar</b></td><td>{{ $first->lembar }}</td></tr>
    </table>
</div>

{{-- PROTOKOL TERAPI --}}
<h3>Protokol Terapi</h3>
<div style="display:flex; gap:15px; margin-bottom:20px;">
    <div style="border:1px solid #000; padding:10px; width:33%; font-size:13px;">
        <b>Diagnosa:</b><br>{{ $first->diagnosa }}
    </div>
    <div style="border:1px solid #000; padding:10px; width:33%; font-size:13px;">
        <b>FT:</b><br>{{ $first->ft }}
    </div>
    <div style="border:1px solid #000; padding:10px; width:33%; font-size:13px;">
        <b>ST:</b><br>{{ $first->st }}
    </div>
</div>

{{-- TABEL KUNJUNGAN --}}
<h4>Data Kunjungan</h4>
<table>
    <thead>
        <tr>
            <th style="width:8%;">No</th>
            <th style="width:22%;">Program</th>
            <th style="width:12%;">Tanggal</th>
            <th style="width:20%;">TTD Pasien</th>
            <th style="width:20%;">TTD Dokter</th>
            <th style="width:20%;">TTD Terapis</th>
        </tr>
    </thead>
    <tbody>
        @php
            $namaDokter    = $dokterPJ->nm_dokter ?? '-';
            $kdDokter      = $dokterPJ->kd_dokter ?? '-';
            $tglRegistrasi = $first->tgl_registrasi ?? now()->format('Y-m-d');
        @endphp

        @foreach ($data as $row)
            @if (trim($row->program) != '')
                <tr>
                    <td>{{ $row->kunjungan }}</td>
                    <td>{{ $row->program }}</td>
                    <td>
                        @if ($row->tanggal)
                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($row->ttd_pasien)
                            <img src="{{ storage_path('app/public/ttd/'.$row->ttd_pasien) }}" class="ttd-img">
                        @else - @endif
                    </td>
                    <td>
                        @php
                            $qrText = 'Dikeluarkan di '.$setting->nama_instansi.
                                      ', Kabupaten/Kota '.$setting->kabupaten.
                                      ' Ditandatangani secara elektronik oleh '.$namaDokter.
                                      ' ID '.$kdDokter.
                                      ' '.$tglRegistrasi;
                            $qrBase64 = DNS2D::getBarcodePNG($qrText, 'QRCODE');
                        @endphp
                        @if ($row->ttd_dokter && file_exists(storage_path('app/public/qr_dokter/'.$row->ttd_dokter)))
                            <img src="{{ storage_path('app/public/qr_dokter/'.$row->ttd_dokter) }}" class="ttd-img">
                        @else
                            <img src="data:image/png;base64,{{ $qrBase64 }}" class="ttd-img">
                        @endif
                    </td>
                    <td>
                        @if ($row->ttd_terapis)
                            <img src="{{ storage_path('app/public/ttd/'.$row->ttd_terapis) }}" class="ttd-img">
                        @else - @endif
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>

{{-- FOOTER DOKTER --}}
<div style="text-align:right; margin-top:20px;">
    <p style="margin-bottom:10px;">
        <b>Tanggal Kunjungan Pertama:</b>
        {{ \Carbon\Carbon::parse($tanggalPertama)->format('d-m-Y') }}
    </p>

    <img src="data:image/png;base64,{{ $qrBase64 }}" 
         style="width:100px; height:100px; display:block; margin-left:auto; margin-right:3%;">

    <div class="footer-dokter" style="margin-top:10px; text-align:right;">
        <b>{{ $namaDokter }}</b>
    </div>
</div>

</body>
</html>
