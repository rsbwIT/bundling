@extends('layout.layoutDashboard')

@section('title', 'Laporan Antrian Farmasi')

@section('konten')
<div class="container-fluid mt-3">
    {{-- 🔍 FILTER --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="fw-bold small mb-1">Tanggal Awal</label>
                    <input type="date" class="form-control" wire:model.defer="tgl1">
                </div>

                <div class="col-md-3">
                    <label class="fw-bold small mb-1">Tanggal Akhir</label>
                    <input type="date" class="form-control" wire:model.defer="tgl2">
                </div>

                <div class="col-md-3">
                    <label class="fw-bold small mb-1">Pencarian</label>
                    <input type="text" class="form-control"
                        placeholder="Cari Nama / RM / No Rawat / Antrian"
                        wire:model.debounce.500ms="search">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-50" wire:click="loadData" wire:loading.attr="disabled">
                        <i class="fa fa-search me-1"></i> Filter
                    </button>
                    <button class="btn btn-secondary w-50" wire:click="resetFilter" wire:loading.attr="disabled">
                        <i class="fa fa-rotate-left me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 📋 TABEL DATA --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">
            <i class="fa fa-list text-success me-1"></i> Data Antrian Farmasi
        </div>
        <div class="card-body p-0 position-relative">
            {{-- 🔄 Spinner saat loading --}}
            <div wire:loading class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center"
                style="background: rgba(255,255,255,0.6); z-index: 10;">
                <div class="spinner-border text-primary" role="status"></div>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor Antrian</th>
                            <th>Rekam Medik</th>
                            <th>Nama Pasien</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($listData as $row)
                            <tr>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}
                                </td>
                                <td class="text-center">{{ $row->nomor_antrian }}</td>
                                <td class="text-center">{{ $row->rekam_medik }}</td>
                                <td>{{ $row->nama_pasien }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $row->status == 'SELESAI' ? 'success' : 'secondary' }}">
                                        {{ strtoupper($row->status) }}
                                    </span>
                                </td>
                                <td>{{ $row->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">
                                    <i class="fa fa-info-circle me-1"></i> Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 🔔 SweetAlert2 Toast --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('show-toast', event => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: event.detail.type || 'info',
            title: event.detail.message,
            showConfirmButton: false,
            timer: 2000
        });
    });
</script>
@endsection
