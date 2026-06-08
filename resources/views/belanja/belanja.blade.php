```blade
@extends('layout.layoutDashboard')

@section('title', 'Rencana Belanja Farmasi')

@section('konten')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.table-responsive{
    max-height:700px;
    overflow:auto;
}

table th,
table td{
    white-space:nowrap;
    font-size:13px;
    vertical-align:middle;
}

.gudang-wrapper{
    max-height:180px;
    overflow:auto;
    border:1px solid #dee2e6;
    border-radius:6px;
    padding:12px;
    background:#fafafa;
}
</style>

<div class="card">

    <div class="card-header">
        <h5 class="mb-0">Rencana Belanja Farmasi</h5>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('belanja.index') }}">

            <div class="row mb-3">

                <div class="col-md-3">
                    <label>Tanggal Awal</label>
                    <input type="date"
                           name="tanggal_awal"
                           class="form-control"
                           value="{{ $tanggal_awal }}">
                </div>

                <div class="col-md-3">
                    <label>Tanggal Akhir</label>
                    <input type="date"
                           name="tanggal_akhir"
                           class="form-control"
                           value="{{ $tanggal_akhir }}">
                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-12">

                    <label class="mb-2">
                        Setting Gudang
                    </label>

                    <div class="gudang-wrapper">

                        <table class="table table-bordered table-sm mb-0">

                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Gudang</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                            @foreach($bangsal as $b)

                                @php
                                    $isActive = !in_array(
                                        $b->kd_bangsal,
                                        $nonaktif_bangsal ?? []
                                    );
                                @endphp

                                <tr>

                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $b->kd_bangsal }}</td>

                                    <td>{{ $b->nm_bangsal }}</td>

                                    <td>

                                        <input type="checkbox"
                                               class="toggle-bangsal"
                                               data-kd="{{ $b->kd_bangsal }}"
                                               {{ $isActive ? 'checked' : '' }}>

                                        <span
                                            id="label-{{ $b->kd_bangsal }}"
                                            class="badge {{ $isActive ? 'badge-success' : 'badge-danger' }}">
                                            {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <button type="submit" class="btn btn-primary">
                Tampilkan
            </button>

        </form>

        @php
            $selectedBangsal = $bangsal->whereNotIn(
                'kd_bangsal',
                $nonaktif_bangsal ?? []
            );
        @endphp

        <div class="table-responsive mt-3">

            <table class="table table-bordered table-striped" id="tableBelanja">

                <thead>

                <tr>

                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Harga Beli</th>
                    <th>Satuan</th>

                    <th>Total Stok</th>
                    <th>Pengeluaran</th>
                    <th>Kebutuhan</th>

                    @foreach($selectedBangsal as $b)
                        <th>{{ $b->kd_bangsal }}</th>
                    @endforeach

                </tr>

                </thead>

                <tbody>

                @php $no = 1; @endphp

                @foreach($barang as $kode => $item)

                    @php

                        $stokBarang = $stok_lokasi[$kode] ?? collect();

                        $stokPerBangsal = [];

                        $total_stok = 0;

                        foreach($selectedBangsal as $b){

                            $stok = optional(
                                $stokBarang->firstWhere(
                                    'kd_bangsal',
                                    $b->kd_bangsal
                                )
                            )->stok ?? 0;

                            $stokPerBangsal[$b->kd_bangsal] = $stok;

                            $total_stok += $stok;
                        }

                        $total_pengeluaran_barang =
                            $total_pengeluaran[$kode] ?? 0;

                        $rencana_kebutuhan =
                            $total_pengeluaran_barang - $total_stok;

                        if($rencana_kebutuhan < 0){
                            $rencana_kebutuhan = 0;
                        }

                    @endphp

                    <tr>

                        <td>{{ $no++ }}</td>

                        <td>{{ $kode }}</td>

                        <td>{{ $item->nama_brng }}</td>

                        <td class="text-right">
                            {{ number_format($item->h_beli,2,',','.') }}
                        </td>

                        <td>{{ $item->kode_sat }}</td>

                        <td class="text-right font-weight-bold">
                            {{ number_format($total_stok,0,',','.') }}
                        </td>

                        <td class="text-right">
                            {{ number_format($total_pengeluaran_barang,0,',','.') }}
                        </td>

                        <td class="text-right font-weight-bold text-danger">
                            {{ number_format($rencana_kebutuhan,0,',','.') }}
                        </td>

                        @foreach($selectedBangsal as $b)

                            <td class="text-right">
                                {{ number_format($stokPerBangsal[$b->kd_bangsal] ?? 0,0,',','.') }}
                            </td>

                        @endforeach

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>

const token =
document.querySelector('meta[name="csrf-token"]').content;

document.querySelectorAll('.toggle-bangsal').forEach(el => {

    el.addEventListener('change', function(){

        fetch("{{ route('belanja.toggleBangsal') }}", {

            method: "POST",

            headers: {
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":token
            },

            body: JSON.stringify({
                kd_bangsal: this.dataset.kd,
                status: this.checked ? 1 : 0
            })

        })
        .then(r => r.json())
        .then(res => {

            if(res.success){

                let label =
                document.getElementById(
                    'label-' + this.dataset.kd
                );

                label.className =
                    this.checked
                    ? 'badge badge-success'
                    : 'badge badge-danger';

                label.innerText =
                    this.checked
                    ? 'Aktif'
                    : 'Nonaktif';

                location.reload();
            }

        });

    });

});

</script>

@endsection
```
