@if ($getLaborat)
    @foreach ($getLaborat as $periksa)
        <div class="card-body">
            <div class="">
                <table width="700px">
                    <tr>
                        <td rowspan="4"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                width="80" height="80">
                        </td>
                        <td class="text-center">
                            <h2>{{ $getSetting->nama_instansi }} </h2>
                        </td>
                    </tr>
                    <tr class="text-center">
                        <td>{{ $getSetting->alamat_instansi }} , {{ $getSetting->kabupaten }},
                            {{ $getSetting->propinsi }}
                            {{ $getSetting->kontak }}</td>
                    </tr>
                    <tr class="text-center">
                        <td> {{ $getSetting->email }}</td>
                    </tr>
                    <tr class="text-center">
                        <td colspan="">
                            <h3 class="mt-2">HASIL PEMERIKSAAN LABORATORIUM</h3>
                        </td>
                    </tr>
                </table>
                <table width="700px">
                    <tr style="vertical-align: top;">
                        <td width="115px">No.RM</td>
                        <td width="300px">: {{ $periksa->no_rkm_medis }}</td>
                        <td width="110px">No.Rawat </td>
                        <td width="160px">: {{ $periksa->no_rawat }}</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td>Nama Pasien</td>
                        <td>: {{ $periksa->nm_pasien }}</td>

                        <td>Tgl. Periksa </td>
                        <td>:
                            {{ date('d-m-Y', strtotime($periksa->tgl_periksa)) }}
                        </td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td>JK/Umur </td>
                        <td>: {{ $periksa->jk }} / {{ $periksa->umur }}
                        </td>

                        <td>Jam Periksa </td>
                        <td>: {{ $periksa->jam }}</td>
                        </td>
                    </tr>

                    <tr style="vertical-align: top;">
                        <td>Alamat </td>
                        <td>: {{ $periksa->alamat }}</td>
                        @if ($statusLanjut->status_lanjut == 'Ranap')
                            <td width="130px">Kamar </td>
                            <td width="200px">: {{ $periksa->nm_bangsal }}</td>
                        @else
                            <td width="130px">Poli </td>
                            <td width="200px">: {{ $periksa->nm_poli }}</td>
                        @endif
                    </tr>
                    <tr style="vertical-align: top;">
                        <td> Dokter Pengirim </td>
                        <td>: {{ $periksa->nm_dokter_pj }} </td>
                        <td></td>
                        <td> </td>
                    </tr>
                </table>
                <table border="1px" width="700px" class="mt-1">
                    <tr>
                        <td width="152px" class="text-center">Pemeriksaan</td>
                        <td width="152px" class="text-center">Hasil</td>
                        <td width="100px" class="text-center">Satuan</td>
                        <td width="142px" class="text-center">Nilai Rujukan</td>
                        <td width="140px" class="text-center">Keterangan</td>
                    </tr>
                    @foreach ($periksa->getPeriksaLab as $perawatan)
                        <tr>
                            <td colspan="5">- {{ $perawatan->nm_perawatan }}</td>
                        </tr>
                        @foreach ($perawatan->getDetailLab as $detail)
                            <tr>
                                <td> &emsp;{{ $detail->Pemeriksaan }}</td>
                                <td class="text-center">
                                    {{ $detail->nilai }}
                                </td>
                                <td class="text-center">
                                    {{ $detail->satuan }}
                                </td>
                                <td class="text-center">
                                    {{ $detail->nilai_rujukan }}
                                </td>
                                <td>{{ $detail->keterangan }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>

                <table width="700px" class="mt-1">
                    <tr>
                        <td class="text-xs"><b>Catatan :</b> Jika ada keragu-raguan
                            pemeriksaan,
                            diharapkan
                            segera menghubungi laboratorium</td>
                    </tr>
                </table>

                <table width="700px">
                    <tr>
                        <td width="250px" class="text-center">
                            Penanggung Jawab
                            <div class="barcode mt-1">
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $periksa->nm_dokter . ' ID ' . $periksa->kd_dokter . ' ' . $periksa->tgl_periksa, 'QRCODE') }}"
                                    alt="barcode" width="60px" height="60px" />
                            </div>
                            {{ $periksa->nm_dokter }}
                        </td>
                        <td width="150px"></td>
                        <td width="250px" class="text-center">
                            Hasil : {{ date('d-m-Y', strtotime($periksa->tgl_periksa)) }}
                            <br>
                            Petugas Laboratorium
                            <div class="barcode mt-1">
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $periksa->nama_petugas . ' ID ' . $periksa->nip . ' ' . $periksa->tgl_periksa, 'QRCODE') }}"
                                    alt="barcode" width="60px" height="60px" />
                            </div>
                            {{ $periksa->nama_petugas }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
@else
    {{-- NULL --}}
@endif
