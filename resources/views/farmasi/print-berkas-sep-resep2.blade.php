<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .text-xs {
            font-size: 8px;
            /* Adjust font size for paragraphs */
        }

        .text-xm {
            font-size: 10px;
        }

        .h3 {
            font-size: 18px;
            font-weight: 700;
        }

        .h4 {
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            /* Adjust font size for tables */
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-1 {
            margin-top: 10px;
        }

        .mt-0 {
            margin-top: 0px;
        }

        .mb-0 {
            margin-bottom: 0px;
        }

        .mx-1 {
            margin: 5px 8px;
        }

        .ml-3 {
            margin-left: 15px;
        }

        .ml-4 {
            margin-left: 20px;
        }

        .card-body {
            page-break-after: always;
        }

        .pb-4 {
            padding-bottom: 30px;
        }

        .card-body:last-child {
            page-break-after: auto;
        }
    </style>

<body>
    @if ($jumlahData > 0)
        {{-- BERKAS SEP ============================================================= --}}
        @if ($getSEP)
            <div class="card-body">
                <div class="card p-4 d-flex justify-content-center align-items-center">
                    <table width="700px">
                        <thead>
                            <tr>
                                <th rowspan="2" width="150px"><img src="{{ public_path('img/bpjs.png') }}"
                                        width="150px" class="" alt="">
                                </th>
                                <th class="text-center">
                                    <span class="h3">SURAT ELEGIBILITAS PESERTA</span>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center">
                                    <span class="h3">{{ $getSetting->nama_instansi }}</span>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">
                                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($getSEP->no_sep, 'C39+') }}"
                                        alt="barcode" width="200px" height="30px" />
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <table width="700px">
                        <tr>
                            <td width="144px">No. SEP</td>
                            <td width="250px">: {{ $getSEP->no_sep }}</td>
                            <td width="144px">No. Rawat</td>
                            <td width="150px">: {{ $getSEP->no_rawat }}</td>
                        </tr>
                        <tr>
                            <td>Tgl. SEP</td>
                            <td>: {{ date('d/m/Y', strtotime($getSEP->tglsep)) }}</td>
                            <td>No. Reg</td>
                            <td>: {{ $getSEP->no_reg }}</td>
                        </tr>
                        <tr>
                            <td>No. Kartu</td>
                            <td>: {{ $getSEP->no_kartu }} (MR: {{ $getSEP->nomr }})</td>
                            <td>Peserta</td>
                            <td>: {{ $getSEP->peserta }}</td>
                        </tr>
                        <tr>
                            <td>Nama Peserta</td>
                            <td>: {{ $getSEP->nama_pasien }}</td>
                            <td>Jns Rawat</td>
                            @php
                                $jnsRawat = $getSEP->jnspelayanan == '1' ? 'Rawat Inap' : 'Rawat Jalan';
                            @endphp
                            <td>: {{ $jnsRawat }}
                            </td>
                        </tr>
                        <tr>
                            @php
                                $jnsKunjungan =
                                    $getSEP->tujuankunjungan == 0
                                        ? '-Konsultasi dokter(Pertama)'
                                        : 'Kunjungan Kontrol(ulangan)';
                            @endphp
                            <td>Tgl. Lahir</td>
                            <td>: {{ date('d/m/Y', strtotime($getSEP->tanggal_lahir)) }}
                            </td>
                            <td>Jns. Kunjungan</td>
                            <td class="text-xs">: {{ $jnsKunjungan }}</td>
                        </tr>
                        <tr>
                            @php
                                $Prosedur =
                                    $getSEP->flagprosedur == 0
                                        ? '-Prosedur Tidak Berkelanjutan'
                                        : ($getSEP->flagprosedur == 1
                                            ? '- Prosedur dan Terapi Tidak Berkelanjutan'
                                            : '');
                            @endphp
                            <td style="vertical-align: top;">No. Telpon</td>
                            <td style="vertical-align: top;">: {{ $getSEP->notelep }}</td>
                            <td></td>
                            <td class="text-xs">{{ $Prosedur }}</td>
                        </tr>
                        <tr>
                            <td>Sub/Spesialis</td>
                            <td>: {{ $getSEP->nmpolitujuan }}</td>
                            <td>Poli Perujuk</td>
                            <td>: -</td>
                        </tr>
                        <tr>
                            <td>Dokter</td>
                            <td>: {{ $getSEP->nmdpdjp }}</td>
                            <td>Kls. Hak</td>
                            <td>: Kelas {{ $getSEP->klsrawat }}</td>
                        </tr>
                        <tr>
                            <td>Fasker Perujuk</td>
                            <td>: {{ $getSEP->nmppkrujukan }}</td>
                            <td>Kls. Rawat</td>
                            <td>: {{ $getSEP->klsrawat }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Diagnosa Awal</td>
                            <td>: {{ $getSEP->nmdiagnosaawal }}</td>
                            <td style="vertical-align: top;">Penjamin</td>
                            <td style="vertical-align: top;">: BPJS Kesehatan</td>
                        </tr>
                        <tr>
                            <td>Catatan</td>
                            <td>: {{ $getSEP->catatan }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <table width="700px">
                        <tr>
                            <td width="473px" class="text-xs">
                                *Saya Menyetujui BPJS Kesehatan Menggunakan Informasi Medis Pasien jika
                                diperlukan.
                                <br>
                                *SEP bukan sebagai bukti penjamin peserta <br>
                                Catatan Ke 1 {{ date('Y-m-d H:i:s') }}

                            </td>
                            <td class="text-center" width="220px">
                                <p>Pasien/Keluarga Pasien </p>
                                <div class="barcode">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSEP->nmppkpelayanan . ',' . ' Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getSEP->nama_pasien . ' ID ' . $getSEP->no_kartu . ' ' . $getSEP->tglsep, 'QRCODE') }}"
                                        alt="barcode" width="55px" height="55px" />
                                </div>
                                <p><b>{{ $getSEP->nama_pasien }}</b></p>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
        @else
            {{-- NULL --}}
        @endif

        {{-- BERKAS RESEP ============================================================= --}}
        @if ($getSEP->getResep)
            @foreach ($getSEP->getResep as $item)
                @if ($item->ResepNonracik)
                    <div class="card-body">
                        <div class="card p-4 d-flex justify-content-center align-items-center">
                            <table width="700px">
                                <tr>
                                    <td rowspan="5"> <img
                                            src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                            alt="Girl in a jacket" width="80" height="80">
                                    </td>
                                    <td class="text-center">
                                        <span class="h3">{{ $getSetting->nama_instansi }} </span>
                                    </td>
                                    <td class="text-center" width="100px">
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td class="pr-4 h4">{{ $getSetting->alamat_instansi }} ,
                                        {{ $getSetting->kabupaten }}, {{ $getSetting->propinsi }}</td>
                                </tr>
                                <tr class="text-center">
                                    <td class="pr-4 h4">{{ $getSetting->kontak }}</td>
                                </tr>
                                <tr class="text-center">
                                    <td class="pr-4 h4">{{ $getSetting->email }}</td>
                                </tr>
                            </table>
                            <hr width="700px"
                                style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                            <table width="700px">
                                <tr>
                                    <td width="126px">Nama Pasien</td>
                                    <td>: {{ $item->nm_pasien }}</td>
                                </tr>
                                <tr>
                                    <td>No. R.M</td>
                                    <td>: {{ $item->no_rkm_medis }}</td>
                                </tr>
                                <tr>
                                    <td>No. Rawat</td>
                                    <td>: {{ $item->no_rawat }}</td>
                                </tr>
                                <tr>
                                    <td>Pemberi Resep</td>
                                    <td>: {{ $item->nm_dokter }}</td>
                                </tr>
                                <tr>
                                    <td>No. Resep</td>
                                    <td>: {{ $item->no_resep }}</td>
                                </tr>
                            </table>
                            <hr width="700px"
                                style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                            <table border="1px" width="700px" class="mt-0">
                                <tr>
                                    <td class="text-center" style="background-color: rgb(192, 192, 192)">
                                        <b>RESEP</b>
                                    </td>
                                </tr>
                            </table>
                            <table width="700px" class="mt-1 text-xm" border="1px">
                                @foreach ($item->ResepNonracik as $resepnonracik)
                                    <tr>
                                        <td width="340px">R/ {{ $resepnonracik->nama_brng }}</td>
                                        <td width="180px">
                                            {{ $resepnonracik->jml }} {{ $resepnonracik->satuan }}
                                        </td>
                                        <td width="170px">
                                            S_ _ _ _ _ _{{ $resepnonracik->aturan }}
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach ($item->ResepRacik as $resepracik)
                                    <tr>
                                        <td>
                                            R/ {{ $resepracik->no_racik }}.
                                            {{ $resepracik->nama_racik }}
                                            <table class="ml-3 text-xm" border="0">
                                                @foreach ($resepracik->detailResepRacik as $detailresepracik)
                                                    <tr>
                                                        <td> - {{ $detailresepracik->nama_brng }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                        <td style="vertical-align: top;">
                                            {{ $resepracik->jml_dr }}
                                            {{ $resepracik->metode }}</td>
                                        <td style="vertical-align: top;">
                                            S_ _ _ _ _ _ {{ $resepracik->aturan_pakai }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                            <table width="700px" class="mt-1">
                                <tr>
                                    <td>
                                    </td>
                                    <td class="text-center" width="220px">
                                        Bandar Lampung, {{ $item->tgl_perawatan }}<br>
                                        <div class="barcode mt-1">
                                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $item->nm_dokter . ' ID ' . $item->kd_dokter . ' ' . $item->tgl_perawatan, 'QRCODE') }}"
                                                alt="barcode" width="80px" height="75px" />
                                        </div>
                                        <b class="mt-1">{{ $item->nm_dokter }}</b>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

        {{-- BERKAS BIAYA RESEP ============================================================= --}}
        @if ($getSEP->getResep)
            @foreach ($getSEP->getResep as $item)
                @if ($item->ResepNonracik)
                    <div class="card-body">
                        <div class="card p-4 d-flex justify-content-center align-items-center">
                            <table width="700px">
                                <tr>
                                    <td rowspan="5"> <img
                                            src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                            alt="Girl in a jacket" width="80" height="80">
                                    </td>
                                    <td class="text-center">
                                        <span class="h3">{{ $getSetting->nama_instansi }} </span>
                                    </td>
                                    <td class="text-center" width="100px">
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td class="pr-4 h4">{{ $getSetting->alamat_instansi }} ,
                                        {{ $getSetting->kabupaten }}, {{ $getSetting->propinsi }}</td>
                                </tr>
                                <tr class="text-center">
                                    <td class="pr-4 h4">{{ $getSetting->kontak }}</td>
                                </tr>
                                <tr class="text-center">
                                    <td class="pr-4 h4">{{ $getSetting->email }}</td>
                                </tr>
                            </table>
                            <hr width="700px"
                                style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                            <table width="700px">
                                <tr>
                                    <td width="126px">Nama Pasien</td>
                                    <td>: {{ $item->nm_pasien }}</td>
                                </tr>
                                <tr>
                                    <td>No. R.M</td>
                                    <td>: {{ $item->no_rkm_medis }}</td>
                                </tr>
                                <tr>
                                    <td>No. Rawat</td>
                                    <td>: {{ $item->no_rawat }}</td>
                                </tr>
                                <tr>
                                    <td>Pemberi Resep</td>
                                    <td>: {{ $item->nm_dokter }}</td>
                                </tr>
                                <tr>
                                    <td>No. Resep</td>
                                    <td>: {{ $item->no_resep }}</td>
                                </tr>
                            </table>
                            <hr width="700px"
                                style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                            <table width="700px" class="mt-1 text-xm" border="1px">
                                <tr>
                                    <td class="text-center" colspan="4">
                                        <b>BIAYA NO RESEP {{ $item->no_resep }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-center">Barang</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Jml</th>
                                    <th class="text-center">Total</th>
                                </tr>
                                @php
                                    $totalNonracik = 0;
                                    $totalRacik = 0;
                                @endphp
                                @foreach ($item->ResepNonracik as $resepnonracik)
                                    @php
                                        $totalNonracik += $resepnonracik->total;
                                    @endphp
                                    <tr>
                                        <td width="340px">R/ {{ $resepnonracik->nama_brng }}</td>
                                        <td class="text-right">
                                            {{ number_format($resepnonracik->biaya_obat, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">{{ $resepnonracik->jml }}</td>
                                        <td class="text-right">{{ number_format($resepnonracik->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach ($item->ResepRacik as $resepracik)
                                    @php
                                        $totalRacik += $resepracik->detailResepRacik->sum('total');
                                    @endphp
                                    @foreach ($resepracik->detailResepRacik as $detailresepracik)
                                        <tr>
                                            <td>R/ {{ $detailresepracik->nama_brng }}</td>
                                            <td class="text-right">{{ $detailresepracik->biaya_obat }}</td>
                                            <td class="text-center">{{ $detailresepracik->jml }}</td>
                                            <td class="text-right">{{ $detailresepracik->total }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-right"><b>Total</b></td>
                                    <td class="text-right font-weight-bold">
                                        {{ number_format($totalNonracik + $totalRacik, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
        {{-- ERROR HANDLING ============================================================= --}}
    @else
        <div class="card-body">
            <div class="card p-4 d-flex justify-content-center align-items-center">

            </div>
        </div>
    @endif
</body>

</html>
