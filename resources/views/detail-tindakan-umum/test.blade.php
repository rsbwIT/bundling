@extends('..layout.layoutDashboard')
@section('title', 'Test Table UMUM')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('detail-tindakan-umum.component.cari-dokter')
            <div class="row no-print mb-2 mt-3">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>
            <table class="table table-sm table-bordered table-striped table-responsive text-xs text-center" style="white-space: nowrap;" id="tableToCopy">
                <thead style="background-color: #b4a7d6; color: black;">
                    <tr>
                        <th rowspan="3" class="align-middle">No</th>
                        <th rowspan="3" class="align-middle">Kode</th>
                        <th rowspan="3" class="align-middle">KODE ID KHANZA</th>
                        <th rowspan="3" class="align-middle">NAMA KHANZA</th>
                        <th rowspan="3" class="align-middle">Nama</th>
                        <th colspan="4" class="align-middle text-center">UMUM</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="align-middle text-center">UMUM</th>
                        <th rowspan="2" class="align-middle">TOTAL</th>
                    </tr>
                    <tr>
                        <th class="align-middle">Rawat Inap</th>
                        <th class="align-middle">Rawat Jalan</th>
                        <th class="align-middle">IGD</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($dataCombined as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->kd_dokter }}</td>
                            <td></td>
                            <td class="text-left"></td>
                            <td class="text-left">{{ $item->nm_dokter }}</td>
                            <td class="text-right">{{ number_format($item->total_ranap) }}</td>
                            <td class="text-right">{{ number_format($item->total_ralan) }}</td>
                            <td class="text-right">{{ number_format($item->total_igd) }}</td>
                            <td class="text-right">{{ number_format($item->grand_total) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">Tidak ada data di rentang waktu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if (isset($dataParamedis) && $dataParamedis->count() > 0)
                <h5 class="mt-4 mb-2"><strong>Paramedis</strong></h5>
                <table class="table table-sm table-bordered table-striped table-responsive text-xs text-center" style="white-space: nowrap;" id="tableParamedis">
                    <thead style="background-color: #a2d5ab; color: black;">
                        <tr>
                            <th rowspan="3" class="align-middle">No</th>
                            <th rowspan="3" class="align-middle">NIP</th>
                            <th rowspan="3" class="align-middle">KODE ID KHANZA</th>
                            <th rowspan="3" class="align-middle">NAMA KHANZA</th>
                            <th rowspan="3" class="align-middle">Nama</th>
                            <th colspan="4" class="align-middle text-center">UMUM</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="align-middle text-center">UMUM</th>
                            <th rowspan="2" class="align-middle">TOTAL</th>
                        </tr>
                        <tr>
                            <th class="align-middle">Rawat Inap</th>
                            <th class="align-middle">Rawat Jalan</th>
                            <th class="align-middle">IGD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $noPr = 1; @endphp
                        @foreach ($dataParamedis as $item)
                            <tr>
                                <td>{{ $noPr++ }}</td>
                                <td>{{ $item->kd_dokter }}</td>
                                <td></td>
                                <td class="text-left"></td>
                                <td class="text-left">{{ $item->nm_dokter }}</td>
                                <td class="text-right">{{ number_format($item->total_ranap) }}</td>
                                <td class="text-right">{{ number_format($item->total_ralan) }}</td>
                                <td class="text-right">{{ number_format($item->total_igd) }}</td>
                                <td class="text-right">{{ number_format($item->grand_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    
    <style>
        /* Agar border tabel terlihat jelas seperti di Excel */
        #tableToCopy thead th,
        #tableParamedis thead th {
            border: 1px solid #777;
        }
    </style>

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
