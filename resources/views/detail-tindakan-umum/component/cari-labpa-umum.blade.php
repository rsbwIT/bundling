<form action="{{ url($action) }}" method="GET">
    <div class="row">

        {{-- PENCARIAN --}}
        <div class="col-md-6">
            <div class="form-group">
                <div class="input-group input-group-sm">
                    <input type="text"
                        name="cariNomor"
                        class="form-control"
                        placeholder="Cari Nama / No RM / No Rawat"
                        value="{{ request('cariNomor') }}">
                </div>
            </div>
        </div>

        {{-- TANGGAL AWAL --}}
        <div class="col-md-2">
            <input type="date"
                name="tgl1"
                class="form-control form-control-sm"
                value="{{ request('tgl1', now()->format('Y-m-d')) }}">
        </div>

        {{-- TANGGAL AKHIR --}}
        <div class="col-md-2">
            <input type="date"
                name="tgl2"
                class="form-control form-control-sm"
                value="{{ request('tgl2', now()->format('Y-m-d')) }}">
        </div>

        {{-- BUTTON --}}
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="fa fa-search"></i> Cari
            </button>
        </div>

    </div>
</form>