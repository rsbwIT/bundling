@extends('layout.layoutpendaftaran')
@section('title', 'PENDAFTARAN 1')
@push('styles')
    @livewireStyles
    <style>
        body {
            padding-bottom: 70px;
        }

        .footer-tv {
            background: #157347;
            color: #fff;
            padding: .7rem;
            font-size: 1.2rem;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            overflow: hidden;
            z-index: 9999;
            box-shadow: 0 -3px 10px rgba(0,0,0,0.2);
        }

        .footer-tv span {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: marquee 55s linear infinite;
            font-weight: 700;
        }

        @keyframes marquee {
            0%   { transform: translateX(0%); }
            100% { transform: translateX(-100%); }
        }
    </style>
@endpush
@section('konten')
    <div class="d-flex justify-content-between align-items-center container-fluid mt-3">
        <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                    alt="Logo RS" width="120" height="120">
        <div class="pricing-header ">
            <h1 class="display-4 font-weight-bold">Antrian Poli</h1>
        </div>
        <img src="/img/bpjs.png" width="280px" height="50px" alt="" srcset="">
    </div>
    <hr>
    @livewire('antrian-poli.displaypoli')

    <!-- ✅ Running Text Normal -->
    <div class="footer-tv" wire:ignore>
        <span>Selamat datang di Rumah Sakit Bumi Waras | Layanan cepat, nyaman, dan profesional | Terima kasih telah menunggu dengan sabar 🙏 &nbsp;&bull;&nbsp; 📋 <strong>5 JANJI LAYANAN JAMINAN KESEHATAN NASIONAL (JKN):</strong> 1. Tanpa Fotokopi Dokumen (Menerima KTP/KK/KIS/KIS Digital) &bull; 2. Pelayanan Sesuai Jadwal Praktik Dokter &bull; 3. Obat Sesuai Indikasi Medis & Formularium Nasional &bull; 4. Tanpa Biaya Tambahan di Luar Ketentuan &bull; 5. Pelayanan Ramah, Responsif, dan Informatif tanpa diskriminasi &nbsp;&bull;&nbsp; 📢 <strong>INFORMASI LAYANAN KURANG JELAS?</strong> Sampaikan kepada kami dengan Scan QR POROS (1 QR Code untuk 3 Akses Layanan: SIPP Informasi & Pengaduan, KESSAN Kesan & Pesan, dan Web Skrining Riwayat Kesehatan) | Suara Anda membantu kami memperbaiki kualitas pelayanan JKN | 📞 PANDAWA: 0811 8 165 165 | ☎️ Care Center: 165 | 📲 Mobile JKN</span>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
