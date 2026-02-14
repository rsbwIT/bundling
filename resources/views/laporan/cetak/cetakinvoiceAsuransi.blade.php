<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSBW</title>
    <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="/dist/css/adminlte.min.css" />
    {{-- <script type="text/javascript">
        window.onload = function() {
            window.print();
        }
    </script> --}}
</head>
<style>
    @media print {
        .print {
            page-break-after: always;
        }
    }
</style>

<body class="hold-transition sidebar-mini layout-fixed">
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="card py-4  d-flex justify-content-center align-items-center mb-5" style="font-style: italic;">
        <div class="print">
            <table border="0px" width="1000px">
                <tr>
                    <td width ="100px">
                        Nomor
                    </td>
                    <td>
                        : {{ $getListInvoice->nomor_tagihan }}
                    </td>
                </tr>
                <tr>
                    <td width ="100px">
                        Lampiran
                    </td>
                    <td>
                        : {{ $getListInvoice->lamiran }}
                    </td>
                </tr>
                <tr>
                    <td width ="100px">
                        Perihal
                    </td>
                    <td>
                        : Tagihan Perawatan dan Pengobatan
                        <b>{{ $getListInvoice->status_lanjut == 'Ranap' ? 'Rawat Inap' : 'Rawat Jalan' }}</b>
                    </td>
                </tr>
            </table>
            <table border="0px" width="1000px" class="mt-4">
                <tr>
                    <td>
                        Kepada Yth,
                    </td>
                </tr>
                <tr>
                    <td>
                        Bagian Klaim
                    </td>
                </tr>
                <tr>
                    <td>
                        @if ($getDetailAsuransi->nama_perusahaan == '' || $getDetailAsuransi->nama_perusahaan == '-')
                            <b>Input dimenu maping asuransi untuk melengkapi nama asuransi</b>
                        @else
                            {{ $getDetailAsuransi->nama_perusahaan }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        @if ($getDetailAsuransi->alamat_asuransi == '' || $getDetailAsuransi->alamat_asuransi == '-')
                            <b>Input dimenu maping asuransi untuk melengkapi alamat asuransi</b>
                        @else
                            @php
                                $text = $getDetailAsuransi->alamat_asuransi;
                                $words = explode(' ', $text);
                                $newText = '';
                                foreach ($words as $key => $word) {
                                    $newText .= $word . ' ';
                                    if (($key + 1) % 7 == 0) {
                                        $newText .= '<br>';
                                    }
                                }
                            @endphp
                            {!! $newText !!}
                        @endif
                    </td>
                </tr>
            </table>
            <table border="0px" width="1000px" class="mt-4">
                <tr>
                    <td>
                        Dengan hormat,
                    </td>
                </tr>
                <tr>
                    <td>
                        Bersama ini kami kirimkan tagihan biaya perawatan dan pengobatan nasabah
                    </td>
                </tr>
                <tr>
                    <td>
                        @if ($getDetailAsuransi->nama_perusahaan == '' || $getDetailAsuransi->nama_perusahaan == '-')
                            <b>Input dimenu maping asuransi untuk melengkapi nama asuransi</b>
                        @else
                            {{ $getDetailAsuransi->nama_perusahaan }}
                        @endif dengan perincian sebagai berikut:
                    </td>
                </tr>
            </table>
            {{-- TEMPLATE --}}
            @if ($template == 'template1')
                @include('laporan.component.cetak-invoice-asuransi.template-cetak1')
            @elseif ($template == 'template2')
                @include('laporan.component.cetak-invoice-asuransi.template-cetak2')
            @elseif ($template == 'template3')
                @include('laporan.component.cetak-invoice-asuransi.template-cetak3')
            @elseif ($template == 'template4')
                @include('laporan.component.cetak-invoice-asuransi.template-cetak4')
            @elseif ($template == 'template5')
                @include('laporan.component.cetak-invoice-asuransi.template-cetak5')
            @elseif ($template == 'template6')
                @include('laporan.component.cetak-invoice-asuransi.template-cetak6')
            @elseif ($template == 'template7')
                {{-- TERLAMPIR --}}
            @endif
            {{-- //TEMPLATE --}}
            <table border="0px" width="1000px" class="mt-4">
                <tr>
                    <td><b>Terbilang : </b>
                        @if ($getPasien)
                            {{ \App\Services\Keuangan\NomorInvoice::Terbilang(
                                $getPasien->sum(function ($item) {
                                    return $item->getTotalBiaya->sum('totalpiutang');
                                }),
                            ) }}
                        @endif rupiah
                    </td>
                </tr>
            </table>
            <table border="0px" width="1000px">
                <tr>
                    <td>
                        Demikian atas perhatian dan kerjasama yang baik kami ucapkan terimakasih.
                    </td>
                </tr>
            </table>
            <table border="0px" width="1000px" class="mt-4">
                <tr>
                    <td>Bandar Lampung,
                        {{ date('d', strtotime($getListInvoice->tgl_cetak)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($getListInvoice->tgl_cetak))) }}-{{ date('Y', strtotime($getListInvoice->tgl_cetak)) }}<br />
                        <b>Direktur Utama</b>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <b> dr. Arief Yulizar, MARS, FISQua.CHAE</b>
                    </td>
                </tr>
            </table>
            <table border="0px" width="1000px" class="mt-4">
                <tr>
                    <td>NB :</td>
                </tr>
                <tr>
                    <td>Mohon pelunasan dilaksanakan melalui transfer ke rekening</td>
                </tr>
                <tr>
                    <td>Atas Nama Rumah Sakit Bumi Waras No.Rekening. {{$getDetailAsuransi->tf_rekening_rs}}</td>
                </tr>
                <tr>
                    <td>Di {{$getDetailAsuransi->nm_tf_rekening_rs}}</td>
                </tr>
                <tr>
                    <td>(Kami mohon rincian data nama-nama pasien yang dibayar harap diemailkan ke</td>
                </tr>
                <tr>
                    <td><a href="#">Admkeuanganrsbumiwaras@yahoo.co.id Atau Wa Ke No 0823-7244-9677 ( Shity
                            )</a></td>
                </tr>
                <tr>
                    <td>Atas perhatian dan kerjasamanya kami ucapkan terima kasih)</td>
                </tr>
                </tr>
            </table>
        </div>
        @if ($template == 'template5')
            <div class="print">
                @include('laporan.component.cetak-invoice-asuransi.template-cetak5-lampiran')
            </div>
        @elseif ($template == 'template7')
            <div class="print">
                @include('laporan.component.cetak-invoice-asuransi.template-cetak7-lampiran')
            </div>
        @endif
    </div>

</body>

</html>
