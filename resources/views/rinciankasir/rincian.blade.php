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

        <!-- Ringkasan Total & Pembayaran COB (E-Klaim) -->
        <div class="summary-wrapper" style="margin-top: 25px;">
            <div class="summary-card">
                <div class="summary-row">
                    <span>TOTAL HARUS DIBAYAR</span>
                    <span class="val" id="textGrandTotalDisplay">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-3" style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 12px 16px; border-radius: 6px; font-weight: 500;">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mt-3" style="background: #fef2f2; border: 1px solid #ef4444; color: #991b1b; padding: 12px 16px; border-radius: 6px; font-weight: 500;">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Card Pembayaran COB (Integrasi E-Klaim & SIMRS Khanza) -->
        <div class="card mt-4" style="border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); overflow: hidden;">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; padding: 12px 20px;">
                <div style="font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-money-check-alt"></i> Pembayaran Kasir COB (Sesuai Format Piutang Khanza)
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($sepInfo && !empty($sepInfo->no_sep))
                        <span class="badge bg-light text-dark px-2 py-1" style="font-size: 11px; border-radius: 4px;">
                            SEP: <strong>{{ $sepInfo->no_sep }}</strong>
                        </span>
                        <button type="button" class="btn btn-sm btn-warning" id="btnRefreshEklaim" style="font-size: 11px; padding: 3px 10px; font-weight: 600;">
                            <i class="fas fa-sync-alt" id="iconSyncEklaim"></i> Ambil Ulang Tarif E-Klaim
                        </button>
                    @else
                        <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 11px;">
                            <i class="fas fa-exclamation-triangle"></i> Data SEP Belum Terhubung
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body" style="padding: 20px; background: #fafafa;">
                @php
                    $penjaminKategori = $mappingPenjamin->kategori ?? null;
                    if (!$penjaminKategori) {
                        if (isset($header->png_jawab) && (stripos($header->png_jawab, 'COB') !== false || stripos($header->png_jawab, 'KAI') !== false)) {
                            $penjaminKategori = 'BPJS COB';
                        } elseif (isset($header->png_jawab) && stripos($header->png_jawab, 'BPJS') !== false) {
                            $penjaminKategori = 'BPJS Murni';
                        } elseif (isset($header->png_jawab) && stripos($header->png_jawab, 'UMUM') !== false) {
                            $penjaminKategori = 'Umum';
                        } else {
                            $penjaminKategori = 'Asuransi';
                        }
                    }

                    // Tentukan Akun Piutang Perusahaan/Asuransi default
                    $selectedPerusahaanNama = $mappingPenjamin->nama_akun_piutang ?? '';
                    $selectedPerusahaanKdPj = $mappingPenjamin->kd_pj ?? ($header->kd_pj ?? '');

                    if (empty($selectedPerusahaanNama) && isset($akunPiutangCobList)) {
                        foreach($akunPiutangCobList as $ap) {
                            if (isset($header->png_jawab) && stripos($ap->nama_bayar, $header->png_jawab) !== false) {
                                $selectedPerusahaanNama = $ap->nama_bayar;
                                $selectedPerusahaanKdPj = $ap->kd_pj;
                                break;
                            }
                        }
                    }
                    if (empty($selectedPerusahaanNama) && isset($akunPiutangCobList) && count($akunPiutangCobList) > 0) {
                        $selectedPerusahaanNama = $akunPiutangCobList[0]->nama_bayar;
                    }

                    // Hitung default nominal berdasarkan Kategori Mapping
                    if ($penjaminKategori === 'BPJS COB') {
                        $defaultBpjsNominal = $eklaimTarif ?? 0;
                        $defaultCobNominal = max(0, $grandTotal - ($eklaimTarif ?? 0));
                    } elseif ($penjaminKategori === 'BPJS Murni') {
                        $defaultBpjsNominal = $grandTotal;
                        $defaultCobNominal = 0;
                    } elseif ($penjaminKategori === 'Asuransi') {
                        // Asuransi Murni (seperti IPC Terminal Petikemas): 100% masuk piutang asuransi terkait, BPJS = 0
                        $defaultBpjsNominal = 0;
                        $defaultCobNominal = $grandTotal;
                    } else { // Umum
                        $defaultBpjsNominal = 0;
                        $defaultCobNominal = 0;
                    }
                @endphp
                <form action="{{ route('rincian.simpan-pembayaran-cob') }}" method="POST" id="formPembayaranCob">
                    @csrf
                    <input type="hidden" name="no_rawat" value="{{ $header->no_rawat ?? request('no_rawat') }}">
                    <input type="hidden" name="total_tagihan_real" id="inputTotalTagihanReal" value="{{ $grandTotal }}">
                    <input type="hidden" name="tarif_inacbg" id="inputTarifInacbg" value="{{ $eklaimTarif ?? 0 }}">
                    <input type="hidden" name="kategori_penjamin" id="inputKategoriPenjamin" value="{{ $penjaminKategori }}">

                    <!-- Header Ringkasan Tagihan & Penjamin -->
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b;">Total Tagihan RS :</div>
                                <div style="font-size: 20px; font-weight: 800; color: #0f172a; font-family: monospace;">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    Penjamin: <strong>{{ $header->png_jawab ?? '-' }}</strong>
                                    <span class="badge {{ $penjaminKategori === 'BPJS COB' ? 'bg-warning text-dark' : ($penjaminKategori === 'BPJS Murni' ? 'bg-success' : 'bg-primary') }}" style="font-size: 10px;">
                                        {{ $penjaminKategori }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4" style="border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0;">
                                @if(in_array($penjaminKategori, ['BPJS COB', 'BPJS Murni']))
                                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #0284c7;">
                                        Tarif E-Klaim INACBG: <span id="labelCbgCode" class="badge bg-info text-white">{{ $eklaimCbg ?? '-' }}</span>
                                    </div>
                                    <div style="font-size: 20px; font-weight: 800; color: #0284c7; font-family: monospace;" id="displayTarifInacbgText">
                                        Rp {{ number_format($eklaimTarif ?? 0, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted d-block" id="textEklaimDesc" style="font-size: 11px;">{{ $eklaimDesc ?? 'Tarif otomatis dari E-Klaim INACBG' }}</small>
                                @else
                                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b;">
                                        Akun Piutang Mapping:
                                    </div>
                                    <div style="font-size: 15px; font-weight: 800; color: #1e3a8a; margin-top: 4px;">
                                        <i class="fas fa-file-invoice-dollar text-primary me-1"></i> {{ $selectedPerusahaanNama ?: 'Belum Dimapping' }}
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 11px;">
                                        Kode Akun: {{ $mappingPenjamin->kd_rek_piutang ?? '-' }}
                                    </small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                @if($penjaminKategori === 'BPJS COB')
                                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #d97706;">
                                        Selisih Biaya (Masuk ke Perusahaan/COB):
                                    </div>
                                    <div style="font-size: 20px; font-weight: 800; color: #d97706; font-family: monospace;" id="displaySelisihPerusahaanText">
                                        Rp {{ number_format($defaultCobNominal, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">(Pasien COB: Tagihan RS - Tarif INACBG BPJS)</small>
                                @elseif($penjaminKategori === 'BPJS Murni')
                                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #059669;">
                                        Porsi Piutang BPJS (Murni):
                                    </div>
                                    <div style="font-size: 20px; font-weight: 800; color: #059669; font-family: monospace;" id="displaySelisihPerusahaanText">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">(Pasien BPJS Murni: 100% Total Tagihan RS)</small>
                                @elseif($penjaminKategori === 'Asuransi')
                                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #1e40af;">
                                        Porsi Piutang Asuransi / Perusahaan:
                                    </div>
                                    <div style="font-size: 20px; font-weight: 800; color: #1e40af; font-family: monospace;" id="displaySelisihPerusahaanText">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">(Pasien Asuransi Murni: 100% Masuk ke Akun Terkait)</small>
                                @else
                                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #15803d;">
                                        Pembayaran Langsung (Umum):
                                    </div>
                                    <div style="font-size: 20px; font-weight: 800; color: #15803d; font-family: monospace;">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">(Pasien Bayar Tunai / Mandiri)</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- TABEL KOLOM BAYAR & PIUTANG PERSIS SEPERTI DI KHANZA -->
                    <!-- 1. Bagian Uang Muka / Pembayaran Langsung -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size: 13px; font-weight: 700; color: #334155;">
                                <i class="fas fa-hand-holding-usd text-success me-1"></i> Pembayaran Langsung / Kas:
                            </span>
                            <span style="font-size: 12px; font-weight: 600; color: #64748b;">
                                Total Bayar: <strong id="textSummaryBayar">Rp 0</strong>
                            </span>
                        </div>
                        <div class="table-responsive" style="border: 1px solid #cbd5e1; border-radius: 4px; background: #fff;">
                            <table class="table table-sm table-bordered mb-0" style="font-size: 12px;">
                                <thead style="background: #f8fafc;">
                                    <tr>
                                        <th style="font-weight: 600;">Nama Akun Bayar</th>
                                        <th style="width: 250px; font-weight: 600; text-align: right;">Bayar (Rp)</th>
                                        <th style="width: 100px; font-weight: 600; text-align: center;">PPN (%)</th>
                                        <th style="width: 150px; font-weight: 600; text-align: right;">PPN (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-select form-select-sm" name="nama_akun_bayar" id="selectAkunBayar" style="font-size: 12px;">
                                                <option value="">-- Tidak Ada Pembayaran Langsung (Rp 0) --</option>
                                                @if(isset($akunBayarList))
                                                    @foreach($akunBayarList as $ab)
                                                        <option value="{{ $ab->nama_bayar }}">{{ $ab->nama_bayar }} ({{ $ab->kd_rek }})</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control form-control-sm text-end font-monospace" name="besar_bayar_langsung" id="inputBesarBayarLangsung" value="0" style="font-weight: 700;" oninput="hitungOtomatisSemua()">
                                        </td>
                                        <td class="text-center align-middle">0%</td>
                                        <td class="text-end align-middle font-monospace">Rp 0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 2. Bagian Kolom Piutang (Sesuai Khanza: Piutang BPJS + Piutang COB) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size: 13px; font-weight: 700; color: #1e3a8a;">
                                <i class="fas fa-file-invoice-dollar text-primary me-1"></i> Piutang:
                            </span>
                            <span style="font-size: 12px; font-weight: 600; color: #1e3a8a;">
                                Total Piutang: <strong id="textSummaryPiutang">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                            </span>
                        </div>
                        <div class="table-responsive" style="border: 1px solid #cbd5e1; border-radius: 4px; background: #fff;">
                            <table class="table table-sm table-bordered mb-0" style="font-size: 12px;">
                                <thead style="background: #f8fafc;">
                                    <tr>
                                        <th style="font-weight: 600;">Nama Akun Piutang</th>
                                        <th style="width: 250px; font-weight: 600; text-align: right;">Piutang (Rp)</th>
                                        <th style="width: 150px; font-weight: 600; text-align: center;">Jatuh Tempo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Baris 1: Piutang BPJS (Tampil untuk BPJS COB & BPJS Murni) -->
                                    <tr style="background: #f0fdf4; {{ in_array($penjaminKategori, ['Asuransi', 'Umum']) ? 'display: none;' : '' }}" id="rowPiutangBpjs">
                                        <td class="align-middle">
                                            <div style="font-weight: 700; color: #15803d; display: flex; align-items: center; gap: 6px;">
                                                <i class="fas fa-check-circle"></i> {{ $akunPiutangBpjs->nama_bayar ?? '2. Piutang BPJS' }}
                                                @if($penjaminKategori === 'BPJS COB')
                                                    <span class="badge bg-success" style="font-size: 10px; font-weight: 500;">Tarif INACBG Otomatis</span>
                                                @else
                                                    <span class="badge bg-success" style="font-size: 10px; font-weight: 500;">BPJS Murni (100% Tagihan RS)</span>
                                                @endif
                                            </div>
                                            <input type="hidden" name="nama_bayar_bpjs" value="{{ $akunPiutangBpjs->nama_bayar ?? '2. Piutang BPJS' }}">
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control form-control-sm text-end font-monospace" name="piutang_bpjs" id="inputPiutangBpjs" value="{{ $defaultBpjsNominal }}" required style="font-weight: 700; color: #15803d; font-size: 13px;" oninput="hitungOtomatisManualBpjs()">
                                        </td>
                                        <td class="text-center align-middle font-monospace">{{ date('d/m/Y') }}</td>
                                    </tr>

                                    <!-- Baris 2: Piutang Perusahaan / Asuransi (Untuk COB & Asuransi Murni) -->
                                    <tr style="background: #fffbeb; {{ $penjaminKategori === 'BPJS Murni' || $penjaminKategori === 'Umum' ? 'display: none;' : '' }}" id="rowPiutangPerusahaan">
                                        <td class="align-middle">
                                            <div style="font-weight: 700; color: #b45309; display: flex; align-items: center; gap: 6px;">
                                                <i class="fas fa-building"></i> {{ $selectedPerusahaanNama }}
                                                @if($selectedPerusahaanKdPj)
                                                    <span class="badge bg-warning text-dark" style="font-size: 10px; font-weight: 600;">{{ $selectedPerusahaanKdPj }}</span>
                                                @endif
                                                <span class="badge bg-secondary" style="font-size: 10px; font-weight: 500;">
                                                    {{ $penjaminKategori === 'Asuransi' ? 'Akun Piutang Mapping' : 'Perusahaan COB' }} (Terkunci)
                                                </span>
                                            </div>
                                            <input type="hidden" name="nama_bayar_cob" id="inputNamaBayarCob" value="{{ $selectedPerusahaanNama }}">
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control form-control-sm text-end font-monospace" name="selisih_cob" id="inputSelisihCob" value="{{ $defaultCobNominal }}" style="font-weight: 700; color: #b45309; font-size: 13px;" oninput="hitungOtomatisManualCob()">
                                        </td>
                                        <td class="text-center align-middle font-monospace">{{ date('d/m/Y') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 3. Status Kekurangan / Sisa Tagihan (Target: Rp 0) -->
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px 18px;" class="d-flex justify-content-between align-items-center">
                        <div style="font-weight: 700; font-size: 13px; color: #334155;">
                            Kekurangan / Sisa Tagihan Kasir:
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span id="badgeStatusBalance" class="badge bg-success" style="font-size: 11px;">LUNAS (SEIMBANG)</span>
                            <div style="font-size: 18px; font-weight: 800; font-family: monospace;" id="displayKekuranganAkhir">
                                Rp 0
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="tipe_selisih" id="inputTipeSelisih" value="piutang_cob">

                    <!-- Tombol Simpan atau Notifikasi Sudah Bayar -->
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
                        @if(($header->status_bayar ?? '') === 'Sudah Bayar')
                            <div class="alert alert-success d-flex align-items-center mb-0 px-3 py-2" style="font-size: 13px; font-weight: 600; border-radius: 6px;">
                                <i class="fas fa-check-circle me-2 fs-5"></i>
                                Status Pasien: Sudah Bayar (Pembayaran / Piutang Telah Tercatat)
                            </div>
                        @else
                            <button type="submit" class="btn btn-primary px-4 py-2" id="btnSimpanCob" style="font-size: 13px; font-weight: 700; background: #1e3a8a;">
                                <i class="fas fa-save me-1"></i> Simpan Pembayaran Kasir
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function hitungOtomatisSemua() {
        const totalTagihan = parseFloat(document.getElementById('inputTotalTagihanReal').value) || 0;
        const tarifInacbg = parseFloat(document.getElementById('inputTarifInacbg').value) || 0;
        const bayarLangsung = parseFloat(document.getElementById('inputBesarBayarLangsung').value) || 0;
        const kategori = document.getElementById('inputKategoriPenjamin')?.value || 'Asuransi';

        let porsiBpjs = 0;
        let porsiPerusahaan = 0;

        if (kategori === 'BPJS COB') {
            // Pasien COB: Tarif INACBG masuk BPJS, sisanya ke Perusahaan
            porsiBpjs = tarifInacbg;
            porsiPerusahaan = totalTagihan - porsiBpjs - bayarLangsung;
            if (porsiPerusahaan < 0) porsiPerusahaan = 0;
        } else if (kategori === 'BPJS Murni') {
            // Pasien BPJS Murni: 100% Total Tagihan RS masuk ke BPJS
            porsiBpjs = totalTagihan - bayarLangsung;
            if (porsiBpjs < 0) porsiBpjs = 0;
            porsiPerusahaan = 0;
        } else if (kategori === 'Asuransi') {
            // Pasien Asuransi Murni: 100% masuk ke Akun Piutang Perusahaan/Asuransi Mapping
            porsiBpjs = 0;
            porsiPerusahaan = totalTagihan - bayarLangsung;
            if (porsiPerusahaan < 0) porsiPerusahaan = 0;
        } else {
            // Pasien Umum: Bayar Tunai
            porsiBpjs = 0;
            porsiPerusahaan = 0;
        }

        document.getElementById('inputPiutangBpjs').value = porsiBpjs;
        document.getElementById('inputSelisihCob').value = porsiPerusahaan;

        updateTampilanKekurangan();
    }

    function hitungOtomatisManualBpjs() {
        const totalTagihan = parseFloat(document.getElementById('inputTotalTagihanReal').value) || 0;
        const piutangBpjs = parseFloat(document.getElementById('inputPiutangBpjs').value) || 0;
        const bayarLangsung = parseFloat(document.getElementById('inputBesarBayarLangsung').value) || 0;

        let porsiPerusahaan = totalTagihan - piutangBpjs - bayarLangsung;
        if (porsiPerusahaan < 0) porsiPerusahaan = 0;

        document.getElementById('inputSelisihCob').value = porsiPerusahaan;
        updateTampilanKekurangan();
    }

    function hitungOtomatisManualCob() {
        updateTampilanKekurangan();
    }

    function updateTampilanKekurangan() {
        const totalTagihan = parseFloat(document.getElementById('inputTotalTagihanReal').value) || 0;
        const tarifInacbg = parseFloat(document.getElementById('inputTarifInacbg').value) || 0;
        const bayarLangsung = parseFloat(document.getElementById('inputBesarBayarLangsung').value) || 0;
        const piutangBpjs = parseFloat(document.getElementById('inputPiutangBpjs').value) || 0;
        const piutangCob = parseFloat(document.getElementById('inputSelisihCob').value) || 0;

        const totalTerbayar = bayarLangsung + piutangBpjs + piutangCob;
        const sisaKekurangan = totalTagihan - totalTerbayar;

        // Update Text Ringkasan
        document.getElementById('textSummaryBayar').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(bayarLangsung);
        document.getElementById('textSummaryPiutang').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(piutangBpjs + piutangCob);
        document.getElementById('displayKekuranganAkhir').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(sisaKekurangan);

        let selisihPerusahaan = totalTagihan - tarifInacbg;
        if (selisihPerusahaan < 0) selisihPerusahaan = 0;
        if (document.getElementById('displaySelisihPerusahaanText')) {
            document.getElementById('displaySelisihPerusahaanText').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(selisihPerusahaan);
        }

        const badge = document.getElementById('badgeStatusBalance');
        if (Math.abs(sisaKekurangan) < 1) {
            badge.className = 'badge bg-success';
            badge.innerText = 'LUNAS (SEIMBANG)';
            document.getElementById('displayKekuranganAkhir').style.color = '#15803d';
        } else {
            badge.className = 'badge bg-danger';
            badge.innerText = 'SELISIH / KURANG';
            document.getElementById('displayKekuranganAkhir').style.color = '#dc2626';
        }
    }

    // Refresh Tarif E-Klaim via AJAX
    document.getElementById('btnRefreshEklaim')?.addEventListener('click', function() {
        const noRawat = encodeURIComponent("{{ $header->no_rawat ?? request('no_rawat') }}");
        const icon = document.getElementById('iconSyncEklaim');
        if (icon) icon.classList.add('fa-spin');

        fetch(`/rincian-kasir/cek-eklaim/${noRawat}`)
            .then(res => res.json())
            .then(res => {
                if (icon) icon.classList.remove('fa-spin');
                if (res.success) {
                    document.getElementById('inputTarifInacbg').value = res.tarif;
                    document.getElementById('displayTarifInacbgText').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(res.tarif);
                    if (document.getElementById('labelCbgCode')) {
                        document.getElementById('labelCbgCode').innerText = res.cbg || '-';
                    }
                    if (document.getElementById('textEklaimDesc')) {
                        document.getElementById('textEklaimDesc').innerText = res.deskripsi || 'Tarif berhasil diperbarui dari E-Klaim';
                    }
                    hitungOtomatisSemua();
                    alert('Tarif E-Klaim berhasil diperbarui: Rp ' + new Intl.NumberFormat('id-ID').format(res.tarif));
                } else {
                    alert('Gagal mengambil tarif E-Klaim: ' + (res.message || 'Data tidak ditemukan'));
                }
            })
            .catch(err => {
                if (icon) icon.classList.remove('fa-spin');
                console.error(err);
                alert('Terjadi kesalahan saat memanggil E-Klaim.');
            });
    });

    // Inisialisasi awal saat halaman termuat
    document.addEventListener('DOMContentLoaded', function() {
        hitungOtomatisSemua();
    });
</script>
@endsection
