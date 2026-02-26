<form action="{{ url($action) }}">
    @csrf
    <div class="row">
        <div class="col-md-10">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <input type="text" name="cariNomor" class="form-control form-control-xs"
                        placeholder="Cari Nama/RM/No Rawat">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <input type="date" name="tgl1" class="form-control form-control-xs"
                value="{{ request('tgl1', now()->format('Y-m-d')) }}">
        </div>

        <div class="col-md-2">
            <input type="date" name="tgl2" class="form-control form-control-xs"
                value="{{ request('tgl2', now()->format('Y-m-d')) }}">
        </div>

        <div class="col-md-2">
            <select class="form-control" name="statusLunas">
                <option value="Lunas">Lunas</option>
                <option value="Belum Lunas">Belum Lunas</option>
            </select>
        </div>

        <!-- PENJAMIN -->
        <div class="col-2">
            <button type="button"
                class="btn btn-default form-control form-control-xs d-flex justify-content-between"
                data-toggle="modal" data-target="#modal-lg">
                <p>Pilih Penjamin</p>
                <p><i class="nav-icon fas fa-credit-card"></i></p>
            </button>

            <div class="modal fade" id="modal-lg">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Pilih Penjamin</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <select multiple size="10" name="duallistbox[]">
                                @foreach ($penjab as $item)
                                    <option value="{{ $item->kd_pj }}">
                                        {{ $item->png_jawab }}
                                    </option>
                                @endforeach
                            </select>

                            <input type="hidden" name="kdPenjamin">

                            <script>
                                var demo1 = $('select[name="duallistbox[]"]').bootstrapDualListbox();

                                $('form').submit(function(e) {
                                    e.preventDefault();
                                    $('input[name="kdPenjamin"]').val(
                                        $('select[name="duallistbox[]"]').val()?.join(',')
                                    );
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

        <!-- BUTTON CARI -->
        <div class="col-md-2">
            <button type="submit" class="btn btn-md btn-primary">
                <i class="fa fa-search"></i> Cari
            </button>
        </div>
    </div>
</form>