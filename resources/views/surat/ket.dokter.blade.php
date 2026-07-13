@extends('layout.layoutDashboard')
@section('title', 'Surat Keterangan Dokter')

@section('konten')
<div class="container-fluid py-3">

    <style>
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 20mm 15mm 20mm;
            position: relative;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #000000;
        }

        /* ── Header ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-logo {
            width: 80px;
            vertical-align: middle;
        }

        .header-logo img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .header-info {
            vertical-align: middle;
            padding-left: 10px;
        }

        .header-info .rs-name {
            font-size: 14px;
            font-weight: bold;
            color: #b22222;
        }

        .header-info .rs-addr {
            font-size: 11px;
            font-weight: bold;
        }

        .header-info .rs-contact {
            font-size: 10px;
        }

        .stiker-box {
            width: 220px;
            vertical-align: top;
        }

        .stiker-box .box {
            border: 1px solid #000;
            border-radius: 4px;
            width: 210px;
            padding: 6px;
            font-size: 9px;
            line-height: 1.3;
            text-align: left;
            margin-left: auto;
        }

        .stiker-box .box table {
            width: 100%;
            border-collapse: collapse;
        }

        .stiker-box .box td {
            font-size: 9px;
            line-height: 1.3;
            padding: 1px 0;
            color: #000;
            vertical-align: top;
        }

        .hr-double {
            border: none;
            border-top: 3px double #000;
            margin: 8px 0 12px;
        }

        /* ── Judul ── */
        .judul {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .nomor {
            text-align: center;
            font-size: 11px;
            margin-bottom: 16px;
        }

        /* ── Body ── */
        .body-text {
            font-size: 12px;
            line-height: 1.8;
            margin-bottom: 6px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 10px;
        }

        .info-table td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 12px;
            line-height: 1.7;
        }

        .info-table .label { width: 130px; }
        .info-table .colon { width: 12px; }

        .anjuran-text {
            font-size: 12px;
            margin: 6px 0 4px;
        }

        .anjuran-row {
            display: flex;
            gap: 8px;
            align-items: flex-end;
            margin-bottom: 4px;
        }

        .anjuran-no {
            min-width: 20px;
            font-size: 12px;
        }

        .anjuran-line {
            flex: 1;
            border-bottom: 1px dotted #000;
            height: 18px;
        }

        .penutup {
            font-size: 12px;
            margin-top: 14px;
            margin-bottom: 30px;
        }

        /* ── Footer TTD ── */
        .ttd-section {
            text-align: right;
            margin-top: 10px;
        }

        .ttd-section p {
            font-size: 12px;
            line-height: 1.8;
        }

        .input-print {
            border: none;
            border-bottom: 1px dotted #000;
            outline: none;
            font-family: Arial, sans-serif;
            font-size: 12px;
            background: #fbfbfb;
            padding: 0 4px;
            text-align: inherit;
        }

        /* ── Print Media ── */
        @media print {
            .main-sidebar, .main-header, .main-footer, .no-print {
                display: none !important;
            }
            .content-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }
            @page { size: A4; margin: 0; }
            .page { 
                padding: 15mm 20mm 15mm 20mm; 
                box-shadow: none !important;
                border: none !important;
                background: #ffffff !important;
            }
            .input-print { border-bottom: none !important; background: transparent !important; }
            ::placeholder { color: transparent !important; }
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Top Action Bar -->
    <div class="no-print d-flex justify-content-end gap-2 mb-3 mx-auto" style="width: 210mm; max-width: 100%;">
        <button class="btn btn-success" id="btnSimpanSurat">
            <i class="fas fa-save mr-1"></i> Simpan Isi Surat
        </button>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print mr-1"></i> Cetak Surat Dokter
        </button>
    </div>

    <div class="page">

        {{-- ── HEADER ── --}}
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <img src="{{ asset('img/rs.png') }}" alt="Logo RS"
                         onerror="this.style.display='none'">
                </td>
                <td class="header-info">
                    <div class="rs-name">RUMAH SAKIT BUMI WARAS</div>
                    <div class="rs-addr">Jalan Wolter Monginsidi No.235 - Bandar Lampung</div>
                    <div class="rs-contact">
                        Telp. (0721) 254589 – 261122 (Hunting). Fax (0721) 257926 -254499
                    </div>
                    <div class="rs-contact">Email: rs.bumiwaras@yahoo.com</div>
                </td>
                <td class="stiker-box">
                    <div class="box">
                        <table>
                            <tr>
                                <td style="width: 55px; font-weight: bold;">No. Rawat</td>
                                <td style="width: 8px;">:</td>
                                <td>{{ $data->no_rawat ?? '' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">No. RM</td>
                                <td>:</td>
                                <td>{{ $data->no_rkm_medis ?? '' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Nama</td>
                                <td>:</td>
                                <td>{{ $data->nm_pasien ?? '' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Poli</td>
                                <td>:</td>
                                <td>{{ $data->nm_poli ?? '' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Dokter</td>
                                <td>:</td>
                                <td style="font-weight: bold;">{{ $data->nm_dokter ?? '' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <hr class="hr-double">

        {{-- ── JUDUL ── --}}
        <div class="judul">SURAT KETERANGAN DOKTER</div>
        <div class="nomor">
            Nomor: {{ $nomor_surat }}
        </div>

        {{-- ── BODY ── --}}
        <p class="body-text">Yang bertanda tangan di bawah ini,</p>

        <table class="info-table">
            <tr>
                <td class="label">Nama dokter yang menerangkan</td>
                <td class="colon">:</td>
                <td><strong>{{ $data->nm_dokter ?? '' }}</strong></td>
            </tr>
        </table>

        <p class="body-text">Menerangkan bahwa &nbsp;:</p>

        <table class="info-table">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td>{{ $data->nm_pasien ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">No Rekam Medis</td>
                <td class="colon">:</td>
                <td>{{ $data->no_rkm_medis ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Usia / Tgl Lahir</td>
                <td class="colon">:</td>
                <td>
                    {{ $data->umur ?? '' }}
                    @if(!empty($data->tgl_lahir))
                        / {{ \Carbon\Carbon::parse($data->tgl_lahir)->format('d-m-Y') }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">No. KTP/Paspor</td>
                <td class="colon">:</td>
                <td>{{ $data->no_ktp ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="colon">:</td>
                <td>{{ $data->alamat ?? '' }}</td>
            </tr>
        </table>

        <p class="body-text" style="margin-top: 10px;">
            Menerangkan bahwa setelah diperiksa kesehatan badannya saat ini dalam keadaan sehat/tidak sehat.
        </p>

        <p class="body-text" style="margin-top: 6px;">
            Diberikan istirahat selama <input type="text" id="istirahat_hari" class="input-print" style="width: 40px; text-align: center;" value="{{ $isi_surat['istirahat_hari'] ?? '' }}" placeholder="......."> hari, 
            terhitung mulai tanggal <input type="text" id="tgl_mulai" class="input-print" style="width: 140px; text-align: center;" value="{{ $isi_surat['tgl_mulai'] ?? \Carbon\Carbon::parse($data->tgl_registrasi)->translatedFormat('d F Y') }}" placeholder="................"> s/d 
            <input type="text" id="tgl_sd" class="input-print" style="width: 140px; text-align: center;" value="{{ $isi_surat['tgl_sd'] ?? \Carbon\Carbon::parse($data->tgl_registrasi)->translatedFormat('d F Y') }}" placeholder="................">, 
            pasien dianjurkan untuk :
        </p>

        <div style="margin-top: 8px; margin-bottom: 12px;">
            @for ($i = 1; $i <= 5; $i++)
            <div class="anjuran-row">
                <span class="anjuran-no">{{ $i . ')' }}</span>
                <div class="anjuran-line" id="anjuran_{{ $i }}" contenteditable="true">{{ $isi_surat['anjuran_'.$i] ?? '' }}</div>
            </div>
            @endfor
        </div>

        <p class="penutup">
            Surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        {{-- ── FOOTER TTD ── --}}
        <div class="ttd-section">
            <p>
                Bandar Lampung,
                {{ \Carbon\Carbon::parse($data->tgl_registrasi ?? now())->translatedFormat('d F Y') }}
            </p>
            <p>Dokter Pemeriksa,</p>
            <div style="height: 70px; display: flex; align-items: center; justify-content: flex-end; padding-right: 30px; margin: 4px 0;">
                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG('Dikeluarkan di RUMAH SAKIT BUMI WARAS, Ditandatangani secara elektronik oleh ' . ($data->nm_dokter ?? '') . ' untuk pasien ' . ($data->nm_pasien ?? '') . ' No. Rawat ' . ($data->no_rawat ?? ''), 'QRCODE') }}" style="width: 60px; height: 60px; object-fit: contain;">
            </div>
            <p style="margin: 0; line-height: 1.5;">
                Nama Dokter: <strong>{{ $data->nm_dokter ?? '' }}</strong><br>
                SIP No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type="text" id="sip_no" class="input-print" style="width: 150px;" value="{{ $isi_surat['sip_no'] ?? '' }}" placeholder="................">
            </p>
        </div>

    </div>

</div>

{{-- SCRIPT SIMPAN ISI SURAT --}}
<script>
$(function(){
    $('#btnSimpanSurat').click(function(){

        let isiSurat = {
            istirahat_hari: $('#istirahat_hari').val(),
            tgl_mulai:      $('#tgl_mulai').val(),
            tgl_sd:         $('#tgl_sd').val(),
            anjuran_1:      $('#anjuran_1').text(),
            anjuran_2:      $('#anjuran_2').text(),
            anjuran_3:      $('#anjuran_3').text(),
            anjuran_4:      $('#anjuran_4').text(),
            anjuran_5:      $('#anjuran_5').text(),
            sip_no:         $('#sip_no').val()
        };

        $.ajax({
            url: '/surat-simpan-isi',
            type: 'POST',
            data: {
                _token:      $('meta[name="csrf-token"]').attr('content'),
                no_rawat:    '{{ $data->no_rawat }}',
                jenis_surat: 'SKD',
                isi_surat:   isiSurat
            },
            success: function(res){
                alert(res.message);
            },
            error: function(xhr){
                alert('ERROR: ' + xhr.responseText);
            }
        });
    });
});
</script>
@endsection
