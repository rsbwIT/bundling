@if ($getSEP)
    <div class="card-body">
        <div class="card p-4 d-flex justify-content-center align-items-center">
            <table width="700px">
                <thead>
                    <tr>
                        <th rowspan="2" width="150px"><img src="{{ public_path('img/bpjs.png') }}" width="150px"
                                class="" alt="">
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
                        $penunjangOptions = [
                            1 => '- Radioterapi',
                            2 => '- Kemoterapi',
                            3 => '- Rehabilitasi Medik',
                            4 => '- Rehabilitasi Psikososial',
                            5 => '- Transfusi Darah',
                            6 => '- Pelayanan Gigi',
                            7 => '- Laboratorium',
                            8 => '- USG',
                            9 => '- Farmasi',
                            10 => '- Lain-Lain',
                            11 => '- MRI',
                            12 => '- HEMODIALISA',
                        ];
                        $flagProsedurOptions = [
                            0 => '- Prosedur Tidak Berkelanjutan',
                            1 => '- Prosedur dan Terapi Berkelanjutan',
                        ];
                        $assesmentPelOptions = [
                            1 => '- Poli spesialis tidak tersedia pada hari sebelumnya',
                            2 => '- Jam Poli telah berakhir pada hari sebelumnya',
                            3 => '- Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya',
                            4 => '- Atas Instruksi RS',
                            5 => '- Tujuan Kontrol',
                        ];
                        $jnsKunjunganOptions = [
                            0 => '- Konsultasi Dokter (Pertama)',
                            1 => '- Kunjungan Kontrol (Ulang)',
                            2 => '- Kunjungan Kontrol (Ulang)',
                        ];
                        $jnsKunjungan = isset($jnsKunjunganOptions[$getSEP->tujuankunjungan])
                            ? $jnsKunjunganOptions[$getSEP->tujuankunjungan]
                            : '';
                        $flagProcedure = isset($flagProsedurOptions[$getSEP->flagprosedur])
                            ? $flagProsedurOptions[$getSEP->flagprosedur]
                            : '';
                        $penunjang = isset($penunjangOptions[$getSEP->penunjang])
                            ? $penunjangOptions[$getSEP->penunjang]
                            : '';
                        $assesment = isset($assesmentPelOptions[$getSEP->asesmenpelayanan])
                            ? $assesmentPelOptions[$getSEP->asesmenpelayanan]
                            : '';
                    @endphp
                    <td>Tgl. Lahir</td>
                    <td>: {{ date('d/m/Y', strtotime($getSEP->tanggal_lahir)) }}
                    </td>
                    <td>Jns. Kunjungan</td>
                    <td class="text-xs">: {{ $jnsKunjungan }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">No. Telpon</td>
                    <td style="vertical-align: top;">: {{ $getSEP->notelep }}</td>
                    <td></td>
                    <td class="text-xs">
                        {{ $flagProcedure }}
                        {{ $penunjang }}
                        {{ $assesment }}
                    </td>
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
                                $kelasRawat = $getSEP->klsrawat;
                                break;
                        }
                    @endphp
                    <td>: {{ $kelasRawat }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Diagnosa Awal</td>
                    <td>: {{ $getSEP->nmdiagnosaawal }}</td>
                    <td style="vertical-align: top;">Penjamin</td>
                    <td style="vertical-align: top;">: {{ $getSEP->png_jawab }}</td>
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
