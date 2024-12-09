@if ($getSudartKematian)
    <div class="card-body">
        <table width="700px">
            <tr>
                <td rowspan="3"> <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                        alt="Girl in a jacket" width="80" height="80"></td>
                <td class="text-center">
                    <h2>{{ $getSetting->nama_instansi }} </h2>
                </td>
                <td width="50px"></td>
            </tr>
            <tr class="text-center">
                <td>{{ $getSetting->alamat_instansi }} , {{ $getSetting->kabupaten }},
                    {{ $getSetting->propinsi }}
                    {{ $getSetting->kontak }}</td>
                <td width="50px"></td>
            </tr>
            <tr class="text-center">
                <td> E-mail : {{ $getSetting->email }}</td>
                <td width="50px"></td>
            </tr>
        </table>
        <hr width="700px" class="mt-0"
            style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
        <table width="700px" class="mt-0">
            <tr class="text-center">
                <td colspan="0">
                    <h3>SURAT KEMATIAN</h3>
                </td>
            </tr>
        </table>
        <table width="700px">
            <tr>
                <td width="60px">No.RM</td>
                <td width="120px">: {{ $getSudartKematian->no_rkm_medis }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Lamp.</td>
                <td>: </td>
                <td></td>
            </tr>
        </table>
        <table width="700px" class="mt-1">
            <tr>
                <td>Yang bertanda tangan di bawah ini menerangkan bahwa :</td>
            </tr>
        </table>
        <table width="700px" class="mt-1">
            <tr>
                <td width="30px"></td>
                <td width="80px">Nama </td>
                <td>: {{ $getSudartKematian->nm_pasien }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Umur </td>
                <td>: {{ $getSudartKematian->umur }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Alamat </td>
                <td>: {{ $getSudartKematian->alamat }}</td>
            </tr>
        </table>
        <table width="700px" class="mt-1">
            <tr>
                <td>Telah meninggal dunia pada &emsp;
                    <u>{{ date('d-m-Y', strtotime($getSudartKematian->tanggal)) }}</u> &emsp; Jam
                    &emsp; <u>{{ $getSudartKematian->jam }}</u> di {{ $getSetting->nama_instansi }}
                    dikarenakan
                    {{ $getSudartKematian->keterangan }}
                </td>
            </tr>
            <tr>
                <td>
                    Demikian surat keterangan ini dibuat agar menjadikan maklum dan dapat sebagaimana mestinya
                </td>
            </tr>
        </table>
        <table width="700px" class="mt-1">
            <tr>
                <td width="250px" class="text-center">

                </td>
                <td width="150px"></td>
                <td class="text-center" width="250px">
                    {{ $getSetting->kabupaten }},
                    {{ date('d-m-Y', strtotime($getSudartKematian->tanggal)) }}<br>
                    Dokter Pemeriksa <br>
                    <div class="barcode mt-1">
                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getSudartKematian->nm_dokter . ' ID ' . $getSudartKematian->kd_dokter . ' ' . $getSudartKematian->tanggal, 'QRCODE') }}"
                            alt="barcode" width="60px" height="60px" />
                    </div>
                    {{ $getSudartKematian->nm_dokter }}
                </td>
            </tr>
        </table>
    </div>
@else
    {{-- NULL --}}
@endif
