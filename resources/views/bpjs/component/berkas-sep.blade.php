@if ($getSEP)
    <div class="card-body">
        <div class="card p-4 d-flex justify-content-center align-items-center">
            <table width="990px" border="0px">
                <thead>
                    <tr>
                        <th rowspan="2" width="250px"><img src="img/bpjs.png" width="250px" class=""
                                alt="">
                        </th>
                        <th class="text-center pr-5">
                            <h4><b>SURAT ELEGIBILITAS PESERTA</h4></b>
                        </th>
                    </tr>
                    <tr>
                        <th class="text-center pr-5">
                            <h5><b>{{ $getSetting->nama_instansi }}</b></h5>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">
                            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($getSEP->no_sep, 'C39+') }}"
                                alt="barcode" width="300px" height="35px" />
                        </th>
                    </tr>
                </thead>
            </table>
            <table width="990px" border="0px">
                <tr>
                    <td width="150px">No. SEP</td>
                    <td width="400px">: {{ $getSEP->no_sep }}</td>
                    <td width="150px">No. Rawat</td>
                    <td width="279px">: {{ $getSEP->no_rawat }}</td>
                </tr>
                <tr>
                    <td>Tgl. SEP</td>
                    <td width="450px">: {{ date('d/m/Y', strtotime($getSEP->tglsep)) }}</td>
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
                    @php
                        switch ($getSEP->klsnaik) {
                            case 1:
                                $kelasRawat = 'VVIP';
                                break;
                            case 2:
                                $kelasRawat = 'VIP';
                                break;
                            case 3:
                                $kelasRawat = 'Kelas 1';
                                break;
                            case 4:
                                $kelasRawat = 'Kelas 2';
                                break;
                            case 5:
                                $kelasRawat = 'Kelas 3';
                                break;
                            case 6:
                                $kelasRawat = 'ICCU';
                                break;
                            case 7:
                                $kelasRawat = 'ICU';
                                break;
                            case 8:
                                $kelasRawat = 'Diatas Kelas 1';
                                break;
                            default:
                                $kelasRawat = 'Tidak Diketahui';
                                break;
                        }
                    @endphp
                    <td>: {{$kelasRawat}}</td>
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
            <table width="990px" border="0px">
                <tr>
                    <td class="text-xs">
                        *Saya Menyetujui BPJS Kesehatan Menggunakan Informasi Medis Pasien jika
                        diperlukan.
                        <br>
                        *SEP bukan sebagai bukti penjamin peserta <br>
                        Catatan Ke 1 {{ date('d/m/Y', strtotime($getSEP->tglsep)) }}

                    </td>
                    <td class="text-center" width="350px">
                        Pasien/Keluarga Pasien <br>
                        <div class="barcode">
                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSEP->nmppkpelayanan . ',' . ' Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getSEP->nama_pasien . ' ID ' . $getSEP->no_kartu . ' ' . $getSEP->tglsep, 'QRCODE') }}"
                                alt="barcode" width="80px" height="75px" />
                        </div>
                        <b>{{ $getSEP->nama_pasien }}</b>
                    </td>
                </tr>
            </table>

        </div>
    </div>
@else
    {{-- NULL --}}
@endif
