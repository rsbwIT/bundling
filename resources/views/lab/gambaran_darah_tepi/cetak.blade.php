<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HASIL PEMERIKSAAN GAMBARAN DARAH TEPI - {{ $permintaan->nm_pasien }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.5cm 1.2cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            font-size: 11pt;
            line-height: 1.35;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .instansi-title {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .instansi-sub {
            font-size: 9pt;
            line-height: 1.25;
        }
        .divider {
            border-bottom: 2px solid #000;
            margin-top: 5px;
            margin-bottom: 14px;
        }
        .document-title {
            text-align: center;
            font-size: 11.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 14px;
            letter-spacing: 0.3px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 1.5px 2px;
            vertical-align: top;
        }
        .result-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 12px;
        }
        .result-table td, .result-table th {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        .result-table td.param-name {
            font-weight: normal;
            width: 25%;
        }
        .result-table td.param-value {
            width: 75%;
            white-space: pre-line;
        }
        .keterangan-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            border: 1px solid #000;
            border-top: none;
            margin-top: -12px;
            margin-bottom: 25px;
        }
        .keterangan-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }
        .ttd-container {
            width: 100%;
            margin-top: 20px;
        }
        .ttd-box {
            float: right;
            width: 220px;
            text-align: center;
            font-size: 10pt;
        }
        .ttd-space {
            height: 65px;
        }
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 9999;
        }
        @media print {
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print">🖨️ Cetak Lembar Hasil</button>

    {{-- KOP SURAT RUMAH SAKIT --}}
    <table class="header-table">
        <tr>
            <td width="90" style="text-align: center;">
                @if(!empty($setting->logo))
                    <img src="data:image/png;base64,{{ base64_encode($setting->logo) }}" width="75" height="75" alt="Logo">
                @endif
            </td>
            <td style="text-align: center; padding-right: 90px;">
                <div class="instansi-title">{{ $setting->nama_instansi ?? 'RUMAH SAKIT BUMI WARAS' }}</div>
                <div class="instansi-sub">
                    {{ $setting->alamat_instansi ?? 'Jln. Wolter Monginsidi no.235, Bandar Lampung' }}<br>
                    {{ $setting->kontak ?? '(0721) 254589' }}<br>
                    E-mail : {{ $setting->email ?? 'www.rsbw.co.id' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- JUDUL DOKUMEN --}}
    <div class="document-title">
        HASIL PEMERIKSAAN LABORATORIUM KLINIK
    </div>

    {{-- IDENTITAS PASIEN (Persis seperti lembar fisik) --}}
    <table class="meta-table">
        <tr>
            <td width="16%">Tanggal</td>
            <td width="2%">:</td>
            <td width="82%">{{ date('d- m- Y', strtotime($hasil->tanggal ?? $permintaan->tgl_permintaan)) }}</td>
        </tr>
        <tr>
            <td>No. Medrec</td>
            <td>:</td>
            <td>{{ $permintaan->no_rkm_medis }}</td>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td>:</td>
            <td style="font-weight: bold;">{{ strtoupper($permintaan->nm_pasien) }}</td>
        </tr>
        <tr>
            <td>Tgl Lahir</td>
            <td>:</td>
            <td>{{ $permintaan->tgl_lahir ? date('d-m-Y', strtotime($permintaan->tgl_lahir)) : '-' }}</td>
        </tr>
        <tr>
            <td>Ruang</td>
            <td>:</td>
            <td>{{ $ruang }}</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $permintaan->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td>Dokter pengirim</td>
            <td>:</td>
            <td>{{ $permintaan->dokter_pengirim ?? '-' }}</td>
        </tr>
    </table>

    {{-- TABEL HASIL PEMERIKSAAN (Persis seperti lembar fisik) --}}
    <table class="result-table">
        <tr>
            <td class="param-name">Eritrosit</td>
            <td class="param-value">{{ $hasil->eritrosit ?? '-' }}</td>
        </tr>
        <tr>
            <td class="param-name">Leukosit</td>
            <td class="param-value">{{ $hasil->leukosit ?? '-' }}</td>
        </tr>
        <tr>
            <td class="param-name">Trombosit</td>
            <td class="param-value">{{ $hasil->trombosit ?? '-' }}</td>
        </tr>
        <tr>
            <td class="param-name" style="padding-bottom: 2px;">
                Kesan<br>
                Kesimpulan
            </td>
            <td class="param-value">
                @if($hasil->kesan)
                    <div>- &nbsp; {{ $hasil->kesan }}</div>
                @endif
                @if($hasil->kesimpulan)
                    <div>- &nbsp; {{ $hasil->kesimpulan }}</div>
                @endif
                @if(!$hasil->kesan && !$hasil->kesimpulan)
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td class="param-name">Saran</td>
            <td class="param-value">{{ $hasil->saran ?? '-' }}</td>
        </tr>
        <tr>
            <td class="param-name">Manual</td>
            <td class="param-value">{{ $hasil->manual_diff ?? '-' }}</td>
        </tr>
    </table>

    {{-- BARIS KETERANGAN BAWAH (WBC, Hb, PLT) --}}
    <table class="keterangan-table">
        <tr>
            <td width="25%" style="border-right: 1px solid #000;">keterangan</td>
            <td colspan="3">{{ $hasil->keterangan_tambahan ?? '' }}</td>
        </tr>
        <tr style="text-align: center; font-weight: bold;">
            <td width="25%">WBC : {{ $hasil->wbc ?? '-' }} /µl</td>
            <td width="35%">Hb: {{ $hasil->hb ?? '-' }}mg%</td>
            <td width="40%">PLT: {{ $hasil->plt ?? '-' }}/µl</td>
        </tr>
    </table>

    {{-- TANDA TANGAN PEMERIKSA DENGAN BARCODE SEPERTI DI FORM FISIO --}}
    @php
        $settingName = $setting->nama_instansi ?? 'RS. BUMI WARAS';
        $kab = $setting->kabupaten ?? 'Bandar Lampung';
        $dokName = $hasil->nm_dokter_pemeriksa ?? 'dr. Muhammad Nur, MSc.SpPK';
        $kdDoc = $hasil->kd_dokter_pemeriksa ?? 'D0000058';
        $tglHasil = $hasil->tanggal ?? ($permintaan->tgl_permintaan ?? date('Y-m-d'));
        $qrText = "Dikeluarkan di {$settingName}, Kabupaten/Kota {$kab} Ditandatangani secara elektronik oleh {$dokName} ID {$kdDoc} {$tglHasil}";
        $qrBase64 = DNS2D::getBarcodePNG($qrText, 'QRCODE');
    @endphp
    <div class="ttd-container">
        <div class="ttd-box">
            <div>Pemeriksa</div>
            <div style="margin: 8px 0;">
                <img src="data:image/png;base64,{{ $qrBase64 }}" width="75" height="75" alt="Barcode TTD Dokter">
            </div>
            <div style="font-weight: bold; text-decoration: underline;">
                {{ $dokName }}
            </div>
        </div>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.focus();
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
