<form action="{{ url($action) }}" method="GET">
    <div class="row g-2 align-items-end"> {{-- g-2 untuk spacing antar kolom --}}

        {{-- PENCARIAN --}}
        <div class="col-md-6">
            <input type="text"
                name="cariNomor"
                class="form-control form-control-sm"
                placeholder="Cari Nama / No RM / No Rawat"
                value="{{ request('cariNomor') }}">
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

        {{-- BUTTON CARI --}}
        <div class="col-md-2 d-flex">
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="fa fa-search"></i> Cari
            </button>
        </div>

    </div>
</form>