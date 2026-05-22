@extends('layout.layoutDashboard')

@section('title', 'Rencana Belanja Farmasi')

@section('konten')

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

    .badge-status{
        font-size:11px;
        padding:4px 8px;
    }

</style>

<div class="card">

    <div class="card-header">

        <h5 class="mb-0">
            Rencana Belanja Farmasi
        </h5>

    </div>

    <div class="card-body">

        <form method="GET"
              action="{{ route('belanja.index') }}">

            <div class="row mb-3">

                <div class="col-md-3">

                    <label>
                        Tanggal Awal
                    </label>

                    <input type="date"
                           name="tanggal_awal"
                           class="form-control"
                           value="{{ $tanggal_awal }}">

                </div>

                <div class="col-md-3">

                    <label>
                        Tanggal Akhir
                    </label>

                    <input type="date"
                           name="tanggal_akhir"
                           class="form-control"
                           value="{{ $tanggal_akhir }}">

                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-12">

                    <label class="mb-2">
                        Setting Gudang Aktif / Nonaktif
                    </label>

                    <div class="gudang-wrapper">

                        <table class="table table-bordered table-sm mb-0">

                            <thead class="thead-light">

                                <tr>

                                    <th width="60">
                                        No
                                    </th>

                                    <th>
                                        Kode Gudang
                                    </th>

                                    <th width="150">
                                        Status
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($bangsal as $b)

                                    <tr>

                                        <td>
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>
                                            {{ $b }}
                                        </td>

                                        <td>

                                            <div class="custom-control custom-switch">

                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       name="bangsal[]"
                                                       value="{{ $b }}"
                                                       id="switch{{ $loop->index }}"

                                                       @if(
                                                            empty(request('bangsal'))
                                                            ||
                                                            in_array($b, request('bangsal'))
                                                       )
                                                           checked
                                                       @endif>

                                                <label class="custom-control-label"
                                                       for="switch{{ $loop->index }}">

                                                    @if(
                                                        empty(request('bangsal'))
                                                        ||
                                                        in_array($b, request('bangsal'))
                                                    )

                                                        <span class="badge badge-success badge-status">
                                                            Aktif
                                                        </span>

                                                    @else

                                                        <span class="badge badge-danger badge-status">
                                                            Nonaktif
                                                        </span>

                                                    @endif

                                                </label>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-2">

                    <button type="submit"
                            class="btn btn-primary btn-block">

                        Tampilkan

                    </button>

                </div>

                <div class="col-md-2">

                    <button type="button"
                            class="btn btn-success btn-block"
                            onclick="copyTableFormatted('tableBelanja')">

                        Copy Tabel

                    </button>

                </div>

            </div>

        </form>

        @php

            $selectedBangsal =
                request('bangsal', $bangsal->toArray());

        @endphp

        <div class="table-responsive">

            <table class="table table-bordered table-striped"
                   id="tableBelanja">

                <thead class="thead-dark">

                    <tr>

                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Harga Satuan</th>
                        <th>Kode Satuan</th>

                        @foreach($selectedBangsal as $b)

                            <th>
                                {{ $b }}
                            </th>

                        @endforeach

                        <th>Total Stok</th>
                        <th>Total Pengeluaran</th>
                        <th>Rencana Kebutuhan</th>
                        <th>Rencana Belanja</th>

                    </tr>

                </thead>

                <tbody>

                    @php
                        $no = 1;
                    @endphp

                    @foreach($barang as $kode_brng => $item)

                        @php

                            $stokBarang =
                                $stok_lokasi[$kode_brng] ??
                                collect();

                            $total_stok = 0;

                        @endphp

                        <tr>

                            <td>
                                {{ $no++ }}
                            </td>

                            <td>
                                {{ $kode_brng }}
                            </td>

                            <td>
                                {{ $item->nama_brng }}
                            </td>

                            <td class="text-right">

                                {{ number_format($item->h_beli, 2, ',', '.') }}

                            </td>

                            <td>
                                {{ $item->kode_sat }}
                            </td>

                            @foreach($selectedBangsal as $b)

                                @php

                                    $stok =
                                        optional(
                                            $stokBarang->firstWhere(
                                                'kd_bangsal',
                                                $b
                                            )
                                        )->stok ?? 0;

                                    $total_stok += $stok;

                                @endphp

                                <td class="text-right">

                                    {{ number_format($stok, 0, ',', '.') }}

                                </td>

                            @endforeach

                            @php

                                $total_pengeluaran_barang =
                                    $total_pengeluaran[$kode_brng] ?? 0;

                                $rencana_kebutuhan =
                                    $total_pengeluaran_barang -
                                    $total_stok;

                            @endphp

                            <td class="text-right">

                                {{ number_format($total_stok, 0, ',', '.') }}

                            </td>

                            <td class="text-right">

                                {{ number_format($total_pengeluaran_barang, 0, ',', '.') }}

                            </td>

                            <td class="text-right">

                                {{ number_format($rencana_kebutuhan, 0, ',', '.') }}

                            </td>

                            <td></td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>

function copyTableFormatted(tableId){

    const table =
        document.getElementById(tableId);

    let html =
        '<table border="1">' +
        table.innerHTML +
        '</table>';

    const blob =
        new Blob(
            [html],
            {
                type:'text/html'
            }
        );

    const data =
        [
            new ClipboardItem({
                'text/html': blob
            })
        ];

    navigator.clipboard.write(data)
        .then(() => {

            alert(
                'Tabel berhasil disalin'
            );

        })
        .catch(err => {

            console.log(err);

            alert(
                'Gagal copy tabel'
            );

        });

}

</script>

@endsection