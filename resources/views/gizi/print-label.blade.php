<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Diet Gizi</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            font-family: 'Arial', 'Helvetica', sans-serif;
            color: #000;
        }

        body {
            margin: 0;
            padding: 3mm;
            width: 78mm;
            background: #fff;
        }

        .label-box {
            width: 100%;
            border: none;
            padding: 1mm;
            margin-bottom: 6mm;
            page-break-after: always;
            break-after: page;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }

        .header-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-subtitle {
            font-size: 8pt;
            font-weight: bold;
        }

        .field-group {
            margin-bottom: 2mm;
        }

        .label-title {
            font-size: 7.5pt;
            text-transform: uppercase;
            color: #333;
        }

        .patient-name {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            word-break: break-word;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            margin-bottom: 1mm;
        }

        .info-label {
            font-weight: normal;
        }

        .info-value {
            font-weight: bold;
        }

        .diet-box {
            border: none;
            background: #fff;
            padding: 2mm 0;
            text-align: center;
            margin-top: 2mm;
        }

        .diet-title {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .diet-name {
            font-size: 13pt;
            font-weight: 900;
            text-transform: uppercase;
            margin-top: 1mm;
            letter-spacing: 0.5px;
        }

        .footer-time {
            text-align: center;
            font-size: 7.5pt;
            margin-top: 2mm;
            border-top: 1px dashed #000;
            padding-top: 1.5mm;
        }

        @media print {
            body {
                width: 76mm;
                padding: 1mm 2mm 1mm 3mm; /* Tambah padding kiri agar tidak terpotong */
                margin-left: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 10px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 16px; font-weight: bold; cursor: pointer;">Cetak {{ isset($items) ? count($items) : 0 }} Label</button>
        <button onclick="window.close()" style="padding: 8px 16px; margin-left: 8px; cursor: pointer;">Tutup</button>
    </div>

    @php
        $list = isset($items) && count($items) > 0 ? $items : (isset($item) && $item ? [$item] : []);
    @endphp

    @if(count($list) > 0)
        @foreach($list as $it)
            <div class="label-box">
                <div class="header">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 2px;">
                        <img src="{{ asset('img/rs.png') }}" alt="Logo RS" style="max-height: 28px; max-width: 32px; object-fit: contain;" onerror="this.style.display='none'">
                        <div>
                            <div class="header-title">RS BUMI WARAS</div>
                            <div class="header-subtitle">INSTALASI GIZI & DIETETIK</div>
                        </div>
                    </div>
                </div>

                <div class="field-group">
                    <div class="label-title">Nama Pasien:</div>
                    <div class="patient-name">{{ $it->nm_pasien }}</div>
                </div>

                <div class="info-row">
                    <span class="info-label">No. RM:</span>
                    <span class="info-value">{{ $it->no_rkm_medis }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">No. Rawat:</span>
                    <span class="info-value">{{ $it->no_rawat }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Ruangan / Kamar:</span>
                    <span class="info-value">{{ $it->nm_bangsal ?? '-' }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Tanggal & Shift:</span>
                    <span class="info-value">{{ date('d/m/Y', strtotime($it->tgl_diberi)) }} ({{ $it->jam ?? 'Pagi' }})</span>
                </div>

                <div class="diet-box">
                    <div class="diet-title">JENIS DIET PASIEN:</div>
                    <div class="diet-name">{{ $it->nama_diet ?? 'STANDAR' }}</div>
                </div>

                <div class="footer-time">
                    Dicetak: {{ date('d-m-Y H:i') }}
                </div>
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 20px;">
            <p>Data label diet tidak ditemukan.</p>
        </div>
    @endif

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>
