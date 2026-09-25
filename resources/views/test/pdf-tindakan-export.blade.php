<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Tindakan ({{ $labelJenis }}) - {{ $nmDokter }}</title>
    <style>
        @page {
            margin: 12mm 15mm 15mm 15mm;
            size: a4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #000000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 4px;
        }
        .header h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #000000;
        }
        .header p {
            margin: 1px 0;
            font-size: 8pt;
            color: #000000;
        }
        .kop-divider {
            border-top: 2px solid #000000;
            border-bottom: 0.5px solid #000000;
            height: 2px;
            margin: 4px 0 8px 0;
        }
        .doc-title {
            margin: 0 0 6px 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            color: #000000;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .meta-table td {
            border: none;
            padding: 2px 3px;
            font-size: 8.5pt;
            color: #000000;
        }
        .table-sub-title {
            font-size: 9pt;
            font-weight: bold;
            margin: 8px 0 4px 0;
            text-transform: uppercase;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000000;
            padding: 3.5px 5px;
            word-wrap: break-word;
            vertical-align: middle;
            font-size: 8pt;
            color: #000000;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .data-table thead {
            display: table-header-group;
        }
        .data-table tr {
            page-break-inside: avoid;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .footer-subtotal {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .page-break {
            page-break-before: always;
        }
        .grand-total-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .grand-total-table td {
            border: 1px solid #000000;
            padding: 4px 8px;
            font-size: 8.5pt;
            font-weight: bold;
            color: #000000;
        }
    </style>
</head>
<body>

    {{-- ====================================================================== --}}
    {{-- BAGIAN I: TINDAKAN UMUM (RAWAT INAP DAHULU, KEMUDIAN RAWAT JALAN)      --}}
    {{-- ====================================================================== --}}
    @if(in_array('umum', $selectedJenis))
        {{-- Kop Resmi RS --}}
        <div class="header">
            <table style="border: none; width: 100%; margin-bottom: 2px;">
                <tr style="border: none;">
                    <td style="border: none; width: 65px; text-align: center; vertical-align: middle;">
                        @if(isset($getSetting) && $getSetting->logo)
                            <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" width="50" height="50">
                        @endif
                    </td>
                    <td style="border: none; text-align: center; vertical-align: middle;">
                        <h2>{{ $getSetting->nama_instansi ?? 'RUMAH SAKIT' }}</h2>
                        <p>{{ $getSetting->alamat_instansi ?? '' }}, {{ $getSetting->kabupaten ?? '' }}, {{ $getSetting->propinsi ?? '' }}</p>
                        <p>{{ $getSetting->kontak ?? '' }} | {{ $getSetting->email ?? '' }}</p>
                    </td>
                    <td style="border: none; width: 65px;"></td>
                </tr>
            </table>
            <div class="kop-divider"></div>
            <h3 class="doc-title">RINCIAN DETAIL TINDAKAN - UMUM</h3>
        </div>

        {{-- Meta Informasi Umum --}}
        <table class="meta-table">
            <tr>
                <td width="16%" class="font-bold">Nama Dokter/Petugas</td>
                <td width="2%">:</td>
                <td width="47%">{{ $nmDokter }} ({{ $kdDokter }})</td>
                <td width="15%" class="font-bold">Tanggal Cetak</td>
                <td width="2%">:</td>
                <td width="18%">{{ date('d-m-Y H:i') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Periode Tindakan</td>
                <td>:</td>
                <td>{{ date('d-m-Y', strtotime($tanggl1)) }} s/d {{ date('d-m-Y', strtotime($tanggl2)) }}</td>
                <td class="font-bold">Jenis Penjamin</td>
                <td>:</td>
                <td>UMUM</td>
            </tr>
            <tr>
                <td class="font-bold">Total Tindakan Umum</td>
                <td>:</td>
                <td colspan="3">
                    {{ count($detailsRanapUmum) + count($detailsRalanUmum) }} tindakan 
                    (Ranap: {{ count($detailsRanapUmum) }} | Ralan: {{ count($detailsRalanUmum) }})
                </td>
            </tr>
        </table>

        {{-- 1.A. TABEL RAWAT INAP (RANAP) - UMUM --}}
        <div class="table-sub-title">1. Tindakan Rawat Inap (Ranap) - UMUM ({{ count($detailsRanapUmum) }} Tindakan)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="16%">No. Rawat</th>
                    <th width="20%">Nama Pasien</th>
                    <th width="14%">Penanggung Jawab</th>
                    <th width="22%">Nama Tindakan</th>
                    <th width="12%">Sumber</th>
                    <th width="12%" class="text-right">Tarif (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($detailsRanapUmum as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $item->no_rawat }}</td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td>{{ $item->penjamin ?? '-' }}</td>
                        <td>{{ $item->nm_perawatan }}</td>
                        <td>{{ $item->sumber }}</td>
                        <td class="text-right">{{ number_format($item->tarif, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 8px;">Tidak ada data tindakan Rawat Inap (Ranap) Umum.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="footer-subtotal">
                    <td colspan="6" class="text-right font-bold">SUBTOTAL RAWAT INAP (UMUM) :</td>
                    <td class="text-right font-bold">{{ number_format($totalRanapUmum, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- 1.B. TABEL RAWAT JALAN (RALAN) - UMUM --}}
        <div class="table-sub-title">2. Tindakan Rawat Jalan (Ralan) - UMUM ({{ count($detailsRalanUmum) }} Tindakan)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="16%">No. Rawat</th>
                    <th width="20%">Nama Pasien</th>
                    <th width="14%">Penanggung Jawab</th>
                    <th width="22%">Nama Tindakan</th>
                    <th width="12%">Sumber</th>
                    <th width="12%" class="text-right">Tarif (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($detailsRalanUmum as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $item->no_rawat }}</td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td>{{ $item->penjamin ?? '-' }}</td>
                        <td>{{ $item->nm_perawatan }}</td>
                        <td>{{ $item->sumber }}</td>
                        <td class="text-right">{{ number_format($item->tarif, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 8px;">Tidak ada data tindakan Rawat Jalan (Ralan) Umum.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="footer-subtotal">
                    <td colspan="6" class="text-right font-bold">SUBTOTAL RAWAT JALAN (UMUM) :</td>
                    <td class="text-right font-bold">{{ number_format($totalRalanUmum, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- TOTAL KESELURUHAN UMUM --}}
        <table class="grand-total-table" style="margin-top: 5px;">
            <tr style="background-color: #f2f2f2;">
                <td width="70%" class="text-right">TOTAL TINDAKAN UMUM (Ranap: Rp {{ number_format($totalRanapUmum, 0, ',', '.') }} | Ralan: Rp {{ number_format($totalRalanUmum, 0, ',', '.') }}) :</td>
                <td width="30%" class="text-right">Rp {{ number_format($totalUmum, 0, ',', '.') }}</td>
            </tr>
        </table>
    @endif


    {{-- ====================================================================== --}}
    {{-- PINDAH HALAMAN JIKA KEDUA JENIS (UMUM & ASURANSI) DIPILIH               --}}
    {{-- ====================================================================== --}}
    @if(in_array('umum', $selectedJenis) && in_array('asuransi', $selectedJenis))
        <div class="page-break"></div>
    @endif


    {{-- ====================================================================== --}}
    {{-- BAGIAN II: TINDAKAN ASURANSI (RAWAT INAP DAHULU, KEMUDIAN RAWAT JALAN)  --}}
    {{-- ====================================================================== --}}
    @if(in_array('asuransi', $selectedJenis))
        {{-- Kop Resmi RS --}}
        <div class="header">
            <table style="border: none; width: 100%; margin-bottom: 2px;">
                <tr style="border: none;">
                    <td style="border: none; width: 65px; text-align: center; vertical-align: middle;">
                        @if(isset($getSetting) && $getSetting->logo)
                            <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" width="50" height="50">
                        @endif
                    </td>
                    <td style="border: none; text-align: center; vertical-align: middle;">
                        <h2>{{ $getSetting->nama_instansi ?? 'RUMAH SAKIT' }}</h2>
                        <p>{{ $getSetting->alamat_instansi ?? '' }}, {{ $getSetting->kabupaten ?? '' }}, {{ $getSetting->propinsi ?? '' }}</p>
                        <p>{{ $getSetting->kontak ?? '' }} | {{ $getSetting->email ?? '' }}</p>
                    </td>
                    <td style="border: none; width: 65px;"></td>
                </tr>
            </table>
            <div class="kop-divider"></div>
            <h3 class="doc-title">RINCIAN DETAIL TINDAKAN - ASURANSI</h3>
        </div>

        {{-- Meta Informasi Asuransi --}}
        <table class="meta-table">
            <tr>
                <td width="16%" class="font-bold">Nama Dokter/Petugas</td>
                <td width="2%">:</td>
                <td width="47%">{{ $nmDokter }} ({{ $kdDokter }})</td>
                <td width="15%" class="font-bold">Tanggal Cetak</td>
                <td width="2%">:</td>
                <td width="18%">{{ date('d-m-Y H:i') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Periode Tindakan</td>
                <td>:</td>
                <td>{{ date('d-m-Y', strtotime($tanggl1)) }} s/d {{ date('d-m-Y', strtotime($tanggl2)) }}</td>
                <td class="font-bold">Jenis Penjamin</td>
                <td>:</td>
                <td>ASURANSI</td>
            </tr>
            <tr>
                <td class="font-bold">Total Tindakan Asuransi</td>
                <td>:</td>
                <td colspan="3">
                    {{ count($detailsRanapAsuransi) + count($detailsRalanAsuransi) }} tindakan 
                    (Ranap: {{ count($detailsRanapAsuransi) }} | Ralan: {{ count($detailsRalanAsuransi) }})
                </td>
            </tr>
        </table>

        {{-- 2.A. TABEL RAWAT INAP (RANAP) - ASURANSI --}}
        <div class="table-sub-title">1. Tindakan Rawat Inap (Ranap) - ASURANSI ({{ count($detailsRanapAsuransi) }} Tindakan)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="16%">No. Rawat</th>
                    <th width="20%">Nama Pasien</th>
                    <th width="14%">Nama Asuransi / Penjamin</th>
                    <th width="22%">Nama Tindakan</th>
                    <th width="12%">Sumber</th>
                    <th width="12%" class="text-right">Tarif (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($detailsRanapAsuransi as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $item->no_rawat }}</td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td>{{ $item->penjamin ?? '-' }}</td>
                        <td>{{ $item->nm_perawatan }}</td>
                        <td>{{ $item->sumber }}</td>
                        <td class="text-right">{{ number_format($item->tarif, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 8px;">Tidak ada data tindakan Rawat Inap (Ranap) Asuransi.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="footer-subtotal">
                    <td colspan="6" class="text-right font-bold">SUBTOTAL RAWAT INAP (ASURANSI) :</td>
                    <td class="text-right font-bold">{{ number_format($totalRanapAsuransi, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- 2.B. TABEL RAWAT JALAN (RALAN) - ASURANSI --}}
        <div class="table-sub-title">2. Tindakan Rawat Jalan (Ralan) - ASURANSI ({{ count($detailsRalanAsuransi) }} Tindakan)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="16%">No. Rawat</th>
                    <th width="20%">Nama Pasien</th>
                    <th width="14%">Nama Asuransi / Penjamin</th>
                    <th width="22%">Nama Tindakan</th>
                    <th width="12%">Sumber</th>
                    <th width="12%" class="text-right">Tarif (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($detailsRalanAsuransi as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $item->no_rawat }}</td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td>{{ $item->penjamin ?? '-' }}</td>
                        <td>{{ $item->nm_perawatan }}</td>
                        <td>{{ $item->sumber }}</td>
                        <td class="text-right">{{ number_format($item->tarif, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 8px;">Tidak ada data tindakan Rawat Jalan (Ralan) Asuransi.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="footer-subtotal">
                    <td colspan="6" class="text-right font-bold">SUBTOTAL RAWAT JALAN (ASURANSI) :</td>
                    <td class="text-right font-bold">{{ number_format($totalRalanAsuransi, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- TOTAL KESELURUHAN ASURANSI --}}
        <table class="grand-total-table" style="margin-top: 5px;">
            <tr style="background-color: #f2f2f2;">
                <td width="70%" class="text-right">TOTAL TINDAKAN ASURANSI (Ranap: Rp {{ number_format($totalRanapAsuransi, 0, ',', '.') }} | Ralan: Rp {{ number_format($totalRalanAsuransi, 0, ',', '.') }}) :</td>
                <td width="30%" class="text-right">Rp {{ number_format($totalAsuransi, 0, ',', '.') }}</td>
            </tr>
        </table>
    @endif


    {{-- ====================================================================== --}}
    {{-- REKAPITULASI GRAND TOTAL AKHIR (JIKA KEDUA PENJAMIN DITAMPILKAN)        --}}
    {{-- ====================================================================== --}}
    @if(in_array('umum', $selectedJenis) && in_array('asuransi', $selectedJenis))
        <table class="grand-total-table" style="margin-top: 15px;">
            <tr>
                <td width="70%" class="text-right font-bold">TOTAL TINDAKAN UMUM (Ranap: Rp {{ number_format($totalRanapUmum, 0, ',', '.') }} | Ralan: Rp {{ number_format($totalRalanUmum, 0, ',', '.') }}) :</td>
                <td width="30%" class="text-right font-bold">Rp {{ number_format($totalUmum, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-right font-bold">TOTAL TINDAKAN ASURANSI (Ranap: Rp {{ number_format($totalRanapAsuransi, 0, ',', '.') }} | Ralan: Rp {{ number_format($totalRalanAsuransi, 0, ',', '.') }}) :</td>
                <td class="text-right font-bold">Rp {{ number_format($totalAsuransi, 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #e5e5e5;">
                <td class="text-right font-bold" style="font-size: 9pt;">GRAND TOTAL KESELURUHAN (UMUM + ASURANSI) :</td>
                <td class="text-right font-bold" style="font-size: 9pt;">
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    @endif

</body>
</html>
