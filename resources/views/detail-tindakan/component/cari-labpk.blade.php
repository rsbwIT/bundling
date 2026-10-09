<form action="{{ url($action) }}">
    @csrf
    <div class="row">
        <div class="col-md-10">
            <div class="form-group">
                <div class="input-group input-group-xs">
                    <input type="text" name="cariNomor" class="form-control form-control-xs"
                        placeholder="Cari Nama/RM/No Rawat" value="{{ request('cariNomor') }}">
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
            <select class="form-control form-control-xs" name="jenis_pasien">
                <option value="semua" {{ request('jenis_pasien') == 'semua' ? 'selected' : '' }}>Semua Jenis Bayar</option>
                <option value="umum" {{ request('jenis_pasien') == 'umum' ? 'selected' : '' }}>Umum (Tunai)</option>
                <option value="asuransi" {{ request('jenis_pasien') == 'asuransi' ? 'selected' : '' }}>Asuransi / BPJS / Piutang</option>
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-control form-control-xs" name="statusLunas">
                <option value="Lunas" {{ request('statusLunas') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="Belum Lunas" {{ request('statusLunas') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
            </select>
        </div>



        <!-- BUTTON CARI -->
        <div class="col-md-2">
            <button type="submit" class="btn btn-xs btn-primary form-control form-control-xs">
                <i class="fa fa-search"></i> Cari
            </button>
        </div>
    </div>
</form>
