@extends('..layout.layoutDashboard')
@section('title', 'Bayar Piutang Karyawan')

@section('konten')
    <div class="card">
        <div class="card-body">
            @include('laporan.component.search-bayarPiutangKaryawan')
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>
            <nav aria-label="Page navigation example">
                {{-- {{ $piutangKaryawan->appends(request()->input())->links('pagination::bootstrap-4') }} --}}
            </nav>
            <table class="table table-sm table-bordered table-hover text-xs mb-3" style="white-space: nowrap;"
                id="tableToCopy">
                <tbody class="expandable-body">
                    <tr>
                        <th>No. RM Medis</th>
                        <th>Nama Pasien</th>
                        <th>Nomor Rawat</th>
                        <th>Tanggal Piutang</th>
                        <th>Jenis Jual</th>
                        <th>Uang Muka</th>
                        <th>Sisa Piutang</th>
                        <th>Diskon Piutang</th>
                        <th>Tidak Terbayar</th>
                        <th>Besar Cicilan</th>
                        <th>Catatan</th>
                        <th>Tanggal Bayar</th>
                    </tr>
                    @foreach ($piutangKaryawan as $key => $item)
                        <tr>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->nota_piutang }}</td>
                            <td>{{ $item->tgl_piutang }}</td>
                            <td>{{ $item->jns_jual }}</td>
                            <td>{{ $item->uangmuka }}</td>
                            <td>{{ $item->sisapiutang }}</td>
                            <td>{{ $item->diskon_piutang }}</td>
                            <td>{{ $item->tidak_terbayar }}</td>
                            <td>{{ $item->besar_cicilan }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>{{ $item->tgl_bayar }}</td>
                        </tr>
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
