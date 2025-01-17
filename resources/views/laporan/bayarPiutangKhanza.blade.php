@extends('..layout.layoutDashboard')
@section('title', 'Bayar Piutang')

@section('konten')
    <div class="card">
        <div class="card-body">
            <form action="{{ url($url) }}">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="text" name="cariNomor" class="form-control form-control-xs"
                                    placeholder="Cari Nama/RM/No Rawat">
                                <div class="input-group-append">
                                </div>
                            </div>
                        </div>
                    </div>
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
                                        <i class="fa fa-search"></i> Cari
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
            <nav aria-label="Page navigation example">
                {{ $bayarPiutang->appends(request()->input())->links('pagination::bootstrap-4') }}
            </nav>
            <table class="table table-sm table-bordered table-striped text-xs mb-3" style="white-space: nowrap;"
                id="tableToCopy">
                <tbody>
                    <tr>
                        <th>No</th>
                        <th>Tgl.Bayar</th>
                        <th>Nama Pasien</th>
                        <th>No.Rawat</th>
                        <th>No. Nota</th>
                        <th>Cicilan(Rp)</th>
                        <th>Keterangan</th>
                        <th>Diskon Bayar(Rp)</th>
                        <th>Tidak Terbayar(Rp)</th>
                        <th>Jenis Bayar</th>
                    </tr>
                    @foreach ($bayarPiutang as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->tgl_bayar }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>
                                @foreach ($item->getNomorNota as $detail)
                                {{ str_replace(':', '', $detail->nm_perawatan) }}
                                @endforeach
                            </td>
                            <td>{{ $item->besar_cicilan }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>{{ $item->diskon_piutang }}</td>
                            <td>{{ $item->tidak_terbayar }}</td>
                            <td>{{ $item->png_jawab }}</td>
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
