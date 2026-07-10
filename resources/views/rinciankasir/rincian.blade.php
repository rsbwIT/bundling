@extends('layout.layoutDashboard')
@section('title', 'Rincian Biaya Kasir')

@section('konten')
<div class="container-fluid py-3">

    <!-- Styles for Ledger Table and Invoice styling -->
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #f0f4f8;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        .rincian-wrapper {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            border: 1px solid var(--slate-200);
            width: 100%;
        }

        /* Top Action Bar */
        .actions-area {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--slate-50);
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid var(--slate-200);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #172554;
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--slate-800);
            border: 1px solid var(--slate-200);
        }

        .btn-secondary:hover {
            background: var(--slate-50);
        }

        /* Hospital Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #b22222;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .hospital-logo img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .hospital-info .rs-name {
            font-size: 15px;
            font-weight: bold;
            color: #b22222;
            line-height: 1.2;
        }

        .hospital-info .rs-addr {
            font-size: 11px;
            font-weight: bold;
            color: #000000;
            margin-top: 3px;
            line-height: 1.2;
        }

        .hospital-info .rs-contact {
            font-size: 10px;
            color: #334155;
            margin-top: 3px;
            line-height: 1.2;
        }

        .invoice-title-block {
            text-align: right;
        }

        .invoice-title-block h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 20px;
            color: var(--slate-900);
            font-weight: 800;
            text-transform: uppercase;
        }

        .invoice-title-block .nota-badge {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            font-family: monospace;
        }

        /* Patient Info Grid */
        .patient-card {
            border: 1px solid var(--slate-200);
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 20px;
            background: var(--slate-50);
        }

        .patient-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .info-table td.label {
            font-weight: 600;
            color: #475569;
            width: 110px;
            font-size: 11px;
        }

        .info-table td.colon {
            width: 15px;
            color: #94a3b8;
            text-align: center;
        }

        .info-table td.value {
            color: var(--slate-900);
            font-weight: 600;
        }

        /* Unified Ledger Table */
        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid var(--slate-200);
        }

        table.ledger-table th {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            color: var(--slate-800);
            background: var(--slate-100);
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.05em;
            padding: 8px 10px;
            border-bottom: 2px solid var(--slate-200);
            border-right: 1px solid var(--slate-200);
        }

        table.ledger-table th:last-child {
            border-right: none;
        }

        table.ledger-table td {
            padding: 6px 10px;
            border-bottom: 1px solid var(--slate-200);
            border-right: 1px solid var(--slate-200);
            vertical-align: middle;
        }

        table.ledger-table td:last-child {
            border-right: none;
        }

        /* Category Header Row */
        table.ledger-table tr.category-header td {
            background: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.03em;
            border-bottom: 2px solid var(--slate-200);
            border-top: 1px solid var(--slate-200);
            padding: 8px 10px;
        }

        table.ledger-table td.amount {
            text-align: right;
            font-family: monospace;
            font-size: 11px;
            font-weight: 600;
        }

        table.ledger-table td.qty {
            text-align: center;
            width: 60px;
        }

        table.ledger-table td.center {
            text-align: center;
        }

        /* Retur Row Style */
        table.ledger-table tr.retur-row td {
            color: #e11d48;
            background: #fff1f2;
        }

        /* Grand Summary */
        .summary-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .summary-card {
            border: 2px solid var(--primary);
            border-radius: 6px;
            padding: 10px 15px;
            width: 320px;
            background: var(--slate-50);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 700;
        }

        .summary-row .val {
            color: var(--primary);
            font-family: monospace;
            font-size: 14px;
        }

        /* Print Media optimization */
        @media print {
            .main-sidebar, .main-header, .main-footer, .no-print, .actions-area {
                display: none !important;
            }
            .content-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .rincian-wrapper {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            table.ledger-table {
                border: 1px solid #000000 !important;
            }
            table.ledger-table th {
                background: #f1f5f9 !important;
                border-bottom: 2px solid #000000 !important;
                border-right: 1px solid #000000 !important;
                color: #000000 !important;
            }
            table.ledger-table td {
                border-bottom: 1px solid #e2e8f0 !important;
                border-right: 1px solid #e2e8f0 !important;
            }
            table.ledger-table tr.category-header td {
                background: #e2e8f0 !important;
                border-bottom: 2px solid #000000 !important;
                border-top: 2px solid #000000 !important;
                color: #000000 !important;
            }
            .summary-card {
                border: 2px solid #000000 !important;
                background: #ffffff !important;
            }
            .summary-row .val {
                color: #000000 !important;
            }
        }
    </style>

    <div class="rincian-wrapper">


        <!-- Header RS -->
        <div class="invoice-header">
            <div class="brand-section">
                <div class="hospital-logo">
                    <img src="{{ asset('img/rs.png') }}" alt="Logo RS" onerror="this.style.display='none'">
                </div>
                <div class="hospital-info">
                    <div class="rs-name">RUMAH SAKIT BUMI WARAS</div>
                    <div class="rs-addr">Jalan Wolter Monginsidi No. 235 - Bandar Lampung</div>
                    <div class="rs-contact">Telp. (0721) 254589 | Email: rs.bumiwaras@yahoo.com</div>
                </div>
            </div>
            <div class="invoice-title-block">
                <h1>Rincian Kasir</h1>
                <span class="nota-badge">NO. NOTA: {{ $header->no_nota }}</span>
            </div>
        </div>

        <!-- Informasi Pasien -->
        <div class="patient-card">
            <div class="patient-grid">
                <div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nama Pasien</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $header->nm_pasien }}</td>
                        </tr>
                        <tr>
                            <td class="label">No. Rekam Medis</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $header->no_rkm_medis }}</td>
                        </tr>
                        <tr>
                            <td class="label">No. Rawat</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $header->no_rawat }}</td>
                        </tr>
                        <tr>
                            <td class="label">Alamat</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $header->alamat }}</td>
                        </tr>
                    </table>
                </div>
                <div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Jenis Bayar</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $header->png_jawab }}</td>
                        </tr>
                        <tr>
                            <td class="label">Dokter DPJP</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $header->nm_dokter }}</td>
                        </tr>
                        @if(!$isRalan)
                        <tr>
                            <td class="label">Kamar / Kelas</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $header->nm_bangsal }} ({{ $header->kd_kamar }}) / Kelas {{ $header->kelas }}</td>
                        </tr>
                        <tr>
                            <td class="label">Lama Rawat</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $header->lama }} Hari</td>
                        </tr>
                        @else
                        <tr>
                            <td class="label">Poliklinik</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $header->nm_bangsal }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        @php $grandTotal = 0; $itemNumber = 1; @endphp

        <!-- Single Unified Ledger Table -->
        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Deskripsi Rincian Transaksi / Tindakan</th>
                    <th style="width: 250px;">Pelaksana / Tanggal</th>
                    <th style="width: 60px; text-align: center;">Qty</th>
                    <th style="width: 100px; text-align: right;">Tarif Satuan (Rp)</th>
                    <th style="width: 110px; text-align: right;">Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                
                <!-- I. KAMAR & AKOMODASI (Rawat Inap saja) -->
                @if(!$isRalan && count($kamar) > 0)
                    <tr class="category-header">
                        <td colspan="6">I. SEWA KAMAR & AKOMODASI</td>
                    </tr>
                    @foreach($kamar as $k)
                        @php $sub = $k->ttl_biaya; $grandTotal += $sub; @endphp
                        <tr>
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td style="font-weight: 600;">{{ $k->nm_bangsal }} ({{ $k->kd_kamar }}) - Kelas {{ $k->kelas }}</td>
                            <td>{{ $k->tgl_masuk }} s.d {{ $k->tgl_keluar === '0000-00-00' ? 'Belum Keluar' : $k->tgl_keluar }}</td>
                            <td class="qty">{{ $k->lama }} Hari</td>
                            <td class="amount">{{ number_format($k->lama > 0 ? $sub / $k->lama : $sub, 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- II. ADMINISTRASI & AKOMODASI -->
                @if(count($administrasi) > 0 || (isset($header->biaya_reg) && $header->biaya_reg > 0))
                    <tr class="category-header">
                        <td colspan="6">II. ADMINISTRASI & AKOMODASI</td>
                    </tr>
                    @if(isset($header->biaya_reg) && $header->biaya_reg > 0)
                        @php $grandTotal += $header->biaya_reg; @endphp
                        <tr>
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>Biaya Registrasi Pasien</td>
                            <td>{{ $header->tgl_masuk ? \Carbon\Carbon::parse($header->tgl_masuk)->format('d-m-Y') : '-' }}</td>
                            <td class="qty">1</td>
                            <td class="amount">{{ number_format($header->biaya_reg, 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($header->biaya_reg, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @foreach($administrasi as $item)
                        @php 
                            $arr = (array)$item;
                            $sub = $arr['total_biaya'] > 0 ? $arr['total_biaya'] : ($arr['biaya'] * $arr['jumlah']);
                            $grandTotal += $sub;
                        @endphp
                        <tr>
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>{{ $arr['nm_perawatan'] }}</td>
                            <td>{{ $arr['tgl_perawatan'] }}</td>
                            <td class="qty">{{ $arr['jumlah'] }}</td>
                            <td class="amount">{{ number_format($arr['biaya'], 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- III. PEMERIKSAAN / KONSULTASI DOKTER -->
                @if(count($konsultasi) > 0)
                    <tr class="category-header">
                        <td colspan="6">III. PEMERIKSAAN / KONSULTASI DOKTER</td>
                    </tr>
                    @foreach($konsultasi as $item)
                        @php 
                            $arr = (array)$item;
                            $sub = $arr['total_biaya'] > 0 ? $arr['total_biaya'] : ($arr['biaya'] * $arr['jumlah']);
                            $grandTotal += $sub;
                        @endphp
                        <tr>
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>{{ $arr['nm_perawatan'] }}</td>
                            <td>{{ $arr['nm_dokter'] }} ({{ $arr['tgl_perawatan'] }})</td>
                            <td class="qty">{{ $arr['jumlah'] }}</td>
                            <td class="amount">{{ number_format($arr['biaya'], 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- IV. TINDAKAN MEDIS (DOKTER & KEPERAWATAN) -->
                @if(count($tindakanDokter) > 0 || count($tindakanPerawat) > 0)
                    <tr class="category-header">
                        <td colspan="6">IV. TINDAKAN MEDIS (DOKTER & KEPERAWATAN)</td>
                    </tr>
                    @foreach($tindakanDokter as $item)
                        @php 
                            $arr = (array)$item;
                            $sub = $arr['total_biaya'] > 0 ? $arr['total_biaya'] : ($arr['biaya'] * $arr['jumlah']);
                            $grandTotal += $sub;
                        @endphp
                        <tr>
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>{{ $arr['nm_perawatan'] }} (Dokter)</td>
                            <td>{{ $arr['tgl_perawatan'] }}</td>
                            <td class="qty">{{ $arr['jumlah'] }}</td>
                            <td class="amount">{{ number_format($arr['biaya'], 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    @foreach($tindakanPerawat as $item)
                        @php 
                            $arr = (array)$item;
                            $sub = $arr['total_biaya'] > 0 ? $arr['total_biaya'] : ($arr['biaya'] * $arr['jumlah']);
                            $grandTotal += $sub;
                        @endphp
                        <tr>
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>{{ $arr['nm_perawatan'] }} (Perawat)</td>
                            <td>{{ $arr['tgl_perawatan'] }}</td>
                            <td class="qty">{{ $arr['jumlah'] }}</td>
                            <td class="amount">{{ number_format($arr['biaya'], 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- V. PEMERIKSAAN PENUNJANG (LABORATORIUM & RADIOLOGI) -->
                @if(count($sampel) > 0 || count($lab) > 0 || count($radiologi) > 0)
                    <tr class="category-header">
                        <td colspan="6">V. PEMERIKSAAN PENUNJANG (LABORATORIUM & RADIOLOGI)</td>
                    </tr>
                    @foreach($sampel as $item)
                        @php 
                            $arr = (array)$item;
                            $sub = $arr['total_biaya'] > 0 ? $arr['total_biaya'] : ($arr['biaya'] * $arr['jumlah']);
                            $grandTotal += $sub;
                        @endphp
                        <tr>
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>{{ $arr['nm_perawatan'] }}</td>
                            <td>{{ $arr['tgl_perawatan'] }}</td>
                            <td class="qty">{{ $arr['jumlah'] }}</td>
                            <td class="amount">{{ number_format($arr['biaya'], 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    @foreach($lab as $item)
                        @php 
                            $arr = (array)$item;
                            $sub = $arr['total_biaya'] > 0 ? $arr['total_biaya'] : ($arr['biaya'] * $arr['jumlah']);
                            $grandTotal += $sub;
                        @endphp
                        <tr>
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>{{ $arr['nm_perawatan'] }}</td>
                            <td>{{ $arr['nm_dokter'] }}{{ !empty($arr['nm_petugas']) ? ' & ' . $arr['nm_petugas'] : '' }} ({{ $arr['tgl_perawatan'] }})</td>
                            <td class="qty">{{ $arr['jumlah'] }}</td>
                            <td class="amount">{{ number_format($arr['biaya'], 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    @foreach($radiologi as $item)
                        @php 
                            $arr = (array)$item;
                            $sub = $arr['total_biaya'] > 0 ? $arr['total_biaya'] : ($arr['biaya'] * $arr['jumlah']);
                            $grandTotal += $sub;
                        @endphp
                        <tr>
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>{{ $arr['nm_perawatan'] }}</td>
                            <td>{{ $arr['nm_dokter'] }}{{ !empty($arr['nm_petugas']) ? ' & ' . $arr['nm_petugas'] : '' }} ({{ $arr['tgl_perawatan'] }})</td>
                            <td class="qty">{{ $arr['jumlah'] }}</td>
                            <td class="amount">{{ number_format($arr['biaya'], 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- VI. TINDAKAN OPERASI / BEDAH -->
                @if(count($operasi) > 0)
                    <tr class="category-header">
                        <td colspan="6">VI. TINDAKAN OPERASI / BEDAH</td>
                    </tr>
                    @foreach($operasi as $op)
                        @php 
                            $opArray = (array)$op;
                            $sub = $opArray['total_biaya']; 
                            $grandTotal += $sub;
                            $isHeading = $opArray['biaya'] == 0 && $opArray['total_biaya'] == 0;
                        @endphp
                        <tr @if($isHeading) style="font-weight: bold; background: var(--slate-50);" @endif>
                            <td class="center">@if(!$isHeading) {{ $itemNumber++ }} @endif</td>
                            <td>{{ $opArray['nm_perawatan'] }}</td>
                            <td>{{ $opArray['tgl_perawatan'] }}</td>
                            <td class="qty">@if(!$isHeading) {{ $opArray['jumlah'] }} @endif</td>
                            <td class="amount">@if(!$isHeading && $opArray['biaya'] > 0) {{ number_format($opArray['biaya'], 0, ',', '.') }} @endif</td>
                            <td class="amount">@if($sub > 0) {{ number_format($sub, 0, ',', '.') }} @endif</td>
                        </tr>
                    @endforeach
                @endif

                <!-- VII. OBAT-OBATAN & BAHAN HABIS PAKAI (BHP) -->
                @if(count($obat) > 0)
                    <tr class="category-header">
                        <td colspan="6">VII. OBAT-OBATAN & BAHAN HABIS PAKAI (BHP)</td>
                    </tr>
                    @php $totalObat = 0; @endphp
                    @foreach($obat as $ob)
                        @php 
                            $obArray = (array)$ob;
                            $sub = $obArray['total_bersih']; 
                            $grandTotal += $sub;
                            $totalObat += $sub;
                            $isRetur = strpos($obArray['nama_brng'], '(RETUR)') !== false;
                        @endphp
                        <tr class="{{ $isRetur ? 'retur-row' : '' }}">
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>{{ $obArray['nama_brng'] }}</td>
                            <td>{{ $obArray['tgl_pemberian'] }} [{{ $obArray['kd_bangsal'] }}]</td>
                            <td class="qty">{{ $obArray['jumlah'] > 0 ? $obArray['jumlah'] : -$obArray['jumlah_retur'] }}</td>
                            <td class="amount">{{ number_format($obArray['biaya'], 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    @if($isRalan && $totalObat > 0)
                        @php 
                            $ppnObat = round($totalObat * 0.11); 
                            $grandTotal += $ppnObat;
                        @endphp
                        <tr style="font-weight: 600; background: #f8fafc;">
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>PPN Obat (11%)</td>
                            <td>Perhitungan PPN Obat Ralan</td>
                            <td class="qty">1</td>
                            <td class="amount">{{ number_format($ppnObat, 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($ppnObat, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endif

                <!-- VIII. BIAYA LAIN-LAIN (TAMBAHAN & PENGURANGAN) -->
                @if(count($tambahanBiaya['tambahan']) > 0 || count($tambahanBiaya['pengurangan']) > 0)
                    <tr class="category-header">
                        <td colspan="6">VIII. BIAYA LAIN-LAIN (TAMBAHAN & PENGURANGAN)</td>
                    </tr>
                    @foreach($tambahanBiaya['tambahan'] as $t)
                        @php $grandTotal += $t->besar; @endphp
                        <tr style="color: #059669; background: #ecfdf5;">
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>{{ $t->nama_biaya }}</td>
                            <td>Tambahan Biaya (+)</td>
                            <td class="qty">1</td>
                            <td class="amount">{{ number_format($t->besar, 0, ',', '.') }}</td>
                            <td class="amount">{{ number_format($t->besar, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    @foreach($tambahanBiaya['pengurangan'] as $p)
                        @php $grandTotal -= $p->besar; @endphp
                        <tr class="retur-row">
                            <td class="center">{{ $itemNumber++ }}</td>
                            <td>{{ $p->nama_biaya }}</td>
                            <td>Pengurangan Biaya (-)</td>
                            <td class="qty">1</td>
                            <td class="amount">-{{ number_format($p->besar, 0, ',', '.') }}</td>
                            <td class="amount">-{{ number_format($p->besar, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif

            </tbody>
        </table>

        <!-- Ringkasan Total -->
        <div class="summary-wrapper">
            <div class="summary-card">
                <div class="summary-row">
                    <span>TOTAL HARUS DIBAYAR</span>
                    <span class="val">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
