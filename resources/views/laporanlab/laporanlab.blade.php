@extends('layout.layoutDashboard')

@section('title', 'Laporan Lab Anti HIV')

@section('konten')
<div class="container-fluid">
    {{-- ================= RINGKASAN ================= --}}
    <div class="row mb-3">

        {{-- RALAN --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="summary-icon bg-info">
                        <i class="bi bi-person-walking"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="summary-title">Rawat Jalan (Ralan)</div>
                        <div class="summary-value">{{ $totalRalan }}</div>
                        <div class="small text-muted">
                            Sudah: <span class="text-success fw-semibold">{{ $terisiRalan }}</span> |
                            Belum: <span class="text-danger fw-semibold">{{ $belumRalan }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RANAP --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="summary-icon bg-warning text-dark">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="summary-title">Rawat Inap (Ranap)</div>
                        <div class="summary-value">{{ $totalRanap }}</div>
                        <div class="small text-muted">
                            Sudah: <span class="text-success fw-semibold">{{ $terisiRanap }}</span> |
                            Belum: <span class="text-danger fw-semibold">{{ $belumRanap }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ================= FILTER ================= --}}
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <form method="GET">
                <div class="row align-items-end g-3">

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Dari Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="date" name="tgl_awal" class="form-control"
                                   value="{{ $tglAwal }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Sampai Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-calendar-check"></i>
                            </span>
                            <input type="date" name="tgl_akhir" class="form-control"
                                   value="{{ $tglAkhir }}">
                        </div>
                    </div>

                    <div class="col-md-6 d-flex gap-2">
                        <button class="btn btn-primary px-4">
                            <i class="bi bi-search"></i> Tampilkan
                        </button>

                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary px-4">
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
                        <th>No KTP</th>
                        <th>No Peserta</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        <th width="120">Tgl Lahir</th>
                        <th>Alamat</th>
                        <th>No Telpone</th>
                        <th width="130">Tgl Periksa</th>
                        <th>Pemeriksaan</th>
                        <th width="120">Nilai</th>
                        <th width="110">Status</th>
                        <th>Dokter</th>
                    </tr>
                </thead>
                <tbody>

                @forelse ($data as $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>

                        <td class="fw-semibold text-primary">
                            {{ $row->no_rawat }}
                        </td>
                        <td>{{ $row->no_ktp }}</td>
                        <td>{{ $row->no_peserta }}</td>
                        <td>{{ $row->no_rkm_medis }}</td>
                        <td>{{ $row->nm_pasien }}</td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($row->tgl_lahir)->format('d-m-Y') }}
                        </td>

                        <td>{{ $row->alamat }}</td>
                        <td>{{ $row->no_tlp }}</td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($row->tgl_periksa)->format('d-m-Y') }}
                        </td>

                        <td>
                            <span class="badge bg-info">
                                {{ $row->nm_perawatan }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="badge badge-hasil">
                                {{ $row->nilai ?? '-' }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-secondary">
                                {{ $row->status_lanjut }}
                            </span>
                        </td>

                        <td>{{ $row->nm_dokter }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
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
.badge-hasil{
    background:#e6f4ea;
    color:#1e7e34;
    padding:6px 16px;
    border-radius:30px;
    font-weight:600;
}
</style>
@endpush
