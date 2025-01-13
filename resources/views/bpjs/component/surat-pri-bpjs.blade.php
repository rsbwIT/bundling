@if ($getSuratPriBpjs)
    <div class="card-body">
        <div class="card p-4 d-flex justify-content-center align-items-center">
            <table width="990px" border="0px">
                <tr>
                    <td rowspan="2">
                        <img src="img/bpjs.png" width="250px" class=""
                                alt="">
                    </td>
                    <td class="text-center">
                        <h4><b>SURAT PERINTAH RAWAT INAP</b></h4>
                    </td>
                    <td rowspan="2" width="30%">
                        No. {{ $getSuratPriBpjs->no_surat }} <br>
                        Tgl. {{date('d', strtotime( $getSuratPriBpjs->tgl_surat)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($getSuratPriBpjs->tgl_surat))) }}-{{ date('Y', strtotime($getSuratPriBpjs->tgl_surat)) }}
                    </td>
                </tr>
                <tr>
                    <td class="text-center">
                        <h5><b>{{ $getSetting->nama_instansi }}</b></h5>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($getSuratPriBpjs->no_surat, 'C39+') }}"
                            alt="barcode" width="300px" height="35px" />
                    </td>
                </tr>
            </table>
            <table width="990px" border="0px" class="mt-2">
                <tr>
                    <td>Kepada Yth</td>
                    <td>
                        {{ $getSuratPriBpjs->nm_dokter_bpjs }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        {{ $getSuratPriBpjs->nm_poli_bpjs }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Mohon Pemeriksaan dan Lebih Lanjut :
                    </td>
                </tr>
                <tr>
                    <td>
                        No. Kartu
                    </td>
                    <td>
                        : {{ $getSuratPriBpjs->no_peserta }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Nama Pasien
                    </td>
                    <td>
                        : {{ $getSuratPriBpjs->nm_pasien }} <b>({{ $getSuratPriBpjs->jk }})</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Tgl. Lahir
                    </td>
                    <td>
                        : {{date('d', strtotime( $getSuratPriBpjs->tgl_lahir)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($getSuratPriBpjs->tgl_lahir))) }}-{{ date('Y', strtotime($getSuratPriBpjs->tgl_lahir)) }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Diagnosa Awal
                    </td>
                    <td>
                        : {{ $getSuratPriBpjs->diagnosa }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Tgl. Entri
                    </td>
                    <td>
                        : {{date('d', strtotime( $getSuratPriBpjs->tgl_surat)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($getSuratPriBpjs->tgl_surat))) }}-{{ date('Y', strtotime($getSuratPriBpjs->tgl_surat)) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Demikian atas bantuanya diucapkan terima kasih.
                    </td>
                </tr>
            </table>
            <table width="990px" border="0px">
                <tr>
                    <td class="text-xs">

                    </td>
                    <td class="text-center" width="350px">
                        Mengetahui <br>
                        <div class="barcode">
                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ',' . ' Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getSuratPriBpjs->nm_dokter_bpjs . ' ID ' . $getSuratPriBpjs->kd_dokter . ' ' . $getSuratPriBpjs->tgl_surat, 'QRCODE') }}"
                                alt="barcode" width="80px" height="75px" />
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endif
