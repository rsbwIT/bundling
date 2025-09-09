@extends('layout.layoutDashboard')
@section('title', 'Surat Biometrik Rajal')

@push('styles')
<style>
    .card-header {
        background: linear-gradient(135deg, #0062cc, #0056b3);
        color: #fff;
        border-bottom: none;
        padding: 15px 20px;
        border-radius: .5rem .5rem 0 0;
    }
    .table thead th {
        background: #f8f9fa;
        color: #333;
        text-align: center;
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    .btn-action {
        margin: 2px;
        border-radius: 20px;
        font-size: 13px;
        padding: 5px 12px;
    }
    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: .5rem;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('konten')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fa fa-users"></i> Daftar Pasien Biometrik Rajal</h5>
    </div>
    <div class="card-body">
        {{-- 🔍 Form filter --}}
        <div class="filter-section">
            <form action="{{ route('biometrik.rajal.cari') }}" method="GET">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="no_peserta" class="form-control"
                               placeholder="Masukkan No Kartu BPJS" value="{{ request('no_peserta') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="tgl_awal" class="form-control" value="{{ $tgl_awal }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="tgl_akhir" class="form-control" value="{{ $tgl_akhir }}">
                    </div>
                    <div class="col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Cari Pasien
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- 📋 Tabel hasil --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pasien</th>
                        <th>No Kartu BPJS</th>
                        <th>No SEP</th>
                        <th>Poli Tujuan</th>
                        <th>Tgl Registrasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listPasien as $index => $pasien)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $pasien->nama }}</strong></td>
                        <td><span class="badge bg-info">{{ $pasien->no_kartu_bpjs }}</span></td>
                        <td><span class="badge bg-secondary">{{ $pasien->no_sep }}</span></td>
                        <td>{{ $pasien->poli_tujuan }}</td>
                        <td>{{ \Carbon\Carbon::parse($pasien->tgl_registrasi)->format('d-m-Y') }}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                {{-- Tombol Print --}}
                                <a href="{{ route('biometrik.rajal.print', ['id' => $pasien->id, 'tgl_awal' => $tgl_awal, 'tgl_akhir' => $tgl_akhir]) }}"
                                target="_blank"
                                class="btn btn-sm btn-success">
                                    <i class="fa fa-print"></i> Print
                                </a>

                                {{-- Tombol Formulir --}}
                                <a href="{{ route('formulir.biometrik.rajal.create', ['no_peserta' => $pasien->no_kartu_bpjs, 'no_rawat' => $pasien->id]) }}"
                                target="_blank"
                                class="btn btn-sm btn-primary">
                                    Formulir
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fa fa-exclamation-circle"></i> Tidak ada data ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
