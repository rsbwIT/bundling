@extends('layout.layoutDashboard')

@section('title','Laporan Diagnosa')

@section('konten')

<style>
.card-modern{
    border:none;
    border-radius:14px;
    box-shadow:0 8px 24px rgba(0,0,0,.08);
}
.card-modern .card-header{
    background:linear-gradient(135deg,#0d6efd,#20c997);
    color:#fff;
    font-weight:600;
    border-radius:14px 14px 0 0;
}
.table thead{
    background:#f8f9fa;
}
.badge-ralan{
    background:#0d6efd;
}
.badge-ranap{
    background:#198754;
}
.badge-igd{
    background:#dc3545;
}
</style>

<div class="container-fluid">

    {{-- ================= FILTER ================= --}}
    <div class="card card-modern">
        <div class="card-header">
            Filter Diagnosa Penyakit
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('rm.diagnosa') }}">
                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kode / Nama Penyakit</label>
                        <input type="text" 
                               name="keyword" 
                               class="form-control"
                               placeholder="Contoh: J06 atau Influenza"
                               value="{{ request('keyword') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Awal</label>
                        <input type="date" 
                               name="tgl_awal" 
                               class="form-control"
                               value="{{ request('tgl_awal') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" 
                               name="tgl_akhir" 
                               class="form-control"
                               value="{{ request('tgl_akhir') }}">
                    </div>

                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>


    {{-- ================= DATA TABLE ================= --}}
    <div class="card card-modern mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Data Diagnosa</span>
            <span class="badge bg-light text-dark">
                Total: {{ count($data) }} Data
            </span>
        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover table-sm align-middle">
                <thead>
                    <tr class="text-center">
                        <th width="50">No</th>
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
                    @forelse($data as $key => $row)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>

                        <td>{{ $row->no_rawat ?? '-' }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($row->tgl_registrasi)->format('d-m-Y') }}
                        </td>

                        <td>{{ $row->nm_pasien ?? '-' }}</td>

                        <td>
                            {{ $row->umurdaftar ?? 0 }} {{ $row->sttsumur ?? '' }}
                        </td>

                        <td class="text-center">
                            @if($row->jk == 'L')
                                Laki-laki
                            @elseif($row->jk == 'P')
                                Perempuan
                            @else
                                -
                            @endif
                        </td>

                        <td>{{ $row->kd_penyakit ?? '-' }}</td>

                        <td>{{ $row->nm_penyakit ?? '-' }}</td>

                        <td class="text-center">
                            @php
                                $status = strtolower($row->status_lanjut ?? '');
                            @endphp

                            @if($status == 'ralan')
                                <span class="badge badge-ralan">Rawat Jalan</span>
                            @elseif($status == 'ranap')
                                <span class="badge badge-ranap">Rawat Inap</span>
                            @elseif($status == 'igd')
                                <span class="badge badge-igd">IGD</span>
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

        </div>
    </div>

</div>

@endsection
