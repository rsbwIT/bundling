@if ($getSuratPriBpjs)
<div>
    <table width="700px">
        <tr>
            <td rowspan="2" width="150px">
                <img src="img/bpjs.png" width="150px" class=""
                        alt="">
            </td>
            <td class="text-center">
                <span class="h3">SURAT PERINTAH RAWAT INAP</span>
            </td>
            <td rowspan="2" width="30%">
                No. {{ $getSuratPriBpjs->no_surat }} <br>
                Tgl. {{date('d', strtotime( $getSuratPriBpjs->tgl_surat)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($getSuratPriBpjs->tgl_surat))) }}-{{ date('Y', strtotime($getSuratPriBpjs->tgl_surat)) }}
            </td>
        </tr>
        <tr>
            <td class="text-center">
                <span class="h3">{{ $getSetting->nama_instansi }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($getSuratPriBpjs->no_surat, 'C39+') }}"
                    alt="barcode" width="200px" height="30px" />
            </td>
        </tr>
    </table>
    <table width="700px" class="mt-2">
        <tr>
            <td width="144px">Kepada Yth</td>
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
    <table width="700px">
        <tr>
            <td class="text-xs" width="473px">

            </td>
            <td class="text-center" width="220px">
                Mengetahui <br>
                <div class="barcode">
                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di ' . $getSetting->nama_instansi . ',' . ' Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $getSuratPriBpjs->nm_dokter_bpjs . ' ID ' . $getSuratPriBpjs->kd_dokter . ' ' . $getSuratPriBpjs->tgl_surat, 'QRCODE') }}"
                        alt="barcode" width="55px" height="55px" />
                </div>
            </td>
        </tr>
    </table>
</div>
@endif
