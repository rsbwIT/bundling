@extends('..layout.layoutDashboard')
@section('title', 'Laporan Pengeluaran Harian')

@section('konten')
<div class="card shadow-sm border-0">
    <div class="card-body">
        
        <style>
            /* Custom Modern Styles */
            .filter-card {
                background: linear-gradient(145deg, #ffffff, #f8f9fa);
                border: 1px solid #e9ecef;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            }
            .filter-label {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #6c757d;
                font-weight: 700;
                margin-bottom: 4px;
                display: block;
            }
            .btn-modern {
                border-radius: 8px;
                font-weight: 600;
                letter-spacing: 0.3px;
                transition: all 0.2s;
            }
            .btn-modern:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 12px rgba(0,0,0,0.15);
            }
            .form-control-modern {
                border-radius: 6px;
                border: 1px solid #ced4da;
                padding: 0.375rem 0.75rem;
                transition: border-color 0.2s, box-shadow 0.2s;
            }
            .form-control-modern:focus {
                border-color: #80bdff;
                box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
                outline: 0;
            }
            /* Styling for radio toggle in BS4 */
            .custom-toggle .btn {
                border-radius: 20px;
                font-size: 0.85rem;
                padding: 4px 12px;
                font-weight: 600;
            }
            .divider-vertical {
                width: 1px;
                background-color: #dee2e6;
                height: 40px;
                margin: 0 15px;
            }
        </style>

        <form method="GET" action="{{ route('laporan.pengeluaran-harian') }}" class="mb-4 no-print">
            <div class="filter-card p-3">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
                    
                    <!-- Kiri: Filter Controls -->
                    <div class="d-flex flex-wrap align-items-center">
                        
                        <!-- Tipe Filter -->
                        <div>
                            <span class="filter-label"><i class="fas fa-filter text-primary"></i> Berdasarkan</span>
                            <div class="btn-group btn-group-toggle custom-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary btn-sm {{ $filter_type == 'bulan' ? 'active' : '' }}" onclick="document.getElementById('filterBulan').click(); toggleFilter();">
                                    <input type="radio" name="filter_type" id="filterBulan" value="bulan" {{ $filter_type == 'bulan' ? 'checked' : '' }} autocomplete="off"> Bulan
                                </label>
                                <label class="btn btn-outline-primary btn-sm {{ $filter_type == 'tanggal' ? 'active' : '' }}" onclick="document.getElementById('filterTanggal').click(); toggleFilter();">
                                    <input type="radio" name="filter_type" id="filterTanggal" value="tanggal" {{ $filter_type == 'tanggal' ? 'checked' : '' }} autocomplete="off"> Tanggal
                                </label>
                            </div>
                        </div>

                        <div class="divider-vertical d-none d-md-block"></div>

                        <!-- Input Bulan -->
                        <div id="wrapBulan" style="display: {{ $filter_type == 'bulan' ? 'block' : 'none' }};">
                            <span class="filter-label"><i class="far fa-calendar-alt text-info"></i> Pilih Bulan & Tahun</span>
                            <div class="d-flex align-items-center">
                                <select name="bulan" class="form-control-modern bg-white me-2" style="width: auto;">
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ sprintf('%02d', $i) }}" {{ $bulan == sprintf('%02d', $i) ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                        </option>
                                    @endfor
                                </select>
                                <input type="number" name="tahun" value="{{ $tahunSekarang }}" class="form-control-modern bg-white" style="width: 80px;">
                            </div>
                        </div>

                        <!-- Input Tanggal -->
                        <div id="wrapTanggal" style="display: {{ $filter_type == 'tanggal' ? 'block' : 'none' }};">
                            <span class="filter-label"><i class="far fa-calendar-alt text-info"></i> Rentang Waktu</span>
                            <div class="d-flex align-items-center">
                                <input type="date" name="tgl_awal" class="form-control-modern bg-white" value="{{ $tglAwal }}">
                                <span class="text-muted fw-bold mx-2">-</span>
                                <input type="date" name="tgl_akhir" class="form-control-modern bg-white" value="{{ $tglAkhir }}">
                            </div>
                        </div>

                        <div class="divider-vertical d-none d-md-block"></div>

                        <!-- Switch Tahun Lalu -->
                        <div class="pt-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="showPrevYear" name="show_prev_year" value="1" {{ $showPrevYear ? 'checked' : '' }}>
                                <label class="custom-control-label text-dark fw-bold" for="showPrevYear" style="cursor: pointer; font-size: 0.85rem; margin-top:2px;">Tampilkan Tahun Lalu</label>
                            </div>
                        </div>
                    </div>

                    <!-- Kanan: Action Buttons -->
                    <div class="d-flex align-items-center mt-3 mt-lg-0" style="gap: 8px;">
                        <button type="button" class="btn btn-warning btn-modern text-dark" data-toggle="modal" data-target="#modalDetail" data-bs-toggle="modal" data-bs-target="#modalDetail">
                            <i class="fas fa-eye me-1"></i> Detail
                        </button>
                        <button type="submit" class="btn btn-primary btn-modern shadow-sm">
                            <i class="fas fa-search me-1"></i> Tampilkan
                        </button>
                        <button type="button" class="btn btn-success btn-modern shadow-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Cetak
                        </button>
                        <button type="button" class="btn btn-secondary btn-modern shadow-sm" onclick="copyTableToClipboard()">
                            <i class="fas fa-copy me-1"></i> Copy
                        </button>
                        <a href="{{ route('laporan.mapping-suplier') }}" class="btn btn-info btn-modern text-white shadow-sm">
                            <i class="fas fa-cog me-1"></i> Mapping
                        </a>
                    </div>
                    
                </div>
            </div>
        </form>

        <!-- Modal Detail -->
        <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header text-dark border-0" style="background-color: #ffeb3b !important;">
                        <h5 class="modal-title fw-bold" id="modalDetailLabel"><i class="fas fa-list"></i> Detail Pembayaran Non Medis</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="table-responsive bg-white p-3 rounded shadow-sm">
                            <table class="table table-bordered table-sm table-hover align-middle">
                                <thead class="text-dark sticky-top" style="background-color: #ffeb3b !important;">
                                    <tr class="text-center">
                                        <th class="py-2">No</th>
                                        <th class="py-2">Tanggal Bayar</th>
                                        <th class="py-2">No Bukti</th>
                                        <th class="py-2">Nama Supplier</th>
                                        <th class="py-2">Kategori</th>
                                        <th class="py-2">Keterangan</th>
                                        <th class="py-2">Besar Bayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalDetail = 0; @endphp
                                    @forelse($dataDetail as $idx => $dt)
                                        @php $totalDetail += $dt->besar_bayar; @endphp
                                        <tr>
                                            <td class="text-center">{{ $idx + 1 }}</td>
                                            <td class="text-center">{{ $dt->tgl_bayar }}</td>
                                            <td>{{ $dt->no_bukti }}</td>
                                            <td>{{ $dt->nama_suplier }}</td>
                                            <td class="text-center"><span class="badge bg-secondary rounded-pill px-3">{{ $dt->kategori }}</span></td>
                                            <td class="small text-muted">{{ $dt->keterangan }}</td>
                                            <td class="text-right fw-bold">Rp. {{ number_format($dt->besar_bayar, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-box-open fa-3x mb-2 text-light"></i><br>
                                                Tidak ada data detail.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold" style="background-color: #f8f9fa;">
                                        <td colspan="6" class="text-right py-2">TOTAL :</td>
                                        <td class="text-right text-danger py-2">Rp. {{ number_format($totalDetail, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleFilter() {
                var isBulan = document.getElementById('filterBulan').checked;
                document.getElementById('wrapBulan').style.display = isBulan ? 'block' : 'none';
                document.getElementById('wrapTanggal').style.display = isBulan ? 'none' : 'block';
            }

            function copyTableToClipboard() {
                var container = document.getElementById("reportTableContainer");
                if(!container) return;
                
                var range = document.createRange();
                range.selectNode(container);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
                
                try {
                    document.execCommand('copy');
                    window.getSelection().removeAllRanges();
                    alert('Data Laporan berhasil di-copy ke Clipboard! Anda bisa mem-paste nya di Excel atau Word.');
                } catch(err) {
                    alert('Gagal menyalin data.');
                }
            }
        </script>

        <div class="table-responsive mt-3" id="reportTableContainer">
            
            <!-- KOP RS (Hanya tampil saat di-print) -->
            <table class="table table-borderless table-sm w-100 mb-4 d-none d-print-table" style="color: #000; border-bottom: 3px solid #000;">
                <tr>
                    <td width="10%" class="text-center align-middle">
                        @if(isset($setting) && $setting->logo)
                            <img src="data:image/jpeg;base64,{{ base64_encode($setting->logo) }}" width="80" height="80" alt="Logo RS">
                        @endif
                    </td>
                    <td width="90%" class="text-center align-middle">
                        <h4 class="mb-1 fw-bold" style="color:#000;">{{ $setting->nama_instansi ?? 'NAMA INSTANSI' }}</h4>
                        <span class="small">{{ $setting->alamat_instansi ?? '' }}, {{ $setting->kabupaten ?? '' }}, {{ $setting->propinsi ?? '' }}</span><br>
                        <span class="small">No. Telp: {{ $setting->kontak ?? '' }} | E-mail: {{ $setting->email ?? '' }}</span>
                    </td>
                </tr>
            </table>

            <div class="card shadow-sm border-0 mb-4 no-break">
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover table-sm table-report mb-0" style="color:#000;">
                        <thead>
                            <tr class="text-dark text-center" style="background-color: #ffeb3b !important; border-bottom: 2px solid #333;">
                                <th colspan="{{ $showPrevYear ? 5 : 4 }}" class="py-3 fs-5 fw-bold text-dark">
                                    <i class="fas fa-file-invoice-dollar me-2 text-secondary no-print"></i>
                                    PENGELUARAN HARIAN {{ strtoupper(date('F', mktime(0, 0, 0, (int)$bulan, 10))) }} {{ $tahunSekarang }}
                                </th>
                            </tr>
                            <tr class="bg-light text-center text-dark">
                                <th rowspan="2" class="align-middle fw-bold py-2">KATEGORI</th>
                                <th rowspan="2" class="align-middle fw-bold py-2">SUBTOTAL</th>
                                <th colspan="{{ $showPrevYear ? 2 : 1 }}" class="fw-bold py-2">JUMLAH</th>
                                <th rowspan="2" class="align-middle fw-bold py-2">GRANDTOTAL</th>
                            </tr>
                            <tr class="text-dark text-center" style="background-color: #ffeb3b !important;">
                                @if($showPrevYear)
                                    <th class="py-2">{{ $tahunSebelumnya }}</th>
                                @endif
                                <th class="py-2">{{ $tahunSekarang }}</th>
                            </tr>
                        </thead>
                        <tbody class="align-middle">
                            @php 
                                $totalPrev = 0; 
                                $totalCurr = 0; 
                                $totalGrand = 0; 
                            @endphp
                            
                            @foreach($reportData as $row)
                                @php
                                    $totalPrev += $row['jumlah_sebelumnya'];
                                    $totalCurr += $row['jumlah_sekarang'];
                                    $totalGrand += $row['grandtotal'];
                                @endphp
                                <tr>
                                    <td class="px-3 fw-semibold text-secondary">{{ $row['kategori'] }}</td>
                                    <td class="text-center text-muted small">Sub Total</td>
                                    @if($showPrevYear)
                                        <td class="text-right px-3">Rp. {{ number_format($row['jumlah_sebelumnya'], 0, ',', '.') }}</td>
                                    @endif
                                    <td class="text-right px-3">Rp. {{ number_format($row['jumlah_sekarang'], 0, ',', '.') }}</td>
                                    <td class="text-right px-3 fw-bold">Rp. {{ number_format($row['grandtotal'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr style="font-weight: bold; background-color: #e9ecef; border-top: 2px solid #333;">
                                <td colspan="2" class="text-right px-3 py-2 text-uppercase text-dark">Grand Total :</td>
                                @if($showPrevYear)
                                    <td class="text-right px-3 py-2 text-dark">Rp. {{ number_format($totalPrev, 0, ',', '.') }}</td>
                                @endif
                                <td class="text-right px-3 py-2 text-dark">Rp. {{ number_format($totalCurr, 0, ',', '.') }}</td>
                                <td class="text-right px-3 py-2 text-dark fs-6">Rp. {{ number_format($totalGrand, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row no-break">
                <div class="col-md-6 rekap-wrapper">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover table-sm mb-0 text-nowrap" style="color:#000;">
                                <thead>
                                    <tr style="text-align: center; background-color: #f8f9fa;">
                                        <th colspan="3" class="py-3 fw-bold">
                                            <i class="fas fa-chart-pie me-2 text-info no-print"></i>
                                            REKAP PENGELUARAN {{ strtoupper(date('F', mktime(0, 0, 0, (int)$bulan, 10))) }} {{ $tahunSekarang }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle">
                                    @if($showPrevYear)
                                    <tr>
                                        <td class="px-3 text-muted">BEBAN {{ $tahunSebelumnya }}</td>
                                        <td></td>
                                        <td class="text-right px-3 text-muted">{{ number_format($totalBebanSebelumnya, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="px-3 fw-bold" style="color:#d32f2f;">BEBAN {{ $tahunSekarang }}</td>
                                        <td class="text-right px-3 fw-bold" style="color:#d32f2f;">{{ number_format($totalBebanSekarang, 0, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                    <tr style="background-color: #fcfcfc;">
                                        <td colspan="2" class="text-right"></td>
                                        <td class="text-right px-3 fw-bold" style="border-top: 1px dashed #ccc;">{{ number_format($totalBebanSekarang, 0, ',', '.') }}</td>
                                    </tr>
                                    
                                    @if($showPrevYear)
                                    <tr>
                                        <td class="px-3 text-muted">INVESTASI ({{ $tahunSebelumnya }})</td>
                                        <td></td>
                                        <td class="text-right px-3 text-muted">{{ number_format($totalInvestasiSebelumnya, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="px-3 fw-bold" style="color:#1976d2;">INVESTASI ({{ $tahunSekarang }})</td>
                                        <td class="text-right px-3 fw-bold" style="color:#1976d2;">{{ number_format($totalInvestasiSekarang, 0, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                    <tr style="background-color: #fcfcfc;">
                                        <td colspan="2" class="text-right"></td>
                                        <td class="text-right px-3 fw-bold" style="border-top: 1px dashed #ccc;">{{ number_format($totalInvestasiSekarang, 0, ',', '.') }}</td>
                                    </tr>

                                    <tr style="background-color: #e9ecef; border-top: 2px solid #333;">
                                        <td class="px-3 py-2 fw-bold fs-6 text-dark">TOTAL KESELURUHAN</td>
                                        <td class="py-2"></td>
                                        <td class="text-right px-3 py-2 fw-bold fs-6 text-dark">Rp. {{ number_format($totalBebanSekarang + $totalInvestasiSekarang, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 no-print mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 d-flex flex-column justify-content-center align-items-center">
                            <h6 class="fw-bold text-secondary mb-3"><i class="fas fa-chart-pie me-1"></i> Proporsi Beban vs Investasi</h6>
                            <div style="position: relative; height: 250px; width: 100%;">
                                <canvas id="expensePieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var totalBeban = {{ $totalBebanSekarang > 0 ? $totalBebanSekarang : 0 }};
        var totalInvestasi = {{ $totalInvestasiSekarang > 0 ? $totalInvestasiSekarang : 0 }};

        if (totalBeban > 0 || totalInvestasi > 0) {
            var ctx = document.getElementById('expensePieChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['BEBAN', 'INVESTASI'],
                    datasets: [{
                        data: [totalBeban, totalInvestasi],
                        backgroundColor: ['#d32f2f', '#1976d2'],
                        borderWidth: 1,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: 10 },
                    plugins: {
                        legend: { 
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 15,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    if (label) { label += ': '; }
                                    label += 'Rp. ' + context.parsed.toLocaleString('id-ID');
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('expensePieChart').parentElement.innerHTML = '<div class="text-muted text-center mt-5">Tidak ada data pengeluaran bulan ini.</div>';
        }
    });
</script>

<style>
    /* Styling khusus print agar lebih rapi */
    @media print {
        /* Sembunyikan elemen yang tidak perlu diprint (navbar, sidebar, tombol) */
        .no-print, .main-header, .main-sidebar, footer { display: none !important; }
        .content-wrapper, .main-panel { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        
        .d-print-table { display: table !important; }
        .card { border: none !important; box-shadow: none !important; margin-bottom: 20px !important; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
        
        /* Posisi rekap di pojok kiri dengan ukuran lebih memanjang ke kanan */
        .rekap-wrapper {
            flex: 0 0 60% !important;
            max-width: 60% !important;
            width: 60% !important;
        }

        /* Sembunyikan scrollbar horizontal saat dicetak */
        .table-responsive {
            overflow: visible !important;
        }
        
        /* Ubah teks abu-abu menjadi hitam murni saat dicetak */
        .text-muted, .text-secondary, .text-black-50 { color: #000 !important; }
        
        /* Mengatasi warna background tabel yang hilang saat print */
        tr, th, td {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* Mencegah tabel terpotong jelek di halaman baru */
        .no-break { page-break-inside: avoid; }
    }
    .table-report th, .table-report td {
        border-color: #000;
    }
</style>
@endsection
