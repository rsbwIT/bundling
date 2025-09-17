<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Biometrik Rajal</title>
    <style>
        /* 🔹 Aturan ukuran kertas A4 */
        @page {
            size: A4;
            margin: 20mm 15mm 20mm 15mm; /* atas, kanan, bawah, kiri */
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            opacity: 0.08;
            z-index: -1;
        }

        @media print {
            body { width: 210mm; height: 297mm; }
        }

        .kop-surat { text-align: center; margin-bottom: 10px; }
        .kop-surat h2 { margin: 0; color: green; font-size: 16pt; font-weight: bold; }
        .kop-surat h3 { margin: 0; font-size: 13pt; }
        .kop-surat p { margin: 0; font-size: 10pt; }

        .nomor-surat { margin-top: 15px; margin-bottom: 15px; }
        .isi { text-align: justify; }

        table.data-pasien { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-pasien td { padding: 4px 6px; vertical-align: top; }

        /* 🔹 Tanda tangan berdampingan */
        .ttd { width: 100%; margin-top: 40px; display: flex; justify-content: space-between; }
        .ttd div { text-align: center; width: 48%; }
    </style>
</head>
<body onload="window.print()">

    {{-- 🔹 Watermark --}}
    <img src="{{ asset('img/bw2.png') }}" class="watermark">

    {{-- 🔹 Kop Surat --}}
    <div class="kop-surat">
        <table width="100%">
            <tr>
                <td width="15%" align="center">
                    <img src="{{ asset('img/bw2.png') }}" alt="Logo" width="80">
                </td>
                <td width="70%" align="center">
                    <h2>RUMAH SAKIT BUMI WARAS</h2>
                    <h3>Jalan Wolter Monginsidi No.235 - Bandar Lampung</h3>
                    <p>Telp. (0721) 254589 – 261122 (Hunting). Fax (0721) 257926 -254499</p>
                    <p>Email: rs.bumiwaras@yahoo.com | Pemilik: PT Andal Waras</p>
                </td>
                <td width="15%" align="center">
                    <img src="{{ asset('img/kars.png') }}" alt="Akreditasi" style="width:100px; height:auto;">
                </td>
            </tr>
        </table>
        <hr style="border: 1px solid #000;">
    </div>

    {{-- 🔹 Nomor & Perihal --}}
    <div class="nomor-surat">
        <table width="100%">
            <tr>
                <td width="10%">Nomor</td>
                <td>: {{ $nomorSurat }}</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: Surat Keterangan Tidak Bisa Melakukan Face ID dan Sidik Jari</td>
            </tr>
        </table>
    </div>

    {{-- 🔹 Alamat Tujuan --}}
    <p>
        Kepada Yth.<br>
        Kepala Cabang BPJS Kesehatan<br>
        Kantor Cabang Bandar Lampung<br>
        Di Tempat
    </p>

    {{-- 🔹 Isi Surat --}}
    <p class="isi">
        Dengan hormat,<br>
        Bersama surat ini kami sampaikan bahwa benar telah terjadi pasien rawat jalan tidak bisa melakukan Face ID dan Sidik Jari
        pada tanggal <b>{{ \Carbon\Carbon::parse($pasien->tgl_registrasi)->translatedFormat('d F Y') }}</b>
        di bagian pendaftaran Rumah Sakit Bumi Waras,
        karena Face ID dan Sidik Jari tidak terbaca sehingga dilakukan pengajuan <i>Approval SEP</i> di V-Claim.
    </p>

    <p>Adapun data pasien tersebut adalah sebagai berikut:</p>

    <table class="data-pasien">
        <tr>
            <td width="25%">Nama</td>
            <td>: {{ strtoupper($pasien->nama) }}</td>
        </tr>
        <tr>
            <td>No. Kartu BPJS</td>
            <td>: {{ $pasien->no_kartu_bpjs }}</td>
        </tr>
        <tr>
            <td>No. SEP</td>
            <td>: {{ $pasien->no_sep }}</td>
        </tr>
        <tr>
            <td>Poli Tujuan</td>
            <td>: {{ $pasien->poli_tujuan }}</td>
        </tr>
        <tr>
            <td>Diagnosis</td>
            <td>: {{ $pasien->diagnosis }}</td>
        </tr>
    </table>

    <p class="isi">
        Demikian kami sampaikan atas kerja sama yang baik kami ucapkan terima kasih.
    </p>

    <table width="100%" style="margin-top:50px;">
    <tr>
        <!-- Kolom Pasien -->
        <td width="50%" align="center">
            Pasien <br>
            <div style="margin:20px 0; height:100px;">
                @if(!empty($pasien->file_ttd))
                    <img src="{{ asset('storage/ttd/'.$pasien->file_ttd) }}"
                         alt="TTD Pasien"
                         style="width:150px; height:150px; object-fit:contain; display:block; margin:auto;">
                @endif
            </div>
            <b>{{ strtoupper($pasien->nama) }}</b>
        </td>

        <!-- Kolom Dokter -->
        <td width="50%" align="center">
            Bandar Lampung, {{ \Carbon\Carbon::parse($pasien->tgl_registrasi)->translatedFormat('d F Y') }}<br>
            DPJP yang Merawat,<br>
            <div style="margin:20px 0; height:100px;">
                {!! QrCode::size(100)->generate($pasien->nama_dokter) !!}
            </div>
            <b>{{ $pasien->nama_dokter }}</b>
        </td>
    </tr>
</table>

    </div>

</body>
</html>
