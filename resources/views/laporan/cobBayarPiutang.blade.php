@extends('..layout.layoutDashboard')
@section('title', 'COB Bayar Piutang')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('laporan.component.search-bayarPiutang')
            Jumlah Data : {{ count($getCob) }}
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>
            <table class="table table-sm table-bordered table-responsive text-xs mb-3" style="white-space: nowrap;"
                id="tableToCopy">
                <thead>
                    <th>No</th>
                    <th>Tgl. Bayar</th>
                    <th>No. RM</th>
                    <th>No. Rawat</th>
                    <th>Nama Pasien</th>
                    <th>Status Lanjut</th>
                    <th>No. Nota</th>
                    <th>Registrasi</th>
                    <th>Obat+Emb+Tsl</th>
                    <th>Retur Oabt</th>
                    <th>Resep Pulang</th>
                    <th>Paket Tindakan</th>
                    <th>Operasi</th>
                    <th>Laborat</th>
                    <th>Radiologi</th>
                    <th>Tambahan</th>
                    <th>Kamar+Service</th>
                    <th>Potongan</th>
                    <th>Total</th>
                    <th>Penjamin</th>
                    <th>Jumlah</th>
                </thead>
                <tbody>
                    @foreach ($getCob as $key => $item)
                        @php
                            $rowspan = count($item->getDetailCob) + 1;
                        @endphp
                        <tr>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $key + 1 }}</td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $item->tgl_bayar }}</td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $item->no_rkm_medis }}</td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $item->no_rawat }}</td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $item->nm_pasien }}</td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $item->status_lanjut }}
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                @foreach ($item->getNomorNota as $detail)
                                    {{ str_replace(':', '', $detail->nm_perawatan) }}
                                @endforeach
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getRegistrasi->sum('totalbiaya') }}</td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getObat->sum('totalbiaya') }}</td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getReturObat->sum('totalbiaya') }}
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getResepPulang->sum('totalbiaya') }}
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getRalanDokter->sum('totalbiaya') +
                                    $item->getRalanParamedis->sum('totalbiaya') +
                                    $item->getRalanDrParamedis->sum('totalbiaya') +
                                    $item->getRanapDokter->sum('totalbiaya') +
                                    $item->getRanapDrParamedis->sum('totalbiaya') +
                                    $item->getRanapParamedis->sum('totalbiaya') }}
                                <div class="badge-group-sm float-right">
                                    <a data-toggle="dropdown" href="#"><i class="fas fa-eye"></i></a>
                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                        <div class="dropdown-item">
                                            Dokter =
                                            {{ $item->getRalanDokter->sum('totalbiaya') + $item->getRanapDokter->sum('totalbiaya') }}
                                        </div>
                                        <div class="dropdown-item">
                                            Paramedis =
                                            {{ $item->getRalanParamedis->sum('totalbiaya') + $item->getRanapParamedis->sum('totalbiaya') }}
                                        </div>
                                        <div class="dropdown-item">
                                            Dokter Paramedis =
                                            {{ $item->getRalanDrParamedis->sum('totalbiaya') + $item->getRanapDrParamedis->sum('totalbiaya') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getOprasi->sum('totalbiaya') }}
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getLaborat->sum('totalbiaya') }}
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getRadiologi->sum('totalbiaya') }}
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getTambahan->sum('totalbiaya') }}
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getKamarInap->sum('totalbiaya') }}
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                {{ $item->getPotongan->sum('totalbiaya') }}
                            </td>
                            <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">
                                    {{ $item->getRegistrasi->sum('totalbiaya') +
                                        $item->getObat->sum('totalbiaya') +
                                        $item->getReturObat->sum('totalbiaya') +
                                        $item->getResepPulang->sum('totalbiaya') +
                                        $item->getRalanDokter->sum('totalbiaya') +
                                        $item->getRalanParamedis->sum('totalbiaya') +
                                        $item->getRalanDrParamedis->sum('totalbiaya') +
                                        $item->getRanapDokter->sum('totalbiaya') +
                                        $item->getRanapDrParamedis->sum('totalbiaya') +
                                        $item->getRanapParamedis->sum('totalbiaya') +
                                        $item->getOprasi->sum('totalbiaya') +
                                        $item->getLaborat->sum('totalbiaya') +
                                        $item->getRadiologi->sum('totalbiaya') +
                                        $item->getTambahan->sum('totalbiaya') +
                                        $item->getKamarInap->sum('totalbiaya') +
                                        $item->getPotongan->sum('totalbiaya') }}
                            </td>
                        </tr>
                        @foreach ($item->getDetailCob as $cob)
                            <tr>
                                <td class="m-0 py-0 px-2">{{ $cob->png_jawab }}</td>
                                <td class="m-0 p-0  px-2">{{ $cob->totalpiutang }}</td>
                            </tr>
                        @endforeach
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.getElementById("copyButton").addEventListener("click", function() {
            copyTableToClipboard("tableToCopy");
        });

        function copyTableToClipboard(tableId) {
            const table = document.getElementById(tableId);
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            try {
                document.execCommand("copy");
                window.getSelection().removeAllRanges();
                alert("Tabel telah berhasil disalin ke clipboard.");
            } catch (err) {
                console.error("Tidak dapat menyalin tabel:", err);
            }
        }
    </script>
@endsection
