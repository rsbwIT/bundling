<form action="{{ url($actionCari) }}">
    @csrf
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <input type="text" name="cariNomor" class="form-control form-control-xs"
                        placeholder="Cari Nama/RM/No Rawat" value="{{ request('cariNomor') }}">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <input type="date" name="tgl1" class="form-control form-control-xs"
                        value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <input type="date" name="tgl2" class="form-control form-control-xs"
                        value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                </div>
            </div>
        </div>
        <div class="col-2">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <button type="button"
                        class="btn btn-default form-control form-control-xs d-flex justify-content-between"
                        data-toggle="modal" data-target="#modal-lg-penjab">
                        <p class="mb-0">Pilih Penjamin</p>
                        <p class="mb-0"><i class="nav-icon fas fa-credit-card"></i></p>
                    </button>
                </div>
                <div class="modal fade" id="modal-lg-penjab">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Pilih Penjamin / Asuransi</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <select multiple="multiple" size="10" name="duallistbox_penjab[]">
                                    @foreach ($penjab ?? [] as $item)
                                        <option value="{{ $item->kd_pj }}">{{ $item->png_jawab }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="kdPenjamin" value="{{ request('kdPenjamin') }}">
                                <script>
                                    var demoPenjab = $('select[name="duallistbox_penjab[]"]').bootstrapDualListbox();
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
                        <p class="mb-0">Pilih Dokter</p>
                        <p class="mb-0"><i class="nav-icon fas fa-hospital-user"></i></p>
                    </button>
                </div>
                <div class="modal fade" id="modal-lg2">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Pilih Dokter</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <select multiple="multiple" size="10" name="duallistbox2[]">
                                    @foreach ($dokter as $item)
                                        <option value="{{ $item->kd_dokter }}">{{ $item->nm_dokter }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="kdDokter" value="{{ request('kdDokter') }}">
                                <script>
                                    var demo2 = $('select[name="duallistbox2[]"]').bootstrapDualListbox();
                                    $('form').submit(function(e) {
                                        if ($('select[name="duallistbox_penjab[]"]').length && $('select[name="duallistbox_penjab[]"]').val()) {
                                            $('input[name="kdPenjamin"]').val($('select[name="duallistbox_penjab[]"]').val().join(','));
                                        }
                                        if ($('select[name="duallistbox2[]"]').length && $('select[name="duallistbox2[]"]').val()) {
                                            $('input[name="kdDokter"]').val($('select[name="duallistbox2[]"]').val().join(','));
                                        }
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
