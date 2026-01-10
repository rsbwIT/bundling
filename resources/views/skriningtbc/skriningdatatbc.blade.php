@extends('layout.layoutDashboard')

@section('title', 'Data Skrining TBC')

@section('konten')
<div class="container-fluid">

    {{-- ================= RINGKASAN ================= --}}
    <div class="row mb-3">

        {{-- TOTAL PASIEN --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="summary-icon bg-info">
                        <i class="bi bi-card-list"></i>
                    </div>
                    <div>
                        <div class="summary-title">Total Data Pasien TBC</div>
                        <div class="summary-value">{{ $total_pasien }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUDAH --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="summary-icon bg-success">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div>
                        <div class="summary-title">Total Pasien TBC Yang Sudah Terisi</div>
                        <div class="summary-value">{{ $total_sudah }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BELUM --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="summary-icon bg-warning text-dark">
                        <i class="bi bi-pencil-fill"></i>
                    </div>
                    <div>
                        <div class="summary-title">Total Yang Belum Terisi</div>
                        <div class="summary-value">{{ $total_belum }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ================= FILTER ================= --}}
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <form method="GET" action="{{ url('/skrining-tbc') }}">
                <div class="row align-items-end g-3">

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Dari Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="date" name="tgl_dari" class="form-control"
                                   value="{{ $tgl_dari }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Sampai Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-calendar-check"></i>
                            </span>
                            <input type="date" name="tgl_sampai" class="form-control"
                                   value="{{ $tgl_sampai }}">
                        </div>
                    </div>

                    <div class="col-md-6 d-flex gap-2">
                        <button class="btn btn-primary px-4">
                            <i class="bi bi-search"></i> Tampilkan
                        </button>

                        <a href="{{ url('/skrining-tbc') }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-clockwise"></i> Hari Ini
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ================= TABEL ================= --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-primary text-center">
                    <tr>
                        <th width="40">No</th>
                        <th>No Rawat</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        <th width="120">Tgl Registrasi</th>
                        <th width="120">Status Lanjut</th>
                        <th width="160">Skrining TBC</th>
                    </tr>
                </thead>
                <tbody>

                @forelse ($data as $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $row->no_rawat }}</td>
                        <td>{{ $row->no_rkm_medis }}</td>
                        <td>{{ $row->nm_pasien }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($row->tgl_registrasi)->format('d-m-Y') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $row->status_lanjut }}</span>
                        </td>
                        <td class="text-center">
                            @if ($row->status_skrining_tbc === '✔')
                                <span class="badge badge-sudah">
                                    <i class="bi bi-check-circle-fill me-1"></i> Sudah
                                </span>
                            @else
                                <span class="badge badge-belum">
                                    <i class="bi bi-x-circle-fill me-1"></i> Belum
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Tidak ada data pada rentang tanggal ini
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection


{{-- ================= STYLE ================= --}}
@push('styles')
<style>
.summary-icon{
    width:56px;
    height:56px;
    border-radius:6px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:26px;
    color:#fff;
}
.summary-title{
    font-size:.9rem;
    font-weight:600;
    color:#555;
}
.summary-value{
    font-size:1.8rem;
    font-weight:700;
}

.badge-sudah{
    background:#e6f4ea;
    color:#1e7e34;
    padding:6px 16px;
    border-radius:30px;
    font-weight:600;
}
.badge-belum{
    background:#fdecea;
    color:#b02a37;
    padding:6px 16px;
    border-radius:30px;
    font-weight:600;
}
</style>
@endpush
