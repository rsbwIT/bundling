@extends('layout.layoutDashboard')
@section('title', 'BPJS API Monitoring Dashboard')

@section('konten')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    /* ==========================================================
       DASHBOARD STYLING - TELEMETRY COMMAND CENTER (RS BUMI WARAS)
       Ringan, Cepat, Tanpa Beban Server & Tanpa Auto-Ping di Background
       ========================================================== */
    :root {
        --tele-primary: #0284c7;
        --tele-success: #10b981;
        --tele-warning: #f59e0b;
        --tele-danger: #ef4444;
        --tele-dark: #0f172a;
        --tele-card-bg: #ffffff;
        --tele-border: #e2e8f0;
        --tele-text-main: #1e293b;
        --tele-text-muted: #64748b;
    }

    body.dark-mode {
        --tele-card-bg: #1e222d;
        --tele-border: #2d3343;
        --tele-text-main: #f1f5f9;
        --tele-text-muted: #94a3b8;
    }

    .telemetry-dashboard {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--tele-text-main);
    }

    .telemetry-mono {
        font-family: 'JetBrains Mono', monospace;
    }

    /* Glassmorphism Header */
    .tele-command-header {
        background: linear-gradient(135deg, rgba(2, 132, 199, 0.08) 0%, rgba(16, 185, 129, 0.06) 50%, rgba(99, 102, 241, 0.05) 100%), var(--tele-card-bg);
        border: 1px solid var(--tele-border);
        border-radius: 1.25rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        backdrop-filter: blur(8px);
        position: relative;
        overflow: hidden;
    }

    .tele-command-header::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #0284c7, #10b981, #6366f1, #0284c7);
        background-size: 200% 100%;
    }

    /* Pulse Radar Animation */
    .pulse-radar-dot {
        position: relative;
        display: inline-flex;
        width: 9px;
        height: 9px;
        border-radius: 50%;
    }

    .pulse-radar-dot.standby { background-color: #0284c7; }
    .pulse-radar-dot.online { background-color: #10b981; }
    .pulse-radar-dot.warning { background-color: #f59e0b; }
    .pulse-radar-dot.offline { background-color: #ef4444; }
    .pulse-radar-dot.loading { background-color: #0284c7; }

    .pulse-radar-dot.online::before,
    .pulse-radar-dot.warning::before,
    .pulse-radar-dot.offline::before {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        border-radius: 50%;
        animation: radarPing 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
        opacity: 0.75;
    }

    .pulse-radar-dot.online::before { border: 2px solid #10b981; }
    .pulse-radar-dot.warning::before { border: 2px solid #f59e0b; }
    .pulse-radar-dot.offline::before { border: 2px solid #ef4444; }

    @keyframes radarPing {
        75%, 100% {
            transform: scale(2.2);
            opacity: 0;
        }
    }

    /* Top KPI Metric Cards */
    .kpi-card {
        background: var(--tele-card-bg);
        border: 1px solid var(--tele-border);
        border-radius: 1rem;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
    }

    .kpi-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    /* Service Grid Endpoint Card */
    .endpoint-card {
        background: var(--tele-card-bg);
        border: 1px solid var(--tele-border);
        border-radius: 1.15rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .endpoint-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 28px -10px rgba(0, 0, 0, 0.12);
        border-color: rgba(2, 132, 199, 0.4);
    }

    .endpoint-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #cbd5e1;
        transition: background-color 0.3s ease;
    }

    .endpoint-card.state-standby::after { background: #94a3b8; }
    .endpoint-card.state-online::after { background: #10b981; }
    .endpoint-card.state-warning::after { background: #f59e0b; }
    .endpoint-card.state-offline::after { background: #ef4444; }
    .endpoint-card.state-checking::after { background: #0284c7; }

    .service-badge {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .service-badge.badge-standby {
        background: rgba(100, 116, 139, 0.12);
        color: #64748b;
        border: 1px solid rgba(100, 116, 139, 0.25);
    }

    .service-badge.badge-online {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .service-badge.badge-warning {
        background: rgba(245, 158, 11, 0.12);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }

    .service-badge.badge-offline {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    .service-badge.badge-checking {
        background: rgba(2, 132, 199, 0.12);
        color: #0284c7;
        border: 1px solid rgba(2, 132, 199, 0.25);
    }

    /* Latency Progress Bar */
    .tele-bar-track {
        height: 6px;
        background: #f1f5f9;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }

    body.dark-mode .tele-bar-track {
        background: #2a303f;
    }

    .tele-bar-fill {
        height: 100%;
        border-radius: 10px;
        width: 0%;
        transition: width 0.4s ease, background 0.4s ease;
        background: #94a3b8;
    }

    /* Copy Button */
    .btn-copy-url {
        opacity: 0.6;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-copy-url:hover {
        opacity: 1;
        color: #0284c7;
    }

    /* Chart Card */
    .tele-chart-card {
        background: var(--tele-card-bg);
        border: 1px solid var(--tele-border);
        border-radius: 1.15rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
    }

    /* Quick Action Pill */
    .btn-quick-action {
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0.45rem 1rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-quick-action:hover {
        transform: translateY(-1px);
    }

    /* Category Filter Pills */
    .filter-tab-pill {
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.35rem 1rem;
        color: var(--tele-text-muted);
        background: transparent;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-tab-pill:hover {
        color: var(--tele-primary);
        background: rgba(2, 132, 199, 0.08);
    }

    .filter-tab-pill.active {
        color: #ffffff !important;
        background: #0284c7 !important;
        border-color: #0284c7 !important;
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
    }

    /* Fullscreen Mode Styling */
    .telemetry-dashboard:fullscreen {
        background: #0b0f19;
        color: #f1f5f9;
        padding: 1.5rem;
        overflow-y: auto;
    }

    .telemetry-dashboard:fullscreen .endpoint-card,
    .telemetry-dashboard:fullscreen .kpi-card,
    .telemetry-dashboard:fullscreen .tele-chart-card,
    .telemetry-dashboard:fullscreen .tele-command-header {
        background: #151a26;
        border-color: #242c3d;
        color: #f1f5f9;
    }

    .telemetry-dashboard:fullscreen .text-muted {
        color: #94a3b8 !important;
    }

    /* Leaderboard item */
    .rank-item {
        padding: 8px 12px;
        border-radius: 8px;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .rank-item:hover {
        background-color: rgba(2, 132, 199, 0.06);
    }

    .refresh-counter-pill {
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 20px;
        padding: 0.25rem 0.6rem;
        background: rgba(100, 116, 139, 0.12);
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>

<div class="telemetry-dashboard pb-5" id="telemetryRoot">

    <!-- ==========================================
         TOP COMMAND CENTER & MANUAL CONTROLS
         ========================================== -->
    <div class="tele-command-header p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                    <span class="badge" style="background: rgba(2, 132, 199, 0.15); color: #0284c7; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 8px;">
                        <i class="fas fa-satellite-dish me-1"></i> SIMRS RS BUMI WARAS
                    </span>
                    <span class="badge" id="masterStatusBadge" style="background: rgba(2, 132, 199, 0.12); color: #0284c7; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 8px;">
                        <span class="pulse-radar-dot standby me-1" id="masterRadarDot"></span>
                        <span id="masterStatusText">STANDBY (MODE MANUAL)</span>
                    </span>
                    <span class="refresh-counter-pill" id="autoRefreshPill" title="Mode eksekusi tes monitoring">
                        <i class="fas fa-hand-pointer"></i> Mode: <span id="refreshCountdown">Manual</span>
                    </span>
                    <span class="text-muted small telemetry-mono" id="liveClock">--:--:-- WIB</span>
                </div>
                <h4 class="fw-bold mb-1" style="letter-spacing: -0.5px;">
                    BPJS API & Infrastructure Live Telemetry
                </h4>
                <p class="text-muted mb-0 small">
                    Mode hemat server: pengecekan sinyal tidak berjalan otomatis. Klik tombol <strong>"Test Ping Semua"</strong> untuk menguji respons layanan saat dibutuhkan.
                </p>
            </div>

            <!-- Toolbar Controls -->
            <div class="d-flex align-items-center flex-wrap gap-2">
                <!-- ON / OFF Layar Monitoring Toggle Switch -->
                <button type="button" class="btn btn-outline-danger btn-quick-action shadow-sm" id="btnTogglePower" onclick="toggleScreenPower()" title="Nyalakan / Matikan Layar Monitoring">
                    <i class="fas fa-power-off text-danger" id="iconPower"></i>
                    <span id="labelPower">Layar: AKTIF</span>
                </button>

                <!-- Check All Button (Utama) -->
                <button type="button" class="btn btn-primary btn-quick-action shadow" id="btnCheckAll" onclick="checkAll()">
                    <i class="fas fa-play" id="iconCheckAll"></i>
                    <span>Test Ping Sekarang</span>
                </button>

                <!-- Auto Refresh Interval Dropdown (Default OFF / Manual) -->
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary btn-quick-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownIntervalBtn">
                        <i class="fas fa-sliders-h"></i> <span id="intervalLabel">Mode: Manual</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" style="font-size: 0.85rem;">
                        <a class="dropdown-item active fw-bold text-primary" href="javascript:void(0)" onclick="setAutoRefreshInterval(0)">
                            <i class="fas fa-check me-1"></i> Mode Manual Saja (Rekomendasi - Ringan)
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="setAutoRefreshInterval(30)">Auto-Ping Tiap 30 Detik</a>
                        <a class="dropdown-item" href="javascript:void(0)" onclick="setAutoRefreshInterval(60)">Auto-Ping Tiap 60 Detik</a>
                    </div>
                </div>

                <!-- Sound Alert Toggle -->
                <button type="button" class="btn btn-outline-secondary btn-quick-action" id="btnSoundAlert" onclick="toggleSoundAlert()" title="Bunyikan nada jika ada layanan down saat tes">
                    <i class="fas fa-volume-up" id="iconSound"></i>
                    <span id="labelSound">Sound: ON</span>
                </button>

                <!-- Riwayat Gangguan Modal -->
                <button type="button" class="btn btn-outline-danger btn-quick-action" onclick="bukaModalGangguan()" title="Lihat log riwayat dan ekspor laporan PDF">
                    <i class="fas fa-file-invoice"></i>
                    <span>Log Gangguan</span>
                </button>

                <!-- Fullscreen Toggle -->
                <button type="button" class="btn btn-outline-dark btn-quick-action" onclick="toggleFullscreen()" title="Tampilan Layar Penuh (NOC TV Display)">
                    <i class="fas fa-expand" id="iconFullscreen"></i>
                </button>
            </div>
        </div>

        <!-- TV BPJS Alert Banner (Jika sedang mode gangguan) -->
        @if(isset($bpjsTvStatus) && !empty($bpjsTvStatus['is_gangguan']))
        <div class="alert alert-danger mb-0 mt-3 d-flex align-items-center justify-content-between p-2 px-3 border-0 shadow-sm" style="border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-bullhorn fa-lg me-2 text-danger"></i>
                <div>
                    <strong class="d-block" style="font-size: 0.85rem;">Status TV Antrian Aktif: MODE PENGUMUMAN GANGGUAN BPJS</strong>
                    <small class="text-white-50">Layar TV Ruang Tunggu Antrian sedang menampilkan pemberitahuan gangguan server BPJS.</small>
                </div>
            </div>
            <a href="{{ route('antrian.status-bpjs.view') }}" class="btn btn-sm btn-light text-danger fw-bold py-1 px-3" style="border-radius: 6px;">Kelola TV</a>
        </div>
        @endif
    </div>

    <!-- ==========================================
         DEACTIVATED MONITORING PLACEHOLDER VIEW
         ========================================== -->
    <div id="monitoringDeactivatedView" class="mb-4" style="display: none;">
        <div class="card border-0 shadow-sm p-4 p-md-5 text-center" style="border-radius: 1.25rem; background: var(--tele-card-bg); border: 1px solid var(--tele-border) !important;">
            <div class="mx-auto mb-3" style="width: 76px; height: 76px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 2.2rem;">
                <i class="fas fa-power-off"></i>
            </div>
            <h4 class="fw-bold mb-2">Layar Monitoring Dinonaktifkan</h4>
            <p class="text-muted mx-auto mb-4" style="max-width: 600px; font-size: 0.95rem;">
                Layar pemantauan sinyal & latensi BPJS saat ini berada dalam mode <strong>Nonaktif</strong> untuk menghemat resource sistem dan menjaga performa server tetap maksimal.
            </p>
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-success px-4 py-2 fw-bold shadow-sm" onclick="toggleScreenPower(true)" style="border-radius: 10px;">
                    <i class="fas fa-toggle-on me-1"></i> Aktifkan Layar Monitoring
                </button>
                <button type="button" class="btn btn-outline-danger px-4 py-2" onclick="bukaModalGangguan()" style="border-radius: 10px;">
                    <i class="fas fa-file-invoice me-1"></i> Riwayat Gangguan & Log
                </button>
            </div>
        </div>
    </div>

    <!-- ==========================================
         ACTIVE MONITORING WRAPPER (KPI, GRIDS, CHARTS)
         ========================================== -->
    <div id="monitoringActiveView">
        <!-- TOP KPI STATISTIC CARDS (5 METRIC CARDS) -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
        <!-- KPI 1: BPJS Cloud -->
        <div class="col">
            <div class="kpi-card p-3 h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">BPJS Cloud</span>
                        <h4 class="fw-bold mb-0 mt-1" id="statBpjsCount">6 Endpoint</h4>
                    </div>
                    <div class="kpi-icon-box" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                        <i class="fas fa-cloud"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between small text-muted">
                    <span>Status Monitoring</span>
                    <span class="fw-bold text-primary" id="statBpjsPercent">Siap Diuji</span>
                </div>
            </div>
        </div>

        <!-- KPI 2: Server Lokal & SIMRS -->
        <div class="col">
            <div class="kpi-card p-3 h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Server Lokal</span>
                        <h4 class="fw-bold mb-0 mt-1" id="statLocalCount">4 Endpoint</h4>
                    </div>
                    <div class="kpi-icon-box" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between small text-muted">
                    <span>E-Klaim & Khanza</span>
                    <span class="fw-bold text-primary" id="statLocalPercent">Siap Diuji</span>
                </div>
            </div>
        </div>

        <!-- KPI 3: Koneksi ISP / Jaringan -->
        <div class="col">
            <div class="kpi-card p-3 h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">ISP & Internet</span>
                        <h4 class="fw-bold mb-0 mt-1" id="statNetworkCount">2 Gateway</h4>
                    </div>
                    <div class="kpi-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                        <i class="fas fa-wifi"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between small text-muted">
                    <span>iForte FO 1 & 2</span>
                    <span class="fw-bold text-primary" id="statNetworkPercent">Siap Diuji</span>
                </div>
            </div>
        </div>

        <!-- KPI 4: Rata-rata Latensi Global -->
        <div class="col">
            <div class="kpi-card p-3 h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Avg Latency</span>
                        <h4 class="fw-bold mb-0 mt-1 telemetry-mono text-primary" id="statAvgLatency">- ms</h4>
                    </div>
                    <div class="kpi-icon-box" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between small text-muted">
                    <span>Beban Server</span>
                    <span class="fw-bold text-success" id="statQualityLabel">0% (Aman)</span>
                </div>
            </div>
        </div>

        <!-- KPI 5: Mode Ringan & Log Gangguan -->
        <div class="col">
            <div class="kpi-card p-3 h-100 shadow-sm" onclick="bukaModalGangguan()" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Laporan Log</span>
                        <h4 class="fw-bold mb-0 mt-1 text-primary">
                            Riwayat
                        </h4>
                    </div>
                    <div class="kpi-icon-box" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between small text-muted">
                    <span>Cetak Bukti Gangguan</span>
                    <span class="text-primary fw-semibold">Buka <i class="fas fa-chevron-right ms-1" style="font-size: 0.65rem;"></i></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         FILTER TABS & SEARCH BAR
         ========================================== -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <!-- Category Tabs -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="filter-tab-pill active" onclick="filterCategory('all', this)">
                <i class="fas fa-layer-group me-1"></i> Semua Layanan ({{ count($services) }})
            </button>
            <button type="button" class="filter-tab-pill" onclick="filterCategory('bpjs', this)">
                <i class="fas fa-heartbeat me-1"></i> BPJS Cloud (6)
            </button>
            <button type="button" class="filter-tab-pill" onclick="filterCategory('local', this)">
                <i class="fas fa-server me-1"></i> Server Lokal & SIMRS (4)
            </button>
            <button type="button" class="filter-tab-pill" onclick="filterCategory('network', this)">
                <i class="fas fa-network-wired me-1"></i> ISP & Gateway (2)
            </button>
        </div>

        <!-- Search Input -->
        <div class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm" style="max-width: 220px;">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 20px 0 0 20px;">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 shadow-none" placeholder="Cari layanan..." style="border-radius: 0 20px 20px 0;" onkeyup="filterServices()">
            </div>
        </div>
    </div>

    <!-- ==========================================
         ENDPOINT GRID (CARDS)
         ========================================== -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3 mb-4" id="servicesGrid">
        @foreach($services as $service)
        <div class="col service-item" data-category="{{ $service['category'] ?? 'other' }}" data-name="{{ strtolower($service['name']) }}" data-url="{{ strtolower($service['url']) }}">
            <div class="endpoint-card state-standby p-3 h-100" id="card-{{ $service['id'] }}">
                <!-- Header Card -->
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="kpi-icon-box" style="background: {{ $service['accent'] ?? '#0284c7' }}18; color: {{ $service['accent'] ?? '#0284c7' }}; width: 38px; height: 38px; font-size: 1rem;">
                            <i class="{{ $service['icon'] }}"></i>
                        </div>
                        <div>
                            <span class="badge" style="background: rgba(100, 116, 139, 0.1); color: var(--tele-text-muted); font-size: 0.65rem; font-weight: 600;">
                                {{ $service['category_label'] ?? 'Layanan' }}
                            </span>
                        </div>
                    </div>

                    <!-- Status Pill (Default: Standby) -->
                    <span class="service-badge badge-standby" id="badge-{{ $service['id'] }}">
                        <span class="pulse-radar-dot standby" id="dot-{{ $service['id'] }}"></span>
                        <span id="text-{{ $service['id'] }}">Standby</span>
                    </span>
                </div>

                <!-- Title & Desc -->
                <h6 class="fw-bold mb-1 text-truncate" title="{{ $service['name'] }}" style="font-size: 0.95rem;">
                    {{ $service['name'] }}
                </h6>
                <p class="text-muted small mb-2 text-truncate" style="font-size: 0.75rem;" title="{{ $service['description'] ?? '' }}">
                    {{ $service['description'] ?? 'Integrasi Sistem RS' }}
                </p>

                <!-- URL with Copy -->
                <div class="d-flex align-items-center justify-content-between bg-light p-1 px-2 rounded mb-3 small" style="background: rgba(100, 116, 139, 0.06) !important; font-size: 0.72rem;">
                    <span class="text-muted text-truncate telemetry-mono me-1" title="{{ $service['url'] }}">
                        {{ $service['url'] }}
                    </span>
                    <i class="fas fa-copy btn-copy-url" onclick="copyToClipboard('{{ $service['url'] }}', this)" title="Salin URL / IP"></i>
                </div>

                <!-- Footer: Latency Visualizer & Single Refresh Button -->
                <div class="mt-auto pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <div>
                            <span class="text-muted d-block" style="font-size: 0.68rem; line-height: 1;">Response Time</span>
                            <span class="fw-bold fs-6 telemetry-mono text-muted" id="latency-{{ $service['id'] }}">- ms</span>
                        </div>
                        <div class="text-end">
                            <span class="text-muted d-block" style="font-size: 0.65rem;" id="time-{{ $service['id'] }}">Siap diuji</span>
                        </div>
                    </div>

                    <!-- Progress Latency Bar -->
                    <div class="tele-bar-track mb-2">
                        <div class="tele-bar-fill" id="bar-{{ $service['id'] }}"></div>
                    </div>

                    <!-- Action Single Check -->
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted" style="font-size: 0.68rem;" id="httpstatus-{{ $service['id'] }}">HTTP: Standby</span>
                        <button class="btn btn-sm btn-light border-0 shadow-none text-muted rounded-circle p-0 d-flex align-items-center justify-content-center" 
                                style="width: 28px; height: 28px; background: rgba(100, 116, 139, 0.08);"
                                onclick="checkSingle('{{ $service['id'] }}', '{{ $service['name'] }}', '{{ $service['url'] }}')" 
                                title="Periksa Layanan Ini Saja">
                            <i class="fas fa-sync-alt small" id="spin-{{ $service['id'] }}"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ==========================================
         TELEMETRY CHART & LEADERBOARD SECTION
         ========================================== -->
    <div class="row g-3 mb-4">
        <!-- Left: Real-time Multi-Service Latency Graph -->
        <div class="col-lg-8">
            <div class="tele-chart-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="kpi-icon-box" style="background: rgba(2, 132, 199, 0.1); color: #0284c7; width: 34px; height: 34px; font-size: 0.9rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size: 0.92rem;">Grafik Latensi Jaringan</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">Grafik waktu respon (ms) dari setiap endpoint saat diuji</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge" style="background: rgba(2, 132, 199, 0.12); color: #0284c7; font-size: 0.7rem;">
                            <span class="pulse-radar-dot standby me-1" style="width: 6px; height: 6px;"></span> Mode Manual
                        </span>
                    </div>
                </div>

                <div style="height: 260px; position: relative;">
                    <canvas id="latencyChart" style="height: 260px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Right: Latency Leaderboard / Ranking Matrix -->
        <div class="col-lg-4">
            <div class="tele-chart-card p-3 h-100 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="kpi-icon-box" style="background: rgba(16, 185, 129, 0.1); color: #10b981; width: 34px; height: 34px; font-size: 0.9rem;">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size: 0.92rem;">Peringkat Respon Tercepat</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">Hasil pengujian urutan kecepatan respons</small>
                        </div>
                    </div>
                </div>

                <div class="overflow-auto pe-1 flex-grow-1" id="leaderboardList" style="max-height: 250px;">
                    <div class="text-center text-muted py-5 small">
                        <i class="fas fa-info-circle me-1 text-primary"></i> Klik <strong>"Test Ping Sekarang"</strong> untuk mengukur kecepatan respon.
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div> <!-- End #monitoringActiveView -->

</div>

<!-- ==========================================
     MODAL RIWAYAT GANGGUAN & FILTER CETAK PDF
     ========================================== -->
<div class="modal fade" id="modalRiwayatGangguan" tabindex="-1" role="dialog" aria-labelledby="modalRiwayatGangguanLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">
            <div class="modal-header bg-danger text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-history fa-lg"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalRiwayatGangguanLabel">Riwayat Gangguan Koneksi & Downtime</h5>
                        <small class="text-white-50">Filter dan cetak laporan resmi bukti gangguan koneksi BPJS & SIMRS</small>
                    </div>
                </div>
                <button type="button" class="close text-white" onclick="tutupModal()" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-0">
                <!-- Filter Form Bar -->
                <div class="p-3 bg-light border-bottom">
                    <form class="row g-2 align-items-center" onsubmit="event.preventDefault(); fetchLogs();">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-server me-1"></i> Pilih Layanan:</label>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-white border w-100 text-start dropdown-toggle bg-white" type="button" id="dropdownLayanan" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    -- Semua Layanan --
                                </button>
                                <div class="dropdown-menu p-3 shadow-sm border-0" aria-labelledby="dropdownLayanan" style="max-height: 250px; overflow-y: auto; min-width: 280px;" onclick="event.stopPropagation();">
                                    @foreach($services as $service)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input filter-layanan-cb" type="checkbox" id="cb-{{ $service['id'] }}" value="{{ $service['id'] }}">
                                        <label class="form-check-label small" for="cb-{{ $service['id'] }}">{{ $service['name'] }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1"><i class="far fa-calendar-alt me-1"></i> Periode Tanggal:</label>
                            <div class="d-flex align-items-center gap-1">
                                <input type="date" id="filterTanggalAwal" class="form-control form-control-sm shadow-none bg-white">
                                <span class="text-muted small">s/d</span>
                                <input type="date" id="filterTanggalAkhir" class="form-control form-control-sm shadow-none bg-white">
                            </div>
                        </div>

                        <div class="col-md-4 d-flex align-items-end justify-content-end gap-2 pt-2 pt-md-0">
                            <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm" style="border-radius: 20px;">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                            <button type="button" class="btn btn-sm btn-danger px-3 shadow-sm" style="border-radius: 20px;" onclick="cetakPdf()">
                                <i class="fas fa-file-pdf me-1"></i> Ekspor PDF
                            </button>
                            <button type="button" class="btn btn-sm btn-light border px-3" style="border-radius: 20px;" onclick="resetLogFilter()">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Table Content -->
                <div class="table-responsive" style="max-height: 55vh; overflow-y: auto;">
                    <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-white text-secondary sticky-top border-bottom">
                            <tr>
                                <th class="text-center py-2" width="5%">No</th>
                                <th class="py-2" width="20%">Waktu Gangguan</th>
                                <th class="py-2" width="20%">Waktu Pemulihan</th>
                                <th class="py-2" width="25%">Layanan</th>
                                <th class="py-2" width="15%">Keterangan</th>
                                <th class="py-2 text-center" width="15%">Durasi Downtime</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRiwayatGangguan">
                            <tr><td colspan="6" class="text-center py-4 text-muted">Klik Cari untuk memuat data log riwayat.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-light py-2 px-3">
                <button type="button" class="btn btn-sm btn-secondary" onclick="tutupModal()" style="border-radius: 20px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const services = @json($services);
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
    
    // Status Storage
    let serviceStatus = {};
    let isCheckingAll = false;
    let countdownInterval = null;
    let refreshSeconds = 0; // Default 0 (Manual Only)
    let currentCountdown = 0;
    let soundEnabled = true;

    // Load sound preference from localStorage
    if (localStorage.getItem('bpjs_sound_alert') !== null) {
        soundEnabled = localStorage.getItem('bpjs_sound_alert') === 'true';
    }
    updateSoundUI();

    services.forEach(s => {
        serviceStatus[s.id] = { 
            status: 'standby', 
            latency: 0, 
            statusCode: null,
            timestamp: '-'
        };
    });

    // Realtime Clock
    setInterval(() => {
        const now = new Date();
        const clk = document.getElementById('liveClock');
        if (clk) clk.textContent = now.toLocaleTimeString() + ' WIB';
    }, 1000);

    // ==========================================
    // AUDIO SYNTHESIZER (WEB AUDIO API)
    // ==========================================
    function playAlertChime() {
        if (!soundEnabled) return;
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime);
            osc.frequency.setValueAtTime(440.00, ctx.currentTime + 0.15);
            
            gain.gain.setValueAtTime(0.12, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);

            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.4);
        } catch (e) {
            // Audio context failed
        }
    }

    function toggleSoundAlert() {
        soundEnabled = !soundEnabled;
        localStorage.setItem('bpjs_sound_alert', soundEnabled);
        updateSoundUI();
    }

    function updateSoundUI() {
        const icon = document.getElementById('iconSound');
        const label = document.getElementById('labelSound');
        if (icon && label) {
            if (soundEnabled) {
                icon.className = 'fas fa-volume-up text-success';
                label.textContent = 'Sound: ON';
            } else {
                icon.className = 'fas fa-volume-mute text-danger';
                label.textContent = 'Sound: OFF';
            }
        }
    }

    // ==========================================
    // ON/OFF SCREEN POWER MANAGEMENT (HEMAT DAYA / MATIKAN LAYAR)
    // ==========================================
    let isScreenActive = localStorage.getItem('bpjs_monitoring_screen_power') !== 'off';

    function toggleScreenPower(forceState) {
        if (typeof forceState === 'boolean') {
            isScreenActive = forceState;
        } else {
            isScreenActive = !isScreenActive;
        }

        localStorage.setItem('bpjs_monitoring_screen_power', isScreenActive ? 'on' : 'off');
        applyScreenPowerState();
    }

    function applyScreenPowerState() {
        const activeView = document.getElementById('monitoringActiveView');
        const deactView = document.getElementById('monitoringDeactivatedView');
        const btnPower = document.getElementById('btnTogglePower');
        const iconPower = document.getElementById('iconPower');
        const labelPower = document.getElementById('labelPower');
        const btnCheckAll = document.getElementById('btnCheckAll');
        const dropdownInterval = document.getElementById('dropdownIntervalBtn');
        const masterBadge = document.getElementById('masterStatusBadge');
        const masterDot = document.getElementById('masterRadarDot');
        const masterText = document.getElementById('masterStatusText');

        if (isScreenActive) {
            if (activeView) activeView.style.display = 'block';
            if (deactView) deactView.style.display = 'none';

            if (btnPower) {
                btnPower.className = 'btn btn-outline-danger btn-quick-action shadow-sm';
                btnPower.title = 'Klik untuk Nonaktifkan Layar Monitoring';
            }
            if (iconPower) iconPower.className = 'fas fa-power-off text-danger';
            if (labelPower) labelPower.textContent = 'Layar: AKTIF';
            if (btnCheckAll) btnCheckAll.style.display = 'inline-flex';
            if (dropdownInterval && dropdownInterval.parentElement) {
                dropdownInterval.parentElement.style.display = 'inline-block';
            }

            if (masterBadge && masterDot && masterText) {
                masterBadge.style.background = 'rgba(2, 132, 199, 0.12)';
                masterBadge.style.color = '#0284c7';
                masterDot.className = 'pulse-radar-dot standby me-1';
                masterDot.style.backgroundColor = '#0284c7';
                masterText.textContent = 'STANDBY (MODE MANUAL)';
            }

            // Sesuaikan ulang Chart.js jika sebelumnya tersembunyi
            if (latencyChart) {
                setTimeout(() => {
                    try { latencyChart.resize(); } catch(e) {}
                }, 100);
            }
        } else {
            // Matikan auto-refresh aktif jika ada
            setAutoRefreshInterval(0);

            if (activeView) activeView.style.display = 'none';
            if (deactView) deactView.style.display = 'block';

            if (btnPower) {
                btnPower.className = 'btn btn-success btn-quick-action shadow-sm text-white';
                btnPower.title = 'Klik untuk Aktifkan Layar Monitoring';
            }
            if (iconPower) iconPower.className = 'fas fa-toggle-on text-white';
            if (labelPower) labelPower.textContent = 'Layar: NONAKTIF';
            if (btnCheckAll) btnCheckAll.style.display = 'none';
            if (dropdownInterval && dropdownInterval.parentElement) {
                dropdownInterval.parentElement.style.display = 'none';
            }

            if (masterBadge && masterDot && masterText) {
                masterBadge.style.background = 'rgba(100, 116, 139, 0.15)';
                masterBadge.style.color = '#64748b';
                masterDot.className = 'pulse-radar-dot me-1';
                masterDot.style.backgroundColor = '#94a3b8';
                masterText.textContent = 'NONAKTIF (HEMAT DAYA)';
            }
        }
    }

    // ==========================================
    // CHART.JS INITIALIZATION (UNIVERSAL v2/v3/v4 COMPATIBLE)
    // ==========================================
    let latencyChart = null;
    const chartCanvas = document.getElementById('latencyChart');

    if (chartCanvas && typeof Chart !== 'undefined') {
        const chartCtx = chartCanvas.getContext('2d');
        const chartColors = [
            '#0284c7', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
            '#ec4899', '#06b6d4', '#f97316', '#6366f1', '#14b8a6',
            '#84cc16', '#a855f7'
        ];

        const chartDatasets = services.map((s, idx) => {
            const clr = s.accent || chartColors[idx % chartColors.length];
            return {
                label: s.name,
                data: [],
                borderColor: clr,
                backgroundColor: 'transparent',
                borderWidth: 2,
                lineTension: 0.35,
                pointRadius: 1,
                pointHoverRadius: 5
            };
        });

        const isV2 = !Chart.version || Chart.version.startsWith('2');

        const v2Options = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        suggestedMax: 150,
                        fontFamily: 'JetBrains Mono, monospace',
                        fontSize: 10,
                        callback: function(v) { return v + ' ms'; }
                    },
                    gridLines: {
                        color: 'rgba(100, 116, 139, 0.08)',
                        zeroLineColor: 'rgba(100, 116, 139, 0.15)'
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontFamily: 'Plus Jakarta Sans, sans-serif',
                        fontSize: 10
                    },
                    gridLines: { display: false }
                }]
            },
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    boxWidth: 8,
                    fontSize: 11,
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                    padding: 8
                }
            },
            tooltips: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                bodyFontFamily: 'JetBrains Mono, monospace',
                titleFontFamily: 'Plus Jakarta Sans, sans-serif',
                callbacks: {
                    label: function(item, data) {
                        var lbl = data.datasets[item.datasetIndex].label || '';
                        return ' ' + lbl + ': ' + item.yLabel + ' ms';
                    }
                }
            }
        };

        const v3Options = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 150,
                    grid: { color: 'rgba(100, 116, 139, 0.08)' },
                    ticks: { 
                        font: { family: 'JetBrains Mono', size: 10 },
                        callback: val => val + ' ms'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 10 } }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 8,
                        boxHeight: 8,
                        usePointStyle: true,
                        padding: 8,
                        font: { size: 11, family: 'Plus Jakarta Sans' }
                    }
                }
            }
        };

        latencyChart = new Chart(chartCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: chartDatasets
            },
            options: isV2 ? v2Options : v3Options
        });
    }

    function pushDataToChart() {
        if (!latencyChart) return;
        const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        
        if (latencyChart.data.labels.length > 15) {
            latencyChart.data.labels.shift();
            latencyChart.data.datasets.forEach(ds => ds.data.shift());
        }

        latencyChart.data.labels.push(timeNow);

        services.forEach((s, idx) => {
            const st = serviceStatus[s.id];
            const val = (st && st.status === 'online') ? st.latency : 0;
            latencyChart.data.datasets[idx].data.push(val);
        });

        latencyChart.update();
    }

    // ==========================================
    // UI UPDATE FUNCTION FOR SERVICE CARDS
    // ==========================================
    function updateUI(id, status, latency, statusCode) {
        const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        serviceStatus[id] = { status, latency, statusCode, timestamp: now };

        const card = document.getElementById('card-' + id);
        const badge = document.getElementById('badge-' + id);
        const dot = document.getElementById('dot-' + id);
        const text = document.getElementById('text-' + id);
        const latText = document.getElementById('latency-' + id);
        const bar = document.getElementById('bar-' + id);
        const timeText = document.getElementById('time-' + id);
        const spin = document.getElementById('spin-' + id);
        const httpText = document.getElementById('httpstatus-' + id);

        if (spin) spin.classList.remove('fa-spin');
        if (timeText) timeText.textContent = 'Dicek: ' + now;

        if (status === 'online') {
            const isSlow = latency > 300;

            if (card) card.className = 'endpoint-card ' + (isSlow ? 'state-warning' : 'state-online') + ' p-3 h-100';
            if (badge) badge.className = 'service-badge ' + (isSlow ? 'badge-warning' : 'badge-online');
            if (dot) dot.className = 'pulse-radar-dot ' + (isSlow ? 'warning' : 'online');
            if (text) text.textContent = isSlow ? 'High Latency' : 'Connected';
            
            if (latText) {
                latText.innerHTML = `${latency} <small class="text-muted" style="font-size: 0.75rem;">ms</small>`;
                if (isSlow) latText.className = 'fw-bold fs-6 telemetry-mono text-warning';
                else latText.className = 'fw-bold fs-6 telemetry-mono text-success';
            }

            if (bar) {
                let pct = Math.min(100, Math.max(5, (latency / 400) * 100));
                bar.style.width = pct + '%';
                if (latency < 100) bar.style.background = '#10b981';
                else if (latency <= 300) bar.style.background = '#f59e0b';
                else bar.style.background = '#ef4444';
            }

            if (httpText) {
                httpText.innerHTML = `<span class="text-success"><i class="fas fa-check"></i> ${statusCode ? statusCode : '200 OK'}</span>`;
            }
        } else if (status === 'offline') {
            if (card) card.className = 'endpoint-card state-offline p-3 h-100';
            if (badge) badge.className = 'service-badge badge-offline';
            if (dot) dot.className = 'pulse-radar-dot offline';
            if (text) text.textContent = 'Disconnected';

            if (latText) {
                latText.textContent = 'Timeout';
                latText.className = 'fw-bold fs-6 telemetry-mono text-danger';
            }

            if (bar) {
                bar.style.width = '100%';
                bar.style.background = '#ef4444';
            }

            if (httpText) {
                httpText.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle"></i> ${statusCode ? 'ERR ' + statusCode : 'Timeout'}</span>`;
            }

            playAlertChime();
        } else if (status === 'checking') {
            if (card) card.className = 'endpoint-card state-checking p-3 h-100';
            if (badge) badge.className = 'service-badge badge-checking';
            if (dot) dot.className = 'pulse-radar-dot loading';
            if (text) text.textContent = 'Checking...';
            if (latText) {
                latText.textContent = '...';
                latText.className = 'fw-bold fs-6 telemetry-mono text-primary';
            }
            if (spin) spin.classList.add('fa-spin');
            if (bar) bar.style.width = '0%';
        }

        recalculateGlobalStats();
        updateLeaderboard();
    }

    // ==========================================
    // STATS & LEADERBOARD RECALCULATION
    // ==========================================
    function recalculateGlobalStats() {
        let bpjsOnline = 0, bpjsTotal = 0;
        let localOnline = 0, localTotal = 0;
        let netOnline = 0, netTotal = 0;
        let totalLatency = 0, validLatencyCount = 0;
        let hasOffline = false;
        let hasSlow = false;
        let hasAnyTested = false;

        services.forEach(s => {
            const st = serviceStatus[s.id];
            const cat = s.category || 'other';

            if (st.status === 'online' || st.status === 'offline') {
                hasAnyTested = true;
            }

            if (cat === 'bpjs') {
                bpjsTotal++;
                if (st.status === 'online') bpjsOnline++;
            } else if (cat === 'local') {
                localTotal++;
                if (st.status === 'online') localOnline++;
            } else if (cat === 'network') {
                netTotal++;
                if (st.status === 'online') netOnline++;
            }

            if (st.status === 'offline') hasOffline = true;
            if (st.status === 'online' && st.latency > 300) hasSlow = true;

            if (st.status === 'online' && st.latency > 0) {
                totalLatency += st.latency;
                validLatencyCount++;
            }
        });

        if (!hasAnyTested) return;

        // Set Counts
        const bpjsEl = document.getElementById('statBpjsCount');
        if (bpjsEl) bpjsEl.textContent = `${bpjsOnline} / ${bpjsTotal}`;
        const bpjsPct = document.getElementById('statBpjsPercent');
        if (bpjsPct) bpjsPct.textContent = bpjsTotal > 0 ? Math.round((bpjsOnline / bpjsTotal) * 100) + '%' : '100%';

        const localEl = document.getElementById('statLocalCount');
        if (localEl) localEl.textContent = `${localOnline} / ${localTotal}`;
        const localPct = document.getElementById('statLocalPercent');
        if (localPct) localPct.textContent = localTotal > 0 ? Math.round((localOnline / localTotal) * 100) + '%' : '100%';

        const netEl = document.getElementById('statNetworkCount');
        if (netEl) netEl.textContent = `${netOnline} / ${netTotal}`;
        const netPct = document.getElementById('statNetworkPercent');
        if (netPct) netPct.textContent = netTotal > 0 ? Math.round((netOnline / netTotal) * 100) + '%' : '100%';

        const avg = validLatencyCount > 0 ? Math.round(totalLatency / validLatencyCount) : 0;
        const avgEl = document.getElementById('statAvgLatency');
        if (avgEl) avgEl.textContent = avg > 0 ? `${avg} ms` : '- ms';

        const qLabel = document.getElementById('statQualityLabel');
        if (qLabel) {
            if (avg > 0 && avg < 80) {
                qLabel.textContent = 'Sangat Cepat';
                qLabel.className = 'fw-bold text-success';
            } else if (avg >= 80 && avg <= 250) {
                qLabel.textContent = 'Optimal / Normal';
                qLabel.className = 'fw-bold text-success';
            } else if (avg > 250) {
                qLabel.textContent = 'Latensi Tinggi';
                qLabel.className = 'fw-bold text-warning';
            }
        }

        // Master Status Banner
        const masterBadge = document.getElementById('masterStatusBadge');
        const masterDot = document.getElementById('masterRadarDot');
        const masterText = document.getElementById('masterStatusText');

        if (masterBadge && masterDot && masterText) {
            if (hasOffline) {
                masterBadge.style.background = 'rgba(239, 68, 68, 0.15)';
                masterBadge.style.color = '#dc2626';
                masterDot.className = 'pulse-radar-dot offline me-1';
                masterText.textContent = 'GANGGUAN TERDETEKSI (LAYANAN TERPUTUS)';
            } else if (hasSlow) {
                masterBadge.style.background = 'rgba(245, 158, 11, 0.15)';
                masterBadge.style.color = '#d97706';
                masterDot.className = 'pulse-radar-dot warning me-1';
                masterText.textContent = 'PERINGATAN (LATENSI JARINGAN TINGGI)';
            } else {
                masterBadge.style.background = 'rgba(16, 185, 129, 0.15)';
                masterBadge.style.color = '#059669';
                masterDot.className = 'pulse-radar-dot online me-1';
                masterText.textContent = 'SEMUA SISTEM STABIL & OPERASIONAL';
            }
        }
    }

    function updateLeaderboard() {
        const container = document.getElementById('leaderboardList');
        if (!container) return;

        const tested = services.filter(s => serviceStatus[s.id].status === 'online' || serviceStatus[s.id].status === 'offline');
        if (tested.length === 0) return;

        const sorted = [...tested].sort((a, b) => {
            const stA = serviceStatus[a.id];
            const stB = serviceStatus[b.id];
            const latA = (stA && stA.status === 'online') ? stA.latency : 99999;
            const latB = (stB && stB.status === 'online') ? stB.latency : 99999;
            return latA - latB;
        });

        let html = '';
        sorted.slice(0, 6).forEach((s, idx) => {
            const st = serviceStatus[s.id];
            const isOnline = st && st.status === 'online';
            const lat = isOnline ? `${st.latency} ms` : (st.status === 'offline' ? 'Offline' : 'Checking');
            const clr = isOnline ? (st.latency < 100 ? 'text-success' : (st.latency < 300 ? 'text-warning' : 'text-danger')) : 'text-danger';

            html += `
                <div class="rank-item mb-1">
                    <div class="d-flex align-items-center gap-2 text-truncate me-2">
                        <span class="badge rounded-circle bg-light text-muted small" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.65rem;">
                            ${idx + 1}
                        </span>
                        <span class="fw-semibold small text-truncate" title="${s.name}" style="max-width: 140px;">${s.name}</span>
                    </div>
                    <span class="telemetry-mono fw-bold small ${clr}">${lat}</span>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    // ==========================================
    // PING CHECKING ENGINE (PARALLEL & FAST)
    // ==========================================
    async function checkSingle(id, name, url) {
        if (!isScreenActive) return;
        updateUI(id, 'checking', 0, null);

        try {
            const response = await fetch("{{ url('/bpjs/monitoring-signal/check') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id: id, name: name, url: url })
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);
            const data = await response.json();

            if (data.status === 'online') {
                updateUI(id, 'online', data.latency, data.status_code);
            } else {
                updateUI(id, 'offline', 0, data.status_code);
            }
        } catch (error) {
            updateUI(id, 'offline', 0, null);
        }
    }

    // Parallel Batch Checker: Cepat tanpa blocking
    async function checkAll() {
        if (!isScreenActive) return;
        if (isCheckingAll) return;
        isCheckingAll = true;

        const btn = document.getElementById('btnCheckAll');
        const icon = document.getElementById('iconCheckAll');
        if (icon) {
            icon.className = 'fas fa-spinner fa-spin';
        }
        if (btn) btn.disabled = true;

        const batchSize = 3;
        for (let i = 0; i < services.length; i += batchSize) {
            const batch = services.slice(i, i + batchSize);
            await Promise.allSettled(
                batch.map(s => checkSingle(s.id, s.name, s.url))
            );
            if (i + batchSize < services.length) {
                await new Promise(r => setTimeout(r, 100));
            }
        }

        pushDataToChart();

        if (icon) {
            icon.className = 'fas fa-check-circle text-success';
            setTimeout(() => { icon.className = 'fas fa-play'; }, 2000);
        }
        if (btn) btn.disabled = false;
        isCheckingAll = false;

        if (refreshSeconds > 0) {
            currentCountdown = refreshSeconds;
        }
    }

    // ==========================================
    // AUTO REFRESH COUNTDOWN TIMER (DEFAULT OFF)
    // ==========================================
    function setAutoRefreshInterval(sec) {
        refreshSeconds = sec;
        currentCountdown = sec;
        
        const pill = document.getElementById('autoRefreshPill');
        const label = document.getElementById('intervalLabel');

        if (countdownInterval) clearInterval(countdownInterval);

        if (sec === 0) {
            if (pill) {
                pill.innerHTML = '<i class="fas fa-hand-pointer"></i> Mode: <span>Manual</span>';
            }
            if (label) label.textContent = 'Mode: Manual';
            return;
        }

        if (pill) {
            pill.innerHTML = `<i class="fas fa-clock"></i> Auto: <span id="refreshCountdown">${sec}s</span>`;
        }
        if (label) label.textContent = `Auto: ${sec}s`;

        countdownInterval = setInterval(() => {
            currentCountdown--;
            const countEl = document.getElementById('refreshCountdown');
            if (countEl) countEl.textContent = `${currentCountdown}s`;

            if (currentCountdown <= 0) {
                currentCountdown = refreshSeconds;
                checkAll();
            }
        }, 1000);
    }

    // ==========================================
    // CLIENT FILTERING & SEARCH
    // ==========================================
    function filterCategory(category, btnElement) {
        document.querySelectorAll('.filter-tab-pill').forEach(el => el.classList.remove('active'));
        if (btnElement) btnElement.classList.add('active');

        const items = document.querySelectorAll('.service-item');
        items.forEach(item => {
            const itemCat = item.getAttribute('data-category');
            if (category === 'all' || itemCat === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function filterServices() {
        const query = document.getElementById('searchInput').value.toLowerCase().trim();
        const items = document.querySelectorAll('.service-item');

        items.forEach(item => {
            const name = item.getAttribute('data-name');
            const url = item.getAttribute('data-url');
            if (!query || name.includes(query) || url.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function copyToClipboard(text, elem) {
        if (!navigator.clipboard) return;
        navigator.clipboard.writeText(text).then(() => {
            const orig = elem.className;
            elem.className = 'fas fa-check text-success';
            setTimeout(() => { elem.className = orig; }, 1500);
        });
    }

    function toggleFullscreen() {
        const root = document.getElementById('telemetryRoot');
        if (!document.fullscreenElement) {
            root.requestFullscreen().catch(err => console.log(err));
            document.getElementById('iconFullscreen').className = 'fas fa-compress';
        } else {
            document.exitFullscreen();
            document.getElementById('iconFullscreen').className = 'fas fa-expand';
        }
    }

    // ==========================================
    // MODAL LOG GANGGUAN & PDF EXPORT
    // ==========================================
    function bukaModalGangguan() {
        const filterInputAwal = document.getElementById('filterTanggalAwal');
        if (filterInputAwal && !filterInputAwal.value) {
            const today = new Date().toISOString().split('T')[0];
            filterInputAwal.value = today;
            document.getElementById('filterTanggalAkhir').value = today;
        }

        fetchLogs();

        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#modalRiwayatGangguan').modal('show');
        } else {
            const modal = document.getElementById('modalRiwayatGangguan');
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.classList.add('modal-open');
            }
        }
    }

    function tutupModal() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#modalRiwayatGangguan').modal('hide');
        } else {
            const modal = document.getElementById('modalRiwayatGangguan');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        }
    }

    function resetLogFilter() {
        document.querySelectorAll('.filter-layanan-cb').forEach(cb => cb.checked = false);
        document.getElementById('filterTanggalAwal').value = '';
        document.getElementById('filterTanggalAkhir').value = '';
        fetchLogs();
    }

    async function fetchLogs() {
        const tbody = document.getElementById('tbodyRiwayatGangguan');
        if (!tbody) return;

        const filterTanggalAwal = document.getElementById('filterTanggalAwal').value;
        const filterTanggalAkhir = document.getElementById('filterTanggalAkhir').value;

        let filterLayanan = [];
        document.querySelectorAll('.filter-layanan-cb:checked').forEach(cb => {
            filterLayanan.push(cb.value);
        });

        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Memuat data log...</td></tr>';

        try {
            let url = "{{ url('/bpjs/monitoring-signal/logs') }}";
            let params = new URLSearchParams();
            if (filterTanggalAwal) params.append('tanggal_awal', filterTanggalAwal);
            if (filterTanggalAkhir) params.append('tanggal_akhir', filterTanggalAkhir);
            filterLayanan.forEach(v => params.append('layanan[]', v));

            if (params.toString()) url += '?' + params.toString();

            const res = await fetch(url);
            const logs = await res.json();

            if (logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-check-circle text-success me-1"></i> Tidak ditemukan catatan gangguan pada periode ini.</td></tr>';
                return;
            }

            let html = '';
            logs.forEach((log, idx) => {
                let normalText = log.waktu_normal ? `<span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i> ${log.waktu_normal}</span>` : '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Belum Pulih</span>';

                let durasi = '-';
                if (log.waktu_gangguan && log.waktu_normal) {
                    let diffMs = new Date(log.waktu_normal) - new Date(log.waktu_gangguan);
                    let diffMins = Math.round(diffMs / 60000);
                    durasi = diffMins > 0 ? `${diffMins} Menit` : '< 1 Menit';
                }

                html += `
                    <tr>
                        <td class="text-center text-muted">${idx + 1}</td>
                        <td><strong class="text-danger">${log.waktu_gangguan}</strong></td>
                        <td>${normalText}</td>
                        <td>
                            <span class="fw-semibold">${log.service_name}</span>
                            <br><small class="text-muted telemetry-mono">${log.url}</small>
                        </td>
                        <td><span class="badge" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">${log.keterangan || 'Disconnected'}</span></td>
                        <td class="text-center fw-bold text-muted">${durasi}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data riwayat gangguan.</td></tr>';
        }
    }

    function cetakPdf() {
        const tglAwal = document.getElementById('filterTanggalAwal').value;
        const tglAkhir = document.getElementById('filterTanggalAkhir').value;
        let layanan = [];
        document.querySelectorAll('.filter-layanan-cb:checked').forEach(cb => {
            layanan.push(cb.value);
        });

        let url = "{{ url('/bpjs/monitoring-signal/pdf') }}";
        let params = new URLSearchParams();
        if (tglAwal) params.append('tanggal_awal', tglAwal);
        if (tglAkhir) params.append('tanggal_akhir', tglAkhir);
        layanan.forEach(v => params.append('layanan[]', v));

        if (params.toString()) url += '?' + params.toString();
        window.open(url, '_blank');
    }

    // ==========================================
    // INITIAL BOOTSTRAP (100% MANUAL, ZERO AUTO-PING)
    // ==========================================
    $(function() {
        // Terapkan status power layar (ON/OFF) dari localStorage
        applyScreenPowerState();

        // Mode Manual: Pengecekan otomatis dinonaktifkan secara default agar aplikasi tetap sangat cepat
        setAutoRefreshInterval(0);
    });

</script>
@endpush
