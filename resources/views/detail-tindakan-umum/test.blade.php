@extends('..layout.layoutDashboard')
@section('title', 'JM Umum')

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
                        <th rowspan="3" class="align-middle">Nama</th>
                        <th colspan="3" class="align-middle text-center">UMUM</th>
                    </tr>
                    <tr>
                        <th colspan="2" class="align-middle text-center">UMUM</th>
                        <th rowspan="2" class="align-middle">TOTAL</th>
                    </tr>
                    <tr>
                        <th class="align-middle">Rawat Inap</th>
                        <th class="align-middle">Rawat Jalan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($mappedTemplate as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->kode_template }}</td>
                            <td>{{ $item->kode_id_khanza }}</td>
                            <td class="text-left">{{ $item->nama_template }}</td>
                            <td class="text-right">{{ number_format($item->total_ranap) }}</td>
                            <td class="text-right">{{ number_format($item->total_ralan) }}</td>
                            <td class="text-right">{{ number_format($item->grand_total) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Tidak ada data di rentang waktu ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot style="background-color: #e9ecef; color: black; font-weight: bold;">
                    <tr>
                        <td colspan="4" class="text-right align-middle">TOTAL KESELURUHAN</td>
                        <td class="text-right">{{ number_format($mappedTemplate->sum('total_ranap')) }}</td>
                        <td class="text-right">{{ number_format($mappedTemplate->sum('total_ralan')) }}</td>
                        <td class="text-right">{{ number_format($mappedTemplate->sum('grand_total')) }}</td>
                    </tr>
                </tfoot>
            </table>


    </div>
    
    <style>
        /* Agar border tabel terlihat jelas seperti di Excel */
        /* Agar border tabel terlihat jelas seperti di Excel */
        #tableToCopy thead th {
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
