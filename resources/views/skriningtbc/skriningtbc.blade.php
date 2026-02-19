@extends('layout.layoutDashboard')

@section('title', 'Data Skrining TBC')

@section('konten')

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
body{
    opacity:0;
    transition:0.2s ease-in;
}

.filter-bar{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:16px;
}
.filter-item{
    display:flex;
    align-items:center;
    gap:10px;
}
.filter-select{
    min-width:180px;
    border-radius:20px;
}
.filter-btn{
    border-radius:20px;
    padding:6px 20px;
}

/* WARNA KALEM */
.card-header{
    background: linear-gradient(135deg, #4fb3a4, #3a8d84) !important;
}

.table thead{
    background-color:#e6f4f2 !important;
    color:#2c6e67 !important;
}

.badge-primary{
    background-color:#6c8ebf !important;
}

.badge-warning{
    background-color:#e6b566 !important;
    color:#fff;
}

.badge-danger{
    background-color:#d97b7b !important;
}

.badge-success{
    background-color:#6fbf8f !important;
}

.table-hover tbody tr:hover{
    background-color:#f3fbfa;
    transition:0.2s;
}

.table td,.table th{
    vertical-align:middle;
}

/* TOMBOL CUSTOM SAMA HEADER */
.btn-custom-green{
    background: linear-gradient(135deg, #4fb3a4, #3a8d84);
    border: none;
    color: #fff;
    transition: 0.2s ease-in-out;
}

.btn-custom-green:hover{
    background: linear-gradient(135deg, #3a8d84, #2f726b);
    color:#fff;
}
</style>

<div class="card shadow-sm border-0 rounded-4 mt-3">

    {{-- HEADER --}}
    <div class="card-header text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-lungs mr-2"></i> Data Skrining TBC
        </h5>
        <span class="badge badge-light text-success">
            Total : {{ count($data) }}
        </span>
    </div>

    {{-- FILTER --}}
    <div class="card-body border-bottom">
        <form method="GET" action="{{ url('skriningtbc') }}">
            <div class="filter-bar">

                <div class="filter-item">
                    <span class="text-muted font-weight-semibold small">
                        Status Pelayanan
                    </span>
                    <select name="status" class="form-control form-control-sm filter-select">
                        <option value="">Semua</option>
                        <option value="ralan" {{ request('status')=='ralan'?'selected':'' }}>
                            Rawat Jalan
                        </option>
                        <option value="ranap" {{ request('status')=='ranap'?'selected':'' }}>
                            Rawat Inap
                        </option>
                    </select>
                </div>

                <div class="filter-item">
                    <span class="text-muted font-weight-semibold small">
                        Kesimpulan
                    </span>
                    <select name="kesimpulan" class="form-control form-control-sm filter-select">
                        <option value="">Semua</option>
                        <option value="terduga" {{ request('kesimpulan')=='terduga'?'selected':'' }}>
                            Terduga TBC
                        </option>
                        <option value="bukan" {{ request('kesimpulan')=='bukan'?'selected':'' }}>
                            Bukan Terduga TBC
                        </option>
                    </select>
                </div>

                <div class="filter-item">
                    <span class="text-muted font-weight-semibold small">
                        Dari Tanggal
                    </span>
                    <input type="date"
                           name="tgl_dari"
                           value="{{ request('tgl_dari') }}"
                           class="form-control form-control-sm filter-select">
                </div>

                <div class="filter-item">
                    <span class="text-muted font-weight-semibold small">
                        Sampai Tanggal
                    </span>
                    <input type="date"
                           name="tgl_sampai"
                           value="{{ request('tgl_sampai') }}"
                           class="form-control form-control-sm filter-select">
                </div>

                <button class="btn btn-sm filter-btn btn-custom-green">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>

                @if(
                    request('status') ||
                    request('kesimpulan') ||
                    request('tgl_dari') ||
                    request('tgl_sampai')
                )
                    <a href="{{ url('skriningtbc') }}"
                       class="btn btn-outline-secondary btn-sm filter-btn">
                        Reset
                    </a>
                @endif

            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>No Rawat</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        <th>Status</th>
                        <th>Penjamin</th>
                        <th>Tanggal</th>
                        <th>IMT</th>
                        <th>Kesimpulan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($data as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $row->no_rawat }}</td>
                        <td>{{ $row->no_rkm_medis }}</td>
                        <td class="font-weight-bold">{{ $row->nm_pasien }}</td>

                        <td class="text-center">
                            <span class="badge {{ $row->status_lanjut=='ralan' ? 'badge-primary' : 'badge-warning' }}">
                                {{ strtoupper($row->status_lanjut) }}
                            </span>
                        </td>

                        <td>{{ $row->png_jawab }}</td>
                        <td>{{ $row->tanggal }}</td>
                        <td class="text-center">{{ $row->imt }}</td>

                        <td class="text-center">
                            @if(stripos($row->kesimpulan_skrining, 'Terduga') !== false)
                                <span class="badge badge-danger">
                                    {{ $row->kesimpulan_skrining }}
                                </span>
                            @else
                                <span class="badge badge-success">
                                    {{ $row->kesimpulan_skrining }}
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <button class="btn btn-sm btn-custom-green"
                                data-toggle="modal"
                                data-target="#modalDetail{{ $i }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            Data skrining TBC tidak ditemukan
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL --}}
@foreach($data as $i => $row)
<div class="modal fade" id="modalDetail{{ $i }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header" style="background:linear-gradient(135deg,#4fb3a4,#3a8d84);color:white;">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-lungs mr-2"></i>
                    Detail Skrining TBC - {{ $row->nm_pasien }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-header text-white font-weight-bold" style="background:#4fb3a4;">
                                Antropometri
                            </div>
                            <div class="card-body small">
                                BB : {{ $row->berat_badan }} kg<br>
                                TB : {{ $row->tinggi_badan }} cm<br>
                                IMT : {{ $row->imt }} ({{ $row->kasifikasi_imt ?? '-' }})<br>
                                Lingkar Pinggang : {{ $row->lingkar_pinggang }} cm<br>
                                Risiko LP : {{ $row->risiko_lingkar_pinggang }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-header font-weight-bold" style="background:#f4e3b2;">
                                Riwayat & Faktor Risiko
                            </div>
                            <div class="card-body small">
                                Kontak TBC : {{ $row->riwayat_kontak_tbc }}<br>
                                Jenis Kontak : {{ $row->jenis_kontak_tbc }}<br>
                                Pernah TBC : {{ $row->faktor_resiko_pernah_terdiagnosa_tbc }}<br>
                                Pernah Berobat : {{ $row->faktor_resiko_pernah_berobat_tbc }}<br>
                                Merokok : {{ $row->faktor_resiko_merokok }}<br>
                                DM : {{ $row->faktor_resiko_riwayat_dm }}<br>
                                ODHIV : {{ $row->faktor_resiko_odhiv }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-header text-white font-weight-bold" style="background:#6c8ebf;">
                                Gejala & Kesimpulan
                            </div>
                            <div class="card-body small">
                                Batuk : {{ $row->gejala_tbc_batuk }}<br>
                                BB Turun : {{ $row->gejala_tbc_bb_turun }}<br>
                                Demam : {{ $row->gejala_tbc_demam }}<br>
                                Keringat Malam : {{ $row->gejala_tbc_berkeringat_malam_hari }}<hr>
                                <strong style="color:#3a8d84;">
                                    {{ $row->kesimpulan_skrining }}
                                </strong>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener("DOMContentLoaded", function(){
    document.body.style.opacity = "1";
});
</script>

@endsection
