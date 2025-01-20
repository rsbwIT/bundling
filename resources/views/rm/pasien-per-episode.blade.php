@extends('..layout.layoutDashboard')
@section('title', 'Pasien Per Episode')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <form action="{{ url('/pasien-per-episode') }}" action="POST">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="date" name="tgl1" class="form-control form-control-xs"
                                    value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                                <div class="input-group-append">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="date" name="tgl2" class="form-control form-control-xs"
                                    value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                                <div class="input-group-append">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-md btn-primary">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-default float-right" id="copyButton">
                        <i class="fas fa-copy"></i> Copy table
                    </button>
                </div>
            </div>
            <table class="table table-sm table-bordered table-striped table-responsive text-xs" style="white-space: nowrap;"
                id="tableToCopy">
                <tbody>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Nama Poli</th>
                        <th>Status Lanjut</th>
                        <th>Jenis Bayar</th>
                        <th>Dokter</th>
                        <th>Dokter DPJP Ranap</th>
                        <th></th>
                    </tr>
                    @foreach ($results as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->tgl_registrasi }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->nm_poli }}</td>
                            <td>{{ $item->status_lanjut }}</td>
                            <td>{{ $item->kd_pj }}</td>
                            <td>{{ $item->nm_dokter_reg }}</td>
                            <td>{{ $item->nm_dokter_dpjp }}</td>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                {{ $results->appends(request()->input())->links('pagination::bootstrap-4') }}
            </nav>
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
@push('scripts')
    @livewireScripts
@endpush
