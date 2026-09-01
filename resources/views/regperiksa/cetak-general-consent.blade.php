<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Consent - {{ $pasien->nm_pasien ?? 'Pasien' }} ({{ $spu->no_surat }})</title>
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            color: #111;
            line-height: 1.45;
            margin: 0;
            padding: 20px 0;
        }

        .paper-container {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }

        .header-table {
            border-bottom: 2px solid #222;
            padding-bottom: 8px;
            margin-bottom: 15px;
            width: 100%;
        }

        .header-table td {
            vertical-align: middle;
        }

        .rs-name {
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            color: #000;
        }

        .rs-address {
            font-size: 12px;
            color: #333;
            line-height: 1.3;
        }

        .doc-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .doc-subtitle {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-bottom: 15px;
        }

        .patient-box {
            border: 1px solid #999;
            background-color: #fafafa;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .table-data {
            width: 100%;
            font-size: 12.5px;
        }

        .table-data td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .konten-surat {
            font-size: 12.5px;
            text-align: justify;
        }

        .konten-surat p {
            margin-bottom: 8px;
        }

        .konten-surat ol {
            margin-bottom: 8px;
            padding-left: 22px;
        }

        .konten-surat ol ol {
            padding-left: 18px;
            margin-top: 4px;
        }

        .konten-surat li {
            margin-bottom: 5px;
        }

        .indent-1 {
            padding-left: 18px;
            margin-top: 3px;
        }

        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 50%;
            font-size: 12.5px;
        }

        .signature-img {
            max-width: 170px;
            max-height: 90px;
            object-fit: contain;
            display: block;
            margin: 5px auto;
        }

        .signature-space {
            height: 75px;
        }

        .top-toolbar {
            max-width: 860px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                font-size: 11.5pt !important;
            }

            .paper-container {
                max-width: 100% !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .patient-box {
                background-color: #ffffff !important;
                border: 1px solid #000 !important;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
        }
    </style>
</head>
<body>

    {{-- TOP ACTION TOOLBAR (HIDDEN IN PRINT) --}}
    <div class="top-toolbar no-print">
        <div>
            <span class="badge badge-primary py-2 px-3">
                <i class="fas fa-file-signature mr-1"></i> No. Surat: <strong>{{ $spu->no_surat }}</strong>
            </span>
            <span class="badge badge-secondary py-2 px-2 ml-1">
                No. Rawat: {{ $spu->no_rawat }}
            </span>
        </div>
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-primary btn-sm mr-2 shadow-sm" onclick="window.print();">
                <i class="fas fa-print mr-1"></i> Cetak Dokumen
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.close();">
                <i class="fas fa-times mr-1"></i> Tutup
            </button>
        </div>
    </div>

    {{-- PAPER CONTAINER --}}
    <div class="paper-container">

        {{-- KOP RUMAH SAKIT --}}
        <table class="header-table">
            <tr>
                <td width="85" align="center" valign="middle">
                    @if(!empty($setting->logo))
                        <img width="75" height="75" src="data:image/jpeg;base64,{{ base64_encode($setting->logo) }}" alt="Logo Instansi" style="object-fit: contain;">
                    @else
                        <div style="width: 70px; height: 70px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">
                            LOGO
                        </div>
                    @endif
                </td>
                <td style="padding-left: 12px;" valign="middle">
                    <div class="rs-name">{{ $setting->nama_instansi ?? 'RUMAH SAKIT BUMI WARAS' }}</div>
                    <div class="rs-address">
                        {{ $setting->alamat_instansi ?? '' }} {{ $setting->kabupaten ?? '' }}, {{ $setting->propinsi ?? '' }}<br>
                        Telp/Kontak: {{ $setting->kontak ?? '-' }} | E-mail: {{ $setting->email ?? '-' }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- TITLE --}}
        <div class="doc-title">PERSETUJUAN UMUM (GENERAL CONSENT)</div>
        <div class="doc-subtitle">Nomor Surat: {{ $spu->no_surat }}</div>

        {{-- DATA IDENTITAS PASIEN --}}
        <div class="patient-box">
            <table class="table-data">
                <tr>
                    <td width="16%"><strong>No. Rawat</strong></td>
                    <td width="2%">:</td>
                    <td width="32%">{{ $pasien->no_rawat ?? $spu->no_rawat }}</td>
                    <td width="16%"><strong>No. Rekam Medis</strong></td>
                    <td width="2%">:</td>
                    <td width="32%"><strong>{{ $pasien->no_rkm_medis ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Nama Pasien</strong></td>
                    <td>:</td>
                    <td><strong>{{ $pasien->nm_pasien ?? '-' }}</strong></td>
                    <td><strong>Tgl Lahir / Umur</strong></td>
                    <td>:</td>
                    <td>
                        @if(!empty($pasien->tgl_lahir))
                            {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d-m-Y') }}
                        @else
                            -
                        @endif
                        ({{ $pasien->jk == 'L' ? 'Laki-laki' : ($pasien->jk == 'P' ? 'Perempuan' : '-') }} / {{ $pasien->umur ?? '-' }})
                    </td>
                </tr>
                <tr>
                    <td><strong>Alamat Pasien</strong></td>
                    <td>:</td>
                    <td colspan="4">{{ $pasien->alamat_lengkap ?? $pasien->alamat ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- KONTEN UTAMA --}}
        <div class="konten-surat">
            <p><strong>Yang bertanda tangan di bawah ini:</strong></p>
            <table class="table-data mb-3" style="margin-left: 5px;">
                <tr>
                    <td width="17%">Nama Penanggung Jawab</td>
                    <td width="2%">:</td>
                    <td width="31%"><strong>{{ $spu->nama_pj }}</strong></td>
                    <td width="17%">No. KTP / Identitas</td>
                    <td width="2%">:</td>
                    <td width="31%">{{ $spu->no_ktppj ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Umur / Jenis Kelamin</td>
                    <td>:</td>
                    <td>{{ $spu->umur_pj }} Th / {{ $spu->jkpj == 'L' ? 'Laki-laki' : ($spu->jkpj == 'P' ? 'Perempuan' : $spu->jkpj) }}</td>
                    <td>No. Telepon / HP</td>
                    <td>:</td>
                    <td>{{ $spu->no_telp ?? '-' }}</td>
                </tr>
                <tr>
                    <td valign="top">Hubungan Keluarga</td>
                    <td valign="top">:</td>
                    <td valign="top"><strong>{{ $spu->bertindak_atas }}</strong></td>
                    <td valign="top">Nilai Kepercayaan / Agama</td>
                    <td valign="top">:</td>
                    <td valign="top">{{ $spu->nilai_kepercayaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Hak Kelas</td>
                    <td>:</td>
                    <td>{{ !empty($tambahan->hak_kelas) ? $tambahan->hak_kelas : '-' }}</td>
                    <td>Permintaan Kelas</td>
                    <td>:</td>
                    <td>{{ !empty($tambahan->permintaan_kelas) ? $tambahan->permintaan_kelas : '-' }}</td>
                </tr>
            </table>

            <p>
                Dengan ini saya telah mendapatkan informasi dari petugas admisi tentang hak dan kewajiban pasien dan juga peraturan yang berlaku di {{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }} seperti yang ada dalam lampiran formulir persetujuan umum ini, yang diberikan oleh petugas admisi pasien rawat inap / rawat jalan dan saya sudah menerima serta menyetujuinya:
            </p>

            <ol>
                <li>
                    <strong>HAK KEWAJIBAN DAN TANGGUNG JAWAB PASIEN</strong>
                    <ol type="a">
                        <li>Saya menyetujui dan memberi kuasa kepada {{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }}, Dokter, Perawat, Bidan dan petugas kesehatan lainnya untuk memberikan asuhan pasien seperti dilakukannya tindakan atau prosedur diagnostik sebagai berikut: pemasangan alat kesehatan (kecuali yang memerlukan persetujuan khusus dan beresiko tinggi), penyuntikan obat-obatan, pemeriksaan radiologi, pengambilan darah untuk pemeriksaan laboratorium dan produk farmasi lainnya termasuk konsultasi medis apabila diperlukan.</li>
                        <li>Saya mengetahui bahwa setiap tindakan yang akan dilakukan dapat diterima atau ditolak oleh pasien atau penanggung jawab pasien.</li>
                        <li>Saya memberikan wewenang kepada {{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }} untuk memenuhi kebutuhan sesuai kondisi kesehatan saya termasuk merujuk ke Rumah Sakit lain apabila diperlukan.</li>
                    </ol>
                </li>

                <li>
                    <strong>KEWAJIBAN PEMBAYARAN</strong>
                    <ol type="a">
                        <li>Untuk pasien jaminan / asuransi apabila belum membawa persyaratan yang diwajibkan melengkapi dalam waktu 2 x 24 jam hari kerja, bila tidak maka dianggap sebagai pasien umum.</li>
                        <li>Saya menyatakan setuju, baik sebagai wali / sebagai pasien bahwa sesuai pertimbangan di atas pelayanan yang diberikan kepada pasien maka saya wajib untuk membayar total biaya pelayanan berdasarkan ketentuan {{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }}.</li>
                    </ol>
                </li>

                <li>
                    <strong>RAHASIA KEDOKTERAN</strong><br>
                    Saya memahami informasi yang ada di dalam diri / keluarga saya, termasuk diagnostik hasil laboratorium dan hasil pemeriksaan penunjang yang akan digunakan untuk perawatan medis, dan akan dijamin kerahasiaannya oleh rumah sakit kecuali untuk kepentingan perawatan, pengobatan, pendidikan, penelitian dan hukum.
                </li>

                <li>
                    <strong>PELEPASAN INFORMASI MEDIS</strong>
                    <ol type="a">
                        <li>
                            Saya menyetujui kepada {{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }} untuk memberikan informasi medis - administratif yang terkait dengan kepentingan saya, kepada:
                            <div class="indent-1">
                                1. <strong>{{ !empty($pelepasan1) ? $pelepasan1 : '..................................................' }}</strong><br>
                                2. <strong>{{ !empty($pelepasan2) ? $pelepasan2 : '..................................................' }}</strong>
                            </div>
                        </li>
                        <li>Saya mengetahui dan menyetujui bahwa berdasarkan Peraturan Menteri Kesehatan Nomor 24 Tahun 2022 tentang Rekam Medis, fasilitas pelayanan kesehatan wajib membuka akses dan mengirimkan data rekam medis kepada Kementerian Kesehatan melalui Platform SATU SEHAT, untuk kepentingan pelayanan kesehatan dan / rujukan.</li>
                        <li>Saya menyetujui kepada {{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }} untuk memberikan informasi medis - administratif kepada pihak ketiga yang menjamin pembayaran perawatan saya yaitu BPJS Kesehatan / BPJS Ketenagakerjaan / Jasa Raharja / Jamkeskot / Taspen / ASABRI atau asuransi lainnya.</li>
                    </ol>
                </li>

                <li>
                    <strong>BARANG PRIBADI</strong><br>
                    Saya memahami bahwa {{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }} tidak bertanggung jawab atas semua kehilangan barang-barang milik saya / keluarga saya. Saya / keluarga saya bertanggung jawab atas barang-barang berharga yang saya miliki seperti uang, perhiasan, buku cek, kartu kredit, handphone atau barang lainnya.
                </li>

                <li>
                    Saya memahami bahwa setiap keluhan yang terjadi dalam pelayanan dan pengobatan di {{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }}, akan saya sampaikan kepada petugas penerima keluhan atau petugas pada unit terkait.
                </li>

                <li>
                    Saya telah mendapat informasi tentang "Hak dan Kewajiban" di {{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }} melalui leaflet / selebaran / banner yang disediakan dan penjelasan oleh petugas Rumah Sakit.
                </li>

                <li>
                    Saya mengerti dan mengetahui tata tertib {{ $setting->nama_instansi ?? 'RS. BUMI WARAS' }}:
                    <ol type="a">
                        <li>Waktu berkunjung: Pagi jam 10.00 - 15.00 WIB | Sore jam 16.00 - 20.00 WIB</li>
                        <li>Pengunjung pasien maksimal 2 orang secara bergantian</li>
                        <li>Penunggu pasien hanya 1 orang saja dan tidak diperbolehkan meninggalkan pasien tanpa koordinasi petugas</li>
                        <li>Anak-anak di bawah usia 12 tahun sebaiknya tidak diajak ke Rumah Sakit</li>
                        <li>Pasien hanya boleh mengonsumsi makanan yang disediakan oleh Rumah Sakit demi keselamatan gizi</li>
                    </ol>
                </li>

                <li>
                    Melalui dokumen ini, saya menegaskan kembali bahwa saya mempercayakan kepada semua tenaga kesehatan rumah sakit untuk memberikan perawatan, diagnostik dan terapi kepada saya sebagai Pasien Rawat Inap, Rawat Jalan Poliklinik dan Rawat Jalan Instalasi Gawat Darurat (IGD), termasuk semua pemeriksaan penunjang, yang dibutuhkan untuk pengobatan dan tindakan yang aman kepada: <strong>{{ $spu->pengobatan_kepada }}</strong>.
                </li>
            </ol>

            <p class="mt-3">
                Demikian persetujuan ini saya buat dengan sesungguhnya dengan kesadaran penuh dan tanpa paksaan dari pihak manapun.
            </p>

            @if(!empty($tambahan->tambahan_teks))
                <div class="alert alert-light border py-2 px-3 mt-2 mb-2 font-weight-bold" style="font-size: 12px;">
                    Catatan Tambahan: {{ $tambahan->tambahan_teks }}
                </div>
            @endif

            {{-- TANDA TANGAN --}}
            <table class="signature-table">
                <tr>
                    <td>
                        <br>
                        <strong>Petugas Admisi / RS</strong>
                        <div class="signature-space">
                            {{-- Space for officer signature or stamp --}}
                        </div>
                        <strong>( {{ !empty($namaPetugas) ? $namaPetugas : '..................................................' }} )</strong>
                        @if(!empty($spu->nip) && $spu->nip != '-')
                            <div class="text-muted small">NIP/NIK: {{ $spu->nip }}</div>
                        @endif
                    </td>
                    <td>
                        Bandar Lampung, {{ \Carbon\Carbon::parse($spu->tanggal)->format('d-m-Y') }}<br>
                        <strong>Sebagai Pembuat Pernyataan</strong>
                        <div style="min-height: 75px; display: flex; align-items: center; justify-content: center;">
                            @if(!empty($photoUrl))
                                <img src="{{ $photoUrl }}" class="signature-img" alt="Tanda Tangan Pembuat Pernyataan">
                            @else
                                <div class="signature-space"></div>
                            @endif
                        </div>
                        <strong>( {{ !empty($spu->nama_pj) ? $spu->nama_pj : '..................................................' }} )</strong>
                        <div class="text-muted small">Hubungan: {{ $spu->bertindak_atas }}</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>
</html>
