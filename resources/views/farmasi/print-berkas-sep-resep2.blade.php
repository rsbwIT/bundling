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

        .pb-4{
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
<h1>PPPP</h1>
        {{-- ERROR HANDLING ============================================================= --}}
    @else
        <div class="card-body">
            <div class="card p-4 d-flex justify-content-center align-items-center">

            </div>
        </div>
    @endif
</body>

</html>
