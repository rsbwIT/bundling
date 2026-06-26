@extends('layout.layoutDashboard')
@section('title', 'Monitoring PKPA')

@section('konten')
<style>
    .pkpa-wrap { font-family: 'Inter', 'Source Sans Pro', -apple-system, sans-serif; }

    /* Filter */
    .pkpa-filter {
        background: #fff;
        border: 1px solid #e8eaed;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 16px;
    }
    .pkpa-filter .fg { flex: 1; min-width: 150px; }
    .pkpa-filter label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #8b95a2;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .pkpa-filter input,
    .pkpa-filter select {
        width: 100%;
        padding: 9px 14px;
        font-size: 13px;
        color: #1e293b;
        background: #f8f9fb;
        border: 1px solid #e2e5ea;
        border-radius: 10px;
        transition: all .2s;
        outline: none;
    }
    .pkpa-filter input:focus,
    .pkpa-filter select:focus {
        border-color: #1d7969;
        box-shadow: 0 0 0 3px rgba(29,121,105,.1);
        background: #fff;
    }
    .pkpa-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
        align-self: flex-end;
    }

    /* Buttons */
    .btn-pkpa {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-pkpa-primary { background: #1d7969; color: #fff; }
    .btn-pkpa-primary:hover { background: #166357; color: #fff; box-shadow: 0 4px 12px rgba(29,121,105,.25); }
    .btn-pkpa-outline { background: #fff; color: #1d7969; border: 1.5px solid #1d7969; }
    .btn-pkpa-outline:hover { background: #f0faf7; color: #1d7969; }
    .btn-pkpa-copy { background: #fff; color: #475569; border: 1.5px solid #e2e5ea; }
    .btn-pkpa-copy:hover { background: #f8fafc; border-color: #cbd5e1; color: #475569; }
    .btn-pkpa-copy.copied { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }

    /* Stats */
    .pkpa-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }
    .pkpa-stat {
        background: #fff;
        border: 1px solid #e8eaed;
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: box-shadow .2s;
    }
    .pkpa-stat:hover { box-shadow: 0 4px 16px rgba(0,0,0,.05); }
    .pkpa-stat-label { font-size: 12px; color: #8b95a2; font-weight: 500; }
    .pkpa-stat-value { font-size: 26px; font-weight: 700; color: #1e293b; margin-top: 2px; }
    .pkpa-stat-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
    .pkpa-stat-icon.teal   { background: #ecfdf5; color: #1d7969; }
    .pkpa-stat-icon.blue   { background: #eff6ff; color: #3b82f6; }
    .pkpa-stat-icon.violet { background: #f5f3ff; color: #7c3aed; }
    .pkpa-stat-icon.amber  { background: #fffbeb; color: #d97706; }

    /* Card */
    .pkpa-card {
        background: #fff;
        border: 1px solid #e8eaed;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .pkpa-card-head {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f3f5;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .pkpa-card-head h5 { font-size: 15px; font-weight: 700; color: #1e293b; margin: 0; }
    .pkpa-card-head h5 i { color: #1d7969; margin-right: 8px; }
    .pkpa-card-head .meta-pill {
        font-size: 11px;
        background: #f8f9fb;
        color: #64748b;
        padding: 5px 12px;
        border-radius: 8px;
        border: 1px solid #e8eaed;
    }

    /* Table */
    .pkpa-tbl-wrap { overflow-x: auto; }
    .pkpa-tbl {
        width: 100%;
        border-collapse: collapse;
        min-width: 1500px;
        font-size: 12.5px;
    }
    .pkpa-tbl thead th {
        position: sticky;
        top: 0;
        background: #fafbfc;
        color: #64748b;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 14px 12px;
        border-bottom: 1.5px solid #e8eaed;
        text-align: left;
        white-space: nowrap;
    }
    .pkpa-tbl thead th.center { text-align: center; }
    .pkpa-tbl thead th.right  { text-align: right; }
    .pkpa-tbl tbody td {
        padding: 12px 12px;
        border-bottom: 1px solid #f1f3f5;
        color: #334155;
        vertical-align: top;
    }
    .pkpa-tbl tbody tr { transition: background .15s; }
    .pkpa-tbl tbody tr:hover { background: #f8faf9; }
    .cell-rm   { font-weight: 700; color: #1e293b; }
    .cell-sub  { font-size: 11px; color: #94a3b8; margin-top: 1px; }
    .cell-name { font-weight: 600; color: #334155; font-size: 12px; }
    .cell-bold { font-weight: 700; color: #1e293b; }
    .cell-teal { color: #1d7969; font-weight: 700; }
    .cell-muted{ color: #c0c8d4; }
    .cell-center { text-align: center; }
    .cell-right  { text-align: right; }

    .pkpa-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 10.5px;
        font-weight: 600;
    }
    .pkpa-badge.atc  { background: #f1f5f9; color: #475569; }
    .pkpa-badge.days { background: #eff6ff; color: #2563eb; }
    .pkpa-badge.room { background: #fef3c7; color: #92400e; font-size: 10px; }

    .btn-eye {
        width: 30px; height: 30px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: none; background: transparent;
        color: #94a3b8; cursor: pointer; transition: all .15s;
    }
    .btn-eye:hover { background: #f1f5f9; color: #1d7969; }

    /* Footer & Pagination */
    .pkpa-foot {
        padding: 14px 24px;
        border-top: 1px solid #f1f3f5;
        font-size: 12px;
        color: #94a3b8;
        background: #fafbfc;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .pkpa-pager {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .pkpa-pager a,
    .pkpa-pager span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all .15s;
    }
    .pkpa-pager a {
        background: #fff;
        color: #475569;
        border: 1px solid #e2e5ea;
    }
    .pkpa-pager a:hover {
        background: #f0faf7;
        border-color: #1d7969;
        color: #1d7969;
    }
    .pkpa-pager .active {
        background: #1d7969;
        color: #fff;
        border: 1px solid #1d7969;
    }
    .pkpa-pager .disabled {
        background: #f8f9fb;
        color: #c0c8d4;
        border: 1px solid #eef0f3;
        pointer-events: none;
    }
    .pkpa-perpage {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #64748b;
    }
    .pkpa-perpage select {
        padding: 4px 8px;
        border: 1px solid #e2e5ea;
        border-radius: 8px;
        font-size: 12px;
        color: #334155;
        background: #fff;
        outline: none;
        cursor: pointer;
    }

    .pkpa-empty { text-align: center; padding: 56px 20px; }
    .pkpa-empty i { font-size: 48px; color: #d9dfe6; margin-bottom: 12px; }
    .pkpa-empty h6 { color: #94a3b8; font-weight: 600; font-size: 14px; margin-bottom: 4px; }
    .pkpa-empty p  { color: #c0c8d4; font-size: 12px; }

    /* Toast */
    .pkpa-toast {
        position: fixed; bottom: 32px; right: 32px;
        background: #1e293b; color: #fff;
        padding: 12px 22px; border-radius: 12px;
        font-size: 13px; font-weight: 600;
        display: flex; align-items: center; gap: 8px;
        box-shadow: 0 8px 32px rgba(0,0,0,.18);
        transform: translateY(20px); opacity: 0;
        transition: all .35s cubic-bezier(.16,1,.3,1);
        z-index: 9999; pointer-events: none;
    }
    .pkpa-toast.show { opacity: 1; transform: translateY(0); }
    .pkpa-toast i { color: #34d399; }

    /* Modal */
    .pkpa-overlay {
        position: fixed; inset: 0; z-index: 1050;
        background: rgba(15,23,42,.45);
        backdrop-filter: blur(4px);
        display: none; align-items: center; justify-content: center;
        padding: 24px;
    }
    .pkpa-overlay.active { display: flex; }
    .pkpa-modal {
        background: #fff; border-radius: 18px;
        width: 100%; max-width: 640px; max-height: 85vh;
        overflow: hidden;
        box-shadow: 0 24px 64px rgba(0,0,0,.14);
        animation: modalIn .3s cubic-bezier(.16,1,.3,1);
    }
    @keyframes modalIn { from { opacity:0; transform: scale(.96) translateY(12px); } to { opacity:1; transform: none; } }
    .pkpa-modal-head {
        background: linear-gradient(135deg, #1d7969 0%, #145c4e 100%);
        color: #fff; padding: 22px 28px;
        display: flex; align-items: center; justify-content: space-between;
    }
    .pkpa-modal-head h4 { font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px; }
    .pkpa-modal-close {
        width: 32px; height: 32px; border-radius: 8px;
        border: none; background: rgba(255,255,255,.15);
        color: #fff; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .2s;
    }
    .pkpa-modal-close:hover { background: rgba(255,255,255,.25); }
    .pkpa-modal-body { padding: 24px 28px; overflow-y: auto; max-height: calc(85vh - 140px); }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .detail-box { background: #f8f9fb; border-radius: 10px; padding: 14px 16px; border: 1px solid #eef0f3; }
    .detail-box.full { grid-column: 1 / -1; }
    .detail-label { font-size: 10px; font-weight: 700; color: #8b95a2; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .detail-value { font-size: 13px; font-weight: 600; color: #1e293b; }
    .detail-value.teal { color: #1d7969; }
    .detail-divider { border: none; border-top: 1px solid #f1f3f5; margin: 16px 0; }
    .pkpa-modal-foot { padding: 14px 28px; border-top: 1px solid #f1f3f5; display: flex; justify-content: flex-end; }

    /* Dark mode */
    body.dark-mode .pkpa-filter, body.dark-mode .pkpa-stat, body.dark-mode .pkpa-card { background: #2a2a2c; border-color: #3a3a3c; }
    body.dark-mode .pkpa-filter input, body.dark-mode .pkpa-filter select { background: #333; border-color: #444; color: #e5e7eb; }
    body.dark-mode .pkpa-tbl thead th { background: #333; color: #a1a1aa; border-color: #3a3a3c; }
    body.dark-mode .pkpa-tbl tbody td { border-color: #333; color: #d1d5db; }
    body.dark-mode .pkpa-tbl tbody tr:hover { background: #333; }
    body.dark-mode .pkpa-card-head { border-color: #3a3a3c; }
    body.dark-mode .pkpa-foot { background: #2a2a2c; border-color: #3a3a3c; }
    body.dark-mode .pkpa-stat-label { color: #a1a1aa; }
    body.dark-mode .pkpa-stat-value { color: #f3f4f6; }
    body.dark-mode .pkpa-card-head h5 { color: #f3f4f6; }
    body.dark-mode .pkpa-modal { background: #2a2a2c; }
    body.dark-mode .detail-box { background: #333; border-color: #444; }
    body.dark-mode .detail-value { color: #e5e7eb; }
    body.dark-mode .pkpa-modal-foot { border-color: #3a3a3c; }
    body.dark-mode .btn-pkpa-copy { background: #333; border-color: #444; color: #d1d5db; }
    body.dark-mode .btn-pkpa-outline { background: #2a2a2c; color: #6ee7b7; border-color: #1d7969; }
    body.dark-mode .pkpa-pager a { background: #333; border-color: #444; color: #d1d5db; }
    body.dark-mode .pkpa-pager a:hover { background: #1d7969; color: #fff; }
    body.dark-mode .pkpa-perpage select { background: #333; border-color: #444; color: #d1d5db; }
</style>

<div class="pkpa-wrap">
    {{-- ─── Filter ─── --}}
    <form method="GET" action="{{ route('pkpa.monitoring') }}" class="pkpa-filter">
        <div class="fg">
            <label>Tanggal Awal</label>
            <input type="date" name="tgl_mulai" value="{{ $tgl_mulai }}">
        </div>
        <div class="fg">
            <label>Tanggal Akhir</label>
            <input type="date" name="tgl_selesai" value="{{ $tgl_selesai }}">
        </div>
        <div class="fg">
            <label>Cari Ruangan / Bangsal</label>
            <input type="text" name="bangsal" value="{{ $bangsal }}" placeholder="Ketik nama ruangan... (kosongkan = semua)" autocomplete="off">
        </div>
        <div class="pkpa-actions">
            <button type="submit" class="btn-pkpa btn-pkpa-primary">
                <i class="fas fa-search"></i> Tampilkan
            </button>
            <a href="{{ route('pkpa.monitoring', array_merge(request()->except('page'), ['export' => 'excel'])) }}" class="btn-pkpa btn-pkpa-outline">
                <i class="fas fa-file-excel"></i> Export
            </a>
            <button type="button" id="btnCopyExcel" onclick="copyTableToClipboard()" class="btn-pkpa btn-pkpa-copy">
                <i class="far fa-copy"></i> Copy
            </button>
        </div>
    </form>

    {{-- ─── Stats ─── --}}
    <div class="pkpa-stats">
        <div class="pkpa-stat">
            <div>
                <div class="pkpa-stat-label">Total Baris</div>
                <div class="pkpa-stat-value">{{ $stats['totalRows'] }}</div>
            </div>
            <div class="pkpa-stat-icon teal"><i class="fas fa-capsules"></i></div>
        </div>
        <div class="pkpa-stat">
            <div>
                <div class="pkpa-stat-label">Dewasa</div>
                <div class="pkpa-stat-value">{{ $stats['totalDewasa'] }}</div>
            </div>
            <div class="pkpa-stat-icon blue"><i class="fas fa-user"></i></div>
        </div>
        <div class="pkpa-stat">
            <div>
                <div class="pkpa-stat-label">Anak</div>
                <div class="pkpa-stat-value">{{ $stats['totalAnak'] }}</div>
            </div>
            <div class="pkpa-stat-icon amber"><i class="fas fa-baby"></i></div>
        </div>
    </div>

    {{-- ─── Table ─── --}}
    <div class="pkpa-card">
        <div class="pkpa-card-head">
            <h5><i class="fas fa-prescription-bottle-alt"></i> Data Penggunaan Antibiotik</h5>
            <span class="meta-pill">
                {{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('d M Y') }}
                @if($bangsal) &middot; {{ $bangsal }} @endif
            </span>
        </div>

        <div class="pkpa-tbl-wrap">
            <table class="pkpa-tbl" id="tblPkpa">
                <thead>
                    <tr>
                        <th class="center">#</th>
                        <th>RM</th>
                        <th>Nama Pasien</th>
                        <th>Ruangan</th>
                        <th>Diagnosis</th>
                        <th>DPJP</th>
                        <th>Nama Antibiotik</th>
                        <th>Regimen Dosis</th>
                        <th class="right">Dosis/Hari</th>
                        <th>Kode ATC</th>
                        <th class="center">Lama Terapi</th>
                        <th class="right">Total Dosis</th>
                        <th>Kode DDD</th>
                        <th class="right">DDD</th>
                        <th class="center"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $i => $row)
                        <tr>
                            <td class="cell-center" style="color:#94a3b8;">{{ ($results->currentPage() - 1) * $results->perPage() + $i + 1 }}</td>
                            <td class="cell-rm">{{ $row->RM }}</td>
                            <td>
                                <div class="cell-name">{{ $row->NAMA }}</div>
                                <div class="cell-sub">{{ $row->{"Kategori Pasien"} }}</div>
                            </td>
                            <td><span class="pkpa-badge room">{{ $row->Ruangan }}</span></td>
                            <td><span title="{{ $row->DIAGNOSIS }}" style="max-width:140px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $row->DIAGNOSIS }}</span></td>
                            <td>{{ $row->DPJP }}</td>
                            <td class="cell-bold">{{ $row->{"Nama Antibiotik"} }}</td>
                            <td>{{ $row->{"Regimen Dosis"} }}</td>
                            <td class="cell-right cell-teal">{{ number_format($row->{"Dosis per-hari"}, 0, ',', '.') }}</td>
                            <td><span class="pkpa-badge atc">{{ $row->Kode }}</span></td>
                            <td class="cell-center"><span class="pkpa-badge days">{{ $row->{"Lama Terapi AB"} }} hr</span></td>
                            <td class="cell-right cell-bold">{{ number_format($row->{"Total Dosis"}, 3, ',', '.') }}</td>
                            <td>{{ $row->{"Kode DDD"} }}</td>
                            <td class="cell-right {{ $row->DDD !== null ? 'cell-teal' : 'cell-muted' }}">
                                {{ $row->DDD !== null ? number_format($row->DDD, 3, ',', '.') : '-' }}
                            </td>
                            <td class="cell-center">
                                <button class="btn-eye" onclick="openDetail({{ json_encode($row) }})" title="Detail">
                                    <i class="fas fa-eye" style="font-size:13px;"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15">
                                <div class="pkpa-empty">
                                    <i class="fas fa-inbox d-block"></i>
                                    <h6>Tidak ada data ditemukan</h6>
                                    <p>Coba sesuaikan filter tanggal atau ketik nama ruangan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: info + per page + pagination --}}
        <div class="pkpa-foot">
            <div>
                Menampilkan <strong>{{ $results->firstItem() ?? 0 }}</strong>–<strong>{{ $results->lastItem() ?? 0 }}</strong>
                dari <strong>{{ $results->total() }}</strong> baris
            </div>

            <div class="pkpa-perpage">
                <span>Per halaman:</span>
                <select onchange="changePerPage(this.value)">
                    @foreach([10, 25, 50, 100] as $pp)
                        <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                    @endforeach
                </select>
            </div>

            @if($results->lastPage() > 1)
                <div class="pkpa-pager">
                    {{-- Prev --}}
                    @if($results->onFirstPage())
                        <span class="disabled"><i class="fas fa-chevron-left" style="font-size:10px;"></i></span>
                    @else
                        <a href="{{ $results->previousPageUrl() }}"><i class="fas fa-chevron-left" style="font-size:10px;"></i></a>
                    @endif

                    {{-- Pages --}}
                    @php
                        $current = $results->currentPage();
                        $last    = $results->lastPage();
                        $from    = max(1, $current - 2);
                        $to      = min($last, $current + 2);
                    @endphp

                    @if($from > 1)
                        <a href="{{ $results->url(1) }}">1</a>
                        @if($from > 2) <span class="disabled">&hellip;</span> @endif
                    @endif

                    @for($p = $from; $p <= $to; $p++)
                        @if($p == $current)
                            <span class="active">{{ $p }}</span>
                        @else
                            <a href="{{ $results->url($p) }}">{{ $p }}</a>
                        @endif
                    @endfor

                    @if($to < $last)
                        @if($to < $last - 1) <span class="disabled">&hellip;</span> @endif
                        <a href="{{ $results->url($last) }}">{{ $last }}</a>
                    @endif

                    {{-- Next --}}
                    @if($results->hasMorePages())
                        <a href="{{ $results->nextPageUrl() }}"><i class="fas fa-chevron-right" style="font-size:10px;"></i></a>
                    @else
                        <span class="disabled"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ─── Detail Modal ─── --}}
<div id="pkpaDetailOverlay" class="pkpa-overlay" onclick="if(event.target===this)closeDetail()">
    <div class="pkpa-modal">
        <div class="pkpa-modal-head">
            <h4><i class="fas fa-file-medical-alt"></i> Detail Antibiotik</h4>
            <button class="pkpa-modal-close" onclick="closeDetail()"><i class="fas fa-times"></i></button>
        </div>
        <div class="pkpa-modal-body">
            <div class="detail-grid">
                <div class="detail-box">
                    <div class="detail-label">Pasien</div>
                    <div class="detail-value" id="mdNama">-</div>
                    <div style="font-size:11px;color:#8b95a2;margin-top:2px;">RM: <span id="mdRM">-</span></div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">DPJP</div>
                    <div class="detail-value" id="mdDPJP">-</div>
                    <div style="font-size:11px;color:#8b95a2;margin-top:2px;">No Rawat: <span id="mdNoRawat">-</span></div>
                </div>
            </div>
            <div class="detail-grid" style="margin-top:14px;">
                <div class="detail-box">
                    <div class="detail-label">Kategori</div>
                    <div class="detail-value" id="mdKat">-</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Ruangan</div>
                    <div class="detail-value" id="mdRuangan">-</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Lama Rawat Inap</div>
                    <div class="detail-value" id="mdLRI">-</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Status Resep</div>
                    <div class="detail-value" id="mdStatusResep">-</div>
                </div>
                <div class="detail-box full">
                    <div class="detail-label">Alamat</div>
                    <div class="detail-value" id="mdAlamat" style="font-weight:500;">-</div>
                </div>
                <div class="detail-box full">
                    <div class="detail-label">Diagnosis</div>
                    <div class="detail-value" id="mdDiag" style="font-weight:500;">-</div>
                </div>
            </div>
            <hr class="detail-divider">
            <div class="detail-grid">
                <div class="detail-box">
                    <div class="detail-label">Nama Antibiotik</div>
                    <div class="detail-value" id="mdObat">-</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Kode ATC</div>
                    <div class="detail-value" id="mdATC">-</div>
                </div>
                <div class="detail-box full">
                    <div class="detail-label">Aturan Pakai</div>
                    <div class="detail-value" id="mdAturan" style="font-weight:500;">-</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Dosis / Hari</div>
                    <div class="detail-value teal" id="mdDosisHr">-</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Lama Terapi AB</div>
                    <div class="detail-value" id="mdLamaTerapi">-</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Total Dosis</div>
                    <div class="detail-value" id="mdTotalDosis">-</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">DDD</div>
                    <div class="detail-value teal" id="mdDDD">-</div>
                </div>
                <div class="detail-box full">
                    <div class="detail-label">Tanggal Pemberian Obat</div>
                    <div class="detail-value" id="mdTglPemberian" style="font-weight:500;font-family:monospace;font-size:12px;">-</div>
                </div>
            </div>
        </div>
        <div class="pkpa-modal-foot">
            <button onclick="closeDetail()" class="btn-pkpa btn-pkpa-primary" style="padding:8px 20px;">Tutup</button>
        </div>
    </div>
</div>

{{-- ─── Toast ─── --}}
<div id="pkpaToast" class="pkpa-toast">
    <i class="fas fa-check-circle"></i>
    <span id="pkpaToastMsg">Data berhasil disalin!</span>
</div>

<script>
// ═══ Per-page change ═══
function changePerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// ═══ Copy ALL data to clipboard (TSV) ═══
function copyTableToClipboard() {
    const headers = [
        'RM', 'Nama', 'Alamat', 'Diagnosis', 'DPJP', 'Ruangan',
        'Nama Antibiotik', 'Regimen Dosis', 'Dosis per-hari',
        'Kode ATC', 'Lama Terapi AB', 'Total Dosis', 'Kode DDD', 'DDD',
        'No Rawat', 'Kategori Pasien', 'Lama Rawat Inap', 'Tanggal Pemberian Obat', 'Status Resep'
    ];
    const rawData = @json($allResults);
    if (!rawData || rawData.length === 0) {
        showToast('Tidak ada data untuk disalin!');
        return;
    }
    const rows = rawData.map(r => [
        r['RM'], r['NAMA'], r['ALAMAT'], r['DIAGNOSIS'], r['DPJP'], r['Ruangan'],
        r['Nama Antibiotik'], r['Regimen Dosis'], r['Dosis per-hari'],
        r['Kode'], r['Lama Terapi AB'], r['Total Dosis'], r['Kode DDD'],
        r['DDD'] !== null ? r['DDD'] : '',
        r['No Rawat'], r['Kategori Pasien'], r['Lama Rawat Inap'],
        r['Tanggal Pemberian Obat'], r['Status Resep']
    ].map(v => v == null ? '' : String(v).replace(/\t/g, ' ')).join('\t'));

    const tsv = headers.join('\t') + '\n' + rows.join('\n');
    navigator.clipboard.writeText(tsv).then(() => {
        showToast('Semua ' + rawData.length + ' baris berhasil di-copy! Paste ke Excel.');
        const btn = document.getElementById('btnCopyExcel');
        btn.classList.add('copied');
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        setTimeout(() => { btn.classList.remove('copied'); btn.innerHTML = '<i class="far fa-copy"></i> Copy'; }, 2500);
    }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = tsv; ta.style.cssText = 'position:fixed;left:-9999px;';
        document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
        showToast('Semua ' + rawData.length + ' baris berhasil di-copy!');
    });
}

function showToast(msg) {
    const t = document.getElementById('pkpaToast');
    document.getElementById('pkpaToastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

// ═══ Detail Modal ═══
function openDetail(row) {
    const f = new Intl.NumberFormat('id-ID');
    const fd = new Intl.NumberFormat('id-ID', { minimumFractionDigits:3, maximumFractionDigits:3 });
    document.getElementById('mdNama').textContent = row.NAMA;
    document.getElementById('mdRM').textContent = row.RM;
    document.getElementById('mdDPJP').textContent = row.DPJP;
    document.getElementById('mdNoRawat').textContent = row['No Rawat'];
    document.getElementById('mdKat').textContent = row['Kategori Pasien'];
    document.getElementById('mdRuangan').textContent = row['Ruangan'];
    document.getElementById('mdLRI').textContent = row['Lama Rawat Inap'] + ' Hari';
    document.getElementById('mdStatusResep').textContent = row['Status Resep'];
    document.getElementById('mdAlamat').textContent = row.ALAMAT;
    document.getElementById('mdDiag').textContent = row.DIAGNOSIS;
    document.getElementById('mdObat').textContent = row['Nama Antibiotik'];
    document.getElementById('mdATC').textContent = row.Kode;
    document.getElementById('mdAturan').textContent = row['Regimen Dosis'];
    document.getElementById('mdDosisHr').textContent = f.format(row['Dosis per-hari']) + ' mg/hari';
    document.getElementById('mdLamaTerapi').textContent = row['Lama Terapi AB'] + ' Hari';
    document.getElementById('mdTotalDosis').textContent = fd.format(row['Total Dosis']) + ' gram';
    document.getElementById('mdDDD').textContent = row.DDD !== null ? fd.format(row.DDD) : '-';
    document.getElementById('mdTglPemberian').textContent = row['Tanggal Pemberian Obat'];
    document.getElementById('pkpaDetailOverlay').classList.add('active');
}
function closeDetail() { document.getElementById('pkpaDetailOverlay').classList.remove('active'); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });
</script>
@endsection
