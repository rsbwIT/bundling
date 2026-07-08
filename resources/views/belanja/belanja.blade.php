@extends('layout.layoutDashboard')

@section('title','Rencana Belanja Farmasi')

@section('konten')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
:root{
    --primary:#f3f6f9;
    --secondary:#eef2f6;
    --success:#16a34a;
    --danger:#b91c1c;
    --warning:#d97706;
    --dark:#1f2937;
    --light:#ffffff;
    --text:#374151;
    --muted:#6b7280;
}
body{ background:#f1f5f9; }

.main-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

/* Clean Unified Filter Card */
.filter-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
    margin-bottom: 24px;
    border: 1px solid rgba(148, 163, 184, 0.15);
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin-bottom: 0;
}

.filter-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.filter-input-wrapper svg {
    position: absolute;
    left: 14px;
    width: 16px;
    height: 16px;
    color: #94a3b8;
    pointer-events: none;
    opacity: 0.8;
}

.filter-input-wrapper .form-control {
    padding-left: 42px;
    height: 44px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid rgba(148, 163, 184, 0.25);
    color: #1e293b;
    font-size: 0.92rem;
    box-shadow: none;
    transition: all .2s ease;
    width: 100%;
}

.filter-input-wrapper .form-control:focus {
    border-color: #3b82f6;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    outline: none;
}

.filter-help {
    font-size: 0.85rem;
    color: #6b7280;
}

/* Actions Row styling */
.filter-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
    border-top: 1px solid #f1f5f9;
    padding-top: 18px;
    margin-top: 10px;
}

.btn-filter-action {
    height: 42px;
    padding: 0 20px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-filter-apply {
    background: #0f766e;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.15);
}
.btn-filter-apply:hover {
    background: #0d5e58;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(15, 118, 110, 0.25);
}

.btn-filter-export {
    background: #3b82f6;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}
.btn-filter-export:hover {
    background: #2563eb;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25);
}

.btn-filter-reset {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}
.btn-filter-reset:hover {
    background: #e2e8f0;
    color: #334155;
    transform: translateY(-1px);
}

.btn-filter-refresh {
    background: #ffffff;
    color: #64748b;
    border: 1px solid #e2e8f0;
}
.btn-filter-refresh:hover {
    background: #f8fafc;
    color: #475569;
    transform: translateY(-1px);
}
.filter-active-pill{background:#eef2f6;padding:6px 12px;border-radius:12px;border:1px solid #e6eef6;color:var(--text);font-weight:600;display:inline-flex;align-items:center;gap:8px}

.summary-box{
    border-radius:24px;
    padding:20px 24px;
    color:var(--dark);
    position:relative;
    overflow:hidden;
    margin-bottom:16px;
    background:#ffffff;
    border:1px solid rgba(148,163,184,0.16);
    box-shadow:0 18px 40px rgba(15,23,42,0.06);
    transition:transform .2s ease, box-shadow .2s ease;
}
.summary-box:hover{ transform:translateY(-2px); box-shadow:0 22px 48px rgba(15,23,42,0.1); }
.summary-box h6{ margin-bottom:10px; color:#475569; font-size:0.78rem; letter-spacing:0.08em; text-transform:uppercase; }
.summary-box h2{ font-weight:800; margin:0; font-size:2rem; letter-spacing:-0.03em; }
.summary-box.mini-summary{ padding:16px 18px; display:flex; flex-direction:column; justify-content:space-between; min-height:140px; }
.summary-box.mini-summary h6{ font-size:0.78rem; margin-bottom:10px; }
.summary-box.mini-summary h5{ font-size:1.05rem; margin:0 0 10px 0; font-weight:800; color:#0f172a; line-height:1.2; }
.summary-box.mini-summary .value{ font-size:1rem; color:#334155; font-weight:700; }

.variant-stok      { border-left:6px solid #60a5fa; }
.variant-keluar    { border-left:6px solid #f6ad55; }
.variant-kebutuhan { border-left:6px solid #fca5a5; }
.variant-orange    { border-left:6px solid #f97316; background:linear-gradient(180deg,#fff7ed,#ffffff); }
.variant-red       { border-left:6px solid #ef4444; background:linear-gradient(180deg,#fff1f2,#ffffff); }
.variant-blue      { border-left:6px solid #3b82f6; background:linear-gradient(180deg,#eff6ff,#ffffff); }
.variant-gray      { border-left:6px solid #6b7280; background:linear-gradient(180deg,#f3f4f6,#ffffff); }

.gudang-wrapper{ max-height:250px; overflow:auto; border:1px solid #e2e8f0; border-radius:12px; background:#fff; }
/* Clean Flat Table Styling */
.table-responsive {
    max-height: 750px;
    overflow: auto;
    position: relative;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #ffffff;
}

#tableBelanja {
    width: 100% !important;
    border-collapse: collapse;
}

#tableBelanja thead th {
    position: sticky;
    top: 0;
    z-index: 105;
    background: #f8fafc !important;
    color: #475569 !important;
    font-weight: 600;
    text-transform: none;
    font-size: 0.85rem;
    padding: 12px 14px;
    border-bottom: 2px solid #cbd5e1 !important;
    border-top: none !important;
    border-left: 1px solid #e2e8f0 !important;
    border-right: 1px solid #e2e8f0 !important;
    text-align: center !important;
    vertical-align: middle;
    white-space: nowrap;
}

#tableBelanja tbody tr {
    background: #ffffff;
    transition: background 0.15s ease;
}

#tableBelanja tbody tr:nth-of-type(even) {
    background: #f8fafc;
}

#tableBelanja tbody tr:hover {
    background: #f1f5f9 !important;
}

#tableBelanja tbody td {
    padding: 10px 14px;
    font-size: 0.85rem;
    color: #334155;
    border: 1px solid #e2e8f0 !important;
    vertical-align: middle;
    white-space: nowrap;
}

/* Numeric data cells alignment */
.cell-number {
    text-align: right;
    font-weight: 600;
}

/* Text styles for key metrics instead of blocky badges */
.text-stock {
    color: #2563eb;
    font-weight: 700;
}

.text-keluar {
    color: #ea580c;
    font-weight: 700;
}

.text-kebutuhan {
    color: #dc2626;
    font-weight: 700;
}

.text-kebutuhan-zero {
    color: #64748b;
    font-weight: 500;
}

/* Monospace item codes - simplified */
.code-item {
    font-family: SFMono-Regular, Consolas, "Liberation Mono", Menlo, monospace;
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.78rem;
    color: #475569;
}

/* Customizing DataTables Pagination to look clean and flat */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #475569 !important;
    margin: 0 2px !important;
    padding: 5px 10px !important;
    font-weight: 600 !important;
    font-size: 0.82rem !important;
    transition: all 0.15s ease !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current, 
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #0f766e !important;
    color: #ffffff !important;
    border-color: #0f766e !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f1f5f9 !important;
    color: #1e293b !important;
    border-color: #cbd5e1 !important;
}

.dataTables_wrapper .dataTables_info {
    font-size: 0.82rem;
    color: #64748b;
    padding-top: 12px;
}

.switch{ position:relative; display:inline-block; width:34px; height:20px; vertical-align:middle; }
.switch input{ display:none; }
.slider{
    position:absolute; top:0; left:0; right:0; bottom:0;
    cursor:pointer; background:#e5e7eb; transition:.22s ease;
    border-radius:999px; box-shadow:inset 0 1px 2px rgba(16,24,40,0.08);
}
.slider:before{
    content:''; position:absolute; width:16px; height:16px; left:2px; top:2px;
    background:white; transition:transform .22s ease, color .22s ease, background .22s ease;
    border-radius:50%; box-shadow:0 2px 4px rgba(16,24,40,0.08);
}
.switch input:checked + .slider{ background:var(--success); }
.switch input:checked + .slider:before{ transform:translateX(14px); background:#16b981; }
.switch, .slider{ cursor:pointer; }

.gudang-wrapper td:nth-child(4){ display:flex; align-items:center; justify-content:center; gap:6px; }

.badge-active, .badge-inactive{
    display:inline-flex; align-items:center; justify-content:center; gap:6px;
    padding:4px 10px; font-weight:700; border-radius:999px; color:white;
    box-shadow:0 4px 10px rgba(15,23,42,0.05); font-size:0.78rem;
}
.badge-active{ background:linear-gradient(135deg,#34d399,#10b981); }
.badge-inactive{ background:linear-gradient(135deg,#f87171,#ef4444); }

.dataTables_filter input{ border-radius:10px !important; }

@media (max-width: 780px){
    .filter-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    .filter-actions {
        flex-direction: column;
        align-items: stretch;
    }
    .btn-filter-action {
        width: 100%;
    }
}

/* Hide default DataTables buttons and filter */
.dt-buttons, .dataTables_filter {
    display: none !important;
}

/* Custom premium styling for Copy Button and Search Box */
.btn-copy-custom {
    background: #16a34a;
    color: #ffffff;
    border: none;
    padding: 8px 18px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.15);
    transition: all .2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}
.btn-copy-custom:hover {
    background: #15803d;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(22, 163, 74, 0.25);
    color: #ffffff;
    text-decoration: none;
}
.btn-copy-custom:active {
    transform: translateY(0);
}

.search-input-custom-wrapper {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.search-icon-custom {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    font-size: 0.9rem;
    pointer-events: none;
}
.search-input-custom {
    padding: 8px 16px 8px 40px;
    border-radius: 12px;
    border: 1px solid rgba(148, 163, 184, 0.2);
    background: #ffffff;
    color: #1e293b;
    font-size: 0.9rem;
    width: 260px;
    height: 38px;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.04);
    transition: all 0.2s ease;
}
.search-input-custom:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    width: 320px;
}

.btn-atur-gudang {
    height: 44px;
    border-radius: 14px;
    font-weight: 700;
    background: #eff6ff;
    border: 1px solid rgba(37, 99, 235, 0.25);
    color: #2563eb;
    font-size: 0.94rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    box-shadow: none;
    transition: all .2s ease;
    cursor: pointer;
    width: 100%;
}
.btn-atur-gudang:hover {
    background: #dbeafe;
    border-color: rgba(37, 99, 235, 0.4);
    color: #1d4ed8;
    transform: translateY(-1px);
}
.btn-atur-gudang:active {
    transform: translateY(0);
}
</style>

<div class="card main-card">
<div class="card-body">

    <form method="GET" action="{{ route('belanja.index') }}">

        <div class="filter-card">

            <div class="filter-grid">

                <div class="filter-group">
                    <label>Tanggal Awal</label>
                    <div class="filter-input-wrapper">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#6B7280" stroke-width="1.8"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke="#6B7280" stroke-width="1.8"/>
                            <line x1="8" y1="2" x2="8" y2="6" stroke="#6B7280" stroke-width="1.8"/>
                            <line x1="3" y1="10" x2="21" y2="10" stroke="#6B7280" stroke-width="1.8"/>
                        </svg>
                        <input type="date" name="tanggal_awal" class="form-control" value="{{ $tanggal_awal }}">
                    </div>
                </div>

                <div class="filter-group">
                    <label>Tanggal Akhir</label>
                    <div class="filter-input-wrapper">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#6B7280" stroke-width="1.8"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke="#6B7280" stroke-width="1.8"/>
                            <line x1="8" y1="2" x2="8" y2="6" stroke="#6B7280" stroke-width="1.8"/>
                            <line x1="3" y1="10" x2="21" y2="10" stroke="#6B7280" stroke-width="1.8"/>
                        </svg>
                        <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggal_akhir }}">
                    </div>
                </div>

                <div class="filter-group">
                    <label>Urutkan Harga</label>
                    <div class="filter-input-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 6h18M6 12h12M10 18h4" stroke="#6B7280" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <select name="filter_harga" class="form-control">
                            <option value="">Default</option>
                            <option value="termahal" {{ request('filter_harga')=='termahal' ? 'selected' : '' }}>Harga Termahal</option>
                            <option value="termurah" {{ request('filter_harga')=='termurah' ? 'selected' : '' }}>Harga Termurah</option>
                        </select>
                    </div>
                </div>

                <div class="filter-group">
                    <label>Filter Tipe</label>
                    <div class="filter-input-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke="#6B7280" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <select name="filter_type" class="form-control">
                            <option value="">Semua</option>
                            @foreach($filterLabelMap ?? [] as $key => $label)
                                <option value="{{ $key }}" {{ request('filter_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="filter-group">
                    <label>Supplier</label>
                    <div class="filter-input-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m12-10a4 4 0 11-8 0 4 4 0 018 0zm6 3v2m0 0v2m0-2h-2m2-2h2" stroke="#6B7280" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <select name="supplier" class="form-control">
                            <option value="">Semua Supplier</option>
                            @foreach($supplierList ?? [] as $supplier)
                                <option value="{{ $supplier->kode_suplier }}" {{ request('supplier') == $supplier->kode_suplier ? 'selected' : '' }}>
                                    {{ $supplier->nama_suplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="filter-group">
                    <label>Gudang</label>
                    <button type="button" class="btn btn-atur-gudang" data-toggle="modal" data-target="#modalSettingGudang">
                        <i class="fas fa-warehouse"></i> Atur Gudang
                    </button>
                </div>

            </div>

            <div class="filter-help">Pilih filter, lalu klik <strong>Terapkan</strong> untuk memperbarui daftar.</div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-action btn-filter-apply">
                    <i class="fas fa-check"></i> Terapkan
                </button>
                <button type="button" id="exportBtn" class="btn-filter-action btn-filter-export">
                    <i class="fas fa-file-export"></i> Export
                </button>
                <a href="{{ route('belanja.index') }}" class="btn-filter-action btn-filter-reset">
                    <i class="fas fa-undo"></i> Reset
                </a>
                <button type="button" id="refreshBtn" class="btn-filter-action btn-filter-refresh">
                    <i class="fas fa-sync-alt"></i> Segarkan
                </button>
            </div>

        </div>
    </form>

    @if(!empty($filterLabelMap[$filterType ?? ''] ?? null))
        <div class="mb-2">
            <span class="filter-active-pill">Filter aktif: {{ $filterLabelMap[$filterType] }}</span>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="summary-box mini-summary variant-orange">
                <h6>Pengeluaran Terbanyak</h6>
                <h5>{{ $summary['top_pengeluaran']['nama_brng'] ?? '-' }}</h5>
                <div class="value">{{ number_format($summary['top_pengeluaran']['pengeluaran'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box mini-summary variant-red">
                <h6>Pengeluaran Terdikit</h6>
                <h5>{{ $summary['low_pengeluaran']['nama_brng'] ?? '-' }}</h5>
                <div class="value">{{ number_format($summary['low_pengeluaran']['pengeluaran'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box mini-summary variant-blue">
                <h6>Stok Terbanyak</h6>
                <h5>{{ $summary['top_stok']['nama_brng'] ?? '-' }}</h5>
                <div class="value">{{ number_format($summary['top_stok']['stok'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box mini-summary variant-gray">
                <h6>Stok Terdikit</h6>
                <h5>{{ $summary['low_stok']['nama_brng'] ?? '-' }}</h5>
                <div class="value">{{ number_format($summary['low_stok']['stok'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="summary-box variant-stok">
                <h6>Stok Saat Ini</h6>
                <h2>{{ number_format($summary['grand_stok'] ?? 0, 0, ',', '.') }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box variant-keluar">
                <h6>Stok Tanggal Sebelumnya</h6>
                <h2>{{ number_format($summary['grand_stok_sebelumnya'] ?? 0, 0, ',', '.') }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box variant-kebutuhan">
                <h6>Total Pengeluaran</h6>
                <h2>{{ number_format($summary['grand_keluar'] ?? 0, 0, ',', '.') }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box variant-gray">
                <h6>Rencana Pembelian</h6>
                <h2>{{ number_format($summary['grand_kebutuhan'] ?? 0, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>

    <!-- Toolbar Aksi Table: Copy Data & Pencarian -->
    <div class="d-flex align-items-center mb-3 flex-wrap justify-content-between" style="gap: 15px;">
        <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
            <button type="button" id="btnCopyObat" class="btn-copy-custom">
                <i class="fas fa-copy"></i> Copy Data Obat
            </button>
            <div class="search-input-custom-wrapper">
                <i class="fas fa-search search-icon-custom"></i>
                <input type="search" id="customSearchInput" class="search-input-custom" placeholder="Cari nama / kode barang...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="tableBelanja">
            <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Nama Supplier</th>
                <th>Kategori</th>
                <th>Golongan</th>
                <th>Dasar</th>
                <th>Harga Jual</th>
                <th>Diskon</th>
                <th>Besar Diskon</th>
                <th>Satuan</th>
                <th>Jumlah Beli</th>
                <th>Stok Saat Ini</th>
                <th>Stok Sebelumnya</th>
                <th>Pengeluaran</th>
                <th>Kebutuhan</th>
                @foreach($selectedBangsal ?? [] as $b)
                    <th>{{ $b->kd_bangsal }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @forelse($rows ?? [] as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><span class="code-item">{{ $row['kode_brng'] }}</span></td>
                    <td class="font-weight-bold" style="color: #0f172a;">{{ $row['nama_brng'] }}</td>
                    <td>{{ $row['supplier_names'] ?: '-' }}</td>
                    <td>{{ $row['kategori_nama'] ?? '-' }}</td>
                    <td>{{ $row['golongan_nama'] ?? '-' }}</td>
                    <td class="cell-number">{{ number_format($row['harga_beli'], 0, ',', '.') }}</td>
                    <td class="cell-number">{{ number_format($row['ralan'], 0, ',', '.') }}</td>
                    <td class="cell-number">{{ number_format($row['dis'] ?? 0, 0, ',', '.') }}%</td>
                    <td class="cell-number">{{ number_format($row['besardis'] ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $row['kode_sat'] }}</td>
                    <td class="cell-number">{{ number_format($row['jumlah_beli'], 0, ',', '.') }}</td>
                    <td class="cell-number text-stock">
                        {{ number_format($row['stok'], 0, ',', '.') }}
                    </td>
                    <td class="cell-number text-muted">{{ number_format($row['stok_sebelumnya'], 0, ',', '.') }}</td>
                    <td class="cell-number text-keluar">
                        {{ number_format($row['pengeluaran'], 0, ',', '.') }}
                    </td>
                    <td class="cell-number">
                        @if($row['kebutuhan'] > 0)
                            <span class="text-kebutuhan">{{ number_format($row['kebutuhan'], 0, ',', '.') }}</span>
                        @else
                            <span class="text-kebutuhan-zero">0</span>
                        @endif
                    </td>
                    @foreach($selectedBangsal ?? [] as $b)
                        <td class="cell-number text-muted">{{ number_format($stokPerBangsalMap[$row['kode_brng']][$b->kd_bangsal] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="20" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>
</div>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
(function(){
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const tableEl = document.getElementById('tableBelanja');
    if(!tableEl) return;

    const tableBelanja = $(tableEl).DataTable({
        pageLength: 25,
        ordering: false,
        autoWidth: false,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'copyHtml5',
            text: '<i class="fas fa-copy"></i> Copy Data Obat',
            className: 'btn btn-success btn-sm',
            title: 'Rencana Belanja Farmasi',
            exportOptions: { columns: ':visible' },
            action: function(e, dt, button, config){
                $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                if(window.Swal){
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: 'Data berhasil dicopy',
                        text: 'Silakan paste ke Excel, WhatsApp, atau Telegram',
                        showConfirmButton: false, timer: 2500, timerProgressBar: true
                    });
                }
            }
        }]
    });

    // Custom Copy Button Action
    document.getElementById('btnCopyObat')?.addEventListener('click', function() {
        tableBelanja.button(0).trigger();
    });

    // Custom Search Action with debounce
    const searchInput = document.getElementById('customSearchInput');
    if(searchInput){
        let debounceTimer = null;
        const doSearch = (val) => tableBelanja.search(val || '').draw();
        searchInput.addEventListener('input', function(){
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => doSearch(this.value), 300);
        });
        searchInput.addEventListener('keydown', function(ev){
            if(ev.key === 'Enter'){ ev.preventDefault(); doSearch(this.value); }
        });
    }

    // Header action buttons
    document.getElementById('refreshBtn')?.addEventListener('click', () => location.reload());
    document.getElementById('exportBtn')?.addEventListener('click', () => tableBelanja.button(0).trigger());

    // Toggle gudang with error feedback
    document.querySelectorAll('.toggle-bangsal').forEach(el => {
        el.addEventListener('change', function(){
            fetch("{{ route('belanja.toggleBangsal') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ kd_bangsal: this.dataset.kd, status: this.checked ? 1 : 0 })
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if(ok && data.success){
                    location.reload();
                } else {
                    if(window.Swal){
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Tidak dapat memperbarui status gudang.' });
                    } else {
                        alert(data.message || 'Gagal memperbarui status gudang.');
                    }
                    this.checked = !this.checked; // revert
                }
            })
            .catch(() => {
                if(window.Swal){ Swal.fire({ icon: 'error', title: 'Gagal', text: 'Koneksi ke server terputus.' }); }
                this.checked = !this.checked;
            });
        });
    });
})();
</script>

<!-- Modal Setting Gudang -->
<div class="modal fade" id="modalSettingGudang" tabindex="-1" role="dialog" aria-labelledby="modalSettingGudangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header border-0 bg-light py-3 px-4">
                <h5 class="modal-title font-weight-bold text-dark" id="modalSettingGudangLabel">
                    <i class="fas fa-warehouse text-primary mr-2"></i> Setting Gudang
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 pb-4 pt-2">
                <p class="text-muted small mb-3">Aktifkan atau nonaktifkan gudang/bangsal yang datanya ingin dihitung dalam laporan rencana belanja ini.</p>
                <div class="gudang-wrapper">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Gudang</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bangsal ?? [] as $b)
                                @php $isActive = !in_array($b->kd_bangsal, $nonaktif_bangsal ?? []); @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $b->kd_bangsal }}</td>
                                    <td>{{ $b->nm_bangsal }}</td>
                                    <td class="text-center">
                                        <label class="switch mb-0">
                                            <input type="checkbox"
                                                   class="toggle-bangsal"
                                                   data-kd="{{ $b->kd_bangsal }}"
                                                   {{ $isActive ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <span class="{{ $isActive ? 'badge-active' : 'badge-inactive' }} ml-1">
                                            {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary btn-sm" style="border-radius: 10px;" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection
