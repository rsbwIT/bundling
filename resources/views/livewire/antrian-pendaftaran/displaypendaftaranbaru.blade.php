<div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #e9f7ef, #d4edda);
            color: #000;
            font-family: 'Segoe UI', sans-serif;
        }

        body.bpjs-mode-active {
            overflow: hidden !important;
            height: 100vh !important;
        }

        /* ✅ Header Modern */
        .header-tv {
            background: linear-gradient(135deg, #198754, #157347);
            padding: 1rem;
            text-align: center;
            font-size: 2.2rem;
            font-weight: bold;
            letter-spacing: 2px;
            color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* ✅ Card Loket Glassmorphism */
        .loket-card {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            min-height: 280px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .loket-card:hover {
            transform: translateY(-5px);
        }

        /* ✅ Judul Loket */
        .loket-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
            background: #198754;
            border-radius: 10px;
            padding: .6rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        /* ✅ Nomor Antrian */
        .antrian-no {
            font-size: 3.2rem;
            font-weight: bold;
            color: #ecfc18;
            animation: glow 1.5s infinite alternate;
        }

        /* @keyframes glow {
            from { text-shadow: 0 0 5px rgba(220,53,69,0.6); }
            to { text-shadow: 0 0 20px rgba(220,53,69,1); }
        } */

        .antrian-pasien { font-size: 1.8rem; font-weight: bold; margin-top:.5rem; }
        .antrian-dokter { font-size: 1.1rem; color: #444; margin-top: .3rem; }

        .antrian-pasien {
            font-size: 1.8rem;
            font-weight: bold;
            margin-top: .5rem;
            text-align: center;

            white-space: nowrap;      /* ✅ jangan turun ke baris baru */
            overflow: hidden;         /* ✅ sembunyikan yang kepanjangan */
            text-overflow: ellipsis;  /* ✅ kasih "..." di ujung */
            display: block;
        }

        .antrian-dokter {
            display: block;
            background: #000000;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            padding: .4rem .8rem;
            border-radius: 8px;
            margin-top: .8rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);

            /* ⬇️ Supaya tidak turun baris */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .antrian-dokter i {
            margin-right: 6px;
        }



        /* ✅ Highlight Pasien Dipanggil */
        .highlight {
            background: #198754;
            color: #fff;
            padding: 1rem;
            border-radius: 12px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(25,135,84, 0.6); }
            70% { box-shadow: 0 0 0 15px rgba(25,135,84, 0); }
            100% { box-shadow: 0 0 0 0 rgba(25,135,84, 0); }
        }

        /* ✅ Footer Running Text */
        .footer-tv {
            background: #157347;
            color: #fff;
            padding: .7rem;
            font-size: 1.2rem;
            position: fixed;
            bottom: 0;
            width: 100%;
            overflow: hidden;
        }

        .footer-tv span {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: marquee 15s linear infinite;
        }

        @keyframes marquee {
            0%   { transform: translateX(0%); }
            100% { transform: translateX(-100%); }
        }

        /* ✅ Panel Toggle */
        .toggle-section {
            background: #fff;
            color: #000;
            padding: 10px;
            border-radius: 10px;
            margin: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .toggle-section label {
            margin-right: 15px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        /* ✅ Custom Checkbox Bulat */
        .toggle-section input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #198754;
            border-radius: 50%;
            outline: none;
            cursor: pointer;
            vertical-align: middle;
            margin-right: 6px;
            position: relative;
            transition: all 0.3s ease-in-out;
        }

        .toggle-section input[type="checkbox"]:checked {
            background-color: #198754;
            border-color: #198754;
        }

        .toggle-section input[type="checkbox"]:checked::after {
            content: "";
            position: absolute;
            top: 4px;
            left: 4px;
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
        }

        .footer-tv span {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: marquee 15s linear infinite;
            font-weight: 700;   /* ✅ lebih tebal */
        }

        /* 🚨 FULLSCREEN OVERLAY BPJS GANGGUAN */
        #bpjs-fullscreen-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999999 !important;
            background: radial-gradient(circle at 50% 25%, #1e293b 0%, #0f172a 55%, #020617 100%) !important;
            color: #ffffff !important;
            display: none;
            flex-direction: column !important;
            justify-content: space-between !important;
            overflow: hidden !important;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif !important;
            animation: fadeInOverlay 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #bpjs-fullscreen-overlay.active {
            display: flex !important;
        }

        @keyframes fadeInOverlay {
            from { opacity: 0; transform: scale(1.02); }
            to { opacity: 1; transform: scale(1); }
        }

        .bpjs-fs-header {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid rgba(239, 68, 68, 0.4);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
            padding: 0.8rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .bpjs-fs-brand-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 0.4rem 1.2rem;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.25);
            display: flex;
            align-items: center;
        }

        .bpjs-fs-clock-box {
            text-align: center;
        }
        .bpjs-fs-time {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: #38bdf8;
            text-shadow: 0 0 20px rgba(56, 189, 248, 0.6);
        }
        .bpjs-fs-date {
            font-size: 1.1rem;
            font-weight: 600;
            color: #cbd5e1;
            letter-spacing: 1px;
        }

        .bpjs-fs-center {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 3rem;
            text-align: center;
            position: relative;
        }

        .bpjs-glow-ambient {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.15) 0%, rgba(245, 158, 11, 0.08) 50%, transparent 70%);
            border-radius: 50%;
            filter: blur(40px);
            pointer-events: none;
            animation: ambientPulse 4s ease-in-out infinite alternate;
        }

        @keyframes ambientPulse {
            0% { transform: scale(0.9); opacity: 0.7; }
            100% { transform: scale(1.1); opacity: 1; }
        }

        .bpjs-alert-badge {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.25), rgba(185, 28, 28, 0.4));
            border: 1px solid rgba(239, 68, 68, 0.6);
            color: #fca5a5;
            padding: 0.5rem 1.8rem;
            border-radius: 50px;
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 1rem;
            box-shadow: 0 0 25px rgba(239, 68, 68, 0.35);
        }

        .bpjs-pulse-beacon {
            width: 14px;
            height: 14px;
            background-color: #ef4444;
            border-radius: 50%;
            margin-right: 12px;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.8);
            animation: beaconPulse 1.6s infinite;
        }

        @keyframes beaconPulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.8); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 16px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .bpjs-fs-title {
            font-size: 3rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            background: linear-gradient(135deg, #fef08a 0%, #f59e0b 50%, #fbbf24 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 4px 30px rgba(245, 158, 11, 0.3);
            margin-bottom: 0.7rem;
            line-height: 1.2;
        }

        .bpjs-fs-message {
            font-size: 1.7rem;
            font-weight: 600;
            color: #ffffff;
            max-width: 1200px;
            margin-bottom: 1.8rem;
            line-height: 1.5;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        .bpjs-card-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.6rem;
            width: 100%;
            max-width: 1300px;
            margin-bottom: 1.5rem;
        }

        .bpjs-info-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 1.5rem 1.4rem;
            backdrop-filter: blur(16px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, border-color 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .bpjs-info-card:hover {
            transform: translateY(-5px);
            border-color: rgba(245, 158, 11, 0.4);
        }

        .bpjs-card-icon-wrap {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 0.8rem;
        }

        .bpjs-card-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #f1f5f9;
            margin-bottom: 0.4rem;
        }

        .bpjs-card-desc {
            font-size: 1.1rem;
            color: #cbd5e1;
            line-height: 1.4;
        }

        .bpjs-subpesan-note {
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid rgba(14, 165, 233, 0.35);
            border-radius: 12px;
            padding: 0.8rem 2rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #7dd3fc;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .bpjs-fs-footer {
            background: linear-gradient(90deg, #991b1b 0%, #dc2626 50%, #991b1b 100%);
            border-top: 3px solid #f87171;
            box-shadow: 0 -8px 25px rgba(220, 38, 38, 0.4);
            color: #ffffff;
            padding: 0.8rem 0;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: 1px;
            overflow: hidden;
            white-space: nowrap;
        }

        .bpjs-fs-footer span {
            display: inline-block;
            padding-left: 100%;
            animation: marqueeBpjs 18s linear infinite;
        }

        @keyframes marqueeBpjs {
            0%   { transform: translateX(0%); }
            100% { transform: translateX(-100%); }
        }
    </style>

    <!-- ✅ Header dengan logo kiri & kanan, judul center -->
    <div class="header-bar w-100 bg-white shadow-sm border-bottom fixed-top d-flex align-items-center justify-content-between px-4 py-3">
        <!-- Logo RS -->
        <div class="d-flex align-items-center">
            <img alt="Logo Rumah Sakit" src="{{ asset('img/bw2.png') }}" style="height:90px; max-height:12vh;" />
        </div>

        <!-- Judul Tengah -->
       <!-- Judul Tengah -->
        <div class="header-title position-absolute top-30 start-50 translate-middle-x">
            <h1 class="m-0 fw-bold text-dark" style="font-size:2rem;">ANTRIAN PENDAFTARAN</h1>
        </div>


        <!-- Logo BPJS -->
        <div class="d-flex align-items-center">
            <img alt="Logo BPJS" src="{{ asset('img/bpjs.png') }}" style="height:40px; max-height:12vh;" />
        </div>
    </div>

    <!-- ✅ Panel Toggle Loket -->
    <div class="container-fluid toggle-section text-center" style="margin-top:120px;">
        <label><input type="checkbox" class="toggle-loket" value="Loket 1" checked> Loket 1</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 2" checked> Loket 2</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 3" checked> Loket 3</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 4" checked> Loket 4</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 5" checked> Loket 5</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 6" checked> Loket 6</label>
        <label><input type="checkbox" class="toggle-loket" value="Loket 7" checked> Loket 7</label>
    </div>


    <div class="container-fluid mt-4">
        <div class="row g-4" id="antrian-container">
            {{-- data antrian inject di JS --}}
        </div>
    </div>

    <!-- ✅ Running Text Normal -->
    <div class="footer-tv">
        <span>Selamat datang di Rumah Sakit Bumi Waras | Layanan cepat, nyaman, dan profesional | Terima kasih telah menunggu dengan sabar 🙏</span>
    </div>

    {{-- 🚨 FULLSCREEN PEMBERITAHUAN GANGGUAN BPJS KESEHATAN --}}
    <div id="bpjs-fullscreen-overlay" class="{{ !empty($bpjsStatus['is_gangguan']) ? 'active' : '' }}" style="display: {{ !empty($bpjsStatus['is_gangguan']) ? 'flex !important' : 'none !important' }};">
        <div class="bpjs-glow-ambient"></div>

        <!-- Header Fullscreen -->
        <div class="bpjs-fs-header">
            <div class="bpjs-fs-brand-box">
                <img src="{{ asset('img/bw2.png') }}" style="height:70px; max-height:9.5vh;" alt="Logo RS Bumi Waras">
            </div>

            <div class="bpjs-fs-clock-box">
                <div class="bpjs-fs-time" id="bpjs-live-time">--:--:-- WIB</div>
                <div class="bpjs-fs-date" id="bpjs-live-date">--</div>
            </div>

            <div class="bpjs-fs-brand-box">
                <img src="{{ asset('img/bpjs.png') }}" style="height:36px; max-height:7.5vh;" alt="Logo BPJS Kesehatan">
            </div>
        </div>

        <!-- Center Content -->
        <div class="bpjs-fs-center">
            <div class="bpjs-alert-badge">
                <span class="bpjs-pulse-beacon"></span>
                <span>PEMBERITAHUAN GANGGUAN SISTEM BPJS</span>
            </div>

            <h1 class="bpjs-fs-title" id="bpjs-tv-judul">
                {{ $bpjsStatus['judul'] ?? 'PEMBERITAHUAN GANGGUAN SISTEM BPJS KESEHATAN' }}
            </h1>

            <p class="bpjs-fs-message" id="bpjs-tv-pesan">
                {{ $bpjsStatus['pesan'] ?? 'Mohon maaf atas ketidaknyamanannya. Saat ini sistem / server BPJS Kesehatan pusat sedang mengalami kendala teknis jaringan.' }}
            </p>

            <!-- 3 Informational Cards -->
            <div class="bpjs-card-grid">
                <div class="bpjs-info-card">
                    <div class="bpjs-card-icon-wrap" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35);">
                        <i class="fas fa-hospital-user"></i>
                    </div>
                    <div class="bpjs-card-title">PENDAFTARAN TETAP DILAYANI</div>
                    <div class="bpjs-card-desc">Pelayanan pendaftaran pasien tetap dibuka dan dilayani secara manual oleh petugas loket pendaftaran.</div>
                </div>

                <div class="bpjs-info-card">
                    <div class="bpjs-card-icon-wrap" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35);">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="bpjs-card-title">SIAPKAN DOKUMEN / KTP</div>
                    <div class="bpjs-card-desc">Mohon siapkan Kartu BPJS / KIS, KTP / NIK asli, serta Surat Kontrol / Rujukan untuk verifikasi langsung.</div>
                </div>

                <div class="bpjs-info-card">
                    <div class="bpjs-card-icon-wrap" style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.35);">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="bpjs-card-title">MOHON KESABARAN ANDA</div>
                    <div class="bpjs-card-desc">Petugas kami sedang berupaya maksimal memproses antrian. Silakan menunggu giliran panggilan Anda.</div>
                </div>
            </div>

            <!-- Sub-pesan / Catatan Petunjuk -->
            <div class="bpjs-subpesan-note" id="bpjs-tv-subpesan">
                <i class="fas fa-info-circle me-2 text-warning"></i>
                <span id="bpjs-tv-subpesan-text">{{ $bpjsStatus['sub_pesan'] ?? 'Pelayanan pendaftaran tetap dilayani secara manual oleh petugas loket pendaftaran. Silakan menunggu antrian Anda dipanggil.' }}</span>
            </div>
        </div>

        <!-- Footer Running Text Peringatan -->
        <div class="bpjs-fs-footer">
            <span id="bpjs-tv-running">{{ $bpjsStatus['running_text'] ?? 'PENGUMUMAN RESMI: SISTEM BRIDGING BPJS KESEHATAN SEDANG MENGALAMI GANGGUAN TEKNIS. PENDAFTARAN PASIEN TETAP DILAYANI SECARA MANUAL DI LOKET PENDAFTARAN. TERIMA KASIH ATAS KESABARAN ANDA 🙏' }}</span>
        </div>
    </div>

    <audio id="bell-sound" src="{{ asset('sounds/bell.mp3') }}" preload="auto"></audio>
    <script src="https://code.responsivevoice.org/responsivevoice.js"></script>

    <script>
    let lastDipanggil = {};
    const loketFix = ["Loket 1","Loket 2","Loket 3","Loket 4","Loket 5","Loket 6","Loket 7"];
    let prevGangguanState = {{ !empty($bpjsStatus['is_gangguan']) ? 'true' : 'false' }};

    // Jam Digital Realtime TV
    function updateLiveTvClock() {
        const now = new Date();
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        const dayName = days[now.getDay()];
        const date = String(now.getDate()).padStart(2, '0');
        const monthName = months[now.getMonth()];
        const year = now.getFullYear();

        const hours = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');

        const timeEl = document.getElementById('bpjs-live-time');
        const dateEl = document.getElementById('bpjs-live-date');
        if (timeEl) timeEl.innerText = `${hours}:${mins}:${secs} WIB`;
        if (dateEl) dateEl.innerText = `${dayName}, ${date} ${monthName} ${year}`;
    }
    setInterval(updateLiveTvClock, 1000);
    updateLiveTvClock();

    // Handler update status overlay BPJS
    function handleBpjsOverlay(bpjs) {
        if (!bpjs) return;
        const overlay = document.getElementById("bpjs-fullscreen-overlay");
        if (!overlay) return;

        const isGangguan = Boolean(bpjs.is_gangguan);

        if (isGangguan) {
            document.body.classList.add("bpjs-mode-active");
            overlay.classList.add("active");
            overlay.style.setProperty("display", "flex", "important");

            if (bpjs.judul) document.getElementById("bpjs-tv-judul").innerText = bpjs.judul;
            if (bpjs.pesan) document.getElementById("bpjs-tv-pesan").innerText = bpjs.pesan;
            if (bpjs.sub_pesan) document.getElementById("bpjs-tv-subpesan-text").innerText = bpjs.sub_pesan;
            if (bpjs.running_text) document.getElementById("bpjs-tv-running").innerText = bpjs.running_text;

            // Suara pengumuman saat status berubah ke gangguan
            if (!prevGangguanState) {
                prevGangguanState = true;
                let bell = document.getElementById("bell-sound");
                if (bell) {
                    bell.play().catch(e => console.log("Audio notify:", e));
                }
                if (window.responsiveVoice) {
                    setTimeout(() => {
                        responsiveVoice.speak("Pemberitahuan kepada seluruh pasien. Saat ini sistem jaringan BPJS Kesehatan sedang mengalami kendala teknis. Pelayanan pendaftaran tetap dilayani secara manual oleh petugas loket. Terima kasih atas kesabaran Anda.", "Indonesian Female", {pitch: 1, rate: 0.9, volume: 1});
                    }, 800);
                }
            }
        } else {
            document.body.classList.remove("bpjs-mode-active");
            overlay.classList.remove("active");
            overlay.style.setProperty("display", "none", "important");
            prevGangguanState = false;
        }
    }

    // ambil loket yang aktif dari checkbox
    function getActiveLokets() {
        let aktif = [];
        document.querySelectorAll(".toggle-loket:checked").forEach(cb => aktif.push(cb.value));
        return aktif;
    }

    function renderAntrian(data) {
        let container = document.getElementById("antrian-container");
        container.innerHTML = "";

        // grupkan data berdasarkan nama_loket
        let grouped = {};
        data.forEach(item => {
            const loketKey = item.nama_loket.trim();
            if (!grouped[loketKey]) grouped[loketKey] = [];
            grouped[loketKey].push(item);
        });

        // gabungkan dengan loket fix agar semua loket muncul
        loketFix.forEach(loket => { if (!grouped[loket]) grouped[loket] = []; });

        // filter hanya yang aktif dari checkbox (case insensitive)
        let aktifLower = getActiveLokets().map(l => l.toLowerCase());
        let lokets = Object.keys(grouped).filter(l => aktifLower.includes(l.toLowerCase())).sort();

        lokets.forEach(loket => {
            let col = document.createElement("div");
            col.className = "col-md-4 col-sm-6";

            let card = document.createElement("div");
            card.className = "loket-card";

            let title = document.createElement("div");
            title.className = "loket-title";
            title.innerText = loket.toUpperCase();

            let content = document.createElement("div");

            if (grouped[loket].length === 0) {
                content.innerHTML = `<div class="text-muted"><i class="fas fa-user-clock"></i> BELUM ADA ANTRIAN</div>`;
            } else {
                // ambil pasien yang DIPANGGIL, kalau tidak ada ambil pasien pertama
                let dipanggil = grouped[loket].find(item => item.status.toUpperCase() === "DIPANGGIL");
                let pasien = dipanggil ? dipanggil : grouped[loket][0];

                content.innerHTML = `
                    <div class="slide-item ${pasien.status.toUpperCase() === "DIPANGGIL" ? "highlight" : ""}">
                        <div class="antrian-no">${pasien.no_reg}</div>
                        <div class="antrian-pasien">${pasien.nm_pasien.toUpperCase()}</div>
                        <div class="antrian-dokter"><i class="fas fa-user-md"></i> ${pasien.nm_dokter.toUpperCase()}</div>
                        <div class="mt-3"><span class="badge bg-dark text-white fs-6">${pasien.status.toUpperCase()}</span></div>
                    </div>
                `;

                // mainkan bell & suara hanya kalau pasien DIPANGGIL baru dan tidak dalam mode gangguan
                if (pasien.status.toUpperCase() === "DIPANGGIL" && !prevGangguanState) {
                    let pasienDipanggil = pasien.nm_pasien.toUpperCase();
                    if (lastDipanggil[loket] !== pasienDipanggil) {
                        lastDipanggil[loket] = pasienDipanggil;
                        let bell = document.getElementById("bell-sound");
                        bell.play().then(() => {
                            setTimeout(() => {
                                const text = `Nomor antrian ${pasien.no_reg}, pasien ${pasien.nm_pasien}, menuju ${pasien.nm_dokter} di ${loket}`;
                                responsiveVoice.speak(text, "Indonesian Female", {pitch:1, rate:0.9, volume:1});
                            }, 1000);
                        }).catch(err => console.log("Autoplay blok:", err));
                    }
                }
            }

            card.appendChild(title);
            card.appendChild(content);
            col.appendChild(card);
            container.appendChild(col);
        });
    }

    function fetchAntrian() {
        fetch("{{ route('antrian.apiTv') }}")
            .then(res => res.json())
            .then(data => {
                let antrianList = Array.isArray(data) ? data : (data.antrian || []);
                let bpjsStatus = data.bpjs || null;

                if (bpjsStatus) {
                    handleBpjsOverlay(bpjsStatus);
                }
                renderAntrian(antrianList);
            })
            .catch(err => console.error("Gagal ambil data:", err));
    }

    // reload ketika toggle berubah
    document.querySelectorAll(".toggle-loket").forEach(cb => {
        cb.addEventListener("change", fetchAntrian);
    });

    document.addEventListener("DOMContentLoaded", function() {
        if ({{ !empty($bpjsStatus['is_gangguan']) ? 'true' : 'false' }}) {
            document.body.classList.add("bpjs-mode-active");
        }
        fetchAntrian();
        setInterval(fetchAntrian, 4000); // cek update tiap 4 detik secara berkala
    });
</script>
</div>
