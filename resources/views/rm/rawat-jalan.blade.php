@extends('..layout.layoutDashboard')
@section('title', 'Rawat Jalan')
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
            <form action="{{ url('/rawat-jalan') }}" action="POST">
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
                    <div class="col-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <button type="button"
                                    class="btn btn-default form-control form-control-xs d-flex justify-content-between"
                                    data-toggle="modal" data-target="#modal-lg">
                                    <p>Pilih Penjamin</p>
                                    <p><i class="nav-icon fas fa-credit-card"></i></p>
                                </button>
                            </div>
                            <div class="modal fade" id="modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Pilih Penjamin / Jenis Bayar</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <select multiple="multiple" size="10" name="duallistbox[]">
                                                @foreach ($penjab as $item)
                                                    <option value="{{ $item->kd_pj }}">{{ $item->png_jawab }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="kdPenjamin">
                                            <script>
                                                var demo1 = $('select[name="duallistbox[]"]').bootstrapDualListbox();
                                                $('form').submit(function(e) {
                                                    e.preventDefault();
                                                    $('input[name="kdPenjamin"]').val($('select[name="duallistbox[]"]').val().join(','));
                                                    this.submit();
                                                });
                                            </script>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                        </div>
                                    </div>
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
                        <th>Nomor Rawat</th>
                        <th>Rekam Medis</th>
                        <th>Tanggal Registrasi</th>
                        <th>Nama Pasien</th>
                        <th>Penanggung Jawab</th>
                        <th>Kode Penyakit</th>
                        <th>Nama Dokter</th>
                    </tr>
                    @foreach ($results as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->tgl_registrasi }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->png_jawab }}</td>
                            <td>{{ $item->kd_penyakit }}</td>
                            <td>{{ $item->nm_dokter }} hari</td>
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
