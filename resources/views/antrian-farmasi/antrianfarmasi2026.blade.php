<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Display Antrian Farmasi</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #0f172a;
            color: #fff;
            display: flex;
            flex-direction: column;
            font-family: 'Arial', sans-serif;
        }

        .container-fluid {
            flex: 1;
            overflow-y: auto;
            padding-top: 20px;
        }

        header {
            background-color: #ffffff;
            padding: 15px 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #000;
            text-align: center;
            flex: 1;
            margin: 0 20px;
        }

        .jalur-card {
            border-radius: 18px;
            overflow: hidden;
            height: 100%;
            background: #1e293b;
        }

        .jalur-header {
            font-size: 28px;
            font-weight: 900;
            text-align: center;
            padding: 18px;
            color: #fff;
        }

        .antrian-box {
            background: #ffffff;
            color: #000;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0,0,0,.25);
        }

        .nomor { font-size: 72px; font-weight: 900; line-height: 1; }
        .nama { font-weight: 800; font-size: 20px; margin-top: 6px; }
        .rm { font-size: 14px; opacity: .8; }
        .status { margin-top: 12px; font-size: 30px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; }
        .status-DIPANGGIL { color: #16a34a; }
        .status-MENUNGGU  { color: #f59e0b; }
        .status-SELESAI   { color: #6b7280; }

        .badge { font-size: 14px; padding: 6px 12px; }

        .bg-a { background: #16a34a; }
        .bg-b { background: #0284c7; }
        .bg-c { background: #f59e0b; }
        .bg-d { background: #dc2626; }

        .kosong { text-align: center; padding: 60px 0; opacity: .5; font-size: 18px; }

        footer.footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #1e293b;
            padding: 12px 0;
            overflow: hidden;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.3);
        }

        .marquee-container {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-text {
            display: inline-block;
            padding-left: 100%;
            font-size: 18px;
            font-weight: 700;
            color: #f59e0b;
            animation: marquee 55s linear infinite;
        }

        @keyframes marquee {
            0%   { transform: translateX(0%); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body>

<header>
    <img src="{{ asset('img/bw2.png') }}" alt="Logo Rumah Sakit Bumi Waras" style="height:100px;">
    <h1>Antrian Farmasi</h1>
    <img src="{{ asset('img/bpjs.png') }}" alt="Logo BPJS Kesehatan" style="height:40px;">
</header>

<div class="container-fluid py-4">
    <div class="row" id="antrian-wrapper"></div>
</div>

<script>
function loadAntrian() {
    fetch("{{ url('/antrian-farmasi/data-display') }}")
        .then(res => res.json())
        .then(data => {

            const jalurList = ['A','B','C','D'];
            let html = '';

            jalurList.forEach(jalur => {
                const items = data[jalur] ?? [];
                const row = items.length ? items[0] : null;

                html += `
                <div class="col-md-3">
                    <div class="jalur-card shadow">
                        <div class="jalur-header bg-${jalur.toLowerCase()}">
                            LOKET ${jalur}
                        </div>
                        <div class="p-3">`;

                if (!row) {
                    html += `<div class="kosong">Tidak ada antrian</div>`;
                } else {
                    html += `
                        <div class="antrian-box">
                            <div class="nomor">${row.no_antrian}</div>
                            <div class="nama">${row.nm_pasien}</div>
                            <div class="rm">RM ${row.no_rkm_medis}</div>

                            <div class="status status-${row.status_panggil}">
                                ${row.status_panggil}
                            </div>

                            <div class="mt-3">
                                <span class="badge badge-primary">${row.kelompok_pj}</span>
                                <span class="badge badge-secondary">${row.jenis_obat}</span>
                            </div>
                        </div>`;
                }

                html += `
                        </div>
                    </div>
                </div>`;
            });

            document.getElementById('antrian-wrapper').innerHTML = html;
        });
}

loadAntrian();
setInterval(loadAntrian, 5000);
</script>

<footer class="footer text-white">
    <div class="marquee-container">
        <span class="marquee-text">Selamat datang di Farmasi RS Bumi Waras | Mohon menunggu panggilan sesuai nomor antrian obat | Terima kasih atas kesabaran Anda 🙏 &nbsp;&bull;&nbsp; 📋 <strong>5 JANJI LAYANAN JAMINAN KESEHATAN NASIONAL (JKN):</strong> 1. Tanpa Fotokopi Dokumen (Menerima KTP/KK/KIS/KIS Digital) &bull; 2. Pelayanan Sesuai Jadwal Praktik Dokter &bull; 3. Obat Sesuai Indikasi Medis & Formularium Nasional &bull; 4. Tanpa Biaya Tambahan di Luar Ketentuan &bull; 5. Pelayanan Ramah, Responsif, dan Informatif tanpa diskriminasi &nbsp;&bull;&nbsp; 📢 <strong>INFORMASI LAYANAN KURANG JELAS?</strong> Sampaikan kepada kami dengan Scan QR POROS (1 QR Code untuk 3 Akses Layanan: SIPP Informasi & Pengaduan, KESSAN Kesan & Pesan, dan Web Skrining Riwayat Kesehatan) | Suara Anda membantu kami memperbaiki kualitas pelayanan JKN | 📞 PANDAWA: 0811 8 165 165 | ☎️ Care Center: 165 | 📲 Mobile JKN</span>
    </div>
</footer>

</body>
</html>
