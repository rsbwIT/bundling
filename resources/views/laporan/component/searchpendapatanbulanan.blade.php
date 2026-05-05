<form method="GET">

<!-- 🔹 BARIS 1 -->
<div class="row g-3 mb-2">

    <div class="col-md-4">
        <input type="text"
               name="cariNomor"
               class="form-control"
               placeholder="Cari Nama / RM / No Rawat"
               value="{{ request('cariNomor') }}">
    </div>

    <div class="col-md-2">
        <input type="date"
               name="tgl1"
               class="form-control"
               value="{{ request('tgl1', now()->startOfMonth()->format('Y-m-d')) }}">
    </div>

    <div class="col-md-2">
        <input type="date"
               name="tgl2"
               class="form-control"
               value="{{ request('tgl2', now()->format('Y-m-d')) }}">
    </div>

    <div class="col-md-2">
        <select class="form-control" name="stsLanjut">
            <option value="">Semua</option>
            <option value="Ralan" {{ request('stsLanjut') == 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
            <option value="Ranap" {{ request('stsLanjut') == 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
        </select>
    </div>

</div>

<!-- 🔹 BARIS 2 -->
<div class="row g-3 align-items-end">

    <div class="col-md-2">
        <input type="date"
               name="tgl_bpjs1"
               class="form-control"
               value="{{ request('tgl_bpjs1', now()->startOfMonth()->format('Y-m-d')) }}">
    </div>

    <div class="col-md-2">
        <input type="date"
               name="tgl_bpjs2"
               class="form-control"
               value="{{ request('tgl_bpjs2', now()->format('Y-m-d')) }}">
    </div>

    <div class="row">

    <!-- Ranap -->
    <div class="col-md-4">
        <input type="text"
            id="multi_tanggal_ranap"
            name="multi_tanggal_ranap"
            class="form-control"
            placeholder="Tanggal BPJS Ranap"
            value="{{ request('multi_tanggal_ranap') }}">
    </div>

    <!-- Rajal -->
    <div class="col-md-4">
        <input type="text"
            id="multi_tanggal_ralan"
            name="multi_tanggal_ralan"
            class="form-control"
            placeholder="Tanggal BPJS Rajal"
            value="{{ request('multi_tanggal_ralan') }}">
    </div>

</div>

    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">
            <i class="fa fa-search"></i> Cari
        </button>
    </div>

</div>

{{-- <script>
document.addEventListener("DOMContentLoaded", function () {

    flatpickr("#multi_tanggal", {
        mode: "multiple",
        dateFormat: "Y-m-d",

        // biar tampilannya lebih manusiawi
        altInput: true,
        altFormat: "d M Y",

        // ambil value lama
        defaultDate: "{{ request('multi_tanggal') ? implode(',', explode(',', request('multi_tanggal'))) : '' }}"
    });

});
</script> --}}

<script>
flatpickr("#multi_tanggal_ranap", {
    mode: "multiple",
    dateFormat: "Y-m-d",
    defaultDate: "{{ request('multi_tanggal_ranap') }}"
});

flatpickr("#multi_tanggal_ralan", {
    mode: "multiple",
    dateFormat: "Y-m-d",
    defaultDate: "{{ request('multi_tanggal_ralan') }}"
});
</script>

</form>