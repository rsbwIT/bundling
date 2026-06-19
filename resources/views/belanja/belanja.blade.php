@extends('layout.layoutDashboard')

@section('title','Rencana Belanja Farmasi')

@section('konten')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

<style>

:root{
    --primary:#cfdeff;
    --secondary:#cfdeff;
    --success:#10b981;
    --danger:#ef4444;
    --warning:#f59e0b;
    --dark:#0f172a;
    --light:#f8fafc;
}

body{
    background:#f1f5f9;
}

.main-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.card-header-custom{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:white;
    padding:20px 30px;
}

.card-header-custom h4{
    margin:0;
    font-weight:700;
}

.filter-card{
    background:white;
    border-radius:15px;
    padding:20px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
    margin-bottom:20px;
}

.summary-box{
    border-radius:18px;
    padding:20px;
    color:white;
    position:relative;
    overflow:hidden;
    margin-bottom:20px;
}

.summary-box h6{
    margin-bottom:10px;
    opacity:.9;
}

.summary-box h2{
    font-weight:700;
    margin:0;
}

.bg-stok{
    background:linear-gradient(135deg,#0ea5e9,#2563eb);
}

.bg-keluar{
    background:linear-gradient(135deg,#f59e0b,#ea580c);
}

.bg-kebutuhan{
    background:linear-gradient(135deg,#ef4444,#dc2626);
}

.gudang-wrapper{
    max-height:250px;
    overflow:auto;
    border:1px solid #e2e8f0;
    border-radius:12px;
    background:#fff;
}

.table th{
    white-space:nowrap;
}

.table td{
    white-space:nowrap;
    vertical-align:middle;
}

.table-responsive{
    max-height:750px;
    overflow:auto;
}

.table thead th{
    position:sticky;
    top:0;
    z-index:99;
    background:var(--dark);
    color:white;
    font-size:12px;
}

.table tbody tr:hover{
    background:#eef6ff;
}

.stock{
    color:#2563eb;
    font-weight:700;
}

.keluar{
    color:#f59e0b;
    font-weight:700;
}

.kebutuhan{
    color:#ef4444;
    font-weight:700;
}

.switch{
    position:relative;
    display:inline-block;
    width:52px;
    height:28px;
}

.switch input{
    display:none;
}

.slider{
    position:absolute;
    top:0;
    left:0;
    right:0;
    bottom:0;
    cursor:pointer;
    background:#d1d5db;
    transition:.3s;
    border-radius:50px;
}

.slider:before{
    content:'';
    position:absolute;
    width:22px;
    height:22px;
    left:3px;
    bottom:3px;
    background:white;
    transition:.3s;
    border-radius:50%;
}

.switch input:checked + .slider{
    background:#10b981;
}

.switch input:checked + .slider:before{
    transform:translateX(24px);
}

.badge-active{
    background:#10b981;
    color:white;
    padding:6px 10px;
    border-radius:20px;
}

.badge-inactive{
    background:#ef4444;
    color:white;
    padding:6px 10px;
    border-radius:20px;
}

.btn-primary{
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    padding:10px 25px;
    font-weight:600;
}

.dataTables_filter input{
    border-radius:10px !important;
}

</style>

<div class="card main-card">

<div class="card-header-custom">
    {{-- <h4>Rencana Belanja Farmasi</h4>
    <small>Perencanaan kebutuhan obat berdasarkan pengeluaran dan stok gudang</small> --}}
</div>

<div class="card-body">

    <form method="GET" action="{{ route('belanja.index') }}">

        <div class="filter-card">

            <div class="row">

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

                <div class="col-md-2">
                    <label>Urutkan Harga</label>
                    <select name="filter_harga" class="form-control">
                        <option value="">Default</option>
                        <option value="termahal" {{ request('filter_harga')=='termahal' ? 'selected' : '' }}>
                            Harga Termahal
                        </option>
                        <option value="termurah" {{ request('filter_harga')=='termurah' ? 'selected' : '' }}>
                            Harga Termurah
                        </option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary btn-block">
                        Tampilkan Data
                    </button>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">
                Setting Gudang yang Dihitung
            </h6>

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
                            $isActive=!in_array(
                                $b->kd_bangsal,
                                $nonaktif_bangsal ?? []
                            );
                        @endphp

                        <tr>

                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $b->kd_bangsal }}</td>
                            <td>{{ $b->nm_bangsal }}</td>

                            <td>

                                <label class="switch">

                                    <input type="checkbox"
                                           class="toggle-bangsal"
                                           data-kd="{{ $b->kd_bangsal }}"
                                           {{ $isActive ? 'checked' : '' }}>

                                    <span class="slider"></span>

                                </label>

                                <span id="label-{{ $b->kd_bangsal }}"
                                      class="{{ $isActive ? 'badge-active' : 'badge-inactive' }}">
                                      {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                </span>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </form>

    @php

        $selectedBangsal =
        $bangsal->whereNotIn(
            'kd_bangsal',
            $nonaktif_bangsal ?? []
        );

        $grandStok=0;
        $grandKeluar=0;
        $grandKebutuhan=0;

        $obatTermahal = collect();

        foreach($barang as $kode => $item){

            $stokBarang = $stok_lokasi[$kode] ?? collect();

            $stok = $stokBarang
                ->whereIn(
                    'kd_bangsal',
                    $selectedBangsal->pluck('kd_bangsal')
                )
                ->sum('stok');

            $keluar = $total_pengeluaran[$kode] ?? 0;

            $kebutuhan = max(
                $keluar - $stok,
                0
            );

            // Total nilai pembelian
            $nilaiBelanja = $kebutuhan * $item->h_beli;

            // Simpan untuk diurutkan
            $obatTermahal->push([
                'kode_brng'      => $kode,
                'nama_brng'      => $item->nama_brng,
                'kode_sat'       => $item->kode_sat,
                'harga_beli'     => $item->h_beli,
                'stok'           => $stok,
                'pengeluaran'    => $keluar,
                'kebutuhan'      => $kebutuhan,
                'nilai_belanja'  => $nilaiBelanja
            ]);

            $grandStok += $stok;
            $grandKeluar += $keluar;
            $grandKebutuhan += $kebutuhan;
        }

        // Urutkan dari nilai belanja terbesar
        $obatTermahal = $obatTermahal
            ->sortByDesc('nilai_belanja')
            ->values();

        $filterHarga = request('filter_harga');

            if($filterHarga == 'termahal'){

                $obatTermahal = $obatTermahal
                    ->sortByDesc('harga_beli')
                    ->values();

            }elseif($filterHarga == 'termurah'){

                $obatTermahal = $obatTermahal
                    ->sortBy('harga_beli')
                    ->values();

            }else{

                // Default berdasarkan nilai belanja
                $obatTermahal = $obatTermahal
                    ->sortByDesc('nilai_belanja')
                    ->values();

            }

    @endphp

    <div class="row">

        <div class="col-md-4">
            <div class="summary-box bg-stok">
                <h6>Total Stok</h6>
                <h2>{{ number_format($grandStok,0,',','.') }}</h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-box bg-keluar">
                <h6>Total Pengeluaran</h6>
                <h2>{{ number_format($grandKeluar,0,',','.') }}</h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-box bg-kebutuhan">
                <h6>Rencana Pembelian</h6>
                <h2>{{ number_format($grandKebutuhan,0,',','.') }}</h2>
            </div>
        </div>

    </div>

    <div class="table-responsive">

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

            @php $no=1; @endphp

            @foreach($obatTermahal as $row)

    @php

        $kode = $row['kode_brng'];

        $item = (object)[
            'nama_brng' => $row['nama_brng'],
            'kode_sat'  => $row['kode_sat'],
            'h_beli'    => $row['harga_beli']
        ];

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

        $pengeluaran = $row['pengeluaran'];

        $kebutuhan = $row['kebutuhan'];

    @endphp

    <tr>

        <td>{{ $no++ }}</td>

        <td>{{ $kode }}</td>

        <td>{{ $item->nama_brng }}</td>

        <td align="right">
            {{ number_format($item->h_beli,2,',','.') }}
        </td>

        <td>{{ $item->kode_sat }}</td>

        <td align="right" class="stock">
            {{ number_format($total_stok,0,',','.') }}
        </td>

        <td align="right" class="keluar">
            {{ number_format($pengeluaran,0,',','.') }}
        </td>

        <td align="right" class="kebutuhan">
            {{ number_format($kebutuhan,0,',','.') }}
        </td>

        @foreach($selectedBangsal as $b)

            <td align="right">
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

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>

$('#tableBelanja').DataTable({
    pageLength: 25,
    scrollX: true,
    responsive: true,
    ordering: false,

    dom: 'Bfrtip',

    buttons: [
{
    extend: 'copyHtml5',
    text: '<i class="fas fa-copy"></i> Copy Data Obat',
    className: 'btn btn-success btn-sm',
    title: 'Rencana Belanja Farmasi',
    exportOptions: {
        columns: ':visible'
    },

    action: function (e, dt, button, config) {

        $.fn.dataTable.ext.buttons.copyHtml5.action.call(
            this,
            e,
            dt,
            button,
            config
        );

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '📋 Data berhasil dicopy',
            text: 'Silakan paste ke Excel, WhatsApp, atau Telegram',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });

    }
}
]
});

const token=
document.querySelector('meta[name="csrf-token"]').content;

document.querySelectorAll('.toggle-bangsal').forEach(el=>{

    el.addEventListener('change',function(){

        fetch("{{ route('belanja.toggleBangsal') }}",{

            method:'POST',

            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':token
            },

            body:JSON.stringify({

                kd_bangsal:this.dataset.kd,
                status:this.checked ? 1 : 0

            })

        })
        .then(r=>r.json())
        .then(res=>{

            if(res.success){

                location.reload();

            }

        });

    });

});

</script>

@endsection
