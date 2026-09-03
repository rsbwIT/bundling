@extends('..layout.layoutDashboard')
@section('title', 'Kroscek General Consent')

@push('styles')
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
        .stat-card {
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
@endpush

@section('konten')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 text-dark font-weight-bold">
                <i class="fas fa-file-signature text-primary mr-2"></i>Kroscek General Consent
            </h4>
            <p class="text-muted mb-0">
                Monitoring Kelengkapan General Consent Pasien 
                <span class="badge badge-info ml-1"><i class="fas fa-info-circle"></i> Khusus Ralan: Pasien Baru (Kunjungan Pertama)</span>
            </p>
        </div>
        <div class="text-right">
            <span class="badge badge-light border p-2">
                <i class="far fa-calendar-alt text-primary mr-1"></i>
                @if(!empty($tanggalMulai) && !empty($tanggalSelesai))
                    {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') }}
                @else
                    {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                @endif
            </span>
        </div>
    </div>

    @php
        $isRangeModeActive = !empty($tanggalMulai) && !empty($tanggalSelesai);
        $pctGc = ($statistik->total_wajib_gc > 0) ? round(($statistik->total_sudah_gc / $statistik->total_wajib_gc) * 100, 1) : 0;
    @endphp

    {{-- STATS CARDS --}}
    <div class="row mb-3">
        {{-- Total Pasien Wajib GC --}}
        <div class="col-md-3 col-sm-6 col-12 mb-2">
            <div class="card stat-card border-0 shadow-sm bg-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted font-weight-bold">PASIEN WAJIB GC</small>
                            <h3 class="mb-0 font-weight-bold text-dark">{{ number_format($statistik->total_wajib_gc) }}</h3>
                            <small class="text-muted">Ralan Baru: {{ number_format($statistik->total_ralan_baru) }} | Ranap: {{ number_format($statistik->total_ranap) }}</small>
                        </div>
                        <div class="rounded-circle bg-light p-3 text-primary">
                            <i class="fas fa-user-plus fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sudah General Consent --}}
        <div class="col-md-3 col-sm-6 col-12 mb-2">
            <div class="card stat-card border-0 shadow-sm bg-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-success font-weight-bold">SUDAH GENERAL CONSENT</small>
                            <h3 class="mb-0 font-weight-bold text-success">{{ number_format($statistik->total_sudah_gc) }}</h3>
                            <small class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> {{ $pctGc }}% Lengkap</small>
                        </div>
                        <div class="rounded-circle bg-light p-3 text-success">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Belum General Consent --}}
        <div class="col-md-3 col-sm-6 col-12 mb-2">
            <div class="card stat-card border-0 shadow-sm bg-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-danger font-weight-bold">BELUM GENERAL CONSENT</small>
                            <h3 class="mb-0 font-weight-bold text-danger">{{ number_format($statistik->total_belum_gc) }}</h3>
                            <small class="text-danger"><i class="fas fa-exclamation-circle"></i> Perlu Dilengkapi</small>
                        </div>
                        <div class="rounded-circle bg-light p-3 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GC Terlewat (SEP Ada tapi GC Belum) --}}
        <div class="col-md-3 col-sm-6 col-12 mb-2">
            <a href="{{ url('kroscek-general-consent') }}?filter_status_gc=belum_sep_ada&tanggal={{ $tanggal }}&tanggal_mulai={{ $tanggalMulai }}&tanggal_selesai={{ $tanggalSelesai }}&filter_lanjut={{ request('filter_lanjut', 'wajib_gc') }}&filter_penjamin={{ $filterPenjamin }}" class="text-decoration-none">
                <div class="card stat-card border-0 shadow-sm bg-white" title="Klik untuk memfilter pasien yang SEP-nya sudah dibuat tapi General Consent terlewat">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-danger font-weight-bold"><i class="fas fa-exclamation-circle mr-1"></i>TERLEWAT (SEP ADA)</small>
                                <h3 class="mb-0 font-weight-bold text-danger">{{ number_format($statistik->total_terlewat_sep_ada) }}</h3>
                                <small class="text-danger font-weight-bold"><i class="fas fa-user-times mr-1"></i>Petugas Pembuat Diketahui</small>
                            </div>
                            <div class="rounded-circle bg-light p-3 text-danger">
                                <i class="fas fa-user-tag fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ url('kroscek-general-consent') }}" id="filterForm">
                <div class="row g-2 align-items-end">
                    {{-- Mode Tanggal --}}
                    <div class="col-md-2 col-6 mb-2">
                        <label class="form-label small text-muted font-weight-bold">Mode Tanggal</label>
                        <select id="dateMode" class="form-control form-control-sm">
                            <option value="single" {{ !$isRangeModeActive ? 'selected' : '' }}>Tanggal Tunggal</option>
                            <option value="range" {{ $isRangeModeActive ? 'selected' : '' }}>Rentang Tanggal</option>
                        </select>
                    </div>

                    {{-- Tanggal Tunggal --}}
                    <div class="col-md-2 col-6 mb-2 date-input-group" id="singleDateGroup">
                        <label class="form-label small text-muted font-weight-bold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm"
                            value="{{ $tanggal }}" max="{{ date('Y-m-d') }}">
                    </div>

                    {{-- Rentang Tanggal --}}
                    <div class="col-md-2 col-6 mb-2 date-input-group" id="rangeStartGroup">
                        <label class="form-label small text-muted font-weight-bold">Dari</label>
                        <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                            value="{{ $tanggalMulai }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2 col-6 mb-2 date-input-group" id="rangeEndGroup">
                        <label class="form-label small text-muted font-weight-bold">Sampai</label>
                        <input type="date" name="tanggal_selesai" class="form-control form-control-sm"
                            value="{{ $tanggalSelesai }}" max="{{ date('Y-m-d') }}">
                    </div>

                    {{-- Filter Kategori / Jenis Pasien --}}
                    <div class="col-md-2 col-6 mb-2">
                        <label class="form-label small text-muted font-weight-bold">Kategori Pasien</label>
                        <select name="filter_lanjut" class="form-control form-control-sm">
                            <option value="wajib_gc" {{ request('filter_lanjut', 'wajib_gc') == 'wajib_gc' ? 'selected' : '' }}>Wajib GC (Ralan Baru & Ranap)</option>
                            <option value="ralan_baru" {{ request('filter_lanjut') == 'ralan_baru' ? 'selected' : '' }}>Khusus Ralan Pasien Baru</option>
                            <option value="ranap" {{ request('filter_lanjut') == 'ranap' ? 'selected' : '' }}>Khusus Rawat Inap</option>
                            <option value="igd" {{ request('filter_lanjut') == 'igd' ? 'selected' : '' }}>Khusus IGD</option>
                            <option value="semua" {{ request('filter_lanjut') == 'semua' ? 'selected' : '' }}>Semua Pasien Registrasi</option>
                            <option value="ralan_lama" {{ request('filter_lanjut') == 'ralan_lama' ? 'selected' : '' }}>Ralan Pasien Lama (Tidak Wajib)</option>
                            <option value="batal" {{ request('filter_lanjut') == 'batal' ? 'selected' : '' }}>Pasien Batal</option>
                        </select>
                    </div>

                    {{-- Filter Status GC --}}
                    <div class="col-md-2 col-6 mb-2">
                        <label class="form-label small text-muted font-weight-bold">Status GC</label>
                        <select name="filter_status_gc" class="form-control form-control-sm">
                            <option value="semua" {{ request('filter_status_gc', 'semua') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                            <option value="sudah" {{ request('filter_status_gc') == 'sudah' ? 'selected' : '' }}>Sudah General Consent</option>
                            <option value="belum" {{ request('filter_status_gc') == 'belum' ? 'selected' : '' }}>Belum General Consent</option>
                            <option value="belum_sep_ada" {{ request('filter_status_gc') == 'belum_sep_ada' ? 'selected' : '' }}>⚠️ Terlewat (SEP Ada, GC Belum)</option>
                        </select>
                    </div>

                    {{-- Filter Penjamin --}}
                    <div class="col-md-2 col-6 mb-2">
                        <label class="form-label small text-muted font-weight-bold">Penjamin</label>
                        <select name="filter_penjamin" class="form-control form-control-sm">
                            <option value="">Semua Penjamin</option>
                            @foreach ($allPenjab as $pj)
                                <option value="{{ $pj->kd_pj }}" {{ request('filter_penjamin') == $pj->kd_pj ? 'selected' : '' }}>
                                    {{ $pj->png_jawab }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search Input --}}
                    <div class="col-md-3 col-12 mb-2">
                        <label class="form-label small text-muted font-weight-bold">Pencarian</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="No Rawat / RM / Pasien / Dokter / SEP / Petugas..." value="{{ $searchTerm }}">
                    </div>

                    {{-- Per Page --}}
                    <div class="col-md-1 col-6 mb-2">
                        <label class="form-label small text-muted font-weight-bold">Limit</label>
                        <select name="per_page" class="form-control form-control-sm">
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200</option>
                        </select>
                    </div>

                    {{-- Tombol Cari & Reset --}}
                    <div class="col-md-2 col-6 mb-2 d-flex">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill mr-1" id="submitBtn">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="{{ url('kroscek-general-consent') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>

                {{-- Filter Pengecualian Poliklinik --}}
                <div class="row mt-2">
                    <div class="col-12">
                        <a class="btn btn-outline-info btn-xs w-100 text-left" data-toggle="collapse" href="#collapsePoli" role="button" aria-expanded="false" aria-controls="collapsePoli">
                            <i class="fas fa-filter mr-1"></i> Filter Pengecualian Poliklinik (Pilih poli yang ingin diabaikan)
                            @if(!empty($excludedPoli))
                                <span class="badge badge-warning ml-1">{{ count($excludedPoli) }} Poli Diabaikan</span>
                            @endif
                        </a>
                        <div class="collapse {{ !empty($excludedPoli) ? 'show' : '' }}" id="collapsePoli">
                            <div class="poli-filter-container mt-2">
                                <div class="row">
                                    @forelse ($allPoli as $poli)
                                        <div class="col-md-3 col-sm-6 col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="excluded_poli[]"
                                                       value="{{ $poli->kd_poli }}"
                                                       id="poli_{{ $poli->kd_poli }}"
                                                       {{ in_array($poli->kd_poli, $excludedPoli) ? 'checked' : '' }}>
                                                <label class="form-check-label small text-muted" for="poli_{{ $poli->kd_poli }}">
                                                    {{ $poli->nm_poli }}
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted small">Tidak ada data poliklinik.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-list mr-1"></i> Daftar Pasien & Status General Consent
                <span class="badge badge-primary ml-1">{{ $daftarPasien->total() }} Data</span>
            </h6>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="copyButton" title="Salin tabel ke Excel/Clipboard">
                <i class="fas fa-copy mr-1"></i> Salin Tabel
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped text-xs mb-0" id="tableToCopy" style="white-space: nowrap;">
                    <thead style="background-color: #f1f4f9; color: #333;">
                        <tr>
                            <th class="text-center" width="40">No</th>
                            <th>No. Rawat</th>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th class="text-center">Status Pasien</th>
                            <th>Poliklinik</th>
                            <th>Dokter DPJP</th>
                            <th>Penjamin</th>
                            <th>Tgl & Jam Reg</th>
                            <th class="text-center">Status Rawat</th>
                            <th class="text-center">General Consent</th>
                            <th class="text-center">Lihat Form</th>
                            <th>SEP & Pembuat SEP</th>
                            <th>No. Surat / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = ($daftarPasien->currentPage() - 1) * $daftarPasien->perPage() + 1;
                        @endphp
                        @forelse ($daftarPasien as $item)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>
                                    <strong>{{ $item->no_rawat }}</strong>
                                </td>
                                <td>{{ $item->no_rkm_medis }}</td>
                                <td>
                                    <span class="font-weight-bold">{{ $item->nm_pasien }}</span>
                                    <span class="text-muted ml-1">({{ $item->jk }})</span>
                                </td>
                                <td class="text-center">
                                    @if($item->stts_daftar == 'Baru')
                                        <span class="badge badge-primary" title="Pasien Baru (Pertama Kali Berobat di RS)">
                                            <i class="fas fa-star text-xs"></i> Baru
                                        </span>
                                    @else
                                        <span class="badge badge-secondary" title="Pasien Lama">Lama</span>
                                    @endif
                                </td>
                                <td>{{ $item->nm_poli }}</td>
                                <td>{{ $item->nm_dokter }}</td>
                                <td>{{ $item->png_jawab }}</td>
                                <td>{{ $item->tgl_registrasi }} {{ $item->jam_reg }}</td>
                                <td class="text-center">
                                    @if($item->stts == 'Batal')
                                        <span class="badge badge-secondary">Batal</span>
                                    @elseif($item->kd_poli == 'IGDK')
                                        <span class="badge badge-danger">IGD</span>
                                    @elseif($item->status_lanjut == 'Ranap')
                                        <span class="badge badge-warning">Ranap</span>
                                    @else
                                        <span class="badge badge-info">Ralan</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->stts == 'Batal')
                                        <span class="badge badge-light border">Batal</span>
                                    @elseif($item->status_gc == 'Sudah')
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-check-circle mr-1"></i> Sudah Ada
                                        </span>
                                    @elseif($item->is_wajib_gc == 'Ya')
                                        @if($item->sep_no_sep)
                                            <span class="badge badge-danger px-2 py-1" title="SEP Sudah Dibuat tapi GC Belum Dibuat!">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Terlewat (SEP Ada)
                                            </span>
                                        @else
                                            <span class="badge badge-danger px-2 py-1">
                                                <i class="fas fa-times-circle mr-1"></i> Belum Ada
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge badge-light border text-muted px-2 py-1" title="Ralan Pasien Lama tidak wajib General Consent per kunjungan">
                                            <i class="fas fa-minus-circle mr-1"></i> Pasien Lama (Tidak Wajib)
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->spu_no_surat)
                                        <button type="button" class="btn btn-primary btn-xs px-2 py-1 shadow-sm btn-lihat-form"
                                                data-url="{{ route('kroscek.general-consent.lihat-form', $item->spu_no_surat) }}"
                                                data-title="General Consent - {{ $item->nm_pasien }} ({{ $item->no_rawat }})"
                                                title="Lihat Formulir General Consent">
                                            <i class="fas fa-file-signature mr-1"></i> Lihat Form
                                        </button>
                                    @elseif(!empty($item->bdp_file))
                                        <button type="button" class="btn btn-info btn-xs px-2 py-1 shadow-sm btn-lihat-form"
                                                data-url="{{ rtrim(env('URL_KHANZA', 'http://192.168.5.88'), '/') }}/webapps/berkasrawat/{{ $item->bdp_file }}"
                                                data-title="Berkas General Consent (PDF) - {{ $item->nm_pasien }} ({{ $item->no_rawat }})"
                                                title="Lihat Berkas General Consent PDF">
                                            <i class="fas fa-file-pdf mr-1"></i> Lihat PDF
                                        </button>
                                    @else
                                        <span class="text-muted small"><i class="fas fa-minus mr-1"></i> Belum Dibuat</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->sep_no_sep)
                                        <span class="badge badge-light border text-dark font-weight-bold" title="Nomor SEP BPJS">
                                            <i class="fas fa-file-invoice text-primary mr-1"></i>{{ $item->sep_no_sep }}
                                        </span>
                                        @if(isset($item->total_sep) && $item->total_sep > 1)
                                            <span class="badge badge-info ml-1" title="Pasien memiliki total {{ $item->total_sep }} SEP terdaftar (misal SEP Ralan & Ranap)">+{{ $item->total_sep - 1 }} SEP</span>
                                        @endif
                                        @if($item->status_gc == 'Belum' && $item->is_wajib_gc == 'Ya')
                                            <div class="mt-1">
                                                <span class="badge badge-danger px-2 py-1" title="Petugas yang membuat SEP tetapi tidak membuat General Consent (ID BPJS: {{ $item->sep_user }})">
                                                    <i class="fas fa-user-times mr-1"></i> Pembuat SEP: <strong>{{ $item->sep_nama_petugas ?? $item->sep_user }}</strong>
                                                </span>
                                                @if(!empty($item->sep_user_candidates))
                                                    <div class="text-muted text-xs mt-1" style="font-size: 10px;" title="Beberapa petugas memiliki awalan NIP/NIK yang sama di BPJS">
                                                        <i class="fas fa-info-circle text-warning mr-1"></i>{{ $item->sep_user_candidates }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="mt-1 text-muted text-xs">
                                                <i class="fas fa-user-edit text-info mr-1"></i> Pembuat SEP: <strong>{{ $item->sep_nama_petugas ?? $item->sep_user }}</strong>
                                                @if(!empty($item->sep_user_candidates))
                                                    <span class="text-muted ml-1" style="font-size: 10px;" title="{{ $item->sep_user_candidates }}"><i class="fas fa-info-circle text-secondary"></i></span>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted text-xs"><i class="fas fa-minus mr-1"></i> Belum Ada SEP</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->spu_no_surat)
                                        <span class="text-dark font-weight-bold">{{ $item->spu_no_surat }}</span>
                                        @if($item->spu_nama_petugas || $item->spu_nip)
                                            <div class="mt-1">
                                                <span class="badge badge-success px-2 py-1" title="Petugas Admisi yang membuat formulir General Consent (NIP: {{ $item->spu_nip }})">
                                                    <i class="fas fa-user-check mr-1"></i> Pembuat GC: <strong>{{ $item->spu_nama_petugas ?? $item->spu_nip }}</strong>
                                                </span>
                                            </div>
                                        @endif
                                        @if($item->spu_nama_pj)
                                            <small class="text-muted d-block mt-1"><i class="fas fa-user-friends mr-1"></i>PJ: {{ $item->spu_nama_pj }}</small>
                                        @endif
                                    @elseif($item->status_gc == 'Sudah')
                                        <span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Terdata (Digital)</span>
                                    @elseif($item->is_wajib_gc == 'Ya')
                                        <span class="badge badge-light border text-danger small"><i class="fas fa-exclamation-triangle mr-1"></i> Wajib Lengkapi</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                    Tidak ada data pasien yang ditemukan untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot style="background-color: #f8f9fa; font-weight: bold;">
                        <tr>
                            <td colspan="10" class="text-right">TOTAL HALAMAN INI</td>
                            <td class="text-center">
                                <span class="text-success">{{ $daftarPasien->where('status_gc', 'Sudah')->count() }} Sudah</span> / 
                                <span class="text-danger">{{ $daftarPasien->where('status_gc', 'Belum')->where('is_wajib_gc', 'Ya')->count() }} Belum</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary">{{ $daftarPasien->filter(fn($i) => !empty($i->spu_no_surat) || !empty($i->bdp_file))->count() }} Form</span>
                            </td>
                            <td class="text-center text-danger">
                                {{ $daftarPasien->where('status_gc', 'Belum')->where('is_wajib_gc', 'Ya')->whereNotNull('sep_no_sep')->count() }} Terlewat
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @if ($daftarPasien->hasPages())
            <div class="card-footer bg-white py-2 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $daftarPasien->firstItem() }} s/d {{ $daftarPasien->lastItem() }} dari {{ $daftarPasien->total() }} data
                </small>
                <div>
                    {{ $daftarPasien->appends(request()->input())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- MODAL PREVIEW GENERAL CONSENT --}}
<div class="modal fade" id="modalPreviewGC" tabindex="-1" role="dialog" aria-labelledby="modalPreviewGCLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 950px; height: 90vh; margin: 1.75rem auto;">
        <div class="modal-content shadow-lg" style="height: 90vh; border-radius: 8px; overflow: hidden; border: none;">
            <div class="modal-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                <h6 class="modal-title font-weight-bold text-dark mb-0 text-truncate mr-2" id="modalPreviewGCLabel">
                    <i class="fas fa-file-signature text-primary mr-2"></i>
                    <span id="modalPreviewTitle">Pratinjau General Consent</span>
                </h6>
                <div class="d-flex align-items-center flex-shrink-0">
                    <a href="#" target="_blank" id="btnOpenNewTab" class="btn btn-sm btn-outline-primary mr-1" title="Buka di Tab Baru">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka di Tab Baru
                    </a>
                    <button type="button" id="btnPrintModal" class="btn btn-sm btn-primary mr-2" title="Cetak Formulir">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </button>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative" style="background-color: #525659; height: calc(90vh - 56px);">
                <div id="modalLoadingSpinner" class="d-flex justify-content-center align-items-center position-absolute w-100 h-100 bg-white" style="z-index: 10; top: 0; left: 0;">
                    <div class="text-center text-primary">
                        <div class="spinner-border mb-2" role="status" style="width: 2.5rem; height: 2.5rem;">
                            <span class="sr-only">Memuat form...</span>
                        </div>
                        <div class="small font-weight-bold text-dark">Memuat Formulir General Consent...</div>
                    </div>
                </div>
                <iframe id="iframePreviewGC" src="" style="width: 100%; height: 100%; border: none; display: block;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mode Tanggal Switcher
    const dateMode = document.getElementById('dateMode');
    const singleDateGroup = document.getElementById('singleDateGroup');
    const rangeStartGroup = document.getElementById('rangeStartGroup');
    const rangeEndGroup = document.getElementById('rangeEndGroup');
    const singleDateInput = singleDateGroup.querySelector('input');
    const rangeStartInput = rangeStartGroup.querySelector('input');
    const rangeEndInput = rangeEndGroup.querySelector('input');

    function toggleDateInputs() {
        if (dateMode.value === 'range') {
            singleDateGroup.style.display = 'none';
            rangeStartGroup.style.display = 'block';
            rangeEndGroup.style.display = 'block';
            singleDateInput.disabled = true;
            rangeStartInput.disabled = false;
            rangeEndInput.disabled = false;
        } else {
            singleDateGroup.style.display = 'block';
            rangeStartGroup.style.display = 'none';
            rangeEndGroup.style.display = 'none';
            singleDateInput.disabled = false;
            rangeStartInput.disabled = true;
            rangeEndInput.disabled = true;
        }
    }

    dateMode.addEventListener('change', toggleDateInputs);
    toggleDateInputs();

    // Modal Preview General Consent Handler
    const modalEl = $('#modalPreviewGC');
    const iframeEl = document.getElementById('iframePreviewGC');
    const spinnerEl = document.getElementById('modalLoadingSpinner');
    const modalTitleEl = document.getElementById('modalPreviewTitle');
    const btnOpenNewTab = document.getElementById('btnOpenNewTab');
    const btnPrintModal = document.getElementById('btnPrintModal');

    document.querySelectorAll('.btn-lihat-form').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            const title = this.getAttribute('data-title') || 'Pratinjau General Consent';

            modalTitleEl.innerText = title;
            btnOpenNewTab.setAttribute('href', url);

            // Tampilkan spinner dan muat iframe
            spinnerEl.style.display = 'flex';
            iframeEl.src = url;

            iframeEl.onload = function() {
                spinnerEl.style.display = 'none';
            };

            modalEl.modal('show');
        });
    });

    // Reset iframe saat modal ditutup
    modalEl.on('hidden.bs.modal', function () {
        iframeEl.src = 'about:blank';
    });

    // Print dari dalam iframe
    if (btnPrintModal) {
        btnPrintModal.addEventListener('click', function() {
            try {
                if (iframeEl && iframeEl.contentWindow) {
                    iframeEl.contentWindow.focus();
                    iframeEl.contentWindow.print();
                }
            } catch (err) {
                // Fallback jika iframe di domain berbeda
                window.open(btnOpenNewTab.getAttribute('href'), '_blank');
            }
        });
    }

    // Salin Tabel ke Clipboard (Format Excel Tab-delimited)
    document.getElementById("copyButton").addEventListener("click", function() {
        const table = document.getElementById("tableToCopy");
        const lines = [];

        // Header
        table.querySelectorAll('thead tr').forEach(row => {
            const cells = [];
            row.querySelectorAll('th').forEach(cell => cells.push(cell.innerText.trim()));
            lines.push(cells.join('\t'));
        });

        // Body & Foot
        table.querySelectorAll('tbody tr, tfoot tr').forEach(row => {
            const cells = [];
            row.querySelectorAll('td').forEach(cell => {
                const colspan = parseInt(cell.getAttribute('colspan')) || 1;
                cells.push(cell.innerText.trim());
                for (let i = 1; i < colspan; i++) cells.push('');
            });
            lines.push(cells.join('\t'));
        });

        const text = lines.join('\n');
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => alert("Tabel berhasil disalin ke clipboard!"));
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy');
                alert("Tabel berhasil disalin ke clipboard!");
            } catch(e) {
                alert("Gagal menyalin tabel.");
            }
            document.body.removeChild(ta);
        }
    });
});
</script>
@endsection
