@extends('..layout.layoutDashboard')
@section('title', 'Status Data RM')
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

            {{-- Untuk Mencari Berdasarkan Tanggal --}}
            <form action="{{ url('/status-data-rm') }}" action="POST">
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
                    <div class="col-lg-2">
                        <div class="input-group">
                            <select class="form-control form-control-sm" name="status_lanjut">
                                <option value="Ralan">Rawat Jalan</option>
                                <option value="Ranap">Rawat Inap</option>
                            </select>
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
                        <th>Tgl Registrasi</th>
                        <th>Nama Dokter</th>
                        <th>Rekam Medis</th>
                        <th>Status</th>
                        <th>Nama Pasien</th>
                        <th>Poliklinik</th>
                        <th>Status Lanjut</th>
                        <th>Pemeriksaan Ralan</th>
                        <th>Pemeriksaan Ranap</th>
                        <th>Resume Pasien</th>
                        <th>Resume Pasien Ranap</th>
                        <th>Data Triase IGD</th>
                        <th>Penilaian Awal Keperawatan IGD</th>
                        <th>Diagnosa Pasien</th>
                        <th>Prosedur Pasien</th>
                    </tr>
                    @foreach ($results as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->tgl_registrasi }}</td>
                            <td>{{ $item->nm_dokter }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->stts }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->nm_poli }}</td>
                            <td>{{ $item->status_lanjut }}</td>
                            <td>{{ $item->pemeriksaan_ralan }}</td>
                            <td>{{ $item->pemeriksaan_ranap }}</td>
                            <td>{{ $item->resume_pasien }}</td>
                            <td>{{ $item->resume_pasien_ranap }}</td>
                            <td>{{ $item->data_triase_igd }}</td>
                            <td>{{ $item->penilaian_awal_keperawatan_igd }}</td>
                            <td>{{ $item->diagnosa_pasien }}</td>
                            <td>{{ $item->prosedur_pasien }}</td>

                            </td>

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
@push('scripts')
    @livewireScripts
@endpush
