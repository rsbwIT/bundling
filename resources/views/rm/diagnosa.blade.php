@extends('layout.layoutDashboard')

@section('title','Laporan Diagnosa')

@section('konten')

<style>

/* ================= BASE ================= */
body{
    background:#f6f8fb;
}

/* ================= CARD ================= */
.card-modern{
    border:none;
    border-radius:14px;
    box-shadow:0 4px 20px rgba(0,0,0,.04);
    background:#ffffff;
}
.card-modern .card-header{
    background:#ffffff;
    border-bottom:1px solid #eef1f4;
    font-weight:600;
    font-size:15px;
    color:#2c3e50;
    padding:18px 22px;
}

/* ================= FILTER ================= */
.filter-label{
    font-size:12px;
    font-weight:600;
    color:#6c757d;
    margin-bottom:6px;
}
.form-control{
    border-radius:8px;
    border:1px solid #dee2e6;
    font-size:14px;
}
.form-control:focus{
    box-shadow:none;
    border-color:#364fc7;
}
.btn-primary{
    background:#364fc7;
    border:none;
    border-radius:8px;
}
.btn-primary:hover{
    background:#2f44b2;
}
.btn-outline-secondary{
    border-radius:8px;
}

/* ================= SUMMARY ================= */
.summary-box{
    background:#ffffff;
    border-radius:12px;
    padding:18px;
    box-shadow:0 2px 10px rgba(0,0,0,.04);
    border:1px solid #eef1f4;
    transition:all .2s ease;
}
.summary-box:hover{
    transform:translateY(-2px);
}
.summary-box h6{
    font-size:12px;
    color:#6c757d;
    margin-bottom:6px;
}
.summary-box h3{
    font-weight:700;
    margin:0;
    color:#2c3e50;
}

/* Accent line */
.summary-ralan{ border-left:4px solid #364fc7; }
.summary-ranap{ border-left:4px solid #099268; }
.summary-igd{ border-left:4px solid #c92a2a; }

/* ================= TABLE ================= */
.table{
    font-size:14px;
}
.table thead{
    background:#f8f9fa;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.4px;
}
.table thead th{
    border-bottom:1px solid #e9ecef !important;
}
.table tbody tr{
    border-bottom:1px solid #f1f3f5;
    transition:all .15s ease;
}
.table tbody tr:hover{
    background:#f4f6fb;
}
.table td{
    vertical-align:middle;
    border-top:none !important;
}

/* ================= BADGE STATUS ================= */
.badge-ralan{
    background:#edf2ff;
    color:#364fc7;
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:500;
}
.badge-ranap{
    background:#e6fcf5;
    color:#099268;
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:500;
}
.badge-igd{
    background:#fff5f5;
    color:#c92a2a;
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:500;
}

/* ================= PAGINATION ================= */
.pagination{
    justify-content:center;
}
.page-link{
    border-radius:6px !important;
    margin:0 2px;
}

</style>

<div class="container-fluid">

    {{-- ================= FILTER ================= --}}
    <div class="card card-modern mb-4">
        <div class="card-header">
            Filter Diagnosa Penyakit
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('rm.diagnosa') }}">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="filter-label">Kode / Nama Penyakit</label>
                        <input type="text" 
                               name="keyword" 
                               class="form-control"
                               placeholder="Contoh: J06 atau Influenza"
                               value="{{ request('keyword') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="filter-label">Tanggal Awal</label>
                        <input type="date" 
                               name="tgl_awal" 
                               class="form-control"
                               value="{{ $tgl_awal }}">
                    </div>

                    <div class="col-md-3">
                        <label class="filter-label">Tanggal Akhir</label>
                        <input type="date" 
                               name="tgl_akhir" 
                               class="form-control"
                               value="{{ $tgl_akhir }}">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                Cari
                            </button>
                            <a href="{{ route('rm.diagnosa') }}" 
                               class="btn btn-outline-secondary w-100">
                                Reset
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>


    {{-- ================= SUMMARY ================= --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="summary-box summary-ralan">
                <h6>Rawat Jalan</h6>
                <h3>{{ $summary->ralan ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box summary-ranap">
                <h6>Rawat Inap</h6>
                <h3>{{ $summary->ranap ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box summary-igd">
                <h6>IGD</h6>
                <h3>{{ $summary->igd ?? 0 }}</h3>
            </div>
        </div>
    </div>


    {{-- ================= DATA TABLE ================= --}}
    <div class="card card-modern">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Data Diagnosa</span>
            <span class="badge bg-light text-dark px-3 py-2">
                Total: {{ $data->total() }} Data
            </span>
        </div>

        <div class="card-body table-responsive">

            <table class="table table-hover align-middle">
                <thead class="text-center">
                    <tr>
                        <th width="60">No</th>
                        <th>No Rawat</th>
                        <th>Tgl Registrasi</th>
                        <th>Nama Pasien</th>
                        <th>Umur</th>
                        <th>JK</th>
                        <th>Kode</th>
                        <th>Nama Penyakit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                    <tr>

                        <td class="text-center">
                            {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                        </td>

                        <td>{{ $row->no_rawat ?? '-' }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($row->tgl_registrasi)->format('d-m-Y') }}
                        </td>

                        <td>{{ $row->nm_pasien ?? '-' }}</td>

                        <td>
                            {{ $row->umurdaftar ?? 0 }} {{ $row->sttsumur ?? '' }}
                        </td>

                        <td class="text-center">
                            {{ $row->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </td>

                        <td>{{ $row->kd_penyakit ?? '-' }}</td>

                        <td>{{ $row->nm_penyakit ?? '-' }}</td>

                        <td class="text-center">
                            @php $status = strtolower($row->status_lanjut ?? ''); @endphp

                            @if($status == 'ralan')
                                <span class="badge-ralan">Rawat Jalan</span>
                            @elseif($status == 'ranap')
                                <span class="badge-ranap">Rawat Inap</span>
                            @elseif($status == 'igd')
                                <span class="badge-igd">IGD</span>
                            @else
                                <span class="badge bg-secondary">
                                    {{ $row->status_lanjut ?? '-' }}
                                </span>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            Tidak ada data diagnosa ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $data->appends(request()->query())->links() }}
            </div>

        </div>
    </div>

</div>

@endsection
