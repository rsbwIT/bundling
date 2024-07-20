@extends('..layout.layoutDashboard')
@section('title', 'Farmasi')

@section('konten')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Berkas Khanza</h3>
                </div>
                <div class="card-body">
                    @if ($jumlahData > 0)
                        {{-- BERKAS SEP ============================================================= --}}
                        @if ($getSEP)
                            @include('farmasi.component.berkas-sep')

                            @if ($getSEP->getResep)
                                @foreach ($getSEP->getResep as $item)
                                    @if ($item->ResepNonracik)
                                        <div class="card-body">
                                            <div class="card p-4 d-flex justify-content-center align-items-center">
                                                <table border="0px" width="1000px">
                                                    <tr>
                                                        <td rowspan="3"> <img
                                                                src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                                                alt="Girl in a jacket" width="80" height="80"></td>
                                                        <td class="text-center">
                                                            <h4>{{ $getSetting->nama_instansi }} </h4>
                                                        </td>
                                                        <td class="text-center" width="100px">
                                                        </td>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <td>{{ $getSetting->alamat_instansi }} ,
                                                            {{ $getSetting->kabupaten }},
                                                            {{ $getSetting->propinsi }}
                                                            {{ $getSetting->kontak }}</td>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <td> E-mail : {{ $getSetting->email }}</td>
                                                    </tr>
                                                </table>
                                                <hr width="1000px" class="mt-1 mb-1"
                                                    style=" height:2px; border-top:1px solid black; border-bottom:2px solid black;">
                                                <table border="0px" width="1000px">
                                                    <tr style="vertical-align: top;">
                                                        <td width="130px">Nama Pasien</td>
                                                        <td>: {{ $item->nm_pasien }}</td>
                                                    </tr>
                                                    <tr style="vertical-align: top;">
                                                        <td width="130px">No. R.M</td>
                                                        <td>: {{ $item->no_rkm_medis }}</td>
                                                    </tr>
                                                    <tr style="vertical-align: top;">
                                                        <td width="130px">No. Rawat</td>
                                                        <td>: {{ $item->no_rawat }}</td>
                                                    </tr>
                                                    <tr style="vertical-align: top;">
                                                        <td width="130px">Pemberi Resep</td>
                                                        <td>: {{ $item->nm_dokter }}</td>
                                                    </tr>
                                                    <tr style="vertical-align: top;">
                                                        <td width="130px">No. Resep</td>
                                                        <td>: {{ $item->no_resep }}</td>
                                                    </tr>
                                                </table>
                                                <hr width="1000px" class="mt-1 mb-1"
                                                    style=" height:1px; border-top:1px solid black; border-bottom:2px solid black;">
                                                <table border="1px" width="1000px" class="">
                                                    <tr>
                                                        <td class="text-center"
                                                            style="background-color: rgb(192, 192, 192)">
                                                            <b>RESEP</b>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table border="1px" width="1000px" class="">
                                                    @foreach ($item->ResepNonracik as $resepnonracik)
                                                        <tr>
                                                            <td>R/ {{ $resepnonracik->nama_brng }}</td>
                                                            <td>
                                                                {{ $resepnonracik->jml }} {{ $resepnonracik->satuan }}
                                                            </td>
                                                            <td width="250px">
                                                                S_ _ _ _ _ _{{ $resepnonracik->aturan }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    @foreach ($item->ResepRacik as $resepracik)
                                                        <tr>
                                                            <td width="500px">
                                                                R/ {{ $resepracik->no_racik }}.
                                                                {{ $resepracik->nama_racik }}
                                                                <table class="ml-4">
                                                                    @foreach ($resepracik->detailResepRacik as $detailresepracik)
                                                                        <tr>
                                                                            <td> - {{ $detailresepracik->nama_brng }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            </td>
                                                            <td width="250px" style="vertical-align: top;">
                                                                {{ $resepracik->jml_dr }}
                                                                {{ $resepracik->metode }}</td>
                                                            <td width="250px" style="vertical-align: top;">
                                                                S_ _ _ _ _ _ {{ $resepracik->aturan_pakai }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                                <table width="1000px" border="0px" class="mt-1">
                                                    <tr>
                                                        <td>
                                                        </td>
                                                        <td class="text-center" width="350px">
                                                            Bandar Lampung, {{$item->tgl_perawatan}}<br>
                                                            <div class="barcode mt-1">
                                                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di '.$getSetting->nama_instansi.', Kabupaten/Kota ' . $getSetting->kabupaten . ' Ditandatangani secara elektronik oleh ' . $item->nm_dokter . ' ID ' . $item->kd_dokter . ' ' . $item->tgl_perawatan, 'QRCODE') }}"
                                                                    alt="barcode" width="80px" height="75px" />
                                                            </div>
                                                            <b>{{ $item->nm_dokter }}</b>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        @else
                            {{-- NULL --}}
                        @endif
                        {{-- ERROR HANDLING ============================================================= --}}
                    @else
                        <div class="card-body">
                            <div class="card p-4 d-flex justify-content-center align-items-center">

                            </div>
                        </div>
                    @endif
                    <div class="card-footer">
                        <div class="row">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
