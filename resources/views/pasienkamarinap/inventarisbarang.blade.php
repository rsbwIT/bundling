<!DOCTYPE html>
<html>
<head>
    <title>Cetak Label Inventaris</title>
    <style>
        @page {
            size: 80mm 40mm;
            margin: 0;
        }

        /* Print-only rules */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
            }

            .label-container {
                display: block;
            }

            .label {
                page-break-before: always;
                page-break-inside: avoid;
                break-inside: avoid;
                width: 80mm;
                height: 40mm;
                margin: 0 auto;
            }
        }

        /* Screen-only rules */
        @media screen {
            .label-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: center; /* rata tengah horizontal */
                gap: 12px;
                padding: 20px;
                max-width: 100%;
            }

            .label {
                width: 80mm;
                height: 40mm;
            }
        }

        body {
            font-family: Arial, sans-serif;
            background: #fff;
        }

        .filter-form {
            margin: 20px 0;
            text-align: center;
        }

        .filter-form input[type="text"] {
            padding: 8px 12px;
            width: 280px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .btn-cari, .print-all-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
            transition: all 0.3s ease-in-out;
        }

        .btn-cari {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            font-size: 14px;
        }

        .print-all-btn {
            background-image: linear-gradient(to right, #1d976c, #93f9b9);
            font-size: 15px;
            margin-left: 10px;
        }

        .btn-cari:hover,
        .print-all-btn:hover {
            transform: scale(1.05);
        }

        .label {
            border: 1px solid teal;
            box-sizing: border-box;
            padding: 4mm;
            text-align: center;
            overflow: hidden;
        }

        .label .header-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .label img.logo {
            height: 26px;
        }

        .label .text-header {
            text-align: left;
        }

        .label .text-header .header {
            font-size: 10px;
            font-weight: bold;
            margin: 0;
        }

        .label .text-header .subheader {
            font-size: 8px;
            margin: 0;
            line-height: 1.1;
        }

        .label hr {
            margin: 2px 0;
            border: 0;
            border-top: 1px solid teal;
        }

        .label .barcode {
            margin: 2px 0;
        }

        .label .barcode img {
            height: 10mm;
            max-width: 100%;
        }

        .label .code {
            font-size: 11px;
            font-weight: bold;
        }

        .label .desc {
            font-size: 10px;
            font-weight: bold;
        }

        .label .info {
            font-size: 9px;
            text-align: left;
            margin-top: 3px;
            line-height: 1.2;
        }

        .no-result {
            text-align: center;
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="filter-form no-print">
        <form method="GET" action="">
            <input type="text" name="cari" placeholder="Cari barang / ruangan / no inventaris" value="{{ request('cari') }}">
            <button type="submit" class="btn-cari">🔍 Cari</button>
            <button type="button" onclick="window.print()" class="print-all-btn">🖨️ Cetak Semua</button>
        </form>
    </div>

    <div class="label-container">
        @forelse($dataInventaris as $item)
            <div class="label">
                <div class="header-section">
                    <img src="{{ asset('img/bw2.png') }}" class="logo" alt="Logo RS">
                    <div class="text-header">
                        <p class="header">RS. BUMI WARAS</p>
                        <p class="subheader">Jl. Wolter Monginsidi No. 235<br>(0721) 254589</p>
                    </div>
                </div>

                <hr>

                <div class="barcode">
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($item->no_inventaris, 'C128') }}" style="height: 8mm; max-width: 100%;">
                </div>

                <div class="code">{{ $item->no_inventaris }}</div>
                <div class="desc">{{ strtoupper($item->nama_barang) }}</div>

                <div class="info">
                    POSISI: {{ $item->nama_ruang ?? '-' }}<br>
                    RAK: - - -<br>
                    TGL: {{ $item->tgl_pengadaan ? \Carbon\Carbon::parse($item->tgl_pengadaan)->format('d-m-Y') : '-' }}
                </div>
            </div>
        @empty
            <p class="no-result">Tidak ada data ditemukan.</p>
        @endforelse
    </div>

</body>
</html>
