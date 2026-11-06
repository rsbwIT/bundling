<div wire:poll.3s="loadData" class="tv-display-wrapper">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=JetBrains+Mono:wght@700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #f1f5f9;
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(2, 132, 199, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 15%, rgba(99, 102, 241, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 50% 85%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            color: #0f172a;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .tv-display-wrapper {
            padding-top: 96px;
            padding-bottom: 56px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ================= HEADER BAR ================= */
        .tv-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 96px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1.5px solid #e2e8f0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
        }

        .tv-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 320px;
        }

        .tv-header-logo {
            height: 64px;
            object-fit: contain;
        }

        .tv-header-instansi h2 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .tv-header-instansi p {
            font-size: 0.8rem;
            font-weight: 600;
            color: #0284c7;
            margin: 2px 0 0;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .tv-header-center {
            text-align: center;
            flex: 1;
        }

        .tv-header-title {
            font-size: 1.85rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            margin: 0;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .tv-header-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 2px;
        }

        .tv-header-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 20px;
            min-width: 320px;
        }

        .tv-clock-box {
            text-align: right;
            padding-right: 16px;
            border-right: 1.5px solid #e2e8f0;
        }

        .tv-clock-time {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }

        .tv-clock-date {
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            margin-top: 4px;
        }

        .tv-bpjs-logo {
            height: 38px;
            object-fit: contain;
        }

        /* ================= MAIN CONTAINER ================= */
        .tv-content-container {
            flex: 1;
            padding: 24px;
            max-width: 1800px;
            margin: 0 auto;
            width: 100%;
        }

        .tv-grid-columns {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            align-items: stretch;
        }

        @media (max-width: 991px) {
            .tv-grid-columns {
                grid-template-columns: 1fr;
            }
        }

        /* ================= LOKET CARD ================= */
        .tv-loket-card {
            background: #ffffff;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* Distinct Color Accents */
        .tv-loket-card.non-racik {
            border-top: 6px solid #0284c7;
        }

        .tv-loket-card.racikan {
            border-top: 6px solid #6366f1;
        }

        /* Card Header */
        .tv-card-header {
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border-bottom: 1.5px solid #f1f5f9;
        }

        .tv-card-category {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .category-badge-pill {
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 12px;
            border-radius: 999px;
        }

        .non-racik .category-badge-pill {
            background: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }

        .racikan .category-badge-pill {
            background: #e0e7ff;
            color: #4f46e5;
            border: 1px solid #c7d2fe;
        }

        .category-name {
            font-size: 1.45rem;
            font-weight: 900;
            letter-spacing: -0.3px;
            margin: 0;
            color: #0f172a;
        }

        .tv-card-target-time {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #475569;
            background: #ffffff;
            padding: 5px 12px;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        /* Card Body (Main Hero Callout) */
        .tv-main-callout {
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        /* Active hero banner box */
        .tv-hero-box {
            width: 100%;
            border-radius: 20px;
            padding: 24px 20px;
            color: #ffffff;
            position: relative;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Hero Themes */
        .tv-hero-box.dipanggil {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 12px 32px rgba(5, 150, 105, 0.25);
            animation: pulse-green 2s infinite;
        }

        .tv-hero-box.validasi {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            box-shadow: 0 12px 32px rgba(2, 132, 199, 0.25);
            animation: pulse-blue 2.5s infinite;
        }

        .tv-hero-box.menunggu {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            box-shadow: 0 12px 32px rgba(217, 119, 6, 0.25);
        }

        .tv-hero-box.empty {
            background: #f8fafc;
            color: #64748b;
            border: 2px dashed #cbd5e1;
            box-shadow: none;
            padding: 40px 20px;
        }

        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
            70% { box-shadow: 0 0 0 18px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @keyframes pulse-blue {
            0% { box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.5); }
            70% { box-shadow: 0 0 0 18px rgba(2, 132, 199, 0); }
            100% { box-shadow: 0 0 0 0 rgba(2, 132, 199, 0); }
        }

        /* Hero Elements */
        .hero-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 4px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            margin-bottom: 12px;
        }

        .hero-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 5.4rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: 1px;
            margin: 4px 0 10px;
            text-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .hero-patient-name {
            font-size: 2.1rem;
            font-weight: 800;
            letter-spacing: -0.3px;
            margin: 0 0 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 90%;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        /* Hero Times Ribbon */
        .hero-meta-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            background: rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(8px);
            padding: 6px 18px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.95rem;
            font-weight: 600;
        }

        .hero-meta-row span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Hero Countdown HUD Box */
        .hero-countdown-hud {
            margin-top: 18px;
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            border: 2px solid #ffffff;
            transition: all 0.3s ease;
        }

        .hud-left {
            text-align: left;
        }

        .hud-caption {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }

        .hud-subcaption {
            font-size: 0.76rem;
            font-weight: 700;
            color: #0f172a;
        }

        .hud-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hud-timer-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 2.1rem;
            font-weight: 900;
            letter-spacing: 1px;
            line-height: 1;
            color: #0284c7;
        }

        /* Dynamic HUD States */
        .hero-countdown-hud.normal .hud-timer-val {
            color: #0284c7;
        }

        .hero-countdown-hud.warning {
            border-color: #f59e0b;
            background: #fffbeb;
            animation: pulse-amber 1.5s infinite;
        }

        .hero-countdown-hud.warning .hud-timer-val {
            color: #b45309;
        }

        .hero-countdown-hud.expired {
            border-color: #ef4444;
            background: #fef2f2;
            animation: pulse-red 1s infinite;
        }

        .hero-countdown-hud.expired .hud-timer-val {
            color: #b91c1c;
        }

        .hero-countdown-hud.unvalidated {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .hero-countdown-hud.unvalidated .hud-timer-val {
            font-size: 1.25rem;
            color: #64748b;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
        }

        @keyframes pulse-amber {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
        }

        @keyframes pulse-red {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
            50% { box-shadow: 0 0 0 12px rgba(239, 68, 68, 0); }
        }

        /* Banner Message Bottom */
        .tv-card-note {
            margin-top: 14px;
            font-size: 1.05rem;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            letter-spacing: 0.2px;
            transition: all 0.3s ease;
        }

        .tv-card-note.note-dipanggil {
            background: #ecfdf5;
            color: #047857;
            border: 1.5px solid #a7f3d0;
            animation: pulse-note-green 2s infinite;
        }

        .tv-card-note.note-validasi {
            background: linear-gradient(135deg, #eff6ff 0%, #e0f2fe 100%);
            color: #0369a1;
            border: 1.5px solid #bae6fd;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.12);
        }

        .tv-card-note.note-menunggu {
            background: #fffbeb;
            color: #b45309;
            border: 1.5px solid #fde68a;
        }

        @keyframes pulse-note-green {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        /* ================= NEXT QUEUE SECTION ================= */
        .tv-next-section {
            margin-top: auto;
            background: #f8fafc;
            border-top: 1.5px solid #f1f5f9;
            padding: 20px;
        }

        .tv-next-title-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .tv-next-title {
            font-size: 0.88rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #475569;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .tv-next-count {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 3px 10px;
            background: #e2e8f0;
            border-radius: 999px;
            color: #334155;
        }

        .tv-next-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .tv-next-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease;
        }

        .tv-next-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .tv-next-item-top {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 4px;
        }

        .tv-next-num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
        }

        .tv-next-name {
            font-size: 0.86rem;
            font-weight: 700;
            color: #334155;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 8px;
        }

        .tv-next-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 6px;
            border-top: 1px dashed #f1f5f9;
            font-size: 0.76rem;
        }

        .tv-next-ambil {
            color: #64748b;
            font-weight: 600;
        }

        /* Next item countdown pill */
        .tv-next-timer-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.74rem;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
        }

        .tv-next-timer-pill.normal {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .tv-next-timer-pill.warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .tv-next-timer-pill.expired {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .tv-next-timer-pill.unvalidated {
            background: #f1f5f9;
            color: #64748b;
            border: 1px dashed #cbd5e1;
        }

        .tv-next-empty {
            grid-column: span 2;
            text-align: center;
            padding: 24px 10px;
            color: #94a3b8;
            font-size: 0.88rem;
            font-weight: 600;
            font-style: italic;
        }

        /* ================= FOOTER RUNNING TICKER ================= */
        .tv-footer-ticker {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 52px;
            background: #0f172a;
            color: #f8fafc;
            display: flex;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .ticker-label-badge {
            background: #0284c7;
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            height: 100%;
            display: flex;
            align-items: center;
            padding: 0 18px;
            white-space: nowrap;
            z-index: 2;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
        }

        .ticker-track {
            flex: 1;
            overflow: hidden;
            white-space: nowrap;
        }

        .ticker-content {
            display: inline-block;
            padding-left: 100%;
            animation: marqueeTicker 50s linear infinite;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        @keyframes marqueeTicker {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-100%); }
        }
    </style>

    <!-- ================= 1. HEADER BAR TV ================= -->
    <header class="tv-header">
        <div class="tv-header-left">
            <img src="{{ asset('img/bw2.png') }}" alt="Logo RS" class="tv-header-logo">
            <div class="tv-header-instansi">
                <h2>RS. BUMI WARAS</h2>
                <p><i class="fas fa-pills mr-1"></i> Instalasi Farmasi Terpadu</p>
            </div>
        </div>

        <div class="tv-header-center">
            <h1 class="tv-header-title">
                <i class="fas fa-tv text-primary"></i> ANTREAN FARMASI
            </h1>
            <div class="tv-header-subtitle">
                Pantauan Pelayanan Resep Obat Pasien Rawat Jalan
            </div>
        </div>

        <div class="tv-header-right">
            <div class="tv-clock-box">
                <div class="tv-clock-time" id="digitalClockTime">--:--:--</div>
                <div class="tv-clock-date" id="digitalClockDate">Memuat tanggal...</div>
            </div>
            <img src="{{ asset('img/bpjs.png') }}" alt="Logo BPJS" class="tv-bpjs-logo">
        </div>
    </header>

    <!-- ================= 2. CONTENT CONTAINER ================= -->
    <main class="tv-content-container">
        @php
            $kelompok = collect(['NON RACIK', 'RACIKAN']);
        @endphp

        <div class="tv-grid-columns">
            @foreach($kelompok as $jenis)
                @php
                    $isRacikan = ($jenis === 'RACIKAN');
                    $cardClass = $isRacikan ? 'racikan' : 'non-racik';
                    $targetMenit = $isRacikan ? 60 : 45;
                    $daftar = $antrians->where('keterangan', $jenis);

                    // Prioritas: Sedang DIPANGGIL > Sedang VALIDASI > MENUNGGU
                    $dipanggil = $daftar->where('status', 'DIPANGGIL')->first();

                    if ($dipanggil) {
                        $utama = $dipanggil;
                        $status_utama = 'DIPANGGIL';
                        $selanjutnya = $daftar->whereIn('status', ['VALIDASI', 'MENUNGGU'])->values();
                    } else {
                        // Ambil yang paling depan (tervalidasi didahulukan)
                        $utama = $daftar->whereIn('status', ['VALIDASI', 'MENUNGGU'])->first();
                        $status_utama = $utama ? $utama->status : null;
                        $selanjutnya = $utama ? $daftar->whereIn('status', ['VALIDASI', 'MENUNGGU'])->where('nomor_antrian', '!=', $utama->nomor_antrian)->values() : collect();
                    }
                @endphp

                <div class="tv-loket-card {{ $cardClass }}">
                    <!-- Card Top Header -->
                    <div class="tv-card-header">
                        <div class="tv-card-category">
                            <span class="category-badge-pill">
                                <i class="fas {{ $isRacikan ? 'fa-mortar-pestle' : 'fa-capsules' }}"></i>
                                {{ $jenis }}
                            </span>
                            <h3 class="category-name">LOKET {{ $jenis }}</h3>
                        </div>
                        <div class="tv-card-target-time">
                            <i class="far fa-clock text-primary"></i>
                            <span>Target: <strong>{{ $targetMenit }} Menit</strong></span>
                        </div>
                    </div>

                    <!-- Main Hero Callout -->
                    <div class="tv-main-callout">
                        @if($status_utama == 'DIPANGGIL')
                            <div class="tv-hero-box dipanggil">
                                <div class="hero-status-pill">
                                    <i class="fas fa-volume-up"></i> DIPANGGIL KE LOKET
                                </div>
                                <div class="hero-number">{{ $utama->nomor_antrian }}</div>
                                <div class="hero-patient-name">{{ strtoupper($utama->nama_pasien) }}</div>
                                
                                <div class="hero-meta-row">
                                    <span><i class="far fa-clock"></i> Ambil: <strong>{{ $utama->jam_ambil }}</strong></span>
                                    <span>•</span>
                                    <span><i class="fas fa-hourglass-start"></i> Tunggu: <strong>{{ $utama->lama_tunggu }}</strong></span>
                                </div>

                                @if($utama->sudah_validasi)
                                    <div class="hero-countdown-hud {{ $utama->countdown_status }}">
                                        <div class="hud-left">
                                            <div class="hud-caption">SISA WAKTU PELAYANAN</div>
                                            <div class="hud-subcaption">Target: {{ $targetMenit }} Menit</div>
                                        </div>
                                        <div class="hud-right">
                                            <i class="fas fa-stopwatch text-muted"></i>
                                            <span class="hud-timer-val cd-timer-val" data-target="{{ $utama->target_timestamp }}">{{ $utama->sisa_waktu }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="tv-card-note note-dipanggil">
                                <i class="fas fa-bullhorn fa-beat"></i> Pasien dipersilakan menuju ke Loket {{ $jenis }}
                            </div>

                        @elseif($status_utama == 'VALIDASI')
                            <div class="tv-hero-box validasi">
                                <div class="hero-status-pill">
                                    <i class="fas fa-pills mr-1"></i> OBAT SEDANG DISIAPKAN
                                </div>
                                <div class="hero-number">{{ $utama->nomor_antrian }}</div>
                                <div class="hero-patient-name">{{ strtoupper($utama->nama_pasien) }}</div>
                                
                                <div class="hero-meta-row">
                                    <span><i class="far fa-clock"></i> Ambil: <strong>{{ $utama->jam_ambil }}</strong></span>
                                    <span>•</span>
                                    <span><i class="fas fa-check-circle"></i> Valid: <strong>{{ $utama->jam_validasi }}</strong></span>
                                </div>

                                <div class="hero-countdown-hud {{ $utama->countdown_status }}">
                                    <div class="hud-left">
                                        <div class="hud-caption">HITUNG MUNDUR PENYIAPAN OBAT</div>
                                        <div class="hud-subcaption">Estimasi Standar Pelayanan</div>
                                    </div>
                                    <div class="hud-right">
                                        <i class="fas fa-hourglass-half text-primary"></i>
                                        <span class="hud-timer-val cd-timer-val" data-target="{{ $utama->target_timestamp }}">{{ $utama->sisa_waktu }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tv-card-note note-validasi">
                                <i class="fas fa-mortar-pestle fa-bounce text-primary"></i>
                                <span>Obat sedang disiapkan, mohon ditunggu</span>
                            </div>

                        @elseif($status_utama == 'MENUNGGU')
                            <div class="tv-hero-box menunggu">
                                <div class="hero-status-pill">
                                    <i class="fas fa-clipboard-check"></i> MENUNGGU VALIDASI
                                </div>
                                <div class="hero-number">{{ $utama->nomor_antrian }}</div>
                                <div class="hero-patient-name">{{ strtoupper($utama->nama_pasien) }}</div>
                                
                                <div class="hero-meta-row">
                                    <span><i class="far fa-clock"></i> Ambil: <strong>{{ $utama->jam_ambil }}</strong></span>
                                    <span>•</span>
                                    <span><i class="fas fa-clock"></i> Tunggu: <strong>{{ $utama->lama_tunggu }}</strong></span>
                                </div>

                                <div class="hero-countdown-hud unvalidated">
                                    <div class="hud-left">
                                        <div class="hud-caption">STATUS VALIDASI RESEP</div>
                                        <div class="hud-subcaption">Menunggu pemeriksaan berkas</div>
                                    </div>
                                    <div class="hud-right">
                                        <span class="hud-timer-val">Dalam Antrean</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tv-card-note note-menunggu">
                                <i class="fas fa-hourglass-start"></i> Berkas resep sedang diverifikasi petugas farmasi...
                            </div>

                        @else
                            <div class="tv-hero-box empty">
                                <i class="fas fa-clipboard-list fa-3x mb-3 text-muted"></i>
                                <h4 class="font-weight-bold">Tidak Ada Antrean Saat Ini</h4>
                                <p class="mb-0 text-muted">Seluruh resep pada loket ini telah selesai dilayani.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Next Queue Grid (Antrian Selanjutnya) -->
                    @if($status_utama)
                        <div class="tv-next-section">
                            <div class="tv-next-title-bar">
                                <h4 class="tv-next-title">
                                    <i class="fas fa-list-ol"></i> Antrean Selanjutnya
                                </h4>
                                <span class="tv-next-count">{{ $selanjutnya->count() }} Menunggu</span>
                            </div>

                            <div class="tv-next-grid">
                                @forelse($selanjutnya->take(4) as $next)
                                    <div class="tv-next-item">
                                        <div class="tv-next-item-top">
                                            <span class="tv-next-num">{{ $next->nomor_antrian }}</span>
                                            @if($next->sudah_validasi)
                                                <span class="tv-next-timer-pill {{ $next->countdown_status }}">
                                                    ⏱️ <strong class="cd-timer-val" data-target="{{ $next->target_timestamp }}">{{ $next->sisa_waktu_singkat }}</strong>
                                                </span>
                                            @else
                                                <span class="tv-next-timer-pill unvalidated">
                                                    ⏳ Belum Valid
                                                </span>
                                            @endif
                                        </div>
                                        <div class="tv-next-name" title="{{ $next->nama_pasien }}">{{ strtoupper($next->nama_pasien) }}</div>
                                        <div class="tv-next-meta">
                                            <span class="tv-next-ambil"><i class="far fa-clock"></i> {{ $next->jam_ambil }}</span>
                                            <span class="text-muted">⏱️ {{ $next->lama_tunggu_singkat }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="tv-next-empty">
                                        <i class="fas fa-check-circle mr-1 text-success"></i> Tidak ada antrean lanjutan dalam daftar tunggu
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </main>

    <!-- ================= 3. FOOTER RUNNING TICKER ================= -->
    <footer class="tv-footer-ticker" wire:ignore>
        <div class="ticker-label-badge">
            <i class="fas fa-info-circle mr-1"></i> INFORMASI
        </div>
        <div class="ticker-track">
            <div class="ticker-content">
                Selamat datang di Rumah Sakit Bumi Waras &bull; Layanan Cepat, Nyaman, dan Ramah &bull; 
                ⏱️ Target Waktu Pelayanan Resep: <strong>Non-Racik Maksimal 45 Menit</strong> &bull; <strong>Racikan Maksimal 60 Menit</strong> &bull; 
                📋 <strong>5 JANJI LAYANAN JKN:</strong> 1. Tanpa Fotokopi Dokumen (Cukup KTP/KIS Digital) &bull; 2. Pelayanan Sesuai Jadwal Dokter &bull; 3. Obat Sesuai Formularium Nasional &bull; 4. Tanpa Iur Biaya Tambahan &bull; 5. Pelayanan Ramah & Tanpa Diskriminasi &bull; 
                📞 Kontak Informasi & Pengaduan BPJS PANDAWA: 0811 8 165 165 | Care Center: 165
            </div>
        </div>
    </footer>

    <!-- ================= 4. JAVASCRIPT CLOCK & COUNTDOWN TICKER ================= -->
    <script>
        (function() {
            // A. Digital Clock Header
            function updateClock() {
                const now = new Date();
                const clockEl = document.getElementById('digitalClockTime');
                const dateEl = document.getElementById('digitalClockDate');

                if (clockEl) {
                    const h = String(now.getHours()).padStart(2, '0');
                    const m = String(now.getMinutes()).padStart(2, '0');
                    const s = String(now.getSeconds()).padStart(2, '0');
                    clockEl.textContent = `${h}:${m}:${s}`;
                }

                if (dateEl) {
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    const day = days[now.getDay()];
                    const date = now.getDate();
                    const month = months[now.getMonth()];
                    const year = now.getFullYear();
                    dateEl.textContent = `${day}, ${date} ${month} ${year}`;
                }
            }

            setInterval(updateClock, 1000);
            updateClock();

            // B. Countdown Realtime Ticker
            function updateTimers() {
                const now = Date.now();
                document.querySelectorAll('.cd-timer-val[data-target]').forEach(el => {
                    const targetAttr = el.getAttribute('data-target');
                    if (!targetAttr) return;
                    const target = parseInt(targetAttr);
                    if (!target || isNaN(target)) return;

                    const diffSeconds = Math.floor((target - now) / 1000);
                    const hud = el.closest('.hero-countdown-hud');
                    const pill = el.closest('.tv-next-timer-pill');

                    if (diffSeconds > 0) {
                        const m = Math.floor(diffSeconds / 60);
                        const s = diffSeconds % 60;
                        const formatted = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                        el.textContent = formatted;

                        // HUD container update
                        if (hud) {
                            hud.classList.remove('expired');
                            if (m <= 10) {
                                hud.classList.remove('normal');
                                hud.classList.add('warning');
                            } else {
                                hud.classList.remove('warning');
                                hud.classList.add('normal');
                            }
                        }

                        // Next queue pill update
                        if (pill) {
                            pill.classList.remove('expired');
                            if (m <= 10) {
                                pill.classList.remove('normal');
                                pill.classList.add('warning');
                            } else {
                                pill.classList.remove('warning');
                                pill.classList.add('normal');
                            }
                        }
                    } else {
                        el.textContent = '00:00';
                        if (hud) {
                            hud.classList.remove('normal', 'warning');
                            hud.classList.add('expired');
                        }
                        if (pill) {
                            pill.classList.remove('normal', 'warning');
                            pill.classList.add('expired');
                        }
                    }
                });
            }

            setInterval(updateTimers, 1000);
            updateTimers();

            // C. Livewire Hook: Re-run timers whenever Livewire refreshes DOM
            document.addEventListener('livewire:load', function() {
                if (window.Livewire) {
                    window.Livewire.hook('message.processed', function() {
                        updateTimers();
                    });
                }
            });
        })();
    </script>
</div>
