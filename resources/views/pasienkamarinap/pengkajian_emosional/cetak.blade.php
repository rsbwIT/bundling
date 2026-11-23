<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengkajian Gangguan Emosional - {{ $pasien->nm_pasien }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm 12mm 10mm 12mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 5px;
        }
        .header-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .logo-rs {
            text-align: center;
            width: 25%;
            border-right: 1.5px solid #000;
        }
        .title-doc {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            width: 40%;
            border-right: 1.5px solid #000;
            vertical-align: middle !important;
        }
        .identitas-pasien {
            width: 35%;
            font-size: 10px;
        }
        .identitas-pasien table {
            width: 100%;
            border-collapse: collapse;
        }
        .identitas-pasien td {
            padding: 1px 2px;
            border: none;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 8px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .section-header {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 4px 6px;
            border: 1px solid #000;
        }
        .box-check {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            text-align: center;
            line-height: 10px;
            font-size: 9px;
            margin-right: 3px;
            font-weight: bold;
        }
        .item-inline {
            display: inline-block;
            margin-right: 12px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .signature-table td {
            text-align: center;
            width: 50%;
            vertical-align: top;
            padding: 5px;
        }
        .underline-name {
            display: inline-block;
            min-width: 180px;
            border-bottom: 1px solid #000;
            font-weight: bold;
            padding-bottom: 2px;
            margin-top: 50px;
        }
        .text-dots {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 150px;
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Cetak Browser -->
    <div class="no-print" style="position: fixed; top: 15px; right: 15px; z-index: 9999; background: #fff; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.15);">
        <button onclick="window.print()" style="padding: 8px 16px; background: #0d6efd; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" style="padding: 8px 12px; background: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px;">
            Tutup
        </button>
    </div>

    <!-- KOP FORMULIR & IDENTITAS PASIEN -->
    <table class="header-table">
        <tr>
            <td class="logo-rs">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                    @if(!empty($setting->logo))
                        <img src="data:image/jpeg;base64,{{ base64_encode($setting->logo) }}" alt="Logo RS" style="height: 48px; max-width: 48px; object-fit: contain;">
                    @else
                        <img src="{{ route('hospital.logo') }}" alt="Logo RS" style="height: 48px; max-width: 48px; object-fit: contain;" onerror="this.src='/img/rs.png'">
                    @endif
                    <div style="text-align: left;">
                        <div style="font-weight: bold; font-size: 11px; text-transform: uppercase;">{{ $setting->nama_instansi ?? 'RS BUMI WARAS' }}</div>
                        <div style="font-size: 8px; line-height: 1.2;">{{ $setting->alamat_instansi ?? 'Jl. Wolter Monginsidi No. 235' }}</div>
                        <div style="font-size: 8px; line-height: 1.2;">{{ $setting->kabupaten ?? 'Bandar Lampung' }}, {{ $setting->propinsi ?? 'Lampung' }}</div>
                        <div style="font-size: 8px; line-height: 1.2;">Telp. {{ $setting->kontak ?? '(0721) 254586' }}</div>
                    </div>
                </div>
            </td>
            <td class="title-doc">
                PENGKAJIAN GANGGUAN EMOSIONAL<br>ATAU PSIKIATRI
            </td>
            <td class="identitas-pasien">
                <table>
                    <tr>
                        <td width="30%">No. RM</td>
                        <td width="5%">:</td>
                        <td><strong>{{ $pasien->no_rkm_medis }}</strong></td>
                    </tr>
                    <tr>
                        <td>Nama Pasien</td>
                        <td>:</td>
                        <td><strong>{{ $pasien->nm_pasien }}</strong> ({{ $pasien->jk }})</td>
                    </tr>
                    <tr>
                        <td>Tgl Lahir / Umur</td>
                        <td>:</td>
                        <td>
                            @php
                                $tglLahir = \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d-m-Y');
                                $umur = \Carbon\Carbon::parse($pasien->tgl_lahir)->age;
                            @endphp
                            {{ $tglLahir }} ({{ $umur }} Thn)
                        </td>
                    </tr>
                    <tr>
                        <td>Ruang / Kamar</td>
                        <td>:</td>
                        <td>{{ $pasien->nm_bangsal ?? '-' }} / {{ $pasien->kd_kamar ?? '-' }} ({{ $pasien->kelas ?? '-' }})</td>
                    </tr>
                    <tr>
                        <td>No. Rawat</td>
                        <td>:</td>
                        <td>{{ $pasien->no_rawat }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- TANGGAL DAN SUMBER INFORMASI -->
    <div style="margin-bottom: 6px; font-size: 10.5px;">
        <span style="font-weight: bold;">Tanggal Pengkajian:</span> 
        {{ isset($pengkajian->tanggal) ? \Carbon\Carbon::parse($pengkajian->tanggal)->format('d-m-Y H:i') : date('d-m-Y H:i') }}
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span style="font-weight: bold;">Informasi diperoleh dari:</span> 
        @php
            $info = $pengkajian->informasi_dari ?? 'Pasien';
        @endphp
        <span class="item-inline"><span class="box-check">{!! $info == 'Pasien' ? '&#10003;' : '&nbsp;' !!}</span> Pasien</span>
        <span class="item-inline"><span class="box-check">{!! $info == 'Keluarga pasien' ? '&#10003;' : '&nbsp;' !!}</span> Keluarga Pasien</span>
        <span class="item-inline"><span class="box-check">{!! $info == 'Orang lain' ? '&#10003;' : '&nbsp;' !!}</span> Orang Lain</span>
    </div>

    <!-- ISI DOKUMEN -->
    <table class="main-table">
        <!-- A. ALASAN MASUK -->
        <tr>
            <td colspan="2" class="section-header">A. ALASAN MASUK RUMAH SAKIT</td>
        </tr>
        <tr>
            <td colspan="2" style="min-height: 45px; padding: 6px 8px;">
                {{ $pengkajian->alasan_masuk ?? '-' }}
            </td>
        </tr>

        <!-- B. FAKTOR PREDISPOSISI -->
        <tr>
            <td colspan="2" class="section-header">B. FAKTOR PREDISPOSISI</td>
        </tr>
        <tr>
            <td width="35%">1. Riwayat Gangguan Jiwa di Masa Lalu</td>
            <td width="65%">
                @php $rJiwa = $pengkajian->predisposisi_riwayat_jiwa_lalu ?? 'Tidak'; @endphp
                <span class="item-inline"><span class="box-check">{!! $rJiwa == 'Ya' ? '&#10003;' : '&nbsp;' !!}</span> Ya</span>
                <span class="item-inline"><span class="box-check">{!! $rJiwa == 'Tidak' ? '&#10003;' : '&nbsp;' !!}</span> Tidak</span>
            </td>
        </tr>
        <tr>
            <td>2. Pengobatan Sebelumnya</td>
            <td>
                @php $rObat = $pengkajian->predisposisi_pengobatan_sebelumnya ?? 'Tidak berhasil'; @endphp
                <span class="item-inline"><span class="box-check">{!! $rObat == 'Berhasil' ? '&#10003;' : '&nbsp;' !!}</span> Berhasil</span>
                <span class="item-inline"><span class="box-check">{!! $rObat == 'Kurang berhasil' ? '&#10003;' : '&nbsp;' !!}</span> Kurang Berhasil</span>
                <span class="item-inline"><span class="box-check">{!! $rObat == 'Tidak berhasil' ? '&#10003;' : '&nbsp;' !!}</span> Tidak Berhasil</span>
            </td>
        </tr>
        <tr>
            <td>3. Riwayat Trauma Masa Lalu</td>
            <td>
                @php 
                    $trauma = $pengkajian->predisposisi_trauma_masa_lalu ?? [];
                @endphp
                <span class="item-inline"><span class="box-check">{!! in_array('Aniaya Fisik', $trauma) ? '&#10003;' : '&nbsp;' !!}</span> Aniaya Fisik</span>
                <span class="item-inline"><span class="box-check">{!! in_array('Aniaya Seksual', $trauma) ? '&#10003;' : '&nbsp;' !!}</span> Aniaya Seksual</span>
                <span class="item-inline"><span class="box-check">{!! in_array('Penolakan', $trauma) ? '&#10003;' : '&nbsp;' !!}</span> Penolakan</span>
                <span class="item-inline"><span class="box-check">{!! in_array('Kekerasan dalam Rumah Tangga', $trauma) ? '&#10003;' : '&nbsp;' !!}</span> KDRT</span>
                <span class="item-inline"><span class="box-check">{!! in_array('Tindakan Kriminal', $trauma) ? '&#10003;' : '&nbsp;' !!}</span> Tindakan Kriminal</span>
                <span class="item-inline"><span class="box-check">{!! in_array('Lain-lain', $trauma) ? '&#10003;' : '&nbsp;' !!}</span> Lain-lain</span>
                @if(!empty($pengkajian->predisposisi_trauma_lain_ket))
                    <div style="margin-top: 3px; font-size: 10px;"><em>Keterangan: {{ $pengkajian->predisposisi_trauma_lain_ket }}</em></div>
                @endif
            </td>
        </tr>
        <tr>
            <td>4. Riwayat Anggota Keluarga Gangguan Jiwa</td>
            <td>
                @php $rKel = $pengkajian->predisposisi_riwayat_keluarga ?? 'Tidak ada'; @endphp
                <span class="item-inline"><span class="box-check">{!! $rKel == 'Ada' ? '&#10003;' : '&nbsp;' !!}</span> Ada</span>
                <span class="item-inline"><span class="box-check">{!! $rKel == 'Tidak ada' ? '&#10003;' : '&nbsp;' !!}</span> Tidak Ada</span>
            </td>
        </tr>
        <tr>
            <td>5. Pengalaman Masa Lalu Kurang Menyenangkan</td>
            <td>
                {{ $pengkajian->predisposisi_pengalaman_buruk ?? '-' }}
            </td>
        </tr>

        <!-- HUBUNGAN SOSIAL & SPIRITUAL -->
        <tr>
            <td colspan="2" class="section-header">C. HUBUNGAN SOSIAL & SPIRITUAL</td>
        </tr>
        <tr>
            <td>6. Hubungan Sosial</td>
            <td>
                <div><strong>a. Orang yang berarti:</strong> {{ $pengkajian->sosial_orang_berarti ?? '-' }}</div>
                <div style="margin-top: 3px;"><strong>b. Hambatan berhubungan:</strong> {{ $pengkajian->sosial_hambatan_berhubungan ?? '-' }}</div>
            </td>
        </tr>
        <tr>
            <td>7. Spiritual</td>
            <td>
                <div><strong>a. Nilai dan keyakinan:</strong> {{ $pengkajian->spiritual_nilai_keyakinan ?? '-' }}</div>
                <div style="margin-top: 3px;"><strong>b. Kegiatan ibadah:</strong> {{ $pengkajian->spiritual_kegiatan_ibadah ?? '-' }}</div>
            </td>
        </tr>

        <!-- STATUS MENTAL -->
        <tr>
            <td colspan="2" class="section-header">D. STATUS MENTAL</td>
        </tr>
        <tr>
            <td>8. Kesadaran</td>
            <td>
                @php $kes = $pengkajian->mental_kesadaran ?? 'Composmentis'; @endphp
                <span class="item-inline"><span class="box-check">{!! $kes == 'Composmentis' ? '&#10003;' : '&nbsp;' !!}</span> Composmentis</span>
                <span class="item-inline"><span class="box-check">{!! $kes == 'Apatis' ? '&#10003;' : '&nbsp;' !!}</span> Apatis</span>
                <span class="item-inline"><span class="box-check">{!! $kes == 'Somnolen' ? '&#10003;' : '&nbsp;' !!}</span> Somnolen</span>
                <span class="item-inline"><span class="box-check">{!! $kes == 'Sopor' ? '&#10003;' : '&nbsp;' !!}</span> Sopor</span>
                <span class="item-inline"><span class="box-check">{!! $kes == 'Coma' ? '&#10003;' : '&nbsp;' !!}</span> Coma</span>
            </td>
        </tr>
        <tr>
            <td>9. Penampilan</td>
            <td>
                @php $pen = $pengkajian->mental_penampilan ?? 'Rapi'; @endphp
                <span class="item-inline"><span class="box-check">{!! $pen == 'Rapi' ? '&#10003;' : '&nbsp;' !!}</span> Rapi</span>
                <span class="item-inline"><span class="box-check">{!! $pen == 'Tidak Rapi' ? '&#10003;' : '&nbsp;' !!}</span> Tidak Rapi</span>
                <span class="item-inline"><span class="box-check">{!! $pen == 'Penggunaan pakaian tidak sesuai' ? '&#10003;' : '&nbsp;' !!}</span> Pakaian tidak sesuai</span>
                @if(!empty($pengkajian->mental_penampilan_ket))
                    <div style="margin-top: 2px;"><em>Ket: {{ $pengkajian->mental_penampilan_ket }}</em></div>
                @endif
            </td>
        </tr>
        <tr>
            <td>10. Pembicaraan</td>
            <td>
                @php $pem = $pengkajian->mental_pembicaraan ?? 'Cepat'; @endphp
                <span class="item-inline"><span class="box-check">{!! $pem == 'Cepat' ? '&#10003;' : '&nbsp;' !!}</span> Cepat</span>
                <span class="item-inline"><span class="box-check">{!! $pem == 'Keras' ? '&#10003;' : '&nbsp;' !!}</span> Keras</span>
                <span class="item-inline"><span class="box-check">{!! $pem == 'Gagap' ? '&#10003;' : '&nbsp;' !!}</span> Gagap</span>
                <span class="item-inline"><span class="box-check">{!! $pem == 'Apatis' ? '&#10003;' : '&nbsp;' !!}</span> Apatis</span>
                <span class="item-inline"><span class="box-check">{!! $pem == 'Lambat' ? '&#10003;' : '&nbsp;' !!}</span> Lambat</span>
                <span class="item-inline"><span class="box-check">{!! $pem == 'Membisu' ? '&#10003;' : '&nbsp;' !!}</span> Membisu</span>
                @if(!empty($pengkajian->mental_pembicaraan_ket))
                    <div style="margin-top: 2px;"><em>Ket: {{ $pengkajian->mental_pembicaraan_ket }}</em></div>
                @endif
            </td>
        </tr>
        <tr>
            <td>11. Persepsi (Halusinasi / Ilusi)</td>
            <td>
                @php $per = $pengkajian->mental_persepsi ?? []; @endphp
                <span class="item-inline"><span class="box-check">{!! in_array('Pendengaran', $per) ? '&#10003;' : '&nbsp;' !!}</span> Pendengaran</span>
                <span class="item-inline"><span class="box-check">{!! in_array('Penglihatan', $per) ? '&#10003;' : '&nbsp;' !!}</span> Penglihatan</span>
                <span class="item-inline"><span class="box-check">{!! in_array('Perabaan', $per) ? '&#10003;' : '&nbsp;' !!}</span> Perabaan</span>
                <span class="item-inline"><span class="box-check">{!! in_array('Pengecapan', $per) ? '&#10003;' : '&nbsp;' !!}</span> Pengecapan</span>
                <span class="item-inline"><span class="box-check">{!! in_array('Penghidu', $per) ? '&#10003;' : '&nbsp;' !!}</span> Penghidu</span>
            </td>
        </tr>
        <tr>
            <td>12. Alam Perasaan</td>
            <td>
                @php $ala = $pengkajian->mental_alam_perasaan ?? 'Sedih'; @endphp
                <span class="item-inline"><span class="box-check">{!! $ala == 'Sedih' ? '&#10003;' : '&nbsp;' !!}</span> Sedih</span>
                <span class="item-inline"><span class="box-check">{!! $ala == 'Putus asa' ? '&#10003;' : '&nbsp;' !!}</span> Putus Asa</span>
                <span class="item-inline"><span class="box-check">{!! $ala == 'Gembira berlebihan' ? '&#10003;' : '&nbsp;' !!}</span> Gembira Berlebihan</span>
                <span class="item-inline"><span class="box-check">{!! $ala == 'Ketakutan' ? '&#10003;' : '&nbsp;' !!}</span> Ketakutan</span>
                <span class="item-inline"><span class="box-check">{!! $ala == 'Khawatir' ? '&#10003;' : '&nbsp;' !!}</span> Khawatir</span>
                @if(!empty($pengkajian->mental_alam_perasaan_ket))
                    <div style="margin-top: 2px;"><em>Ket: {{ $pengkajian->mental_alam_perasaan_ket }}</em></div>
                @endif
            </td>
        </tr>

        <!-- E. MASALAH KEPERAWATAN -->
        <tr>
            <td colspan="2" class="section-header">E. MASALAH KEPERAWATAN</td>
        </tr>
        <tr>
            <td colspan="2">
                @php 
                    $masalah = $pengkajian->masalah_keperawatan ?? [];
                    $semuaMasalah = [
                        'Ansietas', 'Harga Diri Rendah', 'Gangguan Citra Tubuh', 
                        'Koping Individu Tidak Efektif', 'Ketidakberdayaan', 'Keputusasaan',
                        'Gangguan Pola Tidur', 'Isolasi Sosial', 'Defisit Perawatan Diri',
                        'Risiko Perilaku Kekerasan', 'Risiko Bunuh Diri', 'Gangguan Persepsi Sensori: Halusinasi'
                    ];
                @endphp
                <table style="width: 100%; border: none;">
                    @foreach(array_chunk($semuaMasalah, 3) as $barisMasalah)
                        <tr>
                            @foreach($barisMasalah as $itemM)
                                <td style="border: none; padding: 2px 4px; width: 33.3%;">
                                    <span class="box-check">{!! in_array($itemM, $masalah) ? '&#10003;' : '&nbsp;' !!}</span> {{ $itemM }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
                @if(!empty($pengkajian->masalah_keperawatan_lain_ket))
                    <div style="margin-top: 5px; font-size: 10px;">
                        <strong>Masalah Lainnya:</strong> {{ $pengkajian->masalah_keperawatan_lain_ket }}
                    </div>
                @endif
            </td>
        </tr>

        <!-- F. RENCANA KEPERAWATAN -->
        <tr>
            <td colspan="2" class="section-header">F. RENCANA KEPERAWATAN</td>
        </tr>
        <tr>
            <td colspan="2" style="min-height: 70px; padding: 6px 8px; white-space: pre-line;">
                {{ $pengkajian->rencana_keperawatan ?? '-' }}
            </td>
        </tr>
    </table>

    <!-- TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td>
                <div>Bandar Lampung, {{ isset($pengkajian->tgl_verifikasi_dpjp) ? \Carbon\Carbon::parse($pengkajian->tgl_verifikasi_dpjp)->format('d-m-Y') : date('d-m-Y') }}</div>
                <div style="font-weight: bold; margin-top: 4px;">Dokter DPJP / Verifikator</div>
                <div class="underline-name">{{ $pengkajian->nama_dokter_dpjp ?? ($pasien->nm_dokter ?? '................................................') }}</div>
            </td>
            <td>
                <div>Bandar Lampung, {{ isset($pengkajian->tgl_pengkajian_petugas) ? \Carbon\Carbon::parse($pengkajian->tgl_pengkajian_petugas)->format('d-m-Y') : date('d-m-Y') }}</div>
                <div style="font-weight: bold; margin-top: 4px;">Perawat / Petugas Pengkaji</div>
                <div class="underline-name">{{ $pengkajian->nama_petugas ?? '................................................' }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
