@if ($getRadiologi)
    @foreach ($getRadiologi as $item)
        <div class="card-body">
            <div class="card py-3  d-flex justify-content-center align-items-center">
                <table border="0px" width="1000px">
                    <tr>
                        <td rowspan="4"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" alt="Girl in a jacket" width="80" height="80">
                        </td>
                        <td class="text-center">
                            <h4>{{$getSetting->nama_instansi}} </h4>
                        </td>
                    </tr>
                    <tr class="text-center">
                        <td>{{$getSetting->alamat_instansi}} , {{$getSetting->kabupaten}}, {{$getSetting->propinsi}}
                            {{$getSetting->kontak}}</td>
                    </tr>
                    <tr class="text-center">
                        <td> E-mail : {{$getSetting->email}}</td>
                    </tr>
                    <tr class="text-center">
                        <td colspan="">
                            <h5 class="mt-2">HASIL PEMERIKSAAN RADIOLOGI</h5>
                        </td>
                    </tr>
                </table>
                <table border="0px" width="1000px">
                    <tr style="vertical-align: top;">
                        <td width="130px">No.RM</td>
                        <td width="300px">: {{ $item->no_rkm_medis }}</td>
                        <td width="130px">Penanggung Jawab</td>
                        <td width="200px">: {{ $item->nm_dokter_pj }}</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td width="130px">Nama Pasien</td>
                        <td width="300px">: {{ $item->nm_pasien }}</td>
                        <td width="130px">Dokter Pengirim</td>
                        <td width="200px">: {{ $item->nm_dokter }}</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td width="130px">JK/Umur </td>
                        <td width="300px">: {{ $item->jk }} | {{ $item->umur }}</td>
                        <td width="130px">Tgl.Pemeriksaan</td>
                        <td width="200px">:
                            {{ date('d-m-Y', strtotime($item->tgl_periksa)) }}
                        </td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td width="130px">Alamat </td>
                        <td width="300px">: {{ $item->alamat }}</td>
                        <td width="130px">Jam Pemeriksaan</td>
                        <td width="200px">: {{ $item->jam }}</td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td width="130px">No.Periksa </td>
                        <td width="300px">: {{ $item->no_rawat }}</td>
                        @if ($statusLanjut->status_lanjut == 'Ranap')
                            <td width="130px">Kamar </td>
                            <td width="200px">: {{ $item->kd_kamar }} |
                                {{ $item->nm_bangsal }}
                            </td>
                        @else
                            <td width="130px">Poli </td>
                            <td width="200px">: {{ $item->nm_poli }}
                            </td>
                        @endif
                    </tr>
                    <tr style="vertical-align: top;">
                        <td width="130px">Pemeriksaan </td>
                        <td width="300px">: {{ $item->nm_perawatan }}</td>
                        <td width="130px"></td>
                        <td width="200px"></td>
                    </tr>
                </table>
                <table border="0px" width="1000px" class="mt-4">
                    <tr>
                        <td width="500px">
                            {!! nl2br(e($item->hasil)) !!}
                        </td>
                        <td></td>
                    </tr>
                </table>
                <table border="0px" width="1000px" class="mt-3">
                    <tr>
                        <td width="250px" class="text-center">
                            Penanggung Jawab
                            <div class="barcode mt-1">
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di '.$getSetting->nama_instansi.', Kabupaten/Kota '.$getSetting->kabupaten.' Ditandatangani secara elektronik oleh ' . $item->nm_dokter_pj . ' ID ' . $item->kd_dokter_pj . ' ' . $item->tgl_periksa, 'QRCODE') }}"
                                    alt="barcode" width="80px" height="75px" />
                            </div>
                            {{ $item->nm_dokter_pj }}
                        </td>
                        <td width="150px"></td>
                        <td width="250px" class="text-center">
                            Periksa : {{ date('d-m-Y', strtotime($item->tgl_periksa)) }} <br>
                            Petugas Radiologi
                            <div class="barcode mt-1">
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di '.$getSetting->nama_instansi.', Kabupaten/Kota '.$getSetting->kabupaten.' Ditandatangani secara elektronik oleh ' . $item->nama_pegawai . ' ID ' . $item->nip . ' ' . $item->tgl_periksa, 'QRCODE') }}"
                                    alt="barcode" width="80px" height="75px" />
                            </div>
                            {{ $item->nama_pegawai }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
@else
    {{-- NULL --}}
@endif
