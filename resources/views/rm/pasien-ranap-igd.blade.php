@extends('..layout.layoutDashboard')
@section('title', 'Pasien Ranap IGD')
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

            <form action="{{ url('/pasien-ranap-igd') }}" action="POST">
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
                        <th>No Rawat</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        <th>Poli</th>
                        <th>Tanggal Periksa</th>
                        <th>Tanggal SOAP</th>
                        <th>Nama User</th>
                        <th>Diagnosa Awal</th>
                        <th>Keluhan</th>
                        <th>Pemeriksaan</th>
                        <th>Kesadaran</th>
                        <th>Suhu Tubuh</th>
                        <th>Tensi</th>
                        <th>Nadi</th>
                        <th>Respirasi</th>
                        <th>Tinggi</th>
                        <th>Berat</th>
                        <th>SPO2</th>
                        <th>GCS</th>
                        <th>Alergi</th>
                        <th>Lingkar Perut</th>
                        <th>Penilaian</th>
                        <th>Instruksi</th>
                        <th>Evaluasi</th>
                        <th>RTL</th>
                        <th></th>
                    </tr>
                    @foreach ($results as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->nomor_rawat }}</td>
                            <td>{{ $item->nomor_rm }}</td>
                            <td>{{ $item->nama_pasien }}</td>
                            <td>{{ $item->poli }}</td>
                            <td>{{ $item->tanggal_registrasi }}</td>
                            <td>{{ $item->tanggal_SOAP }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->diagnosa_awal }}</td>
                            <td>{{ $item->keluhan }}</td>
                            <td>{{ $item->pemeriksaan }}</td>
                            <td>{{ $item->kesadaran }}</td>
                            <td>{{ $item->suhu_tubuh }}</td>
                            <td>{{ $item->tensi }}</td>
                            <td>{{ $item->nadi }}</td>
                            <td>{{ $item->respirasi }}</td>
                            <td>{{ $item->tinggi }}</td>
                            <td>{{ $item->berat }}</td>
                            <td>{{ $item->spo2 }}</td>
                            <td>{{ $item->gcs }}</td>
                            <td>{{ $item->alergi }}</td>
                            <td>{{ $item->lingkar_perut }}</td>
                            <td>{{ $item->penilaian }}</td>
                            <td>{{ $item->instruksi }}</td>
                            <td>{{ $item->evaluasi }}</td>
                            <td>{{ $item->rtl }}</td>
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
