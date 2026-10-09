<div>

    <style>
        :root {
            --c-navy: #3d5a80;  --c-navy-soft: #e8eef5;
            --c-blue: #5b8db8;  --c-blue-soft: #eaf2f9;
            --c-rose: #c06c6c;  --c-rose-soft: #f8ecec;
            --c-sage: #6b9080;  --c-sage-soft: #ecf3ef;
            --c-gray: #64748b;  --c-gray-soft: #f1f5f9;
            --c-warn: #d97706;  --c-warn-soft: #fef3c7;
            --c-succ: #059669;  --c-succ-soft: #d1fae5;
        }
        
        /* Base Card & Forms */
        .card-clean {
            background: #fff;
            border: 1px solid #e8ecf1;
            border-radius: 14px;
            box-shadow: 0 4px 12px -6px rgba(15,23,42,.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .card-header-clean {
            background-color: #fafbfc;
            border-bottom: 1px solid #e8ecf1;
            padding: 1.2rem 1.5rem;
        }
        .card-title-clean {
            margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--c-navy);
        }
        .form-label-clean {
            font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;
        }
        .form-control-clean {
            border: 1px solid #cbd5e1; border-radius: 8px;
            padding: 0.55rem 0.85rem; font-size: 0.9rem; color: #334155;
            transition: all 0.2s;
        }
        .form-control-clean:focus {
            border-color: var(--c-blue); box-shadow: 0 0 0 3px var(--c-blue-soft); outline: none;
        }
        
        /* Badges */
        .badge-clean {
            padding: 0.35rem 0.75rem; border-radius: 99px;
            font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; justify-content: center;
        }
        .bg-blue-100 { background-color: var(--c-blue-soft); color: var(--c-blue); }
        .bg-green-100 { background-color: var(--c-succ-soft); color: var(--c-succ); }
        .bg-red-100 { background-color: var(--c-rose-soft); color: var(--c-rose); }
        .bg-yellow-100 { background-color: var(--c-warn-soft); color: var(--c-warn); }
        .bg-gray-100 { background-color: var(--c-gray-soft); color: var(--c-gray); }
        
        /* Main Table */
        .table-clean th {
            background-color: #f8fafc; color: #475569; font-size: 0.75rem;
            text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;
            padding: 1rem; border-bottom: 2px solid #e2e8f0; border-top: none;
        }
        .table-clean td {
            padding: 1rem; vertical-align: middle; color: #334155;
            font-size: 0.875rem; border-bottom: 1px solid #f1f5f9;
        }
        .table-clean tbody tr { transition: background 0.15s ease; }
        .table-clean tbody tr:hover { background: #f8fafc; }

        /* Rekap Stats & Hero */
        .stat-card {
            position: relative; overflow: hidden;
            background: #fff; border: 1px solid #e8ecf1;
            border-radius: 14px; padding: 1.25rem 1.5rem;
            color: #475569; margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px -6px rgba(15,23,42,.08);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .stat-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--accent); }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 24px -10px rgba(15,23,42,.15); }
        .stat-card .stat-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .08em; color: #64748b; font-weight: 600; }
        .stat-card .stat-value { font-size: 2.2rem; font-weight: 800; line-height: 1.1; margin-top: .35rem; color: var(--accent); }
        .stat-card .stat-sub { font-size: .78rem; color: #94a3b8; margin-top: .25rem; font-weight: 500; }
        .stat-card .stat-icon {
            position: absolute; right: 1.25rem; top: 50%; transform: translateY(-50%);
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem; color: var(--accent); background: var(--accent-soft);
        }
        .stat-total  { --accent: var(--c-navy); --accent-soft: var(--c-navy-soft); }
        .stat-ralan  { --accent: var(--c-blue); --accent-soft: var(--c-blue-soft); }
        .stat-ranap  { --accent: var(--c-rose); --accent-soft: var(--c-rose-soft); }
        .stat-penjab { --accent: var(--c-sage); --accent-soft: var(--c-sage-soft); }

        .hero-ugd {
            background: linear-gradient(135deg, #3d5a80 0%, #4a6fa0 100%);
            border-radius: 16px; padding: 1.5rem 2rem; color: #fff; margin-bottom: 2rem;
            box-shadow: 0 10px 25px -10px rgba(61,90,128,.6);
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;
        }
        .hero-ugd h2 { margin: 0; font-size: 1.5rem; font-weight: 800; letter-spacing: .02em; }
        .hero-ugd p { margin: .35rem 0 0; opacity: .9; font-size: .9rem; font-weight: 500; }
        .hero-ugd .live-dot { display:inline-block; width:8px; height:8px; border-radius:50%; background:#a7d7b8; margin-right:.5rem; box-shadow:0 0 0 0 rgba(167,215,184,.7); animation: pulse 2s infinite; }
        
        @keyframes pulse { 0% { box-shadow:0 0 0 0 rgba(167,215,184,.7);} 70% { box-shadow:0 0 0 8px rgba(167,215,184,0);} 100% { box-shadow:0 0 0 0 rgba(167,215,184,0);} }

        /* Rekap Table Specific */
        .rekap-table td { padding: 1rem !important; }
        .pj-name { font-weight: 700; color: var(--c-navy); }
        .pill { display: inline-block; min-width: 44px; text-align: center; padding: .25rem .7rem; border-radius: 99px; font-weight: 700; font-size: .8rem; }
        .pill-ralan { background: var(--c-blue-soft); color: var(--c-blue); }
        .pill-ranap { background: var(--c-rose-soft); color: var(--c-rose); }
        .pill-total { background: var(--c-navy-soft); color: var(--c-navy); }
        .ratio-bar { display: flex; height: 6px; border-radius: 99px; overflow: hidden; background: #e8ecf1; min-width: 140px; }
        .ratio-bar .r-ralan { background: var(--c-blue); transition: width 0.5s ease; }
        .ratio-bar .r-ranap { background: var(--c-rose); transition: width 0.5s ease; }
        .ratio-text { font-size: .75rem; color: #64748b; margin-top: .35rem; font-weight: 500; }
        
        /* Excel Buttons */
        .btn-xls {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .4rem .8rem; border-radius: 8px; font-size: .8rem; font-weight: 600;
            color: var(--c-sage) !important; text-decoration: none !important;
            background: var(--c-sage-soft); border: 1px solid transparent;
            transition: all .2s ease;
        }
        .btn-xls:hover { background: var(--c-sage); color: #fff !important; transform: translateY(-2px); box-shadow: 0 4px 12px -4px rgba(107,144,128,.5); }
        .btn-xls-lg { padding: .6rem 1.2rem; font-size: .9rem; background: #fff; color: var(--c-navy) !important; box-shadow: 0 4px 12px -4px rgba(0,0,0,.15); border: none; }
        .btn-xls-lg:hover { background: #f8fafc; color: var(--c-navy) !important; box-shadow: 0 8px 16px -6px rgba(0,0,0,.2); }
    </style>

    <section class="content mt-3">
        <div class="container-fluid">
            
            <!-- Filter Section -->
            <div class="card-clean">
                <div class="card-header-clean d-flex justify-content-between align-items-center">
                    <h3 class="card-title-clean">Filter Data UGD</h3>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="form-label-clean">Tanggal Awal</label>
                                <input type="date" class="form-control form-control-clean" wire:model="tgl_awal">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="form-label-clean">Tanggal Akhir</label>
                                <input type="date" class="form-control form-control-clean" wire:model="tgl_akhir">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="form-label-clean">Jenis Bayar</label>
                                <select class="form-control form-control-clean" wire:model="jenis_bayar">
                                    <option value="">Semua Jenis Bayar</option>
                                    @foreach($listPenjab as $pj)
                                        <option value="{{ $pj->kd_pj }}">{{ $pj->png_jawab }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="form-label-clean">Cari Pasien</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-clean" style="border-top-right-radius: 0; border-bottom-right-radius: 0;" wire:model="search" placeholder="No Rawat / Nama...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; background-color: var(--c-navy); border-color: var(--c-navy); z-index: 0; box-shadow: none;">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rekap Section -->


            @php
                $pctRalan = $totalSemua ? round($totalRalan / $totalSemua * 100) : 0;
                $pctRanap = $totalSemua ? round($totalRanap / $totalSemua * 100) : 0;
                $exportBase = url('/pantau-ugd/export') . '?' . http_build_query([
                    'tgl_awal' => $tgl_awal,
                    'tgl_akhir' => $tgl_akhir,
                    'search' => $search,
                ]);
            @endphp

            <div class="hero-ugd">
                <div>
                    <h2><i class="fas fa-ambulance mr-2"></i>Rekap Pasien UGD</h2>
                    <p><span class="live-dot"></span>Periode {{ date('d/m/Y', strtotime($tgl_awal)) }} &ndash; {{ date('d/m/Y', strtotime($tgl_akhir)) }}</p>
                </div>
                <a href="{{ $exportBase . '&kd_pj=' . urlencode($jenis_bayar) }}" class="btn-xls btn-xls-lg" target="_blank">
                    <i class="fas fa-file-excel"></i> Download Excel {{ $jenis_bayar ? '(Jenis Bayar Terpilih)' : '(Semua)' }}
                </a>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card stat-total">
                        <div class="stat-label">Total Pasien UGD</div>
                        <div class="stat-value">{{ $totalSemua }}</div>
                        <div class="stat-sub">{{ date('d/m/Y', strtotime($tgl_awal)) }} - {{ date('d/m/Y', strtotime($tgl_akhir)) }}</div>
                        <i class="fas fa-users stat-icon"></i>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card stat-ralan">
                        <div class="stat-label">Rawat Jalan (Ralan)</div>
                        <div class="stat-value">{{ $totalRalan }}</div>
                        <div class="stat-sub">{{ $pctRalan }}% dari total</div>
                        <i class="fas fa-walking stat-icon"></i>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card stat-ranap">
                        <div class="stat-label">Rawat Inap (Ranap)</div>
                        <div class="stat-value">{{ $totalRanap }}</div>
                        <div class="stat-sub">{{ $pctRanap }}% dari total</div>
                        <i class="fas fa-procedures stat-icon"></i>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card stat-penjab">
                        <div class="stat-label">Jenis Bayar</div>
                        <div class="stat-value">{{ $rekapPenjab->count() }}</div>
                        <div class="stat-sub">jenis bayar terdaftar</div>
                        <i class="fas fa-wallet stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="card-clean">
                <div class="card-header-clean d-flex justify-content-between align-items-center">
                    <h3 class="card-title-clean"><i class="fas fa-chart-bar mr-2" style="color:var(--c-navy);"></i>Rekap per Jenis Bayar</h3>
                    <div style="font-size:.75rem;color:#6b7280;">
                        <span class="pill pill-ralan" style="min-width:auto;">Ralan</span>
                        <span class="pill pill-ranap" style="min-width:auto;">Ranap</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-clean rekap-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>Jenis Bayar</th>
                                <th class="text-center">Ralan</th>
                                <th class="text-center">Ranap</th>
                                <th class="text-center">Total</th>
                                <th>Proporsi Ralan / Ranap</th>
                                <th class="text-center">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekapPenjab as $i => $r)
                                @php
                                    $pR = $r->total ? round($r->ralan / $r->total * 100) : 0;
                                    $pI = $r->total ? 100 - $pR : 0;
                                @endphp
                                <tr>
                                    <td class="text-secondary">{{ $i + 1 }}</td>
                                    <td class="pj-name">{{ $r->jenis_bayar }}</td>
                                    <td class="text-center"><span class="pill pill-ralan">{{ $r->ralan }}</span></td>
                                    <td class="text-center"><span class="pill pill-ranap">{{ $r->ranap }}</span></td>
                                    <td class="text-center"><span class="pill pill-total">{{ $r->total }}</span></td>
                                    <td>
                                        <div class="ratio-bar">
                                            <div class="r-ralan" style="width: {{ $pR }}%"></div>
                                            <div class="r-ranap" style="width: {{ $pI }}%"></div>
                                        </div>
                                        <div class="ratio-text">{{ $pR }}% Ralan &middot; {{ $pI }}% Ranap</div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ $exportBase . '&kd_pj=' . urlencode($r->kd_pj) }}" class="btn-xls" target="_blank" title="Excel: sheet Ralan & Ranap">
                                            <i class="fas fa-file-excel"></i> Excel
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-secondary py-4">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapPenjab->count())
                        <tfoot>
                            <tr style="background:var(--c-gray-soft);font-weight:700;">
                                <td></td>
                                <td>TOTAL</td>
                                <td class="text-center"><span class="pill pill-ralan">{{ $totalRalan }}</span></td>
                                <td class="text-center"><span class="pill pill-ranap">{{ $totalRanap }}</span></td>
                                <td class="text-center"><span class="pill pill-total">{{ $totalSemua }}</span></td>
                                <td>
                                    <div class="ratio-bar">
                                        <div class="r-ralan" style="width: {{ $pctRalan }}%"></div>
                                        <div class="r-ranap" style="width: {{ $totalSemua ? 100 - $pctRalan : 0 }}%"></div>
                                    </div>
                                    <div class="ratio-text">{{ $pctRalan }}% Ralan &middot; {{ $totalSemua ? 100 - $pctRalan : 0 }}% Ranap</div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ $exportBase }}" class="btn-xls" target="_blank" title="Excel semua jenis bayar">
                                        <i class="fas fa-file-excel"></i> Semua
                                    </a>
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Table Section -->
            <div class="card-clean">
                <div class="table-responsive">
                    <table class="table table-hover table-clean text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Rawat</th>
                                <th>No. RM</th>
                                <th>Nama Pasien</th>
                                <th>Tanggal Masuk</th>
                                <th>Poliklinik</th>
                                <th>Jenis Bayar</th>
                                <th>Status</th>
                                <th>Status Lanjut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pasienUgd as $index => $pasien)
                                <tr>
                                    <td class="text-secondary">{{ $pasienUgd->firstItem() + $index }}</td>
                                    <td class="font-weight-bold">{{ $pasien->no_rawat }}</td>
                                    <td>{{ $pasien->no_rkm_medis }}</td>
                                    <td>{{ $pasien->nm_pasien }}</td>
                                    <td>
                                        {{ date('d/m/Y', strtotime($pasien->tgl_registrasi)) }} 
                                        <span class="text-secondary ml-1">{{ $pasien->jam_reg }}</span>
                                    </td>
                                    <td>{{ $pasien->nm_poli }}</td>
                                    <td>{{ $pasien->jenis_bayar }}</td>
                                    <td>
                                        @if($pasien->stts == 'Belum')
                                            <span class="badge-clean bg-yellow-100">Belum</span>
                                        @elseif($pasien->stts == 'Sudah')
                                            <span class="badge-clean bg-green-100">Sudah</span>
                                        @elseif($pasien->stts == 'Batal')
                                            <span class="badge-clean bg-red-100">Batal</span>
                                        @else
                                            <span class="badge-clean bg-blue-100">{{ $pasien->stts }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pasien->status_lanjut == 'Ranap')
                                            <span class="badge-clean bg-red-100">Ranap</span>
                                        @else
                                            <span class="badge-clean bg-blue-100">{{ $pasien->status_lanjut }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-secondary">
                                        Tidak ada data pasien ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($pasienUgd->hasPages())
                <div class="card-footer bg-white border-top border-light px-3 py-2">
                    {{ $pasienUgd->links() }}
                </div>
                @endif
            </div>
            
        </div>
    </section>
</div>
