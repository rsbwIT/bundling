@extends('..layout.layoutLogin')
@section('title', 'Login - SIMRS Portal')

@section('konten')

<style>
    * {
        box-sizing: border-box;
    }

    /* BACKGROUND ASLI SESUAI GAMBAR USER (LIGHT PASTEL GRADIENT + HALUS GRID) */
    body.login-page {
        min-height: 100vh;
        width: 100%;
        margin: 0;
        padding: 24px 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        background:
            radial-gradient(circle at 20% 30%, rgba(95, 156, 255, 0.5) 0%, transparent 45%),
            radial-gradient(circle at 80% 20%, rgba(155, 92, 255, 0.45) 0%, transparent 45%),
            radial-gradient(circle at 50% 85%, rgba(32, 201, 151, 0.45) 0%, transparent 50%),
            #f5f8fc !important;
        overflow-x: hidden;
        overflow-y: auto;
        position: relative;
    }

    /* Container Manjang ke Samping (Landscape Split Card) */
    .horizontal-login-wrapper {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 900px;
        margin: auto;
    }

    .horizontal-card {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 20px;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.12), 0 2px 6px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.8);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    @media (min-width: 820px) {
        .horizontal-card {
            flex-direction: row;
            min-height: 520px;
        }
    }

    /* PANEL KIRI: VISUAL & IDENTITAS RS */
    .hero-side {
        position: relative;
        background: url('{{ asset("img/rs.jpg") }}') center center / cover no-repeat;
        padding: 40px 34px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: #ffffff;
        overflow: hidden;
    }

    @media (min-width: 820px) {
        .hero-side {
            width: 48%;
        }
    }

    /* Overlay Gradient Medis di atas foto RS */
    .hero-side-overlay {
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 10% 15%, rgba(56, 189, 248, 0.28) 0%, transparent 45%),
            radial-gradient(circle at 90% 85%, rgba(129, 140, 248, 0.22) 0%, transparent 50%),
            linear-gradient(145deg, rgba(2, 132, 199, 0.94) 0%, rgba(15, 23, 42, 0.92) 100%);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .hero-logo-box {
        width: 68px;
        height: 68px;
        border-radius: 18px;
        background: #ffffff;
        padding: 6px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }

    .hero-logo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 14px;
    }

    .hero-hospital-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: 0.2px;
        line-height: 1.25;
        margin-bottom: 8px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.35);
    }

    .hero-divider-bar {
        width: 44px;
        height: 3.5px;
        border-radius: 999px;
        background: linear-gradient(90deg, #38bdf8 0%, #a855f7 100%);
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(56, 189, 248, 0.4);
    }

    .hero-hospital-tag {
        font-size: 0.85rem;
        color: rgba(240, 249, 255, 0.88);
        font-weight: 400;
        line-height: 1.55;
        margin-bottom: 16px;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .hero-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #e0f2fe;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .hero-badge-pill i {
        color: #fbbf24;
    }

    /* MODERN FEATURE CARDS LIST */
    .hero-features-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 6px;
    }

    .hero-feature-card {
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 8px 12px;
        transition: all 0.25s ease;
    }

    .hero-feature-card:hover {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.32);
        transform: translateX(4px);
    }

    .hero-feature-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, rgba(56, 189, 248, 0.35) 0%, rgba(99, 102, 241, 0.25) 100%);
        border: 1px solid rgba(56, 189, 248, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #38bdf8;
        font-size: 0.85rem;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .hero-feature-info {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
        text-align: left;
    }

    .hero-feature-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 0.2px;
    }

    .hero-feature-sub {
        font-size: 0.71rem;
        color: rgba(224, 242, 254, 0.78);
        font-weight: 400;
        margin-top: 2px;
    }

    .hero-partner-logos {
        display: flex;
        align-items: center;
        gap: 14px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.18);
        margin-top: 20px;
    }

    .hero-partner-img {
        height: 28px;
        object-fit: contain;
        filter: brightness(0) invert(1);
        opacity: 0.85;
    }

    /* PANEL KANAN: FORM LOGIN */
    .form-side {
        padding: 40px 36px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #ffffff;
    }

    @media (min-width: 820px) {
        .form-side {
            width: 52%;
        }
    }

    .form-header-area {
        margin-bottom: 24px;
        text-align: left;
    }

    .form-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 14px;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #7dd3fc;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #0369a1;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.08);
    }

    .form-kicker i {
        color: #0284c7;
        font-size: 0.8rem;
    }

    .pulse-dot {
        width: 7px;
        height: 7px;
        background: #0284c7;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.7);
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
        0% { box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(2, 132, 199, 0); }
        100% { box-shadow: 0 0 0 0 rgba(2, 132, 199, 0); }
    }

    .form-title {
        font-size: 1.65rem;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.2;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .gradient-title {
        background: linear-gradient(135deg, #0f172a 0%, #0369a1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .form-title-bar {
        width: 38px;
        height: 3.5px;
        border-radius: 999px;
        background: linear-gradient(90deg, #0284c7 0%, #38bdf8 100%);
        margin-top: 8px;
    }

    .wave-hand {
        display: inline-block;
        transform-origin: 70% 70%;
        animation: waveAnimation 2.2s infinite ease-in-out;
    }

    @keyframes waveAnimation {
        0%, 100% { transform: rotate(0deg); }
        15% { transform: rotate(14deg); }
        30% { transform: rotate(-8deg); }
        45% { transform: rotate(14deg); }
        60% { transform: rotate(-4deg); }
        75% { transform: rotate(10deg); }
    }

    .form-desc {
        font-size: 0.88rem;
        color: #64748b;
        margin: 0;
        line-height: 1.5;
        font-weight: 400;
    }

    /* Input Field */
    .form-item {
        margin-bottom: 16px;
        text-align: left;
    }

    .item-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .input-icon-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon-item {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        font-size: 0.92rem;
        pointer-events: none;
        transition: color 0.2s ease;
    }

    .horizontal-input {
        width: 100%;
        height: 44px;
        padding: 0 16px 0 42px;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-size: 0.92rem;
        color: #0f172a;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .horizontal-input:focus {
        outline: none;
        background: #ffffff;
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
    }

    .horizontal-input:focus + .input-icon-item,
    .input-icon-box:focus-within .input-icon-item {
        color: #0284c7;
    }

    /* Eye toggle */
    .btn-eye-toggle {
        position: absolute;
        right: 12px;
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 6px;
        font-size: 0.92rem;
        transition: color 0.2s ease;
    }
    .btn-eye-toggle:hover {
        color: #334155;
    }

    /* ========================================================
       DESAIN CAPTCHA SUPER RAPI (SEJAJAR, 1 ROW, TIDAK WRAPPING)
       ======================================================== */
    .captcha-group-clean {
        display: flex;
        align-items: stretch;
        width: 100%;
        height: 44px;
    }

    .captcha-question-tag {
        background: #f1f5f9;
        border: 1.5px solid #cbd5e1;
        border-right: none;
        border-radius: 10px 0 0 10px;
        color: #0284c7;
        font-size: 1rem;
        font-weight: 800;
        letter-spacing: 1px;
        padding: 0 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap !important;
        user-select: none;
        min-width: 105px;
    }

    .captcha-input-clean {
        flex: 1;
        height: 44px;
        border: 1.5px solid #cbd5e1;
        border-radius: 0;
        background: #ffffff;
        color: #0f172a;
        font-size: 0.95rem;
        font-weight: 600;
        text-align: center;
        padding: 0 12px;
        transition: all 0.2s ease;
    }

    .captcha-input-clean:focus {
        outline: none;
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        z-index: 2;
    }

    .btn-captcha-refresh-clean {
        width: 44px;
        height: 44px;
        border: 1.5px solid #cbd5e1;
        border-left: none;
        border-radius: 0 10px 10px 0;
        background: #f8fafc;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        transition: all 0.2s ease;
    }

    .btn-captcha-refresh-clean:hover {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
    }

    /* Submit Button */
    .btn-horizontal-login {
        width: 100%;
        height: 46px;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        border: none;
        border-radius: 10px;
        color: #ffffff;
        font-size: 0.94rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        box-shadow: 0 8px 20px -4px rgba(2, 132, 199, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-horizontal-login:hover {
        background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
        box-shadow: 0 10px 24px -4px rgba(2, 132, 199, 0.45);
        transform: translateY(-1px);
    }

    .btn-horizontal-login:active {
        transform: translateY(0);
    }

    .form-foot-note {
        margin-top: 18px;
        font-size: 0.76rem;
        color: #94a3b8;
        text-align: center;
    }
</style>

<div class="horizontal-login-wrapper">
    <div class="horizontal-card">

        @php
            if(!isset($getSetting) || !$getSetting){
                $getSetting = DB::table('setting')->first();
            }
            $namaRS = $getSetting->nama_instansi ?? 'RUMAH SAKIT BUMI WARAS';
        @endphp

        <!-- ================= SISI KIRI (HERO & IDENTITAS RS) ================= -->
        <div class="hero-side">
            <div class="hero-side-overlay"></div>
            <div class="hero-content">
                <div>
                    <!-- Logo RS -->
                    <div class="hero-logo-box">
                        @if(!empty($getSetting->logo))
                            <img class="hero-logo-img"
                                 src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                                 alt="Logo {{ $namaRS }}">
                        @else
                            <img class="hero-logo-img" src="{{ asset('img/rs.png') }}" alt="Logo">
                        @endif
                    </div>

                    <div class="hero-badge-pill">
                        <i class="fas fa-shield-alt"></i>
                        Portal Terintegrasi SIMRS Khanza
                    </div>

                    <h1 class="hero-hospital-title">{{ $namaRS }}</h1>
                    <div class="hero-divider-bar"></div>
                    <p class="hero-hospital-tag">
                        Sistem Informasi Manajemen Rumah Sakit, Bridging BPJS Kesehatan, Casemix & Layanan Rawat Digital.
                    </p>

                    <div class="hero-features-list">
                        <div class="hero-feature-card">
                            <div class="hero-feature-icon">
                                <i class="fas fa-network-wired"></i>
                            </div>
                            <div class="hero-feature-info">
                                <span class="hero-feature-title">Bridging BPJS</span>
                                <span class="hero-feature-sub">V-Claim & Antrean Terkoneksi</span>
                            </div>
                        </div>

                        <div class="hero-feature-card">
                            <div class="hero-feature-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="hero-feature-info">
                                <span class="hero-feature-title">Casemix & E-Klaim</span>
                                <span class="hero-feature-sub">Grouping INA-CBG & Berkas Digital</span>
                            </div>
                        </div>

                        <div class="hero-feature-card">
                            <div class="hero-feature-icon">
                                <i class="fas fa-laptop-medical"></i>
                            </div>
                            <div class="hero-feature-info">
                                <span class="hero-feature-title">Rawat Digital</span>
                                <span class="hero-feature-sub">Rekam Medis Elektronik Terpadu</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian Bawah Panel Kiri -->
                <div>
                    <div class="hero-partner-logos">
                        @if(file_exists(public_path('img/bpjs.png')))
                            <img src="{{ asset('img/bpjs.png') }}" alt="BPJS Kesehatan" class="hero-partner-img" title="Bridging BPJS Kesehatan">
                        @endif
                        @if(file_exists(public_path('img/kars.png')))
                            <img src="{{ asset('img/kars.png') }}" alt="KARS" class="hero-partner-img" title="Terakreditasi KARS">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= SISI KANAN (FORMULIR LOGIN) ================= -->
        <div class="form-side">
            <div>
                <div class="form-header-area">
                    <div class="form-kicker">
                        <span class="pulse-dot"></span>
                        <i class="fas fa-fingerprint"></i>
                        <span>Portal SIMRS</span>
                    </div>
                    <h2 class="form-title">
                        <span class="gradient-title">Selamat Datang</span>
                        <span class="wave-hand">👋</span>
                    </h2>
                    <div class="form-title-bar"></div>
                </div>

                <!-- Alert Messages -->
                @if (session('errorLogin'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 p-3 mb-3 border-0 text-left"
                         id="alertLoginBox"
                         style="background: #fef2f2; border-left: 3px solid #ef4444 !important; color: #991b1b; font-size: 0.84rem;">
                        <div>
                            <strong><i class="fas fa-exclamation-circle mr-1"></i> Gagal Masuk:</strong>
                            <span id="alertLoginText">{{ session('errorLogin') }}</span>
                        </div>
                        @if (!empty($lockoutSeconds) && $lockoutSeconds > 0)
                            <div class="mt-2 pt-2 border-top border-danger-subtle font-weight-bold" id="lockoutTimerInfo" style="color: #b91c1c; font-size: 0.82rem;">
                                <i class="fas fa-hourglass-half mr-1 text-danger"></i> Silakan tunggu <span id="lockoutCountdown" class="badge badge-danger px-2 py-1" style="font-size: 0.85rem;">{{ $lockoutSeconds }}</span> detik lagi sebelum mencoba kembali.
                            </div>
                        @endif
                        <button type="button" class="close text-danger" data-dismiss="alert" style="opacity: 0.6;">
                            <span>&times;</span>
                        </button>
                    </div>
                @elseif (session('reqLogin'))
                    <div class="alert alert-warning alert-dismissible fade show rounded-3 p-3 mb-3 border-0 text-left"
                         style="background: #fffbeb; border-left: 3px solid #f59e0b !important; color: #92400e; font-size: 0.84rem;">
                        <strong>Perhatian:</strong> {{ session('reqLogin') }}
                        <button type="button" class="close text-warning" data-dismiss="alert" style="opacity: 0.6;">
                            <span>&times;</span>
                        </button>
                    </div>
                @elseif (session('sucsessLogout'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 p-3 mb-3 border-0 text-left"
                         style="background: #f0fdf4; border-left: 3px solid #10b981 !important; color: #065f46; font-size: 0.84rem;">
                        <strong>Berhasil:</strong> {{ session('sucsessLogout') }}
                        <button type="button" class="close text-success" data-dismiss="alert" style="opacity: 0.6;">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ url('/mesinlogin') }}" method="POST" autocomplete="off" id="formHorizontalLogin">
                    @csrf

                    <!-- ID User / NIK -->
                    <div class="form-item">
                        <label class="item-label" for="id_user">ID Pengguna / NIK</label>
                        <div class="input-icon-box">
                            <i class="fas fa-user-circle input-icon-item"></i>
                            <input type="text"
                                   id="id_user"
                                   name="id_user"
                                   value="{{ old('id_user') }}"
                                   class="horizontal-input"
                                   placeholder="Nomor Identitas Karyawan"
                                   required
                                   autofocus>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-item">
                        <label class="item-label" for="password">Kata Sandi</label>
                        <div class="input-icon-box">
                            <i class="fas fa-lock input-icon-item"></i>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="horizontal-input"
                                   style="padding-right: 42px;"
                                   placeholder="Ketik kata sandi"
                                   required>
                            <button type="button" class="btn-eye-toggle" id="btnEyeToggle" title="Lihat/Sembunyikan Sandi">
                                <i class="fas fa-eye" id="iconEyeToggle"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Captcha Rapi: 1 Baris Sejajar -->
                    <div class="form-item">
                        <label class="item-label" for="captchaInput">
                            <span>Kode Keamanan (Captcha)</span>
                            <small class="text-muted font-weight-normal">Hitung hasilnya</small>
                        </label>
                        <div class="captcha-group-clean">
                            <span class="captcha-question-tag" id="captchaLabel">
                                {{ $captchaQuestion ?? '4 + 5 = ?' }}
                            </span>
                            <input type="number"
                                   id="captchaInput"
                                   name="captcha"
                                   class="captcha-input-clean"
                                   placeholder="Hasil?"
                                   autocomplete="off"
                                   required>
                            <button type="button"
                                    class="btn-captcha-refresh-clean"
                                    id="btnRefreshCaptcha"
                                    title="Ganti Soal Captcha">
                                <i class="fas fa-sync-alt" id="refreshIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="d-flex justify-content-between align-items-center mb-3" style="font-size: 0.84rem;">
                        <div class="icheck-primary text-left">
                            <input type="checkbox" id="remember">
                            <label for="remember" style="font-weight: 500; color: #475569; cursor: pointer;">
                                Ingat sesi saya
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-horizontal-login" id="btnSubmit">
                        <span id="btnText">Masuk ke Sistem</span>
                        <i class="fas fa-sign-in-alt" id="btnIcon"></i>
                    </button>
                </form>

                <div class="form-foot-note">
                    © {{ date('Y') }} {{ $namaRS }}
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Toggle Password Eye
    var btnEye = document.getElementById("btnEyeToggle");
    var pwdInput = document.getElementById("password");
    var iconEye = document.getElementById("iconEyeToggle");

    if (btnEye && pwdInput) {
        btnEye.addEventListener("click", function() {
            if (pwdInput.type === "password") {
                pwdInput.type = "text";
                iconEye.className = "fas fa-eye-slash";
            } else {
                pwdInput.type = "password";
                iconEye.className = "fas fa-eye";
            }
        });
    }

    // 2. Refresh Captcha AJAX
    var btnRefresh = document.getElementById("btnRefreshCaptcha");
    var refreshIcon = document.getElementById("refreshIcon");
    var captchaLabel = document.getElementById("captchaLabel");
    var captchaInput = document.getElementById("captchaInput");

    if (btnRefresh) {
        btnRefresh.addEventListener("click", function() {
            refreshIcon.classList.add("fa-spin");
            btnRefresh.disabled = true;

            fetch("{{ url('/refresh-captcha') }}")
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data && data.question) {
                        captchaLabel.innerText = data.question;
                        if (captchaInput) {
                            captchaInput.value = "";
                            captchaInput.focus();
                        }
                    }
                })
                .catch(function(err) {
                    console.error("Gagal refresh captcha:", err);
                })
                .finally(function() {
                    refreshIcon.classList.remove("fa-spin");
                    btnRefresh.disabled = false;
                });
        });
    }

    // 3. Submit Loading State
    var form = document.getElementById("formHorizontalLogin");
    var btnSubmit = document.getElementById("btnSubmit");
    var btnText = document.getElementById("btnText");
    var btnIcon = document.getElementById("btnIcon");

    if (form && btnSubmit) {
        form.addEventListener("submit", function() {
            btnSubmit.disabled = true;
            btnText.innerText = "Memverifikasi...";
            btnIcon.className = "fas fa-spinner fa-spin";
        });
    }

    // 4. Hitung Mundur Waktu Tunggu Lockout (Jika salah login 3x)
    var lockoutSeconds = {{ !empty($lockoutSeconds) && $lockoutSeconds > 0 ? (int)$lockoutSeconds : 0 }};
    if (lockoutSeconds > 0) {
        var countdownEl = document.getElementById("lockoutCountdown");
        var timerInfoEl = document.getElementById("lockoutTimerInfo");

        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.style.opacity = "0.65";
            btnSubmit.style.cursor = "not-allowed";
            if (btnText) btnText.innerText = "Terkunci (" + lockoutSeconds + "s)";
            if (btnIcon) btnIcon.className = "fas fa-lock";
        }

        var timerInterval = setInterval(function() {
            lockoutSeconds--;
            if (countdownEl) {
                countdownEl.innerText = lockoutSeconds;
            }
            if (btnText && btnSubmit && btnSubmit.disabled) {
                btnText.innerText = "Terkunci (" + lockoutSeconds + "s)";
            }

            if (lockoutSeconds <= 0) {
                clearInterval(timerInterval);
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.style.opacity = "1";
                    btnSubmit.style.cursor = "pointer";
                    if (btnText) btnText.innerText = "Masuk ke Sistem";
                    if (btnIcon) btnIcon.className = "fas fa-sign-in-alt";
                }
                if (timerInfoEl) {
                    timerInfoEl.className = "mt-2 pt-2 border-top font-weight-bold text-success";
                    timerInfoEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Waktu tunggu 1 menit telah selesai. Silakan coba masuk kembali.';
                }
            }
        }, 1000);
    }
});
</script>

@endsection
