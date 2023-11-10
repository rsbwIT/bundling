<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pritn Berkas</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
    }

    .text-xs {
        font-size: 8px;
        /* Adjust font size for paragraphs */
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

    .card-body {
        page-break-after: always;
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
                                        width="150px" class="mt-0" alt="">
                                </th>
                                <th class="text-center">
                                    <span class="h3">SURAT ELEGIBILITAS PESERTA</span>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center">
                                    <span class="h3">RS.BUMI WARAS</span>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">
                                    <img class="mt-2"
                                        src="data:image/png;base64,{{ DNS1D::getBarcodePNG($getSEP->no_sep, 'C39+') }}"
                                        alt="barcode" width="200px" height="30px" />
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <table width="700px" class="mt-2">
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
                            <td colspan="">: {{ $getSEP->no_kartu }} (MR: {{ $getSEP->nomr }})</td>
                            <td>Peserta</td>
                            <td>: {{ $getSEP->peserta }}</td>
                        </tr>
                        <tr>
                            <td>Nama Peserta</td>
                            <td>: {{ $getSEP->nama_pasien }}</td>
                            <td>Jns Rawat</td>
                            @php
                                $jnsRawat = $getSEP->status_lanjut == 'Ranap' ? 'Rawat Inap' : 'Rawat Jalan';
                            @endphp
                            <td>: {{ $jnsRawat }}
                            </td>
                        </tr>
                        <tr>
                            @php
                                $jnsKunjungan = $getSEP->tujuankunjungan == 0 ? '-Konsultasi dokter(Pertama)' : 'Kunjungan Kontrol(ulangan)';
                            @endphp
                            <td>Tgl. Lahir</td>
                            <td>: {{ date('d/m/Y', strtotime($getSEP->tanggal_lahir)) }}
                            </td>
                            <td>Jns. Kunjungan</td>
                            <td class="text-xs">: {{ $jnsKunjungan }}</td>
                        </tr>
                        <tr>
                            @php
                                $Prosedur = $getSEP->flagprosedur == 0 ? '-Prosedur Tidak Berkelanjutan' : ($getSEP->flagprosedur == 1 ? '- Prosedur dan Terapi Tidak Berkelanjutan' : '');
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
                            <td>Diagnosa Awal</td>
                            <td>: {{ $getSEP->nmdiagnosaawal }}</td>
                            <td>Penjamin</td>
                            <td>: BPJS Kesehatan</td>
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
                            <td class="text-xs">
                                *Saya Menyetujui BPJS Kesehatan Menggunakan Informasi Medis Pasien jika
                                diperlukan.
                                <br>
                                *SEP bukan sebagai bukti penjamin peserta <br>
                                Catatan Ke 1 {{ date('d/m/Y', strtotime($getSEP->tglsep)) }}

                            </td>
                            <td class="text-center" width="220px">
                                Pasien/Keluarga Pasien <br>
                                <div class="barcode mt-1">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSEP->nmppkpelayanan . ',' . ' Kabupaten/Kota Bandar Lampung Ditandatangani secara elektronik oleh ' . $getSEP->nama_pasien . ' ID ' . $getSEP->no_kartu . ' ' . $getSEP->tglsep, 'QRCODE') }}"
                                        alt="barcode" width="65px" height="65px" />
                                </div>
                                <b class="mt-1">{{ $getSEP->nama_pasien }}</b>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
        @else
            {{-- NULL --}}
        @endif

        {{-- BERKAS FARMASI (RESEP) --}}
        @if ($berkasResep)
            @foreach ($berkasResep as $itemresep)
                <div class="card-body">
                    <div class="card p-4 d-flex justify-content-center align-items-center">
                        <table width="700px">
                            <tr>
                                <td rowspan="5"> <img src="{{ public_path('img/rs.png') }}" alt="Girl in a jacket"
                                        width="90" height="75">
                                </td>
                                <td class="text-center">
                                    <span class="h3">RS. BUMI WARAS </span>
                                </td>
                                <td class="text-center" width="100px">
                                </td>
                            </tr>
                            <tr class="text-center">
                                <td class="pr-4 h4">Jln. Wolter Monginsidi No. 235 , Bandar Lampung, Lampung</td>
                            </tr>
                            <tr class="text-center">
                                <td class="pr-4 h4">(0721)254589</td>
                            </tr>
                            <tr class="text-center">
                                <td class="pr-4 h4">www.rsbumiwaras.co.id</td>
                            </tr>
                        </table>
                        <hr width="700px"
                            style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                        <table width="700px">
                            <tr>
                                <td width="126px">Faktur No</td>
                                <td width="273px">: {{ $itemresep->nota_piutang }}</td>
                                <td width="126px">Nama Pasien</td>
                                <td width="169px">: {{ $itemresep->nm_pasien }}</td>
                            </tr>
                            <tr>
                                <td>Sales</td>
                                <td>: {{ $itemresep->nama_petugas }}</td>
                                <td>Tanggal Piutang</td>
                                <td>: {{ $itemresep->tgl_piutang }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Jatuh Tempo</td>
                                <td>: {{ $itemresep->tgltempo }}</td>
                            </tr>
                        </table>
                        <table width="700px" class="mt-1" border="1px">
                            @php
                                $no = 1;
                                $totalResep = 0;
                                $ongkir = $itemresep->ongkir;
                                $uangmuka = $itemresep->uangmuka;
                            @endphp
                            <tr class="text-center">
                                <td width="21px">No</td>
                                <td width="310px">Barang</td>
                                <td width="104px">Harga</td>
                                <td width="55px">Jml</td>
                                <td width="97px">Diskon</td>
                                <td width="105px">Total</td>
                            </tr>
                            @foreach ($itemresep->detailberkasResep as $itemresep)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>{{ $itemresep->nama_brng }}</td>
                                    <td>{{ number_format($itemresep->h_jual, 0, ',', '.') }}</td>
                                    <td>{{ $itemresep->jumlah }}</td>
                                    <td>{{ $itemresep->dis }}</td>
                                    <td>{{ number_format($itemresep->total, 0, ',', '.') }}</td>
                                </tr>
                                @php
                                    $totalResep += $itemresep->total; // Menambahkan nilai $itemresep->total ke dalam total
                                @endphp
                            @endforeach
                        </table>
                        <table width="700px" class="mt-1">
                            <tr>
                                <td width="21px"></td>
                                <td width="318px"></td>
                                <td width="104px"></td>
                                <td width="55px"></td>
                                <td width="97px">Total Beli:</td>
                                <td width="105px">{{ number_format($totalResep, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td>Ongkir :</td>
                                <td>{{ number_format($ongkir, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td>Uang Muka :</td>
                                <td>{{ number_format($uangmuka, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td><b>TOTAL :</b></td>
                                <td><b>Rp. {{ number_format($totalResep, 0, ',', '.') }}</b></td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endforeach
            @foreach ($berkasResep as $itemresep)
                <div class="card-body">
                    <div class="card p-4 d-flex justify-content-center align-items-center">
                        <table width="700px">
                            <tr>
                                <td rowspan="5"> <img src="{{ public_path('img/rs.png') }}" alt="Girl in a jacket"
                                        width="90" height="75">
                                </td>
                                <td class="text-center">
                                    <span class="h3">RS. BUMI WARAS </span>
                                </td>
                                <td class="text-center" width="100px">
                                </td>
                            </tr>
                            <tr class="text-center">
                                <td class="pr-4 h4">Jln. Wolter Monginsidi No. 235 , Bandar Lampung, Lampung</td>
                            </tr>
                            <tr class="text-center">
                                <td class="pr-4 h4">(0721)254589</td>
                            </tr>
                            <tr class="text-center">
                                <td class="pr-4 h4">www.rsbumiwaras.co.id</td>
                            </tr>
                        </table>
                        <hr width="700px"
                            style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                        <table width="700px">
                            <tr>
                                <td width="126px">Nama Pasien</td>
                                <td>: {{ $itemresep->nm_pasien }}</td>
                            </tr>
                            <tr>
                                <td>No. R.M</td>
                                <td>: {{ $itemresep->no_rkm_medis }}</td>
                            </tr>
                            <tr>
                                <td>No. Rawat</td>
                                <td>: {{ $itemresep->no_rawat }}</td>
                            </tr>
                            <tr>
                                <td>Jenis Bayar</td>
                                <td>: {{ $itemresep->catatan }}</td>
                            </tr>
                            <tr>
                                <td>Pemberi Resep</td>
                                <td>: {{ $itemresep->nm_dokter }}</td>
                            </tr>
                            <tr>
                                <td>No. Resep</td>
                                <td>: {{ $itemresep->no_resep }} </td>
                            </tr>
                        </table>
                        <hr width="700px"
                            style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                        <table border="1px" width="700px">
                            <tr>
                                <td colspan="3" class="text-center"><b>Resep</b></td>
                            </tr>
                            @php
                                $nm_dokter = $itemresep->nm_dokter;
                                $kd_dokter = $itemresep->kd_dokter;
                                $tgl_peresepan = $itemresep->tgl_peresepan;
                            @endphp
                            @foreach ($itemresep->detailberkasResep as $itemresep)
                                <tr>
                                    <td width="340px">R/ {{ $itemresep->nama_brng }}</td>
                                    <td width="180px">{{ $itemresep->jumlah }} {{ $itemresep->satuan }}</td>
                                    <td width="170px">S_ _ _ _ _ _{{ $itemresep->aturan_pakai }}</td>
                                </tr>
                            @endforeach
                        </table>
                        <table width="700px" class="mt-1">
                            <tr>
                                <td></td>
                                <td class="text-center" width="220px">
                                    Pasien/Keluarga Pasien <br>
                                    <div class="barcode mt-1">
                                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di RS. BUMI WARAS, Kabupaten/Kota Bandar Lampung Ditandatangani secara elektronik oleh ' . $nm_dokter . ' ID ' . $kd_dokter . ' ' . $tgl_peresepan, 'QRCODE') }}"
                                            alt="barcode" width="80px" height="75px" />
                                    </div>
                                    <b class="mt-1">{{ $nm_dokter }}</b>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            {{-- NULL --}}
        @endif
        {{-- ERROR HANDLING ============================================================= --}}
    @endif
</body>

</html>
