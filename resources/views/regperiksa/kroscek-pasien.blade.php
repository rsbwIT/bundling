@extends('..layout.layoutDashboard')
@section('title', 'Kroscek Pasien')

@push('styles')
    {{-- [BARU] Tambahkan style untuk container poli --}}
    <style>
        .poli-filter-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            background-color: #fdfdfd;
        }
        .poli-filter-container .form-check {
            margin-bottom: 5px;
        }
    </style>
@endpush

@section('konten')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-dark">Kroscek Pasien</h4>
            <p class="text-muted mb-0">Monitoring Nota Pembayaran</p>
        </div>
        <div class="text-end">
            <small class="text-muted">
                {{-- Tampilkan tanggal tunggal atau keterangan rentang --}}
                @if(!empty($tanggalMulai) && !empty($tanggalSelesai))
                    {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') }}
                @else
                    {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                @endif
            </small>
        </div>
    </div>

    {{-- Logic untuk menentukan mode tanggal yang sedang aktif --}}
    @php
        $isRangeModeActive = !empty($tanggalMulai) && !empty($tanggalSelesai);
        // [UBAH] Siapkan query string untuk filter poli (pengecualian)
        $poliQuery = http_build_query(['excluded_poli' => $excludedPoli]);
    @endphp

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ url('kroscek-pasien') }}" id="filterForm">
                {{-- BARIS 1: Filter Utama (Tanggal & Tipe) --}}
                <div class="row g-2 align-items-end">

                    <div class="col-md-2">
                        <label class="form-label small text-muted">Mode Tanggal</label>
                        <select id="dateMode" class="form-select form-select-sm">
                            <option value="single" {{ !$isRangeModeActive ? 'selected' : '' }}>Tanggal Tunggal</option>
                            <option value="range" {{ $isRangeModeActive ? 'selected' : '' }}>Rentang Tanggal</option>
                        </select>
                    </div>

                    {{-- 1. Tanggal Tunggal --}}
                    <div class="col-md-2 date-input-group" id="singleDateGroup">
                        <label class="form-label small text-muted">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm"
                            value="{{ $tanggal }}" max="{{ date('Y-m-d') }}" required>
                    </div>

                    {{-- 2. Rentang Tanggal --}}
                    <div class="col-md-2 date-input-group" id="rangeStartGroup">
                        <label class="form-label small text-muted">Dari</label>
                        <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                            value="{{ $tanggalMulai }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-2 date-input-group" id="rangeEndGroup">
                        <label class="form-label small text-muted">Sampai</label>
                        <input type="date" name="tanggal_selesai" class="form-control form-control-sm"
                            value="{{ $tanggalSelesai }}" max="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small text-muted">Filter Data</label>
                        <select name="filter_type" class="form-select form-select-sm">
                            <option value="semua" {{ request('filter_type', 'semua') == 'semua' ? 'selected' : '' }}>Semua Pasien</option>
                            <option value="belum_nota" {{ request('filter_type') == 'belum_nota' ? 'selected' : '' }}>Ralan Belum Nota</option>
                            <option value="batal" {{ request('filter_type') == 'batal' ? 'selected' : '' }}>Pasien Batal</option>
                            <option value="igd" {{ request('filter_type') == 'igd' ? 'selected' : '' }}>Ranap IGD</option>
                            <option value="ralan" {{ request('filter_type') == 'ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                            <option value="ranap_poli" {{ request('filter_type') == 'ranap_poli' ? 'selected' : '' }}>Ranap Poli</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100" id="submitBtn">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ url('kroscek-pasien') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-refresh"></i> Reset
                        </a>
                    </div>
                </div>

                {{-- [BARU] BARIS 2: Filter Poliklinik --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <a class="btn btn-outline-info btn-sm w-100" data-toggle="collapse" href="#collapsePoli" role="button" aria-expanded="false" aria-controls="collapsePoli">
                            <i class="fas fa-filter"></i> Filter Pengecualian Poliklinik (Pilih poli yang ingin DIBUANG)
                        </a>
                        <div class="collapse" id="collapsePoli">
                            <div class="poli-filter-container mt-2">
                                <div class="row">
                                    @forelse ($allPoli as $poli)
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="excluded_poli[]" {{-- [UBAH] --}}
                                                       value="{{ $poli->kd_poli }}"
                                                       id="poli_{{ $poli->kd_poli }}"
                                                       {{-- [UBAH] Gunakan $excludedPoli --}}
                                                       @if(in_array($poli->kd_poli, $excludedPoli)) checked @endif
                                                       >
                                                <label class="form-check-label" for="poli_{{ $poli->kd_poli }}">
                                                    {{ $poli->nm_poli }}
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted">
                                            Gagal memuat daftar poliklinik.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BARIS 3: Quick Filter --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="btn-group btn-group-sm" role="group">
                            {{-- [UBAH] Link quick filter sekarang membawa parameter poli --}}
                            <a href="{{ url('kroscek-pasien') }}?tanggal={{ date('Y-m-d') }}&tanggal_mulai=&tanggal_selesai=&{{ $poliQuery }}"
                               class="btn {{ $tanggal == date('Y-m-d') && !$isRangeModeActive ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Hari Ini
                            </a>
                            <a href="{{ url('kroscek-pasien') }}?tanggal={{ date('Y-m-d', strtotime('-1 day')) }}&tanggal_mulai=&tanggal_selesai=&{{ $poliQuery }}"
                               class="btn {{ $tanggal == date('Y-m-d', strtotime('-1 day')) && !$isRangeModeActive ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Kemarin
                            </a>
                            <a href="{{ url('kroscek-pasien') }}?tanggal_mulai={{ date('Y-m-d', strtotime('-6 days')) }}&tanggal_selesai={{ date('Y-m-d') }}&tanggal=&{{ $poliQuery }}"
                               class="btn {{ $isRangeModeActive && $tanggalMulai == date('Y-m-d', strtotime('-6 days')) && $tanggalSelesai == date('Y-m-d') ? 'btn-primary' : 'btn-outline-secondary' }}">
                                7 Hari
                            </a>
                            <a href="{{ url('kroscek-pasien') }}?tanggal_mulai={{ date('Y-m-01') }}&tanggal_selesai={{ date('Y-m-d') }}&tanggal=&{{ $poliQuery }}"
                               class="btn {{ $isRangeModeActive && $tanggalMulai == date('Y-m-01') && $tanggalSelesai == date('Y-m-d') ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Bulan Ini
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Kartu Statistik (Tidak Berubah) --}}
    <div class="row g-3 mb-4">
        {{-- Total Pasien --}}
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2"><i class="fas fa-users fa-2x"></i></div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_pasien ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Total Pasien</small>
                </div>
            </div>
        </div>
        {{-- Ralan --}}
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2"><i class="fas fa-stethoscope fa-2x"></i></div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_ralan ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Rawat Jalan</small>
                </div>
            </div>
        </div>
        {{-- Total IGD --}}
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2"><i class="fas fa-ambulance fa-2x"></i></div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_igd ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Total IGD</small>
                </div>
            </div>
        </div>
        {{-- Ranap IGD --}}
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2"><i class="fas fa-bed fa-2x"></i></div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_ranap_igd ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Ranap IGD</small>
                </div>
            </div>
        </div>
        {{-- Ranap Poli --}}
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2"><i class="fas fa-hospital fa-2x"></i></div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_ranap_poli ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Ranap Poli</small>
                </div>
            </div>
        </div>
        {{-- Batal --}}
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8d7da 0%, #f1c2c7 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2"><i class="fas fa-times-circle fa-2x"></i></div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_batal ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Batal</small>
                </div>
            </div>
        </div>
        {{-- Belum Nota --}}
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffe8a1 0%, #ffd93d 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_belum_nota ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Ralan Belum Nota</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Bar (Tidak Berubah) --}}
    @if(($statistik->total_pasien_aktif ?? 0) > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            @php
                $totalPasienAktif = $statistik->total_pasien_aktif ?? 1; // Pasien yang tidak batal
                $sudahNota = $statistik->total_sudah_nota ?? 0;
                $progressPercentage = ($sudahNota / $totalPasienAktif) * 100;
            @endphp
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-dark fw-medium">Progress Nota</span>
                <span class="text-dark fw-bold">{{ number_format($progressPercentage, 1) }}%</span>
            </div>
            <div class="progress mb-2" style="height: 12px;">
                <div class="progress-bar bg-success" role="progressbar"
                    style="width: {{ $progressPercentage }}%"></div>
            </div>
            <div class="text-center">
                <small class="text-muted fw-medium">
                    {{ number_format($sudahNota) }} dari {{ number_format($totalPasienAktif) }} pasien aktif sudah memiliki nota
                </small>
            </div>
            @if(($statistik->total_batal ?? 0) > 0)
            <div class="text-center mt-1">
                <small class="text-muted">
                    ({{ number_format($statistik->total_batal) }} pasien batal tidak dihitung)
                </small>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Tabel Hasil --}}
    @if($daftarPasienBelumNota->count() > 0 || request('search') || request('filter_status'))
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0 bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0 fw-bold text-dark">
                        @php
                            $titles = [
                                'semua' => 'Semua Pasien',
                                'belum_nota' => 'Pasien Rawat Jalan Belum Nota',
                                'batal' => 'Pasien Batal',
                                'igd' => 'Pasien Ranap IGD',
                                'ralan' => 'Pasien Rawat Jalan',
                                'ranap_poli' => 'Pasien Ranap Poli'
                            ];
                            $currentFilter = request('filter_type', 'semua');
                        @endphp
                        {{ $titles[$currentFilter] ?? 'Semua Pasien' }}
                    </h6>
                    <small class="text-muted">{{ $daftarPasienBelumNota->total() }} data ({{ $perPage ?? 100 }} per halaman)</small>
                </div>

                {{-- Form 'Per Halaman' --}}
                <div class="col-auto me-3">
                    <form method="GET" action="{{ url('kroscek-pasien') }}" class="d-flex align-items-center">
                        {{-- Sinkronisasi filter tanggal aktif --}}
                        <input type="hidden" name="tanggal" value="{{ $isRangeModeActive ? '' : $tanggal }}">
                        <input type="hidden" name="tanggal_mulai" value="{{ $isRangeModeActive ? $tanggalMulai : '' }}">
                        <input type="hidden" name="tanggal_selesai" value="{{ $isRangeModeActive ? $tanggalSelesai : '' }}">
                        {{-- Sinkronisasi filter lain --}}
                        <input type="hidden" name="filter_type" value="{{ request('filter_type', 'semua') }}">
                        <input type="hidden" name="search" value="{{ $searchTerm }}">
                        {{-- [UBAH] Tambahkan filter poli yang aktif (pengecualian) --}}
                        @foreach($excludedPoli as $poli_id)
                            <input type="hidden" name="excluded_poli[]" value="{{ $poli_id }}">
                        @endforeach

                        <small class="text-muted me-2">Per halaman:</small>
                        <select name="per_page" class="form-select form-select-sm auto-submit" style="width: 80px;">
                            <option value="25" {{ ($perPage ?? 100) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 100) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 100) == 100 ? 'selected' : '' }}>100</option>
                            <option value="250" {{ ($perPage ?? 100) == 250 ? 'selected' : '' }}>250</option>
                        </select>
                    </form>
                </div>

                {{-- Form Pencarian --}}
                <div class="col-auto">
                    <form method="GET" action="{{ url('kroscek-pasien') }}" class="d-flex" id="searchForm">
                        {{-- Sinkronisasi filter tanggal aktif --}}
                        <input type="hidden" name="tanggal" value="{{ $isRangeModeActive ? '' : $tanggal }}">
                        <input type="hidden" name="tanggal_mulai" value="{{ $isRangeModeActive ? $tanggalMulai : '' }}">
                        <input type="hidden" name="tanggal_selesai" value="{{ $isRangeModeActive ? $tanggalSelesai : '' }}">
                        {{-- Sinkronisasi filter lain --}}
                        <input type="hidden" name="filter_type" value="{{ request('filter_type', 'semua') }}">
                        <input type="hidden" name="per_page" value="{{ $perPage ?? 100 }}">
                        {{-- [UBAH] Tambahkan filter poli yang aktif (pengecualian) --}}
                        @foreach($excludedPoli as $poli_id)
                            <input type="hidden" name="excluded_poli[]" value="{{ $poli_id }}">
                        @endforeach

                        <div class="input-group input-group-sm me-2">
                            <input type="text" name="search" class="form-control" id="searchInput"
                                   placeholder="Cari pasien..." value="{{ $searchTerm }}" style="width: 180px;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabel (Tidak Berubah) --}}
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    <tr>
                        <th class="border-0 ps-3 text-dark fw-bold">No</th>
                        <th class="border-0 text-dark fw-bold">No Rawat</th>
                        <th class="border-0 text-dark fw-bold">Pasien</th>
                        <th class="border-0 text-dark fw-bold">Status</th>
                        <th class="border-0 text-dark fw-bold">Poli</th>
                        <th class="border-0 text-dark fw-bold">Status Nota</th>
                        <th class="border-0 text-dark fw-bold">Tgl Reg</th>
                        <th class="border-0 text-dark fw-bold">Jam</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarPasienBelumNota as $index => $pasien)
                    <tr>
                        <td class="ps-3 text-muted">{{ $daftarPasienBelumNota->firstItem() + $index }}</td>
                        <td>
                            <code class="text-primary small fw-bold">{{ $pasien->no_rawat }}</code>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">{{ $pasien->nm_pasien }}</span>
                                <small class="text-muted">{{ $pasien->no_rkm_medis }}</small>
                            </div>
                        </td>
                        <td>
                            @if($pasien->status_lanjut === 'Ranap')
                                @if($pasien->kd_poli === 'IGDK')
                                    <span class="badge fw-bold text-white" style="background-color: #e74c3c; border-radius: 20px;">Ranap IGD</span>
                                @else
                                    <span class="badge fw-bold text-white" style="background-color: #27ae60; border-radius: 20px;">Ranap Poli</span>
                                @endif
                            @else
                                @if($pasien->kd_poli === 'IGDK')
                                    <span class="badge fw-bold text-white" style="background-color: #f39c12; border-radius: 20px;">IGD</span>
                                @else
                                    <span class="badge fw-bold text-white" style="background-color: #3498db; border-radius: 20px;">Ralan</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            <small class="text-muted fw-medium">{{ $pasien->nm_poli ?? '-' }}</small>
                        </td>
                        <td>
                            @if($pasien->status_nota === 'Sudah Nota')
                                <span class="badge fw-bold text-white" style="background-color: #28a745; border-radius: 20px;">Sudah Nota</span>
                            @elseif($pasien->status_nota === 'Batal')
                                <span class="badge fw-bold text-white" style="background-color: #6c757d; border-radius: 20px;">Batal</span>
                            @else
                                <span class="badge fw-bold text-white" style="background-color: #ffc107; border-radius: 20px;">Belum Nota</span>
                            @endif
                        </td>
                        <td class="text-muted small fw-medium">{{ \Carbon\Carbon::parse($pasien->tgl_registrasi)->format('d/m/Y') }}</td>
                        <td class="text-muted small fw-medium">{{ $pasien->jam_reg }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-search fa-3x mb-3 opacity-50"></i>
                                <h6 class="text-muted">Tidak ada data</h6>
                                <p class="mb-0">Tidak ada data pasien sesuai filter yang dipilih</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginasi (Tidak Berubah) --}}
        @if($daftarPasienBelumNota->hasPages())
        <div class="card-footer border-0 bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted fw-medium">
                    Menampilkan {{ $daftarPasienBelumNota->firstItem() }}-{{ $daftarPasienBelumNota->lastItem() }}
                    dari {{ $daftarPasienBelumNota->total() }} total data
                    (Halaman {{ $daftarPasienBelumNota->currentPage() }} dari {{ $daftarPasienBelumNota->lastPage() }})
                </small>
                <div>
                    {{ $daftarPasienBelumNota->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Empty State Global (Tidak Berubah) --}}
    @if(($statistik->total_pasien ?? 0) == 0 && $daftarPasienBelumNota->count() == 0)
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="fas fa-calendar-times fa-4x text-muted opacity-50"></i>
        </div>
        <h5 class="text-dark fw-bold">Tidak Ada Data</h5>
        <p class="text-muted mb-0">Tidak ada data pasien untuk tanggal yang dipilih</p>
    </div>
    @endif
</div>

{{-- Loading Indicator (Tidak Berubah) --}}
<div id="loadingIndicator" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.1); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<style>
/* Style section (No change) */
.card { transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.05) !important; }
.card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important; }
.btn-group-sm .btn { padding: 0.3rem 0.6rem; font-size: 0.8rem; border-width: 2px; }
.table td { vertical-align: middle; padding: 1rem 0.7rem; border-bottom: 1px solid #f1f3f4; }
.table tr:hover { background-color: #f8f9fa !important; }
.badge { font-size: 0.75rem; padding: 0.4rem 0.8rem; letter-spacing: 0.5px; text-shadow: 0 1px 2px rgba(0,0,0,0.2); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.progress { border-radius: 15px; background-color: #e9ecef; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1); }
.progress-bar { border-radius: 15px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
.form-control, .form-select { border: 2px solid #e9ecef; border-radius: 8px; }
.form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); }
.spinner-border { width: 3rem; height: 3rem; }
@media (max-width: 991px) {
    .col-6:nth-child(odd) { padding-right: 0.5rem; }
    .col-6:nth-child(even) { padding-left: 0.5rem; }
    .btn-group-sm .btn { padding: 0.2rem 0.4rem; font-size: 0.7rem; }
    .table-responsive { font-size: 0.9rem; }
    .card-body { padding: 1.5rem !important; }
}
@media (prefers-contrast: high) {
    .card { border: 2px solid #000 !important; }
    .text-muted { color: #333 !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- UTILITIES ---
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    function showLoading() {
        document.getElementById('loadingIndicator').classList.remove('d-none');
    }
    function hideLoading() {
        document.getElementById('loadingIndicator').classList.add('d-none');
    }

    // --- LOGIKA FILTER TANGGAL (Visibility Control & Cleanup) ---
    const dateModeSelect = document.getElementById('dateMode');
    const singleDateGroup = document.getElementById('singleDateGroup');
    const rangeStartGroup = document.getElementById('rangeStartGroup');
    const rangeEndGroup = document.getElementById('rangeEndGroup');

    const singleDateInput = singleDateGroup ? singleDateGroup.querySelector('input') : null;
    const rangeStartInput = rangeStartGroup ? rangeStartGroup.querySelector('input') : null;
    const rangeEndInput = rangeEndGroup ? rangeEndGroup.querySelector('input') : null;

    // Status awal mode range diambil dari PHP
    const isInitialLoadRange = {{ $isRangeModeActive ? 'true' : 'false' }};

    function toggleDateInputs() {
        const mode = dateModeSelect.value;
        const isRange = mode === 'range';

        // Atur status tampilan dan input
        // Mode Single
        if (singleDateGroup) {
            singleDateGroup.style.display = isRange ? 'none' : 'block';
            singleDateInput.disabled = isRange;
            singleDateInput.required = !isRange;
            // Penting: Hapus nilai jika dinonaktifkan
            if (isRange) singleDateInput.value = '';
        }

        // Mode Range
        if (rangeStartGroup) {
            rangeStartGroup.style.display = isRange ? 'block' : 'none';
            rangeStartInput.disabled = !isRange;
            rangeStartInput.required = isRange;
            // Penting: Hapus nilai jika dinonaktifkan
            if (!isRange) rangeStartInput.value = '';
        }
        if (rangeEndGroup) {
            rangeEndGroup.style.display = isRange ? 'block' : 'none';
            rangeEndInput.disabled = !isRange;
            rangeEndInput.required = isRange;
            // Penting: Hapus nilai jika dinonaktifkan
            if (!isRange) rangeEndInput.value = '';
        }
    }

    if (dateModeSelect) {
        dateModeSelect.addEventListener('change', toggleDateInputs);
        // Panggil saat inisialisasi untuk memastikan tampilan sesuai dengan state PHP
        toggleDateInputs();
    }


    // --- LOGIKA AUTO SUBMIT (di header tabel) ---

    // Auto submit untuk Items per page
    const perPageSelect = document.querySelector('select[name="per_page"].auto-submit');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            showLoading();
            this.closest('form').submit();
        });
    }

    // Auto submit untuk search form dengan debounce
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const filterStatusSelect = document.querySelector('.auto-submit-search');

    const debouncedSearch = debounce(function() {
        showLoading();
        searchForm.submit();
    }, 500);

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            debouncedSearch();
        });
    }

    if (filterStatusSelect) {
        filterStatusSelect.addEventListener('change', function() {
            showLoading();
            searchForm.submit();
        });
    }

    // --- HIDE LOADING ---
    window.addEventListener('load', hideLoading);
    if (document.readyState === 'complete') {
        hideLoading();
    }
});
</script>
@endsection
