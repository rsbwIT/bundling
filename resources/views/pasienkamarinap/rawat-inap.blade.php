@extends('layout.layoutDashboard')
@section('title', 'Daftar Pasien Rawat Inap')

@push('styles')
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        /* KUNCI TAMPILAN 1 LAYAR PENUH (ZERO WINDOW SCROLL) */
        html, body {
            height: 100vh !important;
            overflow: hidden !important;
            background-color: #f8fafc;
        }
        .wrapper {
            height: 100vh !important;
            overflow: hidden !important;
        }
        .content-wrapper {
            height: calc(100vh - 56px) !important;
            overflow: hidden !important;
            padding: 0 !important;
            margin-bottom: 0 !important;
            background-color: #f1f5f9;
        }
        .content-header {
            display: none !important;
        }
        .content {
            height: 100% !important;
            overflow: hidden !important;
            padding: 8px 14px !important;
        }
        .content .container-fluid {
            height: 100% !important;
            padding: 0 !important;
        }
        .content .container-fluid > .row {
            height: 100% !important;
            margin: 0 !important;
        }
        .content .container-fluid > .row > .col-lg-12 {
            height: 100% !important;
            padding: 0 !important;
        }
        main.p-0\,5 {
            display: none !important;
        }
        .main-footer {
            display: none !important;
        }

        /* CONTAINER APLIKASI RAWAT INAP */
        .ranap-app-container {
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 8px;
            overflow: hidden;
        }

        /* HEADER & STATISTIK */
        .header-ranap-bar {
            background: #ffffff;
            border-radius: 10px;
            padding: 8px 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .stat-card-ranap {
            background: #ffffff;
            border-radius: 10px;
            padding: 8px 14px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }
        .stat-card-ranap:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .stat-icon-ranap {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
            font-size: 1.25rem;
        }

        /* FILTER PANEL MODERN */
        .filter-panel-ranap {
            background: #ffffff;
            border-radius: 10px;
            padding: 10px 16px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            flex-shrink: 0;
        }

        .status-check-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 10px;
            border-radius: 20px;
            transition: all 0.15s ease;
            user-select: none;
            margin: 0;
        }
        .status-check-pill.check-blue {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .status-check-pill.check-green {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .status-check-pill.check-red {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .status-check-pill input {
            cursor: pointer;
            width: 1rem;
            height: 1rem;
        }

        .input-ranap-date {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.82rem;
            height: 32px;
            padding: 2px 8px;
            color: #334155;
            transition: all 0.15s ease;
        }
        .input-ranap-date:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
        }

        .btn-cari-ranap {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.84rem;
            border: none;
            border-radius: 6px;
            height: 32px;
            padding: 0 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.25);
            transition: all 0.2s ease;
        }
        .btn-cari-ranap:hover {
            color: #ffffff;
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.35);
        }

        /* TABEL DATA SCREEN FLEX 1 */
        .table-panel-ranap {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .table-scroll-ranap {
            flex: 1 1 auto;
            overflow: auto;
            min-height: 0;
        }

        .table-ranap-custom {
            width: 100%;
            border-collapse: separate !important;
            border-spacing: 0;
            white-space: nowrap;
            font-size: 0.84rem;
            color: #1e293b;
            margin: 0;
        }

        .table-ranap-custom thead th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 9px 12px;
            border-bottom: 2px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            z-index: 10;
        }

        .table-ranap-custom thead th:last-child {
            border-right: none;
        }

        .table-ranap-custom tbody td {
            padding: 6px 12px;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table-ranap-custom tbody td:last-child {
            border-right: none;
        }

        .table-ranap-custom tbody tr {
            transition: all 0.12s ease;
            cursor: pointer;
        }

        .table-ranap-custom tbody tr:hover {
            filter: brightness(0.95);
        }

        .table-ranap-custom tbody tr.active-row-context {
            outline: 2px solid #2563eb !important;
            box-shadow: inset 0 0 8px rgba(37, 99, 235, 0.2);
        }

        /* CONTEXT MENU KLIK KANAN */
        .custom-context-menu {
            display: none;
            position: absolute;
            z-index: 999999;
            background-color: #ffffff;
            min-width: 300px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 6px 0;
            font-size: 0.85rem;
        }
        .custom-context-menu .menu-header {
            padding: 8px 16px;
            font-size: 0.74rem;
            font-weight: 700;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .custom-context-menu .menu-item {
            padding: 9px 16px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.12s ease;
        }
        .custom-context-menu .menu-item:hover {
            background-color: #2563eb;
            color: #ffffff;
        }
        .custom-context-menu .menu-item:hover i {
            color: #ffffff !important;
        }
        .custom-context-menu .menu-divider {
            height: 1px;
            background-color: #f1f5f9;
            margin: 4px 0;
        }
    </style>
@endpush

@section('konten')
<div class="ranap-app-container">

    {{-- HEADER BAR ATAS --}}
    <div class="header-ranap-bar">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 bg-light-primary text-primary mr-3" style="background: #eff6ff;">
                <i class="fas fa-procedures fa-lg text-primary"></i>
            </div>
            <div>
                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 1rem;">Rekap Pasien Rawat Inap</h6>
                <small class="text-muted" style="font-size: 11px;">Data kamar menginap, verifikasi hak kelas SEP &amp; asesmen medis/psikiatri</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold px-3 py-1 shadow-sm" id="copyButton">
                <i class="bi bi-clipboard-check mr-1"></i> Copy Table
            </button>
            <a href="{{ url('rawat-inap') }}" class="btn btn-light btn-sm border text-muted px-2 py-1 shadow-sm" title="Refresh Data">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </div>

    {{-- KARTU STATISTIK RINGKAS (1 BARIS PROPORSIONAL) --}}
    @php
        $totalPasien = $results->count();
        $totalHijau = $results->where('warna_kelas', 'hijau')->count();
        $totalKuning = $results->where('keterangan_klsnaik', 'Tidak Ada')->count();
        $totalMerah = $results->where('warna_kelas', 'merah')->count();
    @endphp
    <div class="row g-2 flex-shrink-0" style="margin: 0 -4px;">
        <div class="col-3" style="padding: 0 4px;">
            <div class="stat-card-ranap">
                <div class="stat-icon-ranap text-primary" style="background: #e0f2fe;">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Total Pasien</div>
                    <div class="font-weight-bold text-dark mb-0" style="font-size: 1.2rem; line-height: 1.1;">{{ number_format($totalPasien, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="col-3" style="padding: 0 4px;">
            <div class="stat-card-ranap">
                <div class="stat-icon-ranap text-success" style="background: #dcfce7;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Sesuai Hak Kelas</div>
                    <div class="font-weight-bold text-success mb-0" style="font-size: 1.2rem; line-height: 1.1;">{{ number_format($totalHijau, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="col-3" style="padding: 0 4px;">
            <div class="stat-card-ranap">
                <div class="stat-icon-ranap text-warning" style="background: #fef3c7;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Tanpa Ket. Naik</div>
                    <div class="font-weight-bold text-warning mb-0" style="font-size: 1.2rem; line-height: 1.1;">{{ number_format($totalKuning, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="col-3" style="padding: 0 4px;">
            <div class="stat-card-ranap">
                <div class="stat-icon-ranap text-danger" style="background: #fee2e2;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Tidak Sesuai Kelas</div>
                    <div class="font-weight-bold text-danger mb-0" style="font-size: 1.2rem; line-height: 1.1;">{{ number_format($totalMerah, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER PANEL RAPI (HORIZONTAL FIT) --}}
    <div class="filter-panel-ranap">
        <form action="{{ url('/rawat-inap') }}" method="GET" class="d-flex flex-wrap align-items-center justify-content-between m-0" style="gap: 8px;">
            @csrf

            {{-- Kolom Kiri: Checkbox Status & Date Inputs --}}
            <div class="d-flex flex-wrap align-items-center gap-2">
                <label class="status-check-pill check-blue" for="belum_pulang">
                    <input type="checkbox" name="belum_pulang" id="belum_pulang" class="ceklis"
                        {{ request('belum_pulang', true) ? 'checked' : '' }}>
                    <i class="bi bi-door-open-fill"></i> Belum Pulang
                </label>

                <label class="status-check-pill check-green" for="tgl_masuk">
                    <input type="checkbox" name="tgl_masuk" id="tgl_masuk" class="ceklis"
                        {{ request('tgl_masuk') ? 'checked' : '' }}>
                    <i class="bi bi-box-arrow-in-right"></i> Tgl Masuk
                </label>

                <label class="status-check-pill check-red" for="tgl_pulang">
                    <input type="checkbox" name="tgl_pulang" id="tgl_pulang" value="1" class="ceklis"
                        {{ request('tgl_pulang') ? 'checked' : '' }}>
                    <i class="bi bi-box-arrow-right"></i> Tgl Pulang
                </label>

                <input type="date" name="tgl1" id="tgl1" class="input-ranap-date ml-2" style="width: 130px;"
                    value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                <span class="text-muted small">s.d</span>
                <input type="date" name="tgl2" id="tgl2" class="input-ranap-date" style="width: 130px;"
                    value="{{ request('tgl2', now()->format('Y-m-d')) }}">

                <button type="submit" class="btn btn-cari-ranap ml-1">
                    <i class="fa fa-search"></i> Cari Data
                </button>
            </div>

            {{-- Kolom Kanan: Filter Status Warna & Kelas --}}
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="d-flex align-items-center gap-1">
                    <span class="text-muted small font-weight-bold mr-1">Status:</span>
                    <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['filter_warna' => 'merah'])) }}"
                        class="btn btn-outline-danger btn-xs font-weight-bold px-2 py-1 {{ request('filter_warna') == 'merah' ? 'active' : '' }}" title="Tidak Sesuai Kelas">
                        <i class="bi bi-x-circle-fill"></i> Tidak Sesuai
                    </a>
                    <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['filter_warna' => 'kuning'])) }}"
                        class="btn btn-outline-warning btn-xs font-weight-bold px-2 py-1 {{ request('filter_warna') == 'kuning' ? 'active' : '' }}" title="Tanpa Ket. Naik">
                        <i class="bi bi-exclamation-triangle-fill"></i> Tanpa Ket.
                    </a>
                    <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['filter_warna' => 'hijau'])) }}"
                        class="btn btn-outline-success btn-xs font-weight-bold px-2 py-1 {{ request('filter_warna') == 'hijau' ? 'active' : '' }}" title="Sesuai SEP">
                        <i class="bi bi-check-circle-fill"></i> Sesuai SEP
                    </a>
                    <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['filter_warna' => 'putih'])) }}"
                        class="btn btn-outline-secondary btn-xs font-weight-bold px-2 py-1 {{ request('filter_warna') == 'putih' ? 'active' : '' }}" title="Tanpa SEP">
                        Tanpa SEP
                    </a>
                </div>

                <div class="d-flex align-items-center gap-1 border-left pl-2 ml-1">
                    <span class="text-muted small font-weight-bold mr-1">Kelas:</span>
                    <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['kelas_filter' => 'Kelas 1'])) }}"
                        class="btn btn-outline-primary btn-xs px-2 py-1 {{ request('kelas_filter') == 'Kelas 1' ? 'active' : '' }}">Kls 1</a>
                    <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['kelas_filter' => 'Kelas 2'])) }}"
                        class="btn btn-outline-primary btn-xs px-2 py-1 {{ request('kelas_filter') == 'Kelas 2' ? 'active' : '' }}">Kls 2</a>
                    <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['kelas_filter' => 'Kelas 3'])) }}"
                        class="btn btn-outline-primary btn-xs px-2 py-1 {{ request('kelas_filter') == 'Kelas 3' ? 'active' : '' }}">Kls 3</a>
                </div>
            </div>

        </form>
    </div>

    {{-- TABEL DATA PASIEN: MENGISI RUANG LAYAR SISA TANPA OVERFLOW KE BAWAH --}}
    <div class="table-panel-ranap">
        <div class="table-scroll-ranap">
            <table class="table-ranap-custom" id="tableToCopy">
                <thead>
                    <tr>
                        <th style="width: 35px; text-align: center;">No</th>
                        <th>No Rawat</th>
                        <th>Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Jenis Bayar</th>
                        <th>Kelas SEP</th>
                        <th>Naik Kelas</th>
                        <th>Kamar</th>
                        <th style="text-align: right;">Tarif Kamar</th>
                        <th>Diagnosa Awal</th>
                        <th>Diagnosa Akhir</th>
                        <th>Tgl. Masuk</th>
                        <th>Jam Masuk</th>
                        <th>Tgl. Keluar</th>
                        <th>Jam Keluar</th>
                        <th style="text-align: right;">Ttl. Biaya</th>
                        <th style="text-align: center;">Stts. Pulang</th>
                        <th style="text-align: center;">Lama</th>
                        <th>Dokter DPJP</th>
                        <th>Status Bayar</th>
                        <th>Penanggung Jawab</th>
                        <th>Hubungan P.J</th>
                        <th>Agama</th>
                        <th>Alamat Pasien</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($results as $key => $item)
                        @php
                            $isNonKelas = empty($item->klsrawat);
                            $isSepKosong = empty($item->kelas_sep);
                            $warnaKelas = $item->warna_kelas ?? '';
                            $keteranganKlsNaik = $item->keterangan_klsnaik ?? 'Tidak Ada';

                            if ($isNonKelas && $isSepKosong) {
                                $bgColor = '#ffffff';
                            } elseif ($warnaKelas === 'hijau') {
                                $bgColor = '#f0fdf4';
                            } elseif ($keteranganKlsNaik === 'Tidak Ada') {
                                $bgColor = '#fefce8';
                            } else {
                                $bgColor = '#fef2f2';
                            }
                        @endphp

                        <tr class="row-pasien-ranap" 
                            data-norawat="{{ $item->no_rawat }}" 
                            data-nama="{{ $item->nm_pasien }}" 
                            style="background-color: {{ $bgColor }};">
                            
                            <td style="text-align: center; color: #64748b;">{{ $key + 1 }}</td>
                            <td class="font-monospace font-weight-bold text-dark">{{ $item->no_rawat }}</td>
                            <td class="font-monospace text-primary font-weight-bold">{{ $item->no_rkm_medis }}</td>
                            <td class="font-weight-bold text-dark">{{ $item->nm_pasien }}</td>
                            <td><span class="badge badge-light border text-dark">{{ $item->png_jawab }}</span></td>
                            <td>
                                @if($item->klsrawat)
                                    <span class="badge badge-info text-white">Kelas {{ $item->klsrawat }}</span>
                                @else
                                    <span class="text-muted small">Tanpa SEP</span>
                                @endif
                            </td>
                            <td>
                                @if($item->keterangan_klsnaik && $item->keterangan_klsnaik !== 'Tidak Ada')
                                    <span class="badge badge-warning text-dark">{{ $item->keterangan_klsnaik }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="font-weight-bold text-secondary">{{ $item->kamar_bangsal }}</td>
                            <td style="text-align: right;">{{ number_format($item->trf_kamar) }}</td>
                            <td>{{ $item->diagnosa_awal }}</td>
                            <td>{{ $item->diagnosa_akhir }}</td>
                            <td>{{ $item->tgl_masuk }}</td>
                            <td>{{ $item->jam_masuk }}</td>
                            <td>{{ $item->tgl_keluar ?? '-' }}</td>
                            <td>{{ $item->jam_keluar ?? '-' }}</td>
                            <td style="text-align: right;" class="font-weight-bold text-dark">{{ number_format($item->ttl_biaya) }}</td>
                            <td style="text-align: center;">
                                <span class="badge {{ $item->stts_pulang == 'Belum Pulang' ? 'badge-primary' : 'badge-secondary' }}">
                                    {{ $item->stts_pulang }}
                                </span>
                            </td>
                            <td style="text-align: center;">{{ $item->lama }} Hari</td>
                            <td>{{ $item->nm_dokter }}</td>
                            <td>
                                <span class="badge {{ $item->status_bayar == 'Sudah Bayar' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $item->status_bayar }}
                                </span>
                            </td>
                            <td>{{ $item->p_jawab }}</td>
                            <td>{{ $item->hubunganpj }}</td>
                            <td>{{ $item->agama }}</td>
                            <td>{{ $item->alamat }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="24" style="text-align: center; padding: 40px; color: #64748b;">
                                <i class="fas fa-inbox fa-3x mb-2 text-secondary"></i>
                                <h6>Tidak ada data pasien rawat inap untuk filter yang dipilih.</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Custom Context Menu Klik Kanan -->
<div id="ranapContextMenu" class="custom-context-menu">
    <div class="menu-header" id="contextMenuTitle">
        <i class="bi bi-person-fill mr-1"></i> Data Pasien
    </div>
    <a href="javascript:void(0)" class="menu-item" id="btnMenuPengkajianEmosional">
        <i class="bi bi-file-earmark-medical-fill text-primary" style="font-size: 16px;"></i>
        <span><strong>Form Pengkajian Gangguan Emosional / Psikiatri</strong></span>
    </a>
    <a href="javascript:void(0)" class="menu-item" id="btnMenuCetakPengkajianEmosional">
        <i class="bi bi-printer-fill text-success" style="font-size: 16px;"></i>
        <span>Cetak Pengkajian Gangguan Emosional (PDF)</span>
    </a>
    <div class="menu-divider"></div>
    <a href="javascript:void(0)" class="menu-item text-muted" id="btnMenuSalinNoRawat">
        <i class="bi bi-clipboard mr-1"></i>
        <span>Salin No. Rawat</span>
    </a>
</div>
@endsection

@push('scripts')
    @livewireScripts
    <script>
        // Salin Tabel
        document.getElementById("copyButton").addEventListener("click", () => {
            const table = document.getElementById("tableToCopy");
            const range = document.createRange();
            range.selectNode(table);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);

            try {
                document.execCommand("copy");
                selection.removeAllRanges();
                alert("Tabel berhasil disalin ke clipboard!");
            } catch (err) {
                console.error("Gagal menyalin tabel:", err);
            }
        });

        // Checkbox Behavior
        const ceklis = document.querySelectorAll('.ceklis');
        const belumPulang = document.getElementById('belum_pulang');
        const tanggalInputs = [document.getElementById('tgl1'), document.getElementById('tgl2')];

        function toggleInputs() {
            const disabled = belumPulang.checked;
            ceklis.forEach(cb => {
                if (cb.id !== 'belum_pulang') {
                    cb.disabled = disabled;
                }
            });
            tanggalInputs.forEach(input => input.disabled = disabled);
        }

        ceklis.forEach(cb => {
            cb.addEventListener('change', () => {
                if (cb.id !== 'belum_pulang' && cb.checked) {
                    belumPulang.checked = false;
                    toggleInputs();
                    ceklis.forEach(other => {
                        if (other !== cb && other.id !== 'belum_pulang') other.checked = false;
                    });
                }
            });
        });

        belumPulang.addEventListener('change', toggleInputs);
        toggleInputs();

        // CONTEXT MENU (KLIK KANAN) PADA TABEL PASIEN RAWAT INAP
        const contextMenu = document.getElementById('ranapContextMenu');
        const contextMenuTitle = document.getElementById('contextMenuTitle');
        const btnMenuPengkajian = document.getElementById('btnMenuPengkajianEmosional');
        const btnMenuCetak = document.getElementById('btnMenuCetakPengkajianEmosional');
        const btnMenuSalin = document.getElementById('btnMenuSalinNoRawat');

        let selectedNoRawat = '';
        let selectedNama = '';
        let activeRow = null;

        document.querySelectorAll('.row-pasien-ranap').forEach(row => {
            row.addEventListener('contextmenu', function(e) {
                e.preventDefault();

                if (activeRow) {
                    activeRow.classList.remove('active-row-context');
                }
                activeRow = this;
                activeRow.classList.add('active-row-context');

                selectedNoRawat = this.getAttribute('data-norawat') || '';
                selectedNama = this.getAttribute('data-nama') || '';

                contextMenuTitle.innerHTML = `<i class="bi bi-person-fill mr-1"></i> ${selectedNama.substring(0, 24)}`;

                let posX = e.pageX;
                let posY = e.pageY;

                const menuWidth = 300;
                const menuHeight = 160;
                if (posX + menuWidth > window.innerWidth + window.scrollX) {
                    posX = window.innerWidth + window.scrollX - menuWidth - 10;
                }
                if (posY + menuHeight > window.innerHeight + window.scrollY) {
                    posY = posY - menuHeight;
                }

                contextMenu.style.left = `${posX}px`;
                contextMenu.style.top = `${posY}px`;
                contextMenu.style.display = 'block';
            });
        });

        // Tutup context menu
        document.addEventListener('click', function(e) {
            if (contextMenu && !contextMenu.contains(e.target)) {
                contextMenu.style.display = 'none';
                if (activeRow) {
                    activeRow.classList.remove('active-row-context');
                    activeRow = null;
                }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && contextMenu) {
                contextMenu.style.display = 'none';
                if (activeRow) {
                    activeRow.classList.remove('active-row-context');
                    activeRow = null;
                }
            }
        });

        // Aksi Buka Form Pengkajian Emosional
        if (btnMenuPengkajian) {
            btnMenuPengkajian.addEventListener('click', function() {
                if (!selectedNoRawat) return;
                const formatted = encodeURIComponent(selectedNoRawat.replace(/\//g, '-'));
                window.open(`{{ url('/pengkajian-gangguan-emosional') }}/${formatted}`, '_blank');
                contextMenu.style.display = 'none';
            });
        }

        // Aksi Cetak Pengkajian Emosional
        if (btnMenuCetak) {
            btnMenuCetak.addEventListener('click', function() {
                if (!selectedNoRawat) return;
                const formatted = encodeURIComponent(selectedNoRawat.replace(/\//g, '-'));
                window.open(`{{ url('/pengkajian-gangguan-emosional/cetak') }}/${formatted}`, '_blank');
                contextMenu.style.display = 'none';
            });
        }

        // Aksi Salin No Rawat
        if (btnMenuSalin) {
            btnMenuSalin.addEventListener('click', function() {
                if (!selectedNoRawat) return;
                navigator.clipboard.writeText(selectedNoRawat).then(() => {
                    alert(`No. Rawat ${selectedNoRawat} berhasil disalin!`);
                });
                contextMenu.style.display = 'none';
            });
        }
    </script>
@endpush
