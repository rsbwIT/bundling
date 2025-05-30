<form action2="{{ url($action) }}">
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
        {{-- <div class="col-md-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <select class="form-control" name="statusLanjut" id="">
                        <option value="Ralan">Ralan</option>
                        <option value="Ranap">Ranap</option>
                    </select>
                </div>
            </div>
        </div> --}}
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
        <div class="col-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <button type="button"
                        class="btn btn-default form-control form-control-xs d-flex justify-content-between"
                        data-toggle="modal" data-target="#modal-lg2">
                        <p>Pilih Petugas</p>
                        <p><i class="nav-icon fas fa-user-nurse"></i></p>
                    </button>
                </div>
                <div class="modal fade" id="modal-lg2">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Pilih Petugas</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <select multiple="multiple" size="10" name="duallistbox2[]">
                                    @foreach ($petugas as $item)
                                        <option value="{{ $item->nip }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="kdPetugas">
                                <script>
                                    var demo1 = $('select[name="duallistbox2[]"]').bootstrapDualListbox();
                                    $('form').submit(function(e) {
                                        e.preventDefault();
                                        $('input[name="kdPetugas"]').val($('select[name="duallistbox2[]"]').val().join(','));
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
        <div class="col-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <button type="button"
                        class="btn btn-default form-control form-control-xs d-flex justify-content-between"
                        data-toggle="modal" data-target="#modal-lg3">
                        <p>Pilih Dokter</p>
                        <p><i class="nav-icon fas fa-hospital-user"></i></p>
                    </button>
                </div>
                <div class="modal fade" id="modal-lg3">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Pilih Dokter</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <select multiple="multiple" size="10" name="duallistbox3[]">
                                    @foreach ($dokter as $item)
                                        <option value="{{ $item->kd_dokter }}">{{ $item->nm_dokter }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="kdDokter">
                                <script>
                                    var demo1 = $('select[name="duallistbox3[]"]').bootstrapDualListbox();
                                    $('form').submit(function(e) {
                                        e.preventDefault();
                                        $('input[name="kdDokter"]').val($('select[name="duallistbox3[]"]').val().join(','));
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
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <input type="text" name="cariNomor" class="form-control form-control-xs"
                        placeholder="Cari Nama/RM/No Rawat">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
