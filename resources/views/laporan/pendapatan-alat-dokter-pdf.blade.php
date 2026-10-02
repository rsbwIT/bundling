<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jasa Pelayanan Dokter Per Pasien</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        /* No borders in table as requested */
        td { padding: 1px 4px; vertical-align: top; }
        th { border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight: normal; padding: 5px 4px; }
        .header-title { font-weight: bold; margin: 0; }
        .page-break { page-break-after: always; }
        .fw-bold { font-weight: bold; }
        /* A little border top for the total row */
        .total-row td { padding-top: 10px; padding-bottom: 10px; border-top: 1px solid #000; }
    </style>
</head>
<body>
    @php
        $detailsCollection = collect($details);
        // Group by Dokter first
        $groupedByDokter = $detailsCollection->groupBy('nm_dokter');
    @endphp

    @foreach($groupedByDokter as $dokter => $itemsDokter)
        <table width="100%" style="border: none; margin-bottom: 5px;">
            <tr>
                <td width="80" align="center" style="border: none; padding: 0;">
                    @if($setting && $setting->logo)
                        <img src="data:image/jpeg;base64,{{ base64_encode($setting->logo) }}" width="70" height="70">
                    @endif
                </td>
                <td align="center" style="border: none; padding: 0; vertical-align: middle;">
                    <span style="font-size: 13pt; font-weight: bold;">{{ $setting->nama_instansi ?? 'NAMA RS' }}</span><br>
                    <span style="font-size: 8pt;">{{ $setting->alamat_instansi ?? '' }}, {{ $setting->kabupaten ?? '' }}, {{ $setting->propinsi ?? '' }}</span><br>
                    <span style="font-size: 8pt;">{{ $setting->kontak ?? '' }}, E-mail: {{ $setting->email ?? '' }}</span>
                </td>
            </tr>
        </table>
        <div style="border-bottom: 2px solid #000; margin-bottom: 10px;"></div>

        <div class="text-center" style="margin-top: 10px; margin-bottom: 20px;">
            <p class="header-title" style="font-size: 10pt;">JASA PELAYANAN DOKTER PER PASIEN</p>
            <p class="header-title">{{ date('d/F/Y', strtotime($tgl1)) }} s/d {{ date('d/F/Y', strtotime($tgl2)) }}</p>
            <p class="header-title" style="font-size: 10pt;">DOKTER: {{ strtoupper($dokter) }}</p>
        </div>

        @php
            // Group the doctor's items by Kategori
            $groupedByKategori = collect($itemsDokter)->groupBy('kategori');
            $grandTotalDokter = 0;
        @endphp

        @foreach($groupedByKategori as $kategori => $itemsKategori)
            @php
                $subTotalKategori = 0;
            @endphp
            
            <p class="fw-bold" style="margin-bottom: 5px;">Kategori: {{ $kategori }}</p>
            
            <table>
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">TGL TINDAKAN</th>
                        <th class="text-center">NO RAWAT</th>
                        <th class="text-center">NO.RM</th>
                        <th class="text-left">NAMA PASIEN</th>
                        <th class="text-left">TINDAKAN MEDIS</th>
                        <th class="text-right">JASA MEDIS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itemsKategori as $index => $item)
                        @php
                            $subTotalKategori += $item->jasa_medis;
                            $grandTotalDokter += $item->jasa_medis;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($item->tgl_tindakan)) }}</td>
                            <td class="text-center">{{ $item->no_rawat }}</td>
                            <td class="text-center">{{ $item->no_rkm_medis }}</td>
                            <td class="text-left">{{ $item->nm_pasien }}</td>
                            <td class="text-left">{{ $item->nm_perawatan }}</td>
                            <td class="text-right">{{ number_format($item->jasa_medis, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row fw-bold">
                        <td colspan="6" class="text-center">Subtotal {{ $kategori }}</td>
                        <td class="text-right">{{ number_format($subTotalKategori, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endforeach

        <div style="text-align: right; font-weight: bold; font-size: 9pt; margin-top: 10px; margin-bottom: 20px;">
            Total Pendapatan {{ $dokter }}: {{ number_format($grandTotalDokter, 0, ',', '.') }}
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
