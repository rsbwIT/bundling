@if ($getLaborat)
    <div class="card-body">
        @foreach ($getLaborat as $periksa)
            <div class="card-body">
                <div class="card py-3  d-flex justify-content-center align-items-center">
                    <table border="0px" width="1000px">
                        <tr>
                            <td rowspan="4"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                    alt="Girl in a jacket" width="80" height="80">
                            </td>
                            <td class="text-center">
                                <h4>{{ $getSetting->nama_instansi }} </h4>
                            </td>
                        </tr>
                        <tr class="text-center">
                            <td>{{ $getSetting->alamat_instansi }} , {{ $getSetting->kabupaten }},
                                {{ $getSetting->propinsi }}
                                {{ $getSetting->kontak }}</td>
                        </tr>
                        <tr class="text-center">
                            <td> E-mail : {{ $getSetting->email }}</td>
                        </tr>
                        <tr class="text-center">
                            <td colspan="">
                                <h5 class="mt-2">HASIL PEMERIKSAAN LABORATORIUM</h5>
                            </td>
                        </tr>
                    </table>
                    <table border="0px" width="1000px">
                        <tr style="vertical-align: top;">
                            <td width="130px">No.RM</td>
                            <td width="300px">: {{ $periksa->no_rkm_medis }}</td>
                            <td width="130px">No.Rawat </td>
                            <td width="200px">: {{ $periksa->no_rawat }}</td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td width="130px">Nama Pasien</td>
                            <td width="300px">: {{ $periksa->nm_pasien }}</td>

                            <td width="130px">Tgl. Periksa </td>
                            <td width="200px">:
                                {{ date('d-m-Y', strtotime($periksa->tgl_periksa)) }}
                            </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td width="130px">JK/Umur </td>
                            <td width="300px">: {{ $periksa->jk }} / {{ $periksa->umur }}
                            </td>

                            <td width="130px">Jam Periksa </td>
                            <td width="200px">: {{ $periksa->jam }}</td>
                            </td>
                        </tr>

                        <tr style="vertical-align: top;">
                            <td width="130px">Alamat </td>
                            <td width="300px">: {{ $periksa->alamat }}</td>
                            @if ($statusLanjut->status_lanjut == 'Ranap')
                                <td width="130px">Kamar </td>
                                <td width="200px">: {{ $periksa->nm_bangsal }}</td>
                            @else
                                <td width="130px">Poli </td>
                                <td width="200px">: {{ $periksa->nm_poli }}</td>
                            @endif
                        </tr>
                        <tr style="vertical-align: top;">
                            <td width="130px"> Dokter Pengirim </td>
                            <td width="300px">: {{ $periksa->nm_dokter_pj }} </td>
                            <td width="130px"> </td>
                            <td width="200px"></td>
                        </tr>
                    </table>
                    <table border="1px" width="1000px" class="mt-2">
                        <tr>
                            <td width="220px" class="text-center">Pemeriksaan</td>
                            <td width="130px" class="text-center">Hasil</td>
                            <td width="200px" class="text-center">Satuan</td>
                            <td width="200px" class="text-center">Nilai Rujukan</td>
                            <td width="200px" class="text-center">Keterangan</td>
                        </tr>
                        @foreach ($periksa->getPeriksaLab as $perawatan)
                            <tr>
                                <td colspan="5">- {{ $perawatan->nm_perawatan }}</td>
                            </tr>
                            @foreach ($perawatan->getDetailLab as $detail)
                                <tr>
                                    <td width="220px"> &emsp;{{ $detail->Pemeriksaan }}</td>
                                    <td width="130px" class="text-center">
                                        {{ $detail->nilai }}
                                    </td>
                                    <td width="130px" class="text-center">
                                        {{ $detail->satuan }}
                                    </td>
                                    <td width="130px" class="text-center">
                                        {{ $detail->nilai_rujukan }}
                                    </td>
                                    <td width="130px">{{ $detail->keterangan }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </table>

                    <table border="0px" width="1000px" class="mt-2">
                        <tr>
                            <td class="text-xs"><b>Catatan :</b> Jika ada keragu-raguan
                                pemeriksaan,
                                diharapkan
                                segera menghubungi laboratorium</td>
                        </tr>
                    </table>

                    <table border="0px" width="1000px">
                        <tr>
                            <td width="250px" class="text-center">
                                Penanggung Jawab
                                <div class="barcode mt-1">
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $periksa->nm_dokter . ' ID ' . $periksa->kd_dokter . ' ' . $periksa->tgl_periksa, 'QRCODE') }}"
                                        alt="barcode" width="80px" height="75px" />
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
                                        alt="barcode" width="80px" height="75px" />
                                </div>
                                {{ $periksa->nama_petugas }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@else
    {{-- NULL --}}
@endif
