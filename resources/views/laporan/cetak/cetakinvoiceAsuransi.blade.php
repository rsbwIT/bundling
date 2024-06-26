<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RSBW</title>
    <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="/dist/css/adminlte.min.css" />
    <script type="text/javascript">
        window.onload = function() { window.print(); }
    </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <div class="card py-4  d-flex justify-content-center align-items-center mb-5" style="font-style: italic;">
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
                        <b>Hubungi IT Unuk Melengkapi Nama Asuransi</b>
                    @else
                        {{ $getDetailAsuransi->nama_perusahaan }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    @if ($getDetailAsuransi->alamat_asuransi == '' || $getDetailAsuransi->alamat_asuransi == '-')
                        <b>Hubungi IT Unuk Melengkapi Alamat Asuransi</b>
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
                        <b>Hubungi IT Unuk Melengkapi Nama Asuransi</b>
                    @else
                        {{ $getDetailAsuransi->nama_perusahaan }}
                    @endif dengan perincian sebagai berikut:
                </td>
            </tr>
        </table>
        <table border="0px" width="1000px" class="mt-4">
            <tr>
                <th></th>
                <th></th>
                <th>Nama </th>
                <th>Jumlah Biaya</th>
                <th>No Kwit</th>
                <th>Rm</th>
                <th>Tanggal Rawat</th>
            </tr>
            @if ($getPasien)
                @foreach ($getPasien as $key => $item)
                    <tr>
                        <td width="50px"></td>
                        <td width="15px">{{ $key + 1 }}. </td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td><u>Rp. {{ number_format($item->total_biaya, 0, ',', '.') }}</u> ,</td>
                        <td>
                            @foreach ($item->getNomorNota as $detail)
                                {{ str_replace(':', '', $detail->nm_perawatan) }}
                            @endforeach
                        </td>
                        <td>{{ $item->no_rkm_medis }}</td>
                        <td>
                            @if ($item->tgl_masuk == null && $item->tgl_keluar == null)
                                {{ $item->tgl_byr }}
                            @else
                                {{ date('d', strtotime($item->tgl_masuk)) . ' - ' . date('d', strtotime($item->tgl_keluar)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime($item->tgl_keluar))) }}-{{ date('Y', strtotime($item->tgl_keluar)) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Rp. {{ number_format($getPasien->sum('total_biaya'), 0, ',', '.') }}</b></td>
                </tr>
            @endif
        </table>
        <table border="0px" width="1000px" class="mt-4">
            <tr>
                <td><b>Terbilang : </b>
                    @if ($getPasien)
                        {{ \App\Services\Keuangan\NomorInvoice::Terbilang($getPasien->sum('total_biaya')) }}
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
                    {{ date('d', strtotime( $getListInvoice->tgl_cetak)) }}-{{ \App\Services\BulanRomawi::BulanIndo(date('m', strtotime( $getListInvoice->tgl_cetak))) }}-{{ date('Y', strtotime( $getListInvoice->tgl_cetak)) }}<br />
                    Direktur Utama
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <b> dr. Arief Yulizar, MARS, FISQua</b>
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
                <td>Atas Nama Rumah Sakit Bumi Waras No.Rekening. 0071-488-404</td>
            </tr>
            <tr>
                <td>Di Bank BNI Jl. Kartini Bandar Lampung</td>
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
</body>
</html>
