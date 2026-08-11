<div wire:poll.3s="loadData">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding-top: 130px;
        }

        .header-bar {
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .header-title h1 {
            font-size: 2.6rem;
            font-weight: 700;
            color: #00796b;
            margin: 0;
            letter-spacing: 1px;
        }

        .container-antrian {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 30px;
            padding: 40px 20px 140px;
            align-items: start;
        }

        .loket-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            min-height: 400px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #d0ece7;
        }

        .loket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .loket-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #004d40;
            padding: 0.6rem 1rem;
            border-bottom: 3px solid #00796b;
            margin-bottom: 2rem;
        }

        .status-dipanggil {
            background: #00796b;
            color: #fff;
            border-radius: 15px;
            padding: 1.5rem;
            animation: pulse 1.5s infinite;
        }

        .status-menunggu {
            background: #ffca28;
            color: #000;
            border-radius: 15px;
            padding: 1.5rem;
        }

        .antrian-no {
            font-size: 5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: 2px;
        }

        .antrian-pasien {
            font-size: 2rem;
            font-weight: bold;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        /* --- Keterangan Bawah --- */
        .keterangan-bawah {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 2rem;
            font-style: italic;
            font-weight: 600;
            animation: blink 1.2s infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
        }

        .keterangan-bawah.nonracik {
            color: #f57f17;
        }

        .keterangan-bawah.racikan {
            color: #1565c0;
        }

        .keterangan-bawah .emoji {
            font-size: 3rem;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .empty {
            border: 2px dashed #00796b;
            background: rgba(0, 121, 107, 0.05);
            border-radius: 15px;
            padding: 3rem 1rem;
            color: #004d40;
            font-size: 1.6rem;
            font-weight: 600;
        }

        .empty i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 1rem;
            color: #00796b;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 121, 107, 0.6); }
            70% { box-shadow: 0 0 0 20px rgba(0, 121, 107, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 121, 107, 0); }
        }

        .footer-tv {
            background: #004d40;
            color: #fff;
            padding: 1rem 2rem;
            font-size: 1.2rem;
            position: fixed;
            bottom: 0;
            width: 100%;
            overflow: hidden;
            z-index: 999;
        }

        .footer-tv span {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: marquee 20s linear infinite;
            font-weight: 700;
            letter-spacing: 1px;
        }

        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-100%); }
        }

        /* --- Next Queue Grid --- */
        .next-queue-container {
            margin-top: auto; /* Push to the bottom to keep cards perfectly aligned */
            background: #fafafa;
            border-radius: 15px;
            padding: 1.2rem;
            border: 1px solid #e0e0e0;
        }
        .next-queue-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #546e7a;
            margin-bottom: 1rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .next-queue-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .next-queue-card {
            background: #ffffff;
            border: 1px solid #cfd8dc;
            border-radius: 10px;
            padding: 0.8rem 0.5rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }
        .next-queue-no-grid {
            display: block;
            font-size: 1.4rem;
            font-weight: 800;
            color: #00796b;
            margin-bottom: 0.2rem;
        }
        .next-queue-name-grid {
            display: block;
            font-size: 0.85rem;
            color: #455a64;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 4px;
        }
        .next-queue-empty {
            text-align: center;
            color: #90a4ae;
            font-size: 0.95rem;
            font-style: italic;
            padding: 1rem 0;
        }
        .next-queue-more {
            grid-column: span 2;
            text-align: center;
            color: #00796b;
            font-size: 0.9rem;
            font-weight: 600;
            padding-top: 0.5rem;
        }
    </style>

    <!-- Header -->
    <div class="header-bar">
        <img src="{{ asset('img/bw2.png') }}" alt="Logo RS" style="height:110px;">
        <div class="header-title text-center"><h1>ANTRIAN FARMASI</h1></div>
        <img src="{{ asset('img/bpjs.png') }}" alt="Logo BPJS" style="height:40px;">
    </div>

    <!-- Container Antrian -->
    <div class="container-antrian">
        @php
            $kelompok = collect(['NON RACIK', 'RACIKAN']);
        @endphp

        @foreach($kelompok as $jenis)
            @php
                $daftar = $antrians->where('keterangan', $jenis);
                $dipanggil = $daftar->where('status','DIPANGGIL')->first();
                
                if ($dipanggil) {
                    $utama = $dipanggil;
                    $status_utama = 'DIPANGGIL';
                    $selanjutnya = $daftar->where('status', 'MENUNGGU')->values();
                } else {
                    $utama = $daftar->where('status', 'MENUNGGU')->first();
                    $status_utama = $utama ? 'MENUNGGU' : null;
                    $selanjutnya = $utama ? $daftar->where('status', 'MENUNGGU')->slice(1)->values() : collect();
                }
            @endphp

            <div class="loket-card">
                <div class="loket-title">{{ strtoupper($jenis) }}</div>

                @if($status_utama == 'DIPANGGIL')
                    <div class="status-dipanggil">
                        <div class="antrian-no">{{ $utama->nomor_antrian }}</div>
                        <div class="antrian-pasien">{{ strtoupper($utama->nama_pasien) }}</div>
                    </div>
                    <div class="keterangan-bawah {{ $jenis == 'RACIKAN' ? 'racikan' : 'nonracik' }}">
                        📢 PANGGIL
                    </div>

                @elseif($status_utama == 'MENUNGGU')
                    <div class="status-menunggu">
                        <div class="antrian-no">{{ $utama->nomor_antrian }}</div>
                        <div class="antrian-pasien">{{ strtoupper($utama->nama_pasien) }}</div>
                    </div>
                    <div class="keterangan-bawah {{ $jenis == 'RACIKAN' ? 'racikan' : 'nonracik' }}">
                        <span class="emoji">💊</span> Obat sedang disiapkan...
                    </div>

                @else
                    <div class="empty">
                        <i class="fas fa-clipboard-list"></i>
                        📭 Tidak ada antrian saat ini
                    </div>
                @endif
                
                @if($status_utama)
                    <div class="next-queue-container">
                        <div class="next-queue-title">Antrian Selanjutnya</div>
                        @if($selanjutnya->count() > 0)
                            <div class="next-queue-grid">
                                @foreach($selanjutnya->take(4) as $next)
                                    <div class="next-queue-card">
                                        <span class="next-queue-no-grid">{{ $next->nomor_antrian }}</span>
                                        <span class="next-queue-name-grid">{{ strtoupper($next->nama_pasien) }}</span>
                                    </div>
                                @endforeach
                                @if($selanjutnya->count() > 4)
                                    <div class="next-queue-more">
                                        + {{ $selanjutnya->count() - 4 }} antrian lainnya...
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="next-queue-empty">
                                - Belum ada antrian lain -
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Footer -->
    <div class="footer-tv">
        <span>Selamat datang di Rumah Sakit Bumi Waras - Harap menunggu panggilan sesuai nomor antrian Anda.</span>
    </div>
</div>
