@extends('layout.layoutDashboard')

@section('title','Rencana Belanja Farmasi')

@section('konten')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

<style>

:root{
    --primary:#f3f6f9;
    --secondary:#eef2f6;
    --success:#16a34a;
    --danger:#b91c1c;
    --warning:#d97706;
    --dark:#1f2937;
    --light:#ffffff;
    --text:#374151; /* neutral dark (not pure black) */
    --muted:#6b7280;
}

body{
    background:#f1f5f9;
}

.main-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.card-header-custom{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:white;
    padding:18px 26px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.card-header-custom h4{
    margin:0 0 4px 0;
    font-weight:700;
    font-size:1.15rem;
}

.card-header-custom .header-subtitle{
    display:block;
    opacity:.92;
    font-size:0.92rem;
}

.header-actions .btn{    
    margin-left:8px;
}

.filter-card{
    background:linear-gradient(180deg, #ffffff, #fbfdff);
    border-radius:14px;
    padding:18px;
    box-shadow:0 6px 18px rgba(16,24,40,0.04);
    margin-bottom:20px;
}

.filter-controls{display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap}
.input-icon{position:relative}
.input-icon svg{position:absolute;left:14px;top:50%;transform:translateY(-50%);width:16px;height:16px;opacity:.56}
.input-icon .form-control{padding-left:44px;height:38px;border-radius:8px}
.control-pill{display:flex;gap:8px;align-items:center;background:transparent;padding:8px 10px;border-radius:10px;border:1px solid #f1f5f9;min-height:62px}
.min-w-170{min-width:170px}
.min-w-160{min-width:160px}
.min-w-260{min-width:260px}
.min-w-100{min-width:100px}
 .min-w-110{min-width:110px}
.flex-1{flex:1}
.pill-inner label{display:block;margin-bottom:6px;font-weight:600;color:var(--dark);font-size:0.9rem}
.filter-actions{display:flex;gap:10px;align-items:center}
.filter-actions.column{flex-direction:column;align-items:stretch;gap:6px}
.btn-cta.small{padding:6px 10px;font-size:0.86rem;height:38px;border-radius:8px}
.btn-clear.small{padding:6px 10px;font-size:0.86rem;height:38px;border-radius:8px}
.filter-active-pill{background:linear-gradient(90deg,#eef2ff,#eef9ff);padding:6px 10px;border-radius:999px;border:1px solid #e6eef6;color:var(--text);font-weight:600;display:inline-flex;align-items:center;gap:8px}
.filter-active-pill svg{opacity:.85}
.btn-cta{background:linear-gradient(90deg,#0ea5a4,#34d399);color:white;border:none;padding:7px 16px;border-radius:8px;box-shadow:0 6px 12px rgba(14,165,164,0.08);font-weight:700;font-size:0.95rem}
.btn-clear{background:transparent;border:1px solid #eef6fb;color:var(--text);padding:6px 12px;border-radius:8px;font-size:0.95rem}
.btn-cta:hover{transform:translateY(-2px);box-shadow:0 10px 18px rgba(14,165,164,0.12)}
.btn-clear:hover{background:#fbfdff}
.filter-help{font-size:0.85rem;color:var(--muted);margin-top:8px}

.summary-box{
    border-radius:12px;
    padding:18px;
    color:var(--dark);
    position:relative;
    overflow:hidden;
    margin-bottom:16px;
    background:var(--light);
    border:1px solid #eef2f6;
}

.summary-box h6{
    margin-bottom:10px;
    opacity:.9;
}

.summary-box h2{
    font-weight:700;
    margin:0;
}

.mini-summary{
    padding:12px 14px;
}

.mini-summary h6{font-size:0.85rem;margin-bottom:6px;color:var(--muted)}
.mini-summary h5{font-size:1rem;margin:0 0 4px 0;font-weight:700;color:var(--text)}
.mini-summary .value{font-size:0.95rem;color:var(--text);font-weight:600}

.bg-stok{
    border-left:6px solid #60a5fa;
}

.bg-keluar{
    border-left:6px solid #f6ad55;
}

.bg-kebutuhan{
    border-left:6px solid #fca5a5;
}

.gudang-wrapper{
    max-height:250px;
    overflow:auto;
    border:1px solid #e2e8f0;
    border-radius:12px;
    background:#fff;
}

.table th{
    white-space:nowrap;
}

.table td{
    white-space:nowrap;
    vertical-align:middle;
    color:var(--text);
}

.table-responsive{
    max-height:750px;
    overflow:auto;
}

.table thead th{
    position:sticky;
    top:0;
    z-index:99;
    background:var(--secondary);
    color:var(--text);
    font-size:12px;
    border-bottom:1px solid #e6eef6;
}

.table tbody tr:hover{
    background:#eef6ff;
}

.stock{
    color:#2563eb;
    font-weight:700;
}

.keluar{
    color:#f59e0b;
    font-weight:700;
}

.kebutuhan{
    color:#ef4444;
    font-weight:700;
}

.switch{
    position:relative;
    display:inline-block;
    width:44px;
    height:26px;
    vertical-align:middle;
}

.switch input{
    display:none;
}

.slider{
    position:absolute;
    top:0;
    left:0;
    right:0;
    bottom:0;
    cursor:pointer;
    background:#e6e9ee;
    transition:.22s ease;
    border-radius:999px;
    box-shadow:inset 0 1px 2px rgba(16,24,40,0.04);
}

.slider:before{
    content:'';
    position:absolute;
    width:20px;
    height:20px;
    left:3px;
    bottom:3px;
    background:white;
    transition:transform .22s ease;
    border-radius:50%;
    box-shadow:0 2px 4px rgba(16,24,40,0.06);
}

.switch input:checked + .slider{
    background:var(--success);
}

.switch input:checked + .slider:before{
    transform:translateX(18px);
}

.switch,
.slider{
    cursor:pointer;
}

/* center status column in gudang table */
.gudang-wrapper td:nth-child(4){
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
}

/* stronger, more 'paten' badges */
.badge-active,
.badge-inactive{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:72px;
    padding:6px 12px;
    font-weight:600;
    box-shadow:0 1px 2px rgba(16,24,40,0.04);
}

.badge-active{
    background:#16a34a;
    color:white;
    padding:6px 10px;
    border-radius:16px;
}

.badge-inactive{
    background:#ef4444; /* non-active red */
    color:white;
    padding:6px 10px;
    border-radius:16px;
}

.btn-primary{
    border:1px solid #e6eef9;
    border-radius:8px;
    background:transparent;
    color:var(--dark);
    padding:8px 18px;
    font-weight:600;
}

.dataTables_filter input{
    border-radius:10px !important;
}

@media (max-width: 780px){
    .filter-controls{flex-direction:column;align-items:stretch;gap:10px}
    .filter-controls > .control-pill{min-width:100% !important}
    .filter-controls .btn-cta, .filter-controls .btn-clear{width:100%}
}

</style>

<div class="card main-card">

<div class="card-body">

    <form method="GET" action="{{ route('belanja.index') }}">

        <div class="filter-card">

            <div class="filter-controls">

                <div class="control-pill min-w-170">
                    <div class="input-icon pill-inner">
                        <svg class="icon-calendar" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <rect x="2" y="4" width="20" height="18" rx="3" ry="3" fill="none" stroke="#6B7280" stroke-width="1.2" />
                            <rect x="7" y="9" width="3" height="3" rx="0.6" fill="#6B7280" />
                            <rect x="11" y="9" width="3" height="3" rx="0.6" fill="#6B7280" />
                            <rect x="15" y="9" width="3" height="3" rx="0.6" fill="#6B7280" />
                            <line x1="8" y1="2" x2="8" y2="6" stroke="#6B7280" stroke-width="1.2" />
                            <line x1="16" y1="2" x2="16" y2="6" stroke="#6B7280" stroke-width="1.2" />
                        </svg>
                        <label class="d-block mb-1">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control" value="{{ $tanggal_awal }}">
                    </div>
                </div>

                <div class="control-pill min-w-170">
                    <div class="input-icon pill-inner">
                        <svg class="icon-calendar" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <rect x="2" y="4" width="20" height="18" rx="3" ry="3" fill="none" stroke="#6B7280" stroke-width="1.2" />
                            <rect x="7" y="9" width="3" height="3" rx="0.6" fill="#6B7280" />
                            <rect x="11" y="9" width="3" height="3" rx="0.6" fill="#6B7280" />
                            <rect x="15" y="9" width="3" height="3" rx="0.6" fill="#6B7280" />
                            <line x1="8" y1="2" x2="8" y2="6" stroke="#6B7280" stroke-width="1.2" />
                            <line x1="16" y1="2" x2="16" y2="6" stroke="#6B7280" stroke-width="1.2" />
                        </svg>
                        <label class="d-block mb-1">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggal_akhir }}">
                    </div>
                </div>

                <div class="control-pill min-w-160">
                    <div>
                        <label class="d-block mb-1">Urutkan Harga</label>
                        <select name="filter_harga" class="form-control">
                            <option value="">Default</option>
                            <option value="termahal" {{ request('filter_harga')=='termahal' ? 'selected' : '' }}>Harga Termahal</option>
                            <option value="termurah" {{ request('filter_harga')=='termurah' ? 'selected' : '' }}>Harga Termurah</option>
                        </select>
                    </div>
                </div>

                <div class="control-pill min-w-170 flex-1">
                    <div>
                        <label class="d-block mb-1">Filter Tipe</label>
                        <select name="filter_type" class="form-control">
                            <option value="">Semua</option>
                            <option value="pengeluaran_terbanyak" {{ request('filter_type')=='pengeluaran_terbanyak' ? 'selected' : '' }}>Pengeluaran Terbanyak</option>
                            <option value="pengeluaran_terdikit" {{ request('filter_type')=='pengeluaran_terdikit' ? 'selected' : '' }}>Pengeluaran Terdikit</option>
                            <option value="stok_terbanyak" {{ request('filter_type')=='stok_terbanyak' ? 'selected' : '' }}>Stok Terbanyak</option>
                            <option value="stok_terdikit" {{ request('filter_type')=='stok_terdikit' ? 'selected' : '' }}>Stok Terdikit</option>
                            <option value="nilai_terbanyak" {{ request('filter_type')=='nilai_terbanyak' ? 'selected' : '' }}>Nilai Belanja Terbanyak</option>
                            <option value="nilai_terendah" {{ request('filter_type')=='nilai_terendah' ? 'selected' : '' }}>Nilai Belanja Terdikit</option>
                        </select>
                    </div>
                </div>

                <div class="control-pill min-w-100">
                    <div class="pill-inner">
                        <label class="d-block mb-1">Jumlah</label>
                        <input type="number" name="filter_n" min="1" max="200" class="form-control" value="{{ request('filter_n', 10) }}">
                    </div>
                </div>

                <div class="control-pill min-w-200">
                    <div class="filter-actions">
                        <button type="submit" class="btn-cta small">Terapkan</button>
                        <button type="reset" class="btn-clear small">Reset</button>
                    </div>
                </div>


            </div>

            <div class="filter-help">Pilih filter, lalu klik <strong>Tampilkan</strong> untuk memperbarui daftar.</div>

            <hr>

            <h6 class="mb-3">
                Setting Gudang yang Dihitung
            </h6>

            <div class="gudang-wrapper">

                <table class="table table-bordered table-sm mb-0">

                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Gudang</th>
                        <th>Status</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($bangsal as $b)

                        @php
                            $isActive=!in_array(
                                $b->kd_bangsal,
                                $nonaktif_bangsal ?? []
                            );
                        @endphp

                        <tr>

                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $b->kd_bangsal }}</td>
                            <td>{{ $b->nm_bangsal }}</td>

                            <td>

                                <label class="switch">

                                    <input type="checkbox"
                                           class="toggle-bangsal"
                                           data-kd="{{ $b->kd_bangsal }}"
                                           {{ $isActive ? 'checked' : '' }}>

                                    <span class="slider"></span>

                                </label>

                                <span id="label-{{ $b->kd_bangsal }}"
                                      class="{{ $isActive ? 'badge-active' : 'badge-inactive' }}">
                                      {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                </span>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </form>

    @php

        $selectedBangsal =
        $bangsal->whereNotIn(
            'kd_bangsal',
            $nonaktif_bangsal ?? []
        );

        $grandStok=0;
        $grandKeluar=0;
        $grandKebutuhan=0;

        $obatTermahal = collect();

        foreach($barang as $kode => $item){

            $stokBarang = $stok_lokasi[$kode] ?? collect();

            $stok = $stokBarang
                ->whereIn(
                    'kd_bangsal',
                    $selectedBangsal->pluck('kd_bangsal')
                )
                ->sum('stok');

            $keluar = $total_pengeluaran[$kode] ?? 0;

            $kebutuhan = max(
                $keluar - $stok,
                0
            );

            // Total nilai pembelian
            $nilaiBelanja = $kebutuhan * $item->h_beli;

            // Simpan untuk diurutkan
            $obatTermahal->push([
                'kode_brng'      => $kode,
                'nama_brng'      => $item->nama_brng,
                'kode_sat'       => $item->kode_sat,
                'harga_beli'     => $item->h_beli,
                'stok'           => $stok,
                'pengeluaran'    => $keluar,
                'kebutuhan'      => $kebutuhan,
                'nilai_belanja'  => $nilaiBelanja
            ]);

            $grandStok += $stok;
            $grandKeluar += $keluar;
            $grandKebutuhan += $kebutuhan;
        }

        // Urutkan dari nilai belanja terbesar
        $obatTermahal = $obatTermahal
            ->sortByDesc('nilai_belanja')
            ->values();

        $filterHarga = request('filter_harga');

            if($filterHarga == 'termahal'){

                $obatTermahal = $obatTermahal
                    ->sortByDesc('harga_beli')
                    ->values();

            }elseif($filterHarga == 'termurah'){

                $obatTermahal = $obatTermahal
                    ->sortBy('harga_beli')
                    ->values();

            }else{

                // Default berdasarkan nilai belanja
                $obatTermahal = $obatTermahal
                    ->sortByDesc('nilai_belanja')
                    ->values();

            }

    @endphp

    @php
        $collection = collect($obatTermahal);
        $topPengeluaran = $collection->sortByDesc('pengeluaran')->first() ?? null;
        $lowPengeluaran = $collection->sortBy('pengeluaran')->first() ?? null;
        $topStok = $collection->sortByDesc('stok')->first() ?? null;
        $lowStok = $collection->sortBy('stok')->first() ?? null;
    @endphp

    <div class="row mb-3">

        <div class="col-md-3">
            <div class="summary-box mini-summary" style="border-left:6px solid #fca5a5;">
                <h6>Pengeluaran Terbanyak</h6>
                <h5>{{ $topPengeluaran['nama_brng'] ?? '-' }}</h5>
                <div class="value">{{ number_format($topPengeluaran['pengeluaran'] ?? 0,0,',','.') }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-box mini-summary" style="border-left:6px solid #ef4444;">
                <h6>Pengeluaran Terdikit</h6>
                <h5>{{ $lowPengeluaran['nama_brng'] ?? '-' }}</h5>
                <div class="value">{{ number_format($lowPengeluaran['pengeluaran'] ?? 0,0,',','.') }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-box mini-summary" style="border-left:6px solid #60a5fa;">
                <h6>Stok Terbanyak</h6>
                <h5>{{ $topStok['nama_brng'] ?? '-' }}</h5>
                <div class="value">{{ number_format($topStok['stok'] ?? 0,0,',','.') }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-box mini-summary" style="border-left:6px solid #9ca3af;">
                <h6>Stok Terdikit</h6>
                <h5>{{ $lowStok['nama_brng'] ?? '-' }}</h5>
                <div class="value">{{ number_format($lowStok['stok'] ?? 0,0,',','.') }}</div>
            </div>
        </div>

    </div>

    @php
        // apply filter selection to table data (only affects displayed rows)
        $filterType = request('filter_type');
        $filterN = (int) request('filter_n', 10);

        if($filterType && $filterN > 0){
            $col = collect($obatTermahal);

            if($filterType == 'pengeluaran_terbanyak'){
                $obatTermahal = $col->sortByDesc('pengeluaran')->take($filterN)->values();
            }elseif($filterType == 'pengeluaran_terdikit'){
                // ignore items with pengeluaran == 0 when finding 'terdikit'
                $colNonZero = $col->filter(function($r){
                    return (!empty($r['pengeluaran']) && $r['pengeluaran'] > 0);
                });
                if($colNonZero->isEmpty()){
                    $colNonZero = $col; // fallback if all zero
                }
                $obatTermahal = $colNonZero->sortBy('pengeluaran')->take($filterN)->values();
            }elseif($filterType == 'stok_terbanyak'){
                $obatTermahal = $col->sortByDesc('stok')->take($filterN)->values();
            }elseif($filterType == 'stok_terdikit'){
                $obatTermahal = $col->sortBy('stok')->take($filterN)->values();
            }elseif($filterType == 'nilai_terbanyak'){
                $obatTermahal = $col->sortByDesc('nilai_belanja')->take($filterN)->values();
            }elseif($filterType == 'nilai_terendah'){
                $colNonZeroVal = $col; // keep zeros as valid for nilai
                $obatTermahal = $colNonZeroVal->sortBy('nilai_belanja')->take($filterN)->values();
            }
        }

    @endphp

    @php
        // label for active filter
        $filterLabelMap = [
            'pengeluaran_terbanyak' => 'Pengeluaran Terbanyak',
            'pengeluaran_terdikit' => 'Pengeluaran Terdikit',
            'stok_terbanyak' => 'Stok Terbanyak',
            'stok_terdikit' => 'Stok Terdikit',
            'nilai_terbanyak' => 'Nilai Belanja Terbanyak',
            'nilai_terendah' => 'Nilai Belanja Terdikit'
        ];
        $activeFilterLabel = $filterLabelMap[$filterType] ?? null;
    @endphp

    @if($activeFilterLabel)
        <div class="mb-2">
            <span style="background:#eef2f6;color:var(--text);border:1px solid #e6eef6;padding:6px 10px;border-radius:12px;font-weight:600;">Filter aktif: {{ $activeFilterLabel }} (Top {{ $filterN }})</span>
        </div>
    @endif

    <div class="row">

        <div class="col-md-4">
            <div class="summary-box bg-stok">
                <h6>Total Stok</h6>
                <h2>{{ number_format($grandStok,0,',','.') }}</h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-box bg-keluar">
                <h6>Total Pengeluaran</h6>
                <h2>{{ number_format($grandKeluar,0,',','.') }}</h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-box bg-kebutuhan">
                <h6>Rencana Pembelian</h6>
                <h2>{{ number_format($grandKebutuhan,0,',','.') }}</h2>
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
                <th>Harga Beli</th>
                <th>Satuan</th>
                <th>Total Stok</th>
                <th>Pengeluaran</th>
                <th>Kebutuhan</th>

                @foreach($selectedBangsal as $b)
                    <th>{{ $b->kd_bangsal }}</th>
                @endforeach

            </tr>

            </thead>

            <tbody>

            @php $no=1; @endphp

            @foreach($obatTermahal as $row)

    @php

        $kode = $row['kode_brng'];

        $item = (object)[
            'nama_brng' => $row['nama_brng'],
            'kode_sat'  => $row['kode_sat'],
            'h_beli'    => $row['harga_beli']
        ];

        $stokBarang = $stok_lokasi[$kode] ?? collect();

        $stokPerBangsal = [];

        $total_stok = 0;

        foreach($selectedBangsal as $b){

            $stok = optional(
                $stokBarang->firstWhere(
                    'kd_bangsal',
                    $b->kd_bangsal
                )
            )->stok ?? 0;

            $stokPerBangsal[$b->kd_bangsal] = $stok;

            $total_stok += $stok;
        }

        $pengeluaran = $row['pengeluaran'];

        $kebutuhan = $row['kebutuhan'];

    @endphp

    <tr>

        <td>{{ $no++ }}</td>

        <td>{{ $kode }}</td>

        <td>{{ $item->nama_brng }}</td>

        <td align="right">
            {{ number_format($item->h_beli,2,',','.') }}
        </td>

        <td>{{ $item->kode_sat }}</td>

        <td align="right" class="stock">
            {{ number_format($total_stok,0,',','.') }}
        </td>

        <td align="right" class="keluar">
            {{ number_format($pengeluaran,0,',','.') }}
        </td>

        <td align="right" class="kebutuhan">
            {{ number_format($kebutuhan,0,',','.') }}
        </td>

        @foreach($selectedBangsal as $b)

            <td align="right">
                {{ number_format($stokPerBangsal[$b->kd_bangsal] ?? 0,0,',','.') }}
            </td>

        @endforeach

    </tr>

@endforeach

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

window.tableBelanja = $('#tableBelanja').DataTable({
    pageLength: 25,
    scrollX: true,
    responsive: true,
    ordering: false,

    dom: 'Bfrtip',

    buttons: [
{
    extend: 'copyHtml5',
    text: '<i class="fas fa-copy"></i> Copy Data Obat',
    className: 'btn btn-success btn-sm',
    title: 'Rencana Belanja Farmasi',
    exportOptions: {
        columns: ':visible'
    },

    action: function (e, dt, button, config) {

        $.fn.dataTable.ext.buttons.copyHtml5.action.call(
            this,
            e,
            dt,
            button,
            config
        );

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '📋 Data berhasil dicopy',
            text: 'Silakan paste ke Excel, WhatsApp, atau Telegram',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });

    }
}
]
});

// Tombol aksi header
document.getElementById('refreshBtn')?.addEventListener('click', function(){
    location.reload();
});

document.getElementById('exportBtn')?.addEventListener('click', function(){
    if(window.tableBelanja){
        window.tableBelanja.button(0).trigger();
    }
});

const token=
document.querySelector('meta[name="csrf-token"]').content;

document.querySelectorAll('.toggle-bangsal').forEach(el=>{

    el.addEventListener('change',function(){

        fetch("{{ route('belanja.toggleBangsal') }}",{

            method:'POST',

            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':token
            },

            body:JSON.stringify({

                kd_bangsal:this.dataset.kd,
                status:this.checked ? 1 : 0

            })

        })
        .then(r=>r.json())
        .then(res=>{

            if(res.success){

                location.reload();

            }

        });

    });

});

</script>

@endsection
