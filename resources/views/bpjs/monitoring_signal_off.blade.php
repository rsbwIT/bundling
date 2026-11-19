@extends('layout.layoutDashboard')
@section('title', 'Dashboard Utama')

@section('konten')
@php
    try {
        $getSetting = DB::table('setting')->select('logo', 'nama_instansi', 'alamat_instansi', 'kabupaten', 'propinsi', 'kontak', 'email')->first();
        
        $today = date('Y-m-d');
        $pasienHariIni = DB::table('reg_periksa')->where('tgl_registrasi', $today)->count();
        $ralanHariIni  = DB::table('reg_periksa')->where('tgl_registrasi', $today)->where('status_lanjut', 'Ralan')->count();
        $ranapAktif    = DB::table('kamar_inap')->where('stts_pulang', '-')->count();
        $labHariIni    = DB::table('periksa_lab')->where('tgl_periksa', $today)->count();
    } catch (\Exception $e) {
        $getSetting = null;
        $pasienHariIni = 0;
        $ralanHariIni  = 0;
        $ranapAktif    = 0;
        $labHariIni    = 0;
    }
@endphp

<div class="container-fluid px-3 py-4">

    <!-- PANEL UTAMA BERSATU DENGAN LOGO RS SEPERTI KLINIK -->
    <div class="card card-outline card-primary shadow-sm mb-4" style="border-radius: 16px; overflow: hidden; border-top: 4px solid #0284c7;">
        <div class="card-body bg-white py-5 px-4 text-center">
            
            <!-- LOGO RUMAH SAKIT BUMI WARAS -->
            <div class="mb-4">
                <div class="d-inline-block p-2 rounded-circle shadow-sm" style="background: #ffffff; border: 4px solid #0284c7;">
                    @if(!empty($getSetting->logo))
                        <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}" 
                             alt="Logo {{ $getSetting->nama_instansi ?? 'RS Bumi Waras' }}"
                             class="rounded-circle" 
                             style="width: 140px; height: 140px; object-fit: contain;">
                    @elseif(file_exists(public_path('img/bw2.png')))
                        <img src="{{ asset('img/bw2.png') }}" 
                             alt="Logo RS Bumi Waras" 
                             class="rounded-circle" 
                             style="width: 140px; height: 140px; object-fit: contain;">
                    @elseif(file_exists(public_path('img/logo.jpg')))
                        <img src="{{ asset('img/logo.jpg') }}" 
                             alt="Logo RS Bumi Waras" 
                             class="rounded-circle" 
                             style="width: 140px; height: 140px; object-fit: contain;">
                    @else
                        <img src="{{ asset('img/rs.png') }}" 
                             alt="Logo RS Bumi Waras" 
                             class="rounded-circle" 
                             style="width: 140px; height: 140px; object-fit: contain;">
                    @endif
                </div>
            </div>

            <!-- NAMA & IDENTITAS RS -->
            <h2 class="font-weight-bold text-dark mb-1" style="letter-spacing: 0.5px; font-size: 26px;">
                {{ $getSetting->nama_instansi ?? 'RS. BUMI WARAS' }}
            </h2>
            <h5 class="font-weight-bold text-primary mb-3" style="font-size: 16px;">
                Sistem Informasi Manajemen Rumah Sakit (SIMRS Portal)
            </h5>
            <p class="text-muted mx-auto mb-4" style="max-width: 680px; font-size: 15px; line-height: 1.6;">
                Pelayanan Kesehatan Paripurna, Rawat Jalan Spesialistik, Rawat Inap, Instalasi Gawat Darurat 24 Jam, Laboratorium Patologi Klinik Terintegrasi LIS, Radiologi, dan Farmasi.
            </p>

            <!-- BADGE INFO / LOKASI & KONTAK -->
            <div class="d-inline-flex flex-wrap align-items-center justify-content-center py-2 px-3 mb-4 rounded-pill bg-light border text-secondary" style="font-size: 13px;">
                <span class="mr-3"><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $getSetting->alamat_instansi ?? 'Jln. Wolter Monginsidi No. 235' }}, {{ $getSetting->kabupaten ?? 'Bandar Lampung' }}</span>
                @if(!empty($getSetting->kontak))
                    <span class="mr-3"><i class="fas fa-phone-alt text-success mr-1"></i> {{ $getSetting->kontak }}</span>
                @endif
                <span><i class="fas fa-heartbeat text-info mr-1"></i> <em>"Melayani Dengan Hati & Profesional"</em></span>
            </div>

            <hr class="my-4" style="max-width: 800px; opacity: 0.15;">

            <!-- STATISTIK RINGKAS TOTAL DI DALAM CARD (PERSIS SEPERTI KLINIK) -->
            <div class="row justify-content-center" style="max-width: 850px; margin: 0 auto;">
                <div class="col-sm-3 col-6 mb-2">
                    <div class="p-3 rounded bg-light border text-center">
                        <div class="text-muted text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Registrasi Hari Ini</div>
                        <div class="h3 font-weight-bold text-info mb-0 mt-1">{{ number_format($pasienHariIni, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-sm-3 col-6 mb-2">
                    <div class="p-3 rounded bg-light border text-center">
                        <div class="text-muted text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Rawat Jalan (Poli)</div>
                        <div class="h3 font-weight-bold text-primary mb-0 mt-1">{{ number_format($ralanHariIni, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-sm-3 col-6 mb-2">
                    <div class="p-3 rounded bg-light border text-center">
                        <div class="text-muted text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Pasien Rawat Inap</div>
                        <div class="h3 font-weight-bold text-success mb-0 mt-1">{{ number_format($ranapAktif, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-sm-3 col-6 mb-2">
                    <div class="p-3 rounded bg-light border text-center">
                        <div class="text-muted text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Pemeriksaan Lab</div>
                        <div class="h3 font-weight-bold text-warning mb-0 mt-1">{{ number_format($labHariIni, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
    .hover-shadow:hover {
        background-color: #f8fafc !important;
        border-color: #0284c7 !important;
        transform: translateY(-2px);
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.12) !important;
    }
</style>
@endsection
