@extends('..layout.layoutDashboard')
@section('title', 'Pasien Meninggal')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="card">
        <div class="card-body">
            <form action="{{ url('/pasien-meninggal') }}" action="POST">
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
                        <th>Jam</th>
                        <th>Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Jenis Kelamin</th>
                        <th>Tmp. Lahir</th>
                        <th>Tgl Lahir</th>
                        <th>G.D.</th>
                        <th>Stts. Nikah</th>
                        <th>Agama</th>
                        <th>Keterangan</th>
                        <th>Tempat Meninggal</th>
                        <th>ICD-X</th>
                        <th>Antara 1</th>
                        <th>Antara 2</th>
                        <th>Langsung</th>
                        <th>Dokter</th>
                        <th></th>
                    </tr>
                    @foreach ($results as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->jam }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->jk }}</td>
                            <td>{{ $item->tmp_lahir }}</td>
                            <td>{{ $item->tgl_lahir }}</td>
                            <td>{{ $item->gol_darah }}</td>
                            <td>{{ $item->stts_nikah }}</td>
                            <td>{{ $item->agama }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td>{{ $item->temp_meninggal }}</td>
                            <td>{{ $item->icd1 }}</td>
                            <td>{{ $item->icd2 }}</td>
                            <td>{{ $item->icd3 }}</td>
                            <td>{{ $item->icd4 }}</td>
                            <td>{{ $item->nm_dokter }}</td>

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
