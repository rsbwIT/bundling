<form method="GET">

<div class="row mb-3">

    <!-- Cari Nama / RM / No Rawat -->
    <div class="col-md-3">
        <input type="text"
               name="cariNomor"
               class="form-control"
               placeholder="Cari Nama / RM / No Rawat"
               value="{{ request('cariNomor') }}">
    </div>

    <!-- Tanggal utama mulai -->
    <div class="col-md-2">
        <input type="date"
               name="tgl1"
               class="form-control"
               value="{{ request('tgl1', now()->startOfMonth()->format('Y-m-d')) }}">
    </div>

    <!-- Tanggal utama akhir -->
    <div class="col-md-2">
        <input type="date"
               name="tgl2"
               class="form-control"
               value="{{ request('tgl2', now()->format('Y-m-d')) }}">
    </div>

    <!-- Status Lanjut -->
    <div class="col-md-2">
        <select class="form-control" name="stsLanjut">
            <option value="">Semua</option>
            <option value="Ralan" {{ request('stsLanjut') == 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
            <option value="Ranap" {{ request('stsLanjut') == 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
        </select>
    </div>

    <!-- Tanggal khusus BPJS -->
    <div class="col-md-2">
        <input type="date"
               name="tgl_bpjs1"
               class="form-control"
               value="{{ request('tgl_bpjs1', now()->startOfMonth()->format('Y-m-d')) }}"
               placeholder="BPJS mulai">
    </div>

    <div class="col-md-2 mt-2">
        <input type="date"
               name="tgl_bpjs2"
               class="form-control"
               value="{{ request('tgl_bpjs2', now()->format('Y-m-d')) }}"
               placeholder="BPJS akhir">
    </div>

    <!-- Tombol Cari -->
    <div class="col-md-2 mt-2">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-search"></i> Cari
        </button>
    </div>

</div>

</form>