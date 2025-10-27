@extends('..layout.layoutDashboard')
@section('title', 'Kroscek Pasien')

@section('konten')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-dark">Kroscek Pasien</h4>
            <p class="text-muted mb-0">Monitoring Nota Pembayaran</p>
        </div>
        <div class="text-end">
            <small class="text-muted">{{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</small>
        </div>
    </div>

    <!-- Filter Form - Compact with Auto Submit -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ url('kroscek-pasien') }}" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm auto-submit"
                               value="{{ $tanggal }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Dari</label>
                        <input type="date" name="tanggal_mulai" class="form-control form-control-sm auto-submit"
                               value="{{ $tanggalMulai }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Sampai</label>
                        <input type="date" name="tanggal_selesai" class="form-control form-control-sm auto-submit"
                               value="{{ $tanggalSelesai }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Filter Data</label>
                        <select name="filter_type" class="form-select form-select-sm auto-submit">
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

                <!-- Quick Filters -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ url('kroscek-pasien') }}?tanggal={{ date('Y-m-d') }}"
                               class="btn {{ $tanggal == date('Y-m-d') ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Hari Ini
                            </a>
                            <a href="{{ url('kroscek-pasien') }}?tanggal={{ date('Y-m-d', strtotime('-1 day')) }}"
                               class="btn {{ $tanggal == date('Y-m-d', strtotime('-1 day')) ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Kemarin
                            </a>
                            <a href="{{ url('kroscek-pasien') }}?tanggal_mulai={{ date('Y-m-d', strtotime('-6 days')) }}&tanggal_selesai={{ date('Y-m-d') }}"
                               class="btn btn-outline-secondary">
                                7 Hari
                            </a>
                            <a href="{{ url('kroscek-pasien') }}?tanggal_mulai={{ date('Y-m-01') }}&tanggal_selesai={{ date('Y-m-d') }}"
                               class="btn btn-outline-secondary">
                                Bulan Ini
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards - Warna yang lebih jelas dan kontras tinggi -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_pasien ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Total Pasien</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2">
                        <i class="fas fa-stethoscope fa-2x"></i>
                    </div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_ralan ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Rawat Jalan</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2">
                        <i class="fas fa-ambulance fa-2x"></i>
                    </div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_igd ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Total IGD</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2">
                        <i class="fas fa-bed fa-2x"></i>
                    </div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_ranap_igd ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Ranap IGD</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2">
                        <i class="fas fa-hospital fa-2x"></i>
                    </div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_ranap_poli ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Ranap Poli</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8d7da 0%, #f1c2c7 100%);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_batal ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Batal</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffe8a1 0%, #ffd93d 100__);">
                <div class="card-body p-3 text-center">
                    <div class="text-dark mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h4 class="mb-1 text-dark fw-bold">{{ number_format($statistik->total_belum_nota ?? 0) }}</h4>
                    <small class="text-dark fw-medium">Ralan Belum Nota</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar - Menggunakan total_pasien_aktif -->
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

    <!-- Data Table with Auto-filter Search and 100 items per page -->
    @if($daftarPasienBelumNota->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0 bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0 fw-bold text-dark">
                        @php
                            $titles = [
                                'semua' => 'Semua Pasien',
                                'belum_nota' => 'Pasien Rawat Jalan Belum Nota', // Updated title
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

                <!-- Items per page selector -->
                <div class="col-auto me-3">
                    <form method="GET" action="{{ url('kroscek-pasien') }}" class="d-flex align-items-center">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <input type="hidden" name="filter_type" value="{{ request('filter_type', 'semua') }}">
                        <input type="hidden" name="search" value="{{ $searchTerm }}">
                        <input type="hidden" name="filter_status" value="{{ $filterStatus }}">
                        <small class="text-muted me-2">Per halaman:</small>
                        <select name="per_page" class="form-select form-select-sm auto-submit" style="width: 80px;">
                            <option value="25" {{ ($perPage ?? 100) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 100) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 100) == 100 ? 'selected' : '' }}>100</option>
                            <option value="250" {{ ($perPage ?? 100) == 250 ? 'selected' : '' }}>250</option>
                        </select>
                    </form>
                </div>

                <div class="col-auto">
                    <!-- Search Form with Auto Submit -->
                    <form method="GET" action="{{ url('kroscek-pasien') }}" class="d-flex" id="searchForm">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <input type="hidden" name="filter_type" value="{{ request('filter_type', 'semua') }}">
                        <input type="hidden" name="per_page" value="{{ $perPage ?? 100 }}">
                        <div class="input-group input-group-sm me-2">
                            <input type="text" name="search" class="form-control" id="searchInput"
                                   placeholder="Cari pasien..." value="{{ $searchTerm }}" style="width: 180px;">
                            <select name="filter_status" class="form-select auto-submit-search">
                                <option value="">Semua Status</option>
                                <option value="Ranap" {{ $filterStatus == 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
                                <option value="Ralan" {{ $filterStatus == 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                                <option value="IGD" {{ $filterStatus == 'IGD' ? 'selected' : '' }}>IGD</option>
                                <option value="Sudah_Nota" {{ $filterStatus == 'Sudah_Nota' ? 'selected' : '' }}>Sudah Nota</option>
                                <option value="Belum_Nota" {{ $filterStatus == 'Belum_Nota' ? 'selected' : '' }}>Belum Nota</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

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

        <!-- Pagination with page info -->
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

    <!-- Empty State -->
    @if(($statistik->total_pasien ?? 0) == 0)
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="fas fa-calendar-times fa-4x text-muted opacity-50"></i>
        </div>
        <h5 class="text-dark fw-bold">Tidak Ada Data</h5>
        <p class="text-muted mb-0">Tidak ada data pasien untuk tanggal yang dipilih</p>
    </div>
    @endif
</div>

<!-- Loading Indicator -->
<div id="loadingIndicator" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.1); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<style>
/* Card hover effects */
.card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05) !important;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

/* Button styling */
.btn-group-sm .btn {
    padding: 0.3rem 0.6rem;
    font-size: 0.8rem;
    border-width: 2px;
}

/* Table styling */
.table td {
    vertical-align: middle;
    padding: 1rem 0.7rem;
    border-bottom: 1px solid #f1f3f4;
}

.table tr:hover {
    background-color: #f8f9fa !important;
}

/* Badge styling dengan kontras tinggi */
.badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    letter-spacing: 0.5px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Progress bar */
.progress {
    border-radius: 15px;
    background-color: #e9ecef;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
}

.progress-bar {
    border-radius: 15px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Input styling */
.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Loading indicator */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .col-6:nth-child(odd) {
        padding-right: 0.5rem;
    }
    .col-6:nth-child(even) {
        padding-left: 0.5rem;
    }

    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }

    .table-responsive {
        font-size: 0.9rem;
    }

    .card-body {
        padding: 1.5rem !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .card {
        border: 2px solid #000 !important;
    }

    .text-muted {
        color: #333 !important;
    }
}
</style>

<!-- JavaScript for Auto Submit and Debounce -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debounce function untuk mencegah terlalu banyak request
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

    // Show loading indicator
    function showLoading() {
        document.getElementById('loadingIndicator').classList.remove('d-none');
    }

    // Hide loading indicator
    function hideLoading() {
        document.getElementById('loadingIndicator').classList.add('d-none');
    }

    // Auto submit untuk form filter utama (date, filter_type, per_page)
    const autoSubmitElements = document.querySelectorAll('.auto-submit');
    autoSubmitElements.forEach(element => {
        element.addEventListener('change', function() {
            showLoading();

            // Submit the correct form based on element
            if (element.name === 'per_page') {
                element.closest('form').submit();
            } else {
                document.getElementById('filterForm').submit();
            }
        });
    });

    // Auto submit untuk search form dengan debounce
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const filterStatusSelect = document.querySelector('.auto-submit-search');

    // Debounced search function
    const debouncedSearch = debounce(function() {
        showLoading();
        searchForm.submit();
    }, 500); // Wait 500ms after user stops typing

    // Search input dengan debounce
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            debouncedSearch();
        });
    }

    // Filter status langsung submit
    if (filterStatusSelect) {
        filterStatusSelect.addEventListener('change', function() {
            showLoading();
            searchForm.submit();
        });
    }

    // Hide loading on page load
    window.addEventListener('load', function() {
        hideLoading();
    });

    // Hide loading if page is already loaded
    if (document.readyState === 'complete') {
        hideLoading();
    }
});
</script>
@endsection
