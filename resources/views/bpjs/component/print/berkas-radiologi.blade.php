@if ($getRadiologi)
    @foreach ($getRadiologi as $item)
        <div class="card-body">
            <table width="700px">
                <tr>
                    <td rowspan="4"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                            width="80" height="80">
                    </td>
                    <td class="text-center">
                        <h2>{{ $getSetting->nama_instansi }}</h2>
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
                        <h3 class="mt-2">HASIL PEMERIKSAAN RADIOLOGI</h3>
                    </td>
                </tr>
            </table>
            <table width="700px">
                <tr style="vertical-align: top;">
                    <td width="115px">No.RM</td>
                    <td width="300px">: {{ $item->no_rkm_medis }}</td>
                    <td width="110px">Penanggung Jawab</td>
                    <td width="160px">: {{ $item->nm_dokter_pj }}</td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>Nama Pasien</td>
                    <td>: {{ $item->nm_pasien }}</td>
                    <td>Dokter Pengirim</td>
                    <td>: {{ $item->nm_dokter }}</td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>JK/Umur </td>
                    <td>: {{ $item->jk }} | {{ $item->umur }}</td>
                    <td>Tgl.Pemeriksaan</td>
                    <td>:
                        {{ date('d-m-Y', strtotime($item->tgl_periksa)) }}
                    </td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>Alamat </td>
                    <td>: {{ $item->alamat }}</td>
                    <td>Jam Pemeriksaan</td>
                    <td>: {{ $item->jam }}</td>
                </tr>
                <tr style="vertical-align: top;">
                    <td>No.Periksa </td>
                    <td>: {{ $item->no_rawat }}</td>
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
                    <td>Pemeriksaan </td>
                    <td>: {{ $item->nm_perawatan }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <table width="700px" class="mt-1">
                <tr>
                    <td width="500px">
                        {!! nl2br(e($item->hasil)) !!}
                    </td>
                    <td></td>
                </tr>
            </table>
            <table width="700px" class="mt-1">
                <tr>
                    <td width="250px" class="text-center">
                        Penanggung Jawab
                        <div class="barcode mt-1">
                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $item->nm_dokter_pj . ' ID ' . $item->kd_dokter_pj . ' ' . $item->tgl_periksa, 'QRCODE') }}"
                                alt="barcode" width="60px" height="60px" />
                        </div>
                        {{ $item->nm_dokter_pj }}
                    </td>
                    <td width="150px"></td>
                    <td width="250px" class="text-center">
                        Periksa : {{ date('d-m-Y', strtotime($item->tgl_periksa)) }} <br>
                        Petugas Laboratorium
                        <div class="barcode mt-1">
                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $item->nama_pegawai . ' ID ' . $item->nip . ' ' . $item->tgl_periksa, 'QRCODE') }}"
                                alt="barcode" width="60px" height="60px" />
                        </div>
                        {{ $item->nama_pegawai }}
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
@else
    {{-- NULL --}}
@endif
