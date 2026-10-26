@extends('layout.layoutDashboard')
@section('title', 'Piutang Belum Lunas - Pelunasan Piutang')

@section('konten')
<link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<div class="container-fluid px-3 py-3">

    {{-- HEADER & TITLE --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-hand-holding-usd text-success mr-2"></i> Pelunasan Piutang Pasien
            </h4>
            <span class="text-muted text-xs">
                Versi Web & JavaScript dari modul Keuangan Khanza (<code>KeuanganPiutangBelumLunas.java</code>)
            </span>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge badge-light border px-3 py-2 text-sm shadow-sm mr-2">
                <i class="fas fa-database text-info mr-1"></i> Total Data Tampil: <strong id="lblTotalTampil">{{ count($piutangData) }}</strong>
            </span>
            <span class="badge badge-success px-3 py-2 text-sm shadow-sm" id="badgeSelectedCount">
                <i class="fas fa-check-double mr-1"></i> 0 Pasien Dipilih
            </span>
        </div>
    </div>

    {{-- STATS SUMMARY WIDGETS --}}
    <div class="row mb-3">
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100" style="border-left: 4px solid #17a2b8 !important;">
                <div class="card-body py-2 px-3">
                    <div class="text-muted text-xs font-weight-bold text-uppercase">Total Piutang Dipilih</div>
                    <div class="h5 font-weight-bold text-info mb-0" id="statTotalPiutangDipilih">Rp 0</div>
                    <small class="text-muted" id="statCountDipilih">0 dari {{ count($piutangData) }} transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100" style="border-left: 4px solid #28a745 !important;">
                <div class="card-body py-2 px-3">
                    <div class="text-muted text-xs font-weight-bold text-uppercase">Total Akan Dibayar</div>
                    <div class="h5 font-weight-bold text-success mb-0" id="statTotalAkanDibayar">Rp 0</div>
                    <small class="text-muted">Masuk ke kas / rekening bayar</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body py-2 px-3">
                    <div class="text-muted text-xs font-weight-bold text-uppercase">Total Diskon Bayar</div>
                    <div class="h5 font-weight-bold text-warning mb-0" id="statTotalDiskon">Rp 0</div>
                    <small class="text-muted">Akun: {{ $setAkun->Diskon_Piutang ?? '-' }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body py-2 px-3">
                    <div class="text-muted text-xs font-weight-bold text-uppercase">Total Tidak Terbayar</div>
                    <div class="h5 font-weight-bold text-danger mb-0" id="statTotalTidakTerbayar">Rp 0</div>
                    <small class="text-muted">Akun: {{ $setAkun->Piutang_Tidak_Terbayar ?? '-' }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER FORM & PENGATURAN PEMBAYARAN --}}
    {{-- FILTER PENCARIAN (BISA DISEMBUNYIKAN / COLLAPSIBLE) --}}
    <div class="card border-0 shadow-sm rounded-lg mb-2">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center" id="headingFilter" style="cursor: pointer;" title="Klik untuk menyembunyikan / menampilkan filter">
            <div class="d-flex align-items-center">
                <span class="font-weight-bold text-dark"><i class="fas fa-filter text-primary mr-1"></i> Filter Pencarian Data Piutang</span>
                @if(!empty($no_rawat_list) || !empty($cari) || !empty($kd_pj) || !empty($no_tagihan) || !empty($tgl1))
                    <span class="badge badge-info ml-2 font-weight-normal py-1 px-2">
                        <i class="fas fa-check mr-1"></i> Filter Aktif
                    </span>
                @endif
                <span class="badge badge-warning ml-2 font-weight-normal py-1 px-2 d-none" id="badgeFilterCollapsed">
                    <i class="fas fa-eye-slash mr-1"></i> Filter Disembunyikan (Klik untuk membuka)
                </span>
            </div>
            <button type="button" class="btn btn-xs btn-outline-primary font-weight-bold px-3 py-1 shadow-sm" id="btnToggleCollapseFilter" style="white-space: nowrap;">
                <i class="fas fa-chevron-up mr-1" id="iconFilterCollapse"></i> <span id="textFilterCollapse">Sembunyikan Filter</span>
            </button>
        </div>
        <div id="collapseFilter">
            <div class="card-body py-2 px-3 bg-light border-top">
                <form action="{{ route('keuangan.piutang.belum-lunas') }}" method="GET" id="formFilter">
                    <input type="hidden" name="no_rawat_list" id="filterNoRawatList" value="{{ $no_rawat_list ?? '' }}">
                    @if(!empty($no_rawat_list))
                        <div class="alert alert-info py-2 px-3 text-xs mb-2 d-flex justify-content-between align-items-center shadow-sm">
                            <span><i class="fas fa-filter text-primary mr-1"></i> Sedang memfilter berdasarkan daftar No. Rawat khusus (Excel / Paste). Ditemukan <strong>{{ count($piutangData) }} data piutang</strong> di tabel.</span>
                            <button type="button" class="btn btn-xs btn-danger font-weight-bold" id="btnClearRawatFilter">
                                <i class="fas fa-times mr-1"></i> Hapus Filter No. Rawat & Tampilkan Normal
                            </button>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">Cari No.Rawat / RM / Nama Pasien</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="cari" class="form-control" placeholder="Ketik kata kunci..." value="{{ $cari }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">Pilih Penjamin / Cara Bayar</label>
                            <select name="kd_pj" class="form-control form-control-sm" id="selectPenjab">
                                <option value="">-- Semua Penjamin --</option>
                                @foreach ($penjab as $pj)
                                    <option value="{{ $pj->kd_pj }}" {{ $kd_pj == $pj->kd_pj ? 'selected' : '' }}>
                                        {{ $pj->png_jawab }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">Pilih No. Tagihan (Penagihan Piutang)</label>
                            <select name="no_tagihan" class="form-control form-control-sm" id="selectNoTagihan">
                                <option value="">-- Tanpa No. Tagihan --</option>
                                @foreach ($penagihanList as $pt)
                                    <option value="{{ $pt->no_tagihan }}" {{ $no_tagihan == $pt->no_tagihan ? 'selected' : '' }}>
                                        {{ $pt->no_tagihan }} ({{ $pt->tanggal }}) - {{ $pt->catatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">Status Piutang</label>
                            <select name="statusLunas" class="form-control form-control-sm bg-white" readonly>
                                <option value="Belum Lunas" selected>Belum Lunas Saja (Sisa Piutang > 0)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row align-items-end">
                        <div class="col-md-3 mb-2">
                            <label class="text-xs font-weight-bold text-success mb-1">
                                <i class="fas fa-file-excel mr-1"></i> Filter Cepat dari File Excel (No. Rawat)
                            </label>
                            <div class="custom-file custom-file-sm">
                                <input type="file" class="custom-file-input" id="quickFilterExcel" accept=".xlsx, .xls, .csv, .txt">
                                <label class="custom-file-label text-truncate text-xs" for="quickFilterExcel" id="lblQuickExcel">Pilih file Excel No. Rawat...</label>
                            </div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">Tgl Piutang Dari</label>
                            <input type="date" name="tgl1" class="form-control form-control-sm" value="{{ $tgl1 }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">Tgl Piutang Sampai</label>
                            <input type="date" name="tgl2" class="form-control form-control-sm" value="{{ $tgl2 }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-sm btn-info btn-block">
                                <i class="fas fa-sync-alt mr-1"></i> Terapkan Filter
                            </button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('keuangan.piutang.belum-lunas') }}" class="btn btn-sm btn-outline-secondary btn-block">
                                <i class="fas fa-undo mr-1"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- PARAMETER PELUNASAN (TANGGAL TRANSAKSI, REKENING AKUN BAYAR & PETUGAS PELUNASAN) --}}
    <div class="card border-0 shadow-sm rounded-lg mb-3">
        <div class="card-body py-2 px-3 bg-white">
            <div class="row align-items-center">
                <div class="col-md-2 mb-2">
                    <label class="text-xs font-weight-bold text-primary mb-1">
                        <i class="fas fa-calendar-check mr-1"></i> Tgl Pembayaran:
                    </label>
                    <input type="date" id="tglBayarGlobal" class="form-control form-control-sm font-weight-bold" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="text-xs font-weight-bold text-primary mb-1">
                        <i class="fas fa-university mr-1"></i> Rekening Akun Bayar:
                    </label>
                    <select id="kdRekBayarGlobal" class="form-control form-control-sm font-weight-bold select2" style="width: 100%;">
                        @foreach ($akunBayar as $ab)
                            <option value="{{ $ab->kd_rek }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $ab->nama_bayar }} ({{ $ab->kd_rek }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="text-xs font-weight-bold text-primary mb-1">
                        <i class="fas fa-user-check mr-1"></i> Petugas Pelunasan (Verifikator):
                    </label>
                    <select id="nipPetugasGlobal" class="form-control form-control-sm font-weight-bold select2" style="width: 100%;">
                        @foreach ($petugasList as $p)
                            <option value="{{ $p->nip }}" {{ (isset($defaultNip) && $defaultNip == $p->nip) ? 'selected' : '' }}>
                                {{ $p->nama }} ({{ $p->nip }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2 text-md-right">
                    <span class="text-xs text-muted d-block font-weight-bold">Akun Diskon:</span>
                    <span class="badge badge-light border text-xs font-weight-bold">{{ $setAkun->Diskon_Piutang ?? '-' }}</span>
                    <span class="text-xs text-muted d-block font-weight-bold mt-1">Akun Tdk Terbayar:</span>
                    <span class="badge badge-light border text-xs font-weight-bold">{{ $setAkun->Piutang_Tidak_Terbayar ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS TOOLBAR --}}
    <div class="card border-0 shadow-sm rounded-lg mb-3">
        <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex flex-wrap align-items-center mb-1">
                <div class="btn-group mr-2 mb-1">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAll">
                        <i class="fas fa-check-square mr-1"></i> Centang Semua
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnUnselectAll">
                        <i class="far fa-square mr-1"></i> Hapus Centang
                    </button>
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold mr-2 mb-1" id="btnQuickToggleFilter" title="Sembunyikan / Tampilkan Panel Filter">
                    <i class="fas fa-filter mr-1"></i> <span id="textQuickToggleFilter">Sembunyikan Filter</span>
                </button>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">

                {{-- TOMBOL HITUNG KAPITASI / PAKET (MODAL) --}}
                <button type="button" class="btn btn-sm btn-warning text-dark font-weight-bold shadow-sm mr-2 mb-1" data-toggle="modal" data-target="#modalKapitasi">
                    <i class="fas fa-calculator mr-1"></i> Hitung Kapitasi / Paket
                </button>

                {{-- TOMBOL FILTER EXCEL NO. RAWAT --}}
                <button type="button" class="btn btn-sm btn-success text-white font-weight-bold shadow-sm mr-2 mb-1" data-toggle="modal" data-target="#modalImportExcel">
                    <i class="fas fa-filter mr-1"></i> Filter No. Rawat (Excel / Paste)
                </button>

                {{-- TOMBOL IMPORT CSV UMBAL (MODAL) --}}
                <button type="button" class="btn btn-sm btn-info text-white font-weight-bold shadow-sm mr-2 mb-1" data-toggle="modal" data-target="#modalUmbal">
                    <i class="fas fa-file-csv mr-1"></i> Import Umbal CSV
                </button>

                {{-- TOMBOL SIMPAN PEMBAYARAN KE DATABASE --}}
                <button type="button" class="btn btn-sm btn-success font-weight-bold shadow-sm mb-1 px-3" id="btnProsesBayar">
                    <i class="fas fa-save mr-1"></i> Simpan Pembayaran (<span id="btnCounter">0</span>)
                </button>
            </div>
        </div>
    </div>

    {{-- TABEL DATA PIUTANG PASIEN --}}
    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-body p-0 table-responsive" style="max-height: 65vh; overflow-y: auto;">
            <table class="table table-sm table-hover table-bordered table-head-fixed text-xs mb-0" id="tablePiutang" style="white-space: nowrap;">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="width: 35px;">
                            <input type="checkbox" id="checkAllHeader" title="Centang / Batal Semua">
                        </th>
                        <th>No. Rawat</th>
                        <th>Tgl. Piutang</th>
                        <th>Pasien (RM & Nama)</th>
                        <th>Status</th>
                        <th class="text-right">Total Piutang</th>
                        <th class="text-right">Uang Muka</th>
                        <th class="text-right">Sudah Dibayar</th>
                        <th class="text-right bg-light font-weight-bold text-dark">Sisa Piutang</th>
                        <th>Jatuh Tempo</th>
                        <th>Cara Bayar</th>
                        <th class="text-center bg-success text-white" style="width: 130px;">Bayar (Cicilan)</th>
                        <th class="text-center bg-warning text-dark" style="width: 110px;">Diskon Bayar</th>
                        <th class="text-center bg-danger text-white" style="width: 110px;">Tidak Terbayar</th>
                    </tr>
                </thead>
                <tbody id="tablePiutangBody">
                    @forelse ($piutangData as $index => $row)
                        @php
                            $sisaAwal = $row->sisa_piutang_aktual;
                        @endphp
                        <tr id="row-{{ $index }}" 
                            data-index="{{ $index }}"
                            data-no-rawat="{{ $row->no_rawat }}"
                            data-no-sep="{{ $row->no_sep }}"
                            data-no-rm="{{ $row->no_rkm_medis }}"
                            data-total-piutang="{{ $row->totalpiutang }}"
                            data-uang-muka="{{ $row->uangmuka }}"
                            data-sudah-dibayar="{{ $row->sudah_dibayar }}"
                            data-sisa-awal="{{ $sisaAwal }}"
                            data-kd-rek-kontra="{{ $row->kd_rek_kontra }}">
                            
                            {{-- Checkbox Pilih (P) --}}
                            <td class="text-center align-middle">
                                <input type="checkbox" class="row-checkbox" value="{{ $index }}" id="cb-{{ $index }}">
                            </td>

                            {{-- No. Rawat & Badge SEP --}}
                            <td class="align-middle font-weight-bold">
                                {{ $row->no_rawat }}
                                @if(!empty($row->no_sep))
                                    <br><span class="badge badge-light border text-info"><i class="fas fa-file-alt"></i> {{ $row->no_sep }}</span>
                                @endif
                            </td>

                            {{-- Tgl. Piutang --}}
                            <td class="align-middle">{{ $row->tgl_piutang }}</td>

                            {{-- Pasien --}}
                            <td class="align-middle">
                                <span class="font-weight-bold">{{ $row->no_rkm_medis }}</span> - {{ $row->nm_pasien }}
                            </td>

                            {{-- Status --}}
                            <td class="align-middle">
                                <span class="badge {{ $row->status == 'Lunas' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $row->status }}
                                </span>
                            </td>

                            {{-- Total Piutang --}}
                            <td class="text-right align-middle">{{ number_format($row->totalpiutang, 0, ',', '.') }}</td>

                            {{-- Uang Muka --}}
                            <td class="text-right align-middle">{{ number_format($row->uangmuka, 0, ',', '.') }}</td>

                            {{-- Sudah Dibayar Sebelumnya --}}
                            <td class="text-right align-middle text-muted">{{ number_format($row->sudah_dibayar, 0, ',', '.') }}</td>

                            {{-- Sisa Piutang Aktual (Dynamic) --}}
                            <td class="text-right align-middle font-weight-bold text-dark cell-sisa-piutang bg-light" id="cellSisa-{{ $index }}">
                                {{ number_format($sisaAwal, 0, ',', '.') }}
                            </td>

                            {{-- Jatuh Tempo --}}
                            <td class="align-middle">{{ $row->tgltempo }}</td>

                            {{-- Cara Bayar --}}
                            <td class="align-middle">{{ $row->png_jawab }}</td>

                            {{-- Input Bayar --}}
                            <td class="align-middle p-1">
                                <input type="number" step="any" min="0" 
                                    class="form-control form-control-sm text-right font-weight-bold text-success input-bayar" 
                                    id="inputBayar-{{ $index }}" 
                                    value="0" 
                                    data-index="{{ $index }}"
                                    placeholder="0">
                            </td>

                            {{-- Input Diskon --}}
                            <td class="align-middle p-1">
                                <input type="number" step="any" min="0" 
                                    class="form-control form-control-sm text-right text-warning input-diskon" 
                                    id="inputDiskon-{{ $index }}" 
                                    value="0" 
                                    data-index="{{ $index }}"
                                    placeholder="0">
                            </td>

                            {{-- Input Tidak Terbayar --}}
                            <td class="align-middle p-1">
                                <input type="number" step="any" min="0" 
                                    class="form-control form-control-sm text-right text-danger input-tidak-terbayar" 
                                    id="inputTidakTerbayar-{{ $index }}" 
                                    value="0" 
                                    data-index="{{ $index }}"
                                    placeholder="0">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                                Tidak ada data piutang ditemukan sesuai filter. Silakan ubah filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="5" class="text-right">Grand Total Tampil:</td>
                        <td class="text-right" id="footTotalPiutang">{{ number_format($piutangData->sum('totalpiutang'), 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($piutangData->sum('uangmuka'), 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($piutangData->sum('sudah_dibayar'), 0, ',', '.') }}</td>
                        <td class="text-right text-dark" id="footSisaPiutang">{{ number_format($piutangData->sum('sisa_piutang_aktual'), 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                        <td class="text-right text-success" id="footTotalBayar">Rp 0</td>
                        <td class="text-right text-warning" id="footTotalDiskon">Rp 0</td>
                        <td class="text-right text-danger" id="footTotalTidakTerbayar">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 1: HITUNG KAPITASI / PAKET (Sesuai Khanza WindowHitungPaket)          --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalKapitasi" tabindex="-1" role="dialog" aria-labelledby="modalKapitasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-warning py-2 text-dark">
                <h5 class="modal-title font-weight-bold text-sm" id="modalKapitasiLabel">
                    <i class="fas fa-calculator mr-1"></i> Perhitungan Kapitasi / Paket Piutang
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-3">
                <div class="form-group mb-2">
                    <label class="text-xs font-weight-bold text-muted mb-1">Total Piutang Belum Dibayar (Dipilih):</label>
                    <input type="text" id="kapitasiTotalPiutangDipilih" class="form-control form-control-sm font-weight-bold text-dark" readonly value="Rp 0">
                </div>

                <div class="form-group mb-2">
                    <label class="text-xs font-weight-bold text-primary mb-1">Nilai Kapitasi / Paket yang Dibayar (Rp):</label>
                    <input type="number" id="kapitasiNilaiPaket" class="form-control form-control-sm font-weight-bold text-success" placeholder="Masukkan total bayar paket..." min="1">
                    <small class="text-muted">Contoh: Total tagihan 50jt dibayar paket 40jt</small>
                </div>

                <div class="row mb-2">
                    <div class="col-6">
                        <label class="text-xs font-weight-bold text-muted mb-1">Persentase Bayar (%):</label>
                        <input type="text" id="kapitasiPersentase" class="form-control form-control-sm font-weight-bold text-info" readonly value="0 %">
                    </div>
                    <div class="col-6">
                        <label class="text-xs font-weight-bold text-muted mb-1">Sisa / Kekurangan (Rp):</label>
                        <input type="text" id="kapitasiKekurangan" class="form-control form-control-sm font-weight-bold text-danger" readonly value="Rp 0">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="text-xs font-weight-bold text-dark mb-1">Jika Ada Kekurangan Pembayaran, Alokasikan Ke:</label>
                    <select id="kapitasiOpsiKekurangan" class="form-control form-control-sm">
                        <option value="diskon">1. Masukkan ke Diskon Bayar</option>
                        <option value="tidak_terbayar">2. Masukkan ke Piutang Tidak Terbayar</option>
                        <option value="tetap_sisa">3. Tetap sebagai Sisa Piutang (Kekurangan tetap ditagih)</option>
                    </select>
                </div>

                <div class="alert alert-info py-2 text-xs mb-0">
                    <i class="fas fa-info-circle mr-1"></i> Nilai pelunasan akan dihitung secara proporsional berdasarkan sisa piutang masing-masing pasien yang dicentang di tabel.
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-sm btn-primary font-weight-bold" id="btnApplyKapitasi">
                    <i class="fas fa-check mr-1"></i> Terapkan ke Tabel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 2: IMPORT CSV PELUNASAN MASSAL / UMBAL (Sesuai Khanza ppUmbal)       --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalUmbal" tabindex="-1" role="dialog" aria-labelledby="modalUmbalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-info py-2 text-white">
                <h5 class="modal-title font-weight-bold text-sm" id="modalUmbalLabel">
                    <i class="fas fa-file-csv mr-1"></i> Import Pelunasan Massal dari File CSV (Umbal BPJS/Asuransi)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-3">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group mb-2">
                            <label class="text-xs font-weight-bold text-dark mb-1">Pilih File CSV (.csv):</label>
                            <div class="custom-file custom-file-sm">
                                <input type="file" class="custom-file-input" id="fileCsvUmbal" accept=".csv, .txt">
                                <label class="custom-file-label text-truncate text-xs" for="fileCsvUmbal" id="lblFileCsv">Pilih file CSV...</label>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">Delimiter / Pemisah Kolom CSV:</label>
                            <select id="csvDelimiter" class="form-control form-control-sm">
                                <option value=";">Titik Koma (;) - Standar Khanza & Excel Indonesia</option>
                                <option value=",">Koma (,) - Standar CSV Internasional</option>
                                <option value="\t">Tab</option>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">Pencocokan Kolom Identitas Berdasarkan:</label>
                            <select id="csvMatchType" class="form-control form-control-sm">
                                <option value="auto">Otomatis (Cek No.SEP dulu, jika tidak ada cek No.Rawat)</option>
                                <option value="no_sep">Nomor SEP BPJS</option>
                                <option value="no_rawat">Nomor Rawat</option>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">Alokasi Selisih (Jika Bayar < Sisa Piutang):</label>
                            <select id="csvSelisihType" class="form-control form-control-sm">
                                <option value="diskon">Masukkan Selisih ke Diskon Bayar</option>
                                <option value="tidak_terbayar">Masukkan Selisih ke Tidak Terbayar</option>
                                <option value="tetap_sisa">Biarkan Selisih Tetap sebagai Sisa Piutang</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card bg-light border-0 p-2 text-xs mb-2">
                            <div class="font-weight-bold text-primary mb-1"><i class="fas fa-question-circle mr-1"></i> Format CSV yang Didukung:</div>
                            <p class="mb-1">File CSV berisi minimal 2 kolom tanpa header atau dengan header:</p>
                            <pre class="bg-white p-2 border rounded mb-2" style="font-size: 11px;">
0123R0010921V000123;450000
2021/09/25/000005;1500000
                            </pre>
                            <ul class="pl-3 mb-0 text-muted">
                                <li>Kolom 1: No.SEP atau No.Rawat</li>
                                <li>Kolom 2: Nominal yang disetujui / dibayar</li>
                            </ul>
                        </div>

                        <div id="csvMatchResultAlert" class="alert alert-secondary py-2 text-xs mb-0 d-none">
                            <i class="fas fa-bullhorn mr-1"></i> <span id="csvMatchResultText">Menunggu proses file...</span>
                        </div>
                    </div>
                </div>

                {{-- PREVIEW DATA CSV --}}
                <div class="mt-3 d-none" id="csvPreviewContainer">
                    <label class="text-xs font-weight-bold text-muted mb-1">Preview 5 Baris Pertama CSV:</label>
                    <div class="table-responsive border rounded" style="max-height: 120px;">
                        <table class="table table-sm table-striped text-xs mb-0" id="tablePreviewCsv">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kolom Identitas (SEP / Rawat)</th>
                                    <th class="text-right">Nominal Dibayar (Rp)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-info text-white font-weight-bold" id="btnProcessCsvUmbal" disabled>
                    <i class="fas fa-cogs mr-1"></i> Cocokkan & Terapkan ke Tabel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 3: FILTER DATA PIUTANG BERDASARKAN NO. RAWAT DARI EXCEL / PASTE      --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalImportExcel" tabindex="-1" role="dialog" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-success py-2 text-white">
                <h5 class="modal-title font-weight-bold text-sm" id="modalImportExcelLabel">
                    <i class="fas fa-file-excel mr-1"></i> Filter Data Piutang Berdasarkan No. Rawat (Excel / Paste)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-3">
                <div class="alert alert-light border py-2 px-3 text-xs mb-3 text-muted">
                    <i class="fas fa-info-circle text-info mr-1"></i> 
                    Fitur ini bekerja seperti <strong>filter pencarian</strong>: unggah file Excel atau masukkan daftar No. Rawat, sistem akan langsung memfilter database dan menampilkan seluruh data piutang untuk pasien tersebut di tabel.
                </div>

                <ul class="nav nav-tabs mb-3" id="tabImportExcel" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold text-xs" id="tab-file-tab" data-toggle="tab" href="#tab-file-content" role="tab">
                            <i class="fas fa-file-upload mr-1"></i> Upload File Excel (.xlsx / .xls / .csv)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold text-xs" id="tab-paste-tab" data-toggle="tab" href="#tab-paste-content" role="tab">
                            <i class="fas fa-paste mr-1"></i> Paste Teks dari Clipboard
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="tabImportExcelContent">
                    {{-- TAB 1: UPLOAD FILE EXCEL --}}
                    <div class="tab-pane fade show active" id="tab-file-content" role="tabpanel">
                        <div class="form-group mb-2">
                            <label class="text-xs font-weight-bold text-dark mb-1">Pilih File Excel (.xlsx, .xls, .csv):</label>
                            <div class="custom-file custom-file-sm">
                                <input type="file" class="custom-file-input" id="fileExcelNoRawat" accept=".xlsx, .xls, .csv, .txt">
                                <label class="custom-file-label text-truncate text-xs" for="fileExcelNoRawat" id="lblFileExcel">Pilih file Excel...</label>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle mr-1"></i> File cukup berisi satu kolom No. Rawat (contoh: <code>2026/08/01/000188</code>). Baris header seperti "No Rawat" akan otomatis diabaikan.
                            </small>
                        </div>
                    </div>

                    {{-- TAB 2: PASTE TEKS LANGSUNG --}}
                    <div class="tab-pane fade" id="tab-paste-content" role="tabpanel">
                        <div class="form-group mb-2">
                            <label class="text-xs font-weight-bold text-dark mb-1">Paste Kolom No. Rawat dari Excel / Teks:</label>
                            <textarea id="pasteNoRawatText" class="form-control form-control-sm" rows="5" placeholder="Paste daftar No. Rawat di sini (satu No. Rawat per baris atau dipisahkan koma/spasi)..."></textarea>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-lightbulb mr-1"></i> Tip: Anda bisa langsung mem-blok 1 kolom No. Rawat di Excel, tekan <code>Ctrl+C</code>, lalu paste di kotak ini.
                            </small>
                        </div>
                    </div>
                </div>

                {{-- STATUS DETEKSI & PREVIEW HASIL --}}
                <div id="previewRawatContainer" class="d-none mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="text-xs font-weight-bold text-dark mb-0">
                            <i class="fas fa-list-ol mr-1"></i> No. Rawat Terbaca dari File: <span id="lblDetectedRawatCount" class="badge badge-primary px-2 py-1">0 Pasien</span>
                        </label>
                    </div>
                    <div class="p-2 border rounded bg-light" style="max-height: 120px; overflow-y: auto;" id="previewRawatBadgeList"></div>
                </div>

                <div id="alertExcelStatus" class="alert alert-success py-2 text-xs my-3 d-none">
                    <span id="alertExcelStatusText"></span>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-sm btn-success font-weight-bold px-3 shadow" id="btnFilterByExcelList" disabled>
                    <i class="fas fa-search mr-1"></i> Tampilkan & Filter Data di Tabel (<span id="lblBtnCount">0</span> Pasien)
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="/plugins/select2/js/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    // =========================================================================
    // JAVASCRIPT LOGIC UNTUK PELUNASAN PIUTANG (MODUL KEUANGAN KHANZA)
    // =========================================================================
    $(document).ready(function() {

        // Inisialisasi Select2
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }

        // State & Variabel Global
        let totalPiutangDipilih = 0;
        let totalAkanDibayar = 0;
        let totalDiskon = 0;
        let totalTidakTerbayar = 0;
        let selectedRowsCount = 0;
        let parsedCsvData = [];

        // Inisialisasi tampilan awal: Tidak auto-centang dan tidak auto-lunas agar nilai tetap aman
        updateSummaryStats();

        // ---------------------------------------------------------------------
        // TOGGLE HIDE / SHOW FILTER PENCARIAN (SELALU TERBUKA SAAT REFRESH/LOAD)
        // ---------------------------------------------------------------------
        const $collapseFilter = $('#collapseFilter');
        const $icon = $('#iconFilterCollapse');
        const $text = $('#textFilterCollapse');
        const $badgeCollapsed = $('#badgeFilterCollapsed');
        const $headerBtn = $('#btnToggleCollapseFilter');
        const $quickBtn = $('#btnQuickToggleFilter');
        const $quickText = $('#textQuickToggleFilter');

        let isFilterOpen = true;

        function setFilterVisibility(open, animate = true) {
            isFilterOpen = open;

            if (open) {
                // TAMPILKAN FILTER
                $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $text.text('Sembunyikan Filter');
                $headerBtn.removeClass('btn-primary text-white').addClass('btn-outline-primary');
                $badgeCollapsed.addClass('d-none');
                $quickText.text('Sembunyikan Filter');
                $quickBtn.removeClass('btn-info text-white').addClass('btn-outline-secondary');

                if (animate) {
                    $collapseFilter.stop(true, false).slideDown(200);
                } else {
                    $collapseFilter.show();
                }
            } else {
                // SEMBUNYIKAN FILTER
                $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                $text.text('Buka Filter Pencarian');
                $headerBtn.removeClass('btn-outline-primary').addClass('btn-primary text-white');
                $badgeCollapsed.removeClass('d-none');
                $quickText.text('Tampilkan Filter');
                $quickBtn.removeClass('btn-outline-secondary').addClass('btn-info text-white');

                if (animate) {
                    $collapseFilter.stop(true, false).slideUp(200);
                } else {
                    $collapseFilter.hide();
                }
            }
        }

        function toggleFilterCard() {
            setFilterVisibility(!isFilterOpen, true);
        }

        // 1. Klik pada header card filter (abaikan jika mengklik tombol/input di dalamnya)
        $('#headingFilter').off('click').on('click', function(e) {
            if ($(e.target).closest('button, a, input, select, label').length) {
                return;
            }
            toggleFilterCard();
        });

        // 2. Klik pada tombol khusus di header
        $('#btnToggleCollapseFilter').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleFilterCard();
        });

        // 3. Klik tombol cepat di toolbar tabel
        $('#btnQuickToggleFilter').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleFilterCard();
        });

        // Hapus flag localStorage & selalu buka filter secara default saat load
        try {
            localStorage.removeItem('filter_piutang_hidden');
        } catch(e) {}
        setFilterVisibility(true, false);

        // ---------------------------------------------------------------------
        // 1. EVENT CHECKBOX SELEKSI BARIS & TOMBOL PENGISIAN NILAI BAYAR
        // ---------------------------------------------------------------------
        $('#checkAllHeader').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', isChecked).trigger('change');
        });

        $('#btnSelectAll').on('click', function() {
            $('#checkAllHeader').prop('checked', true);
            $('.row-checkbox').prop('checked', true).trigger('change');
        });

        $('#btnUnselectAll').on('click', function() {
            $('#checkAllHeader').prop('checked', false);
            $('.row-checkbox').prop('checked', false).trigger('change');
        });



        // Event saat checkbox per baris diubah
        $(document).on('change', '.row-checkbox', function() {
            const tr = $(this).closest('tr');
            const index = tr.data('index');
            const isChecked = $(this).is(':checked');

            if (isChecked) {
                tr.addClass('table-primary');
            } else {
                tr.removeClass('table-primary table-danger');
                // Jika batal centang, kembalikan input ke 0
                $(`#inputBayar-${index}`).val(0);
                $(`#inputDiskon-${index}`).val(0);
                $(`#inputTidakTerbayar-${index}`).val(0);
            }

            recalculateRow(tr);
            updateSummaryStats();
        });



        // ---------------------------------------------------------------------
        // 2. EVENT INPUT BAYAR / DISKON / TIDAK TERBAYAR
        // ---------------------------------------------------------------------
        $(document).on('input', '.input-bayar, .input-diskon, .input-tidak-terbayar', function() {
            const tr = $(this).closest('tr');
            const cb = tr.find('.row-checkbox');

            // Jika user mengetik angka > 0 dan baris belum dicentang, otomatis centang
            const val = parseFloat($(this).val()) || 0;
            if (val > 0 && !cb.is(':checked')) {
                cb.prop('checked', true);
                tr.addClass('table-primary');
            }

            recalculateRow(tr);
            updateSummaryStats();
        });

        /**
         * Menghitung sisa piutang per baris saat nilai bayar/diskon berubah
         */
        function recalculateRow(tr) {
            const index = tr.data('index');
            const sisaAwal = parseFloat(tr.data('sisa-awal')) || 0;
            const bayar = parseFloat($(`#inputBayar-${index}`).val()) || 0;
            const diskon = parseFloat($(`#inputDiskon-${index}`).val()) || 0;
            const tidakTerbayar = parseFloat($(`#inputTidakTerbayar-${index}`).val()) || 0;

            const totalPelunasan = bayar + diskon + tidakTerbayar;
            const sisaAkhir = sisaAwal - totalPelunasan;

            const cellSisa = $(`#cellSisa-${index}`);

            if (sisaAkhir < -0.01) {
                // Warning jika pelunasan melebihi sisa piutang
                cellSisa.html(`<span class="badge badge-danger" title="Pelunasan melebihi sisa!">${formatRupiah(sisaAkhir)}</span>`);
                tr.addClass('table-danger');
            } else {
                cellSisa.html(formatRupiah(Math.max(0, sisaAkhir)));
                tr.removeClass('table-danger');
            }
        }

        /**
         * Update summary statistics & cards
         */
        function updateSummaryStats() {
            totalPiutangDipilih = 0;
            totalAkanDibayar = 0;
            totalDiskon = 0;
            totalTidakTerbayar = 0;
            selectedRowsCount = 0;

            $('.row-checkbox:checked').each(function() {
                const tr = $(this).closest('tr');
                const index = tr.data('index');
                const sisaAwal = parseFloat(tr.data('sisa-awal')) || 0;
                const bayar = parseFloat($(`#inputBayar-${index}`).val()) || 0;
                const diskon = parseFloat($(`#inputDiskon-${index}`).val()) || 0;
                const tidakTerbayar = parseFloat($(`#inputTidakTerbayar-${index}`).val()) || 0;

                totalPiutangDipilih += sisaAwal;
                totalAkanDibayar += bayar;
                totalDiskon += diskon;
                totalTidakTerbayar += tidakTerbayar;
                selectedRowsCount++;
            });

            // Update Badge & Buttons
            $('#badgeSelectedCount').html(`<i class="fas fa-check-double mr-1"></i> ${selectedRowsCount} Pasien Dipilih`);
            $('#btnCounter').text(selectedRowsCount);
            $('#statCountDipilih').text(`${selectedRowsCount} transaksi dipilih`);

            // Update Widgets
            $('#statTotalPiutangDipilih').text(formatRupiah(totalPiutangDipilih));
            $('#statTotalAkanDibayar').text(formatRupiah(totalAkanDibayar));
            $('#statTotalDiskon').text(formatRupiah(totalDiskon));
            $('#statTotalTidakTerbayar').text(formatRupiah(totalTidakTerbayar));

            // Update Table Footers
            $('#footTotalBayar').text(formatRupiah(totalAkanDibayar));
            $('#footTotalDiskon').text(formatRupiah(totalDiskon));
            $('#footTotalTidakTerbayar').text(formatRupiah(totalTidakTerbayar));

            // Sync ke Modal Kapitasi
            $('#kapitasiTotalPiutangDipilih').val(formatRupiah(totalPiutangDipilih));
            hitungLiveKapitasi();
        }

        // ---------------------------------------------------------------------
        // 3. LOGIKA HITUNG KAPITASI / PAKET (MODAL KAPITASI)
        // ---------------------------------------------------------------------
        $('#kapitasiNilaiPaket').on('input', function() {
            hitungLiveKapitasi();
        });

        function hitungLiveKapitasi() {
            const nilaiPaket = parseFloat($('#kapitasiNilaiPaket').val()) || 0;
            if (totalPiutangDipilih > 0 && nilaiPaket > 0) {
                const persentase = (nilaiPaket / totalPiutangDipilih) * 100;
                $('#kapitasiPersentase').val(persentase.toFixed(2) + ' %');

                const kekurangan = totalPiutangDipilih - nilaiPaket;
                $('#kapitasiKekurangan').val(formatRupiah(Math.max(0, kekurangan)));
            } else {
                $('#kapitasiPersentase').val('0 %');
                $('#kapitasiKekurangan').val('Rp 0');
            }
        }

        $('#btnApplyKapitasi').on('click', function() {
            const nilaiPaket = parseFloat($('#kapitasiNilaiPaket').val()) || 0;
            const opsiKekurangan = $('#kapitasiOpsiKekurangan').val();

            if (selectedRowsCount === 0) {
                Swal.fire('Perhatian', 'Silakan centang pasien di tabel yang ingin dihitung kapitasinya terlebih dahulu!', 'warning');
                return;
            }
            if (nilaiPaket <= 0) {
                Swal.fire('Perhatian', 'Masukkan nilai pembayaran kapitasi/paket yang valid!', 'warning');
                return;
            }
            if (totalPiutangDipilih <= 0) {
                Swal.fire('Perhatian', 'Total piutang yang dipilih adalah 0!', 'warning');
                return;
            }

            const persentase = nilaiPaket / totalPiutangDipilih;

            // Iterasi semua baris yang dicentang dan distribusikan proporsional
            $('.row-checkbox:checked').each(function() {
                const tr = $(this).closest('tr');
                const index = tr.data('index');
                const sisaAwal = parseFloat(tr.data('sisa-awal')) || 0;

                // Proporsi pembayaran
                let bayarBaris = Math.round(sisaAwal * persentase);
                let kekuranganBaris = sisaAwal - bayarBaris;

                let diskonBaris = 0;
                let tidakTerbayarBaris = 0;

                if (kekuranganBaris > 0) {
                    if (opsiKekurangan === 'diskon') {
                        diskonBaris = kekuranganBaris;
                    } else if (opsiKekurangan === 'tidak_terbayar') {
                        tidakTerbayarBaris = kekuranganBaris;
                    }
                    // Jika tetap_sisa: diskonBaris & tidakTerbayarBaris tetap 0
                }

                $(`#inputBayar-${index}`).val(bayarBaris);
                $(`#inputDiskon-${index}`).val(diskonBaris);
                $(`#inputTidakTerbayar-${index}`).val(tidakTerbayarBaris);

                recalculateRow(tr);
            });

            updateSummaryStats();
            $('#modalKapitasi').modal('hide');

            Swal.fire({
                icon: 'success',
                title: 'Perhitungan Berhasil',
                text: `Kapitasi sebesar ${formatRupiah(nilaiPaket)} (${(persentase*100).toFixed(2)}%) berhasil diterapkan ke ${selectedRowsCount} pasien terpilih.`,
                timer: 2000,
                showConfirmButton: false
            });
        });

        // ---------------------------------------------------------------------
        // 4. LOGIKA IMPORT CSV PELUNASAN MASSAL (UMBAL)
        // ---------------------------------------------------------------------
        $('#fileCsvUmbal').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            $('#lblFileCsv').text(file.name);
            $('#csvPreviewContainer').addClass('d-none');
            $('#btnProcessCsvUmbal').prop('disabled', true);

            const reader = new FileReader();
            reader.onload = function(event) {
                const text = event.target.result;
                parseCsvContent(text);
            };
            reader.readAsText(file);
        });

        $('#csvDelimiter').on('change', function() {
            const fileInput = document.getElementById('fileCsvUmbal');
            if (fileInput.files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    parseCsvContent(event.target.result);
                };
                reader.readAsText(fileInput.files[0]);
            }
        });

        function parseCsvContent(csvText) {
            const delimiter = $('#csvDelimiter').val() === '\\t' ? '\t' : $('#csvDelimiter').val();
            const lines = csvText.split(/\r\n|\n/);
            parsedCsvData = [];

            const previewTbody = $('#tablePreviewCsv tbody');
            previewTbody.empty();

            let previewCount = 0;

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i].trim();
                if (!line) continue;

                const parts = line.split(delimiter);
                if (parts.length >= 2) {
                    const identitas = parts[0].replace(/"/g, '').trim();
                    // Bersihkan karakter non-angka kecuali koma/titik desimal
                    let nominalStr = parts[1].replace(/"/g, '').replace(/Rp|\s|\./g, '').replace(',', '.').trim();
                    const nominal = parseFloat(nominalStr);

                    if (identitas && !isNaN(nominal)) {
                        parsedCsvData.push({ identitas: identitas, nominal: nominal });

                        if (previewCount < 5) {
                            previewTbody.append(`
                                <tr>
                                    <td>${previewCount + 1}</td>
                                    <td><code>${identitas}</code></td>
                                    <td class="text-right font-weight-bold text-success">${formatRupiah(nominal)}</td>
                                </tr>
                            `);
                            previewCount++;
                        }
                    }
                }
            }

            if (parsedCsvData.length > 0) {
                $('#csvPreviewContainer').removeClass('d-none');
                $('#btnProcessCsvUmbal').prop('disabled', false);
                $('#csvMatchResultAlert').removeClass('d-none').removeClass('alert-danger alert-success').addClass('alert-info');
                $('#csvMatchResultText').text(`Terdeteksi ${parsedCsvData.length} baris data CSV valid.`);
            } else {
                $('#csvMatchResultAlert').removeClass('d-none').removeClass('alert-info alert-success').addClass('alert-danger');
                $('#csvMatchResultText').text('Tidak ada data valid yang bisa dibaca. Pastikan format kolom & delimiter sesuai!');
            }
        }

        $('#btnProcessCsvUmbal').on('click', function() {
            if (parsedCsvData.length === 0) return;

            const matchType = $('#csvMatchType').val();
            const selisihType = $('#csvSelisihType').val();

            let matchedCount = 0;
            let totalNominalMatched = 0;

            // Buat lookup map dari CSV
            const csvMap = {};
            parsedCsvData.forEach(item => {
                csvMap[item.identitas.toLowerCase()] = item.nominal;
            });

            // Iterasi baris tabel dan cocokkan
            $('#tablePiutangBody tr').each(function() {
                const tr = $(this);
                const index = tr.data('index');
                const noRawat = (tr.data('no-rawat') || '').toString().toLowerCase();
                const noSep = (tr.data('no-sep') || '').toString().toLowerCase();
                const sisaAwal = parseFloat(tr.data('sisa-awal')) || 0;

                let matchedNominal = null;

                if (matchType === 'no_sep' && noSep && csvMap[noSep] !== undefined) {
                    matchedNominal = csvMap[noSep];
                } else if (matchType === 'no_rawat' && noRawat && csvMap[noRawat] !== undefined) {
                    matchedNominal = csvMap[noRawat];
                } else if (matchType === 'auto') {
                    if (noSep && csvMap[noSep] !== undefined) {
                        matchedNominal = csvMap[noSep];
                    } else if (noRawat && csvMap[noRawat] !== undefined) {
                        matchedNominal = csvMap[noRawat];
                    }
                }

                if (matchedNominal !== null) {
                    matchedCount++;
                    totalNominalMatched += matchedNominal;

                    // Centang baris
                    const cb = tr.find('.row-checkbox');
                    cb.prop('checked', true);
                    tr.addClass('table-primary');

                    // Alokasikan nominal
                    let bayar = matchedNominal;
                    let diskon = 0;
                    let tidakTerbayar = 0;

                    if (bayar < sisaAwal) {
                        const selisih = sisaAwal - bayar;
                        if (selisihType === 'diskon') {
                            diskon = selisih;
                        } else if (selisihType === 'tidak_terbayar') {
                            tidakTerbayar = selisih;
                        }
                    }

                    $(`#inputBayar-${index}`).val(bayar);
                    $(`#inputDiskon-${index}`).val(diskon);
                    $(`#inputTidakTerbayar-${index}`).val(tidakTerbayar);

                    recalculateRow(tr);
                }
            });

            updateSummaryStats();
            $('#modalUmbal').modal('hide');

            if (matchedCount > 0) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pencocokan CSV Berhasil',
                    html: `Berhasil mencocokkan <strong>${matchedCount}</strong> data piutang dengan total pelunasan <strong>${formatRupiah(totalNominalMatched)}</strong>.`
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ada Data Cocok',
                    text: 'Tidak ada No.SEP atau No.Rawat di CSV yang cocok dengan data tabel yang sedang tampil.'
                });
            }
        });

        // ---------------------------------------------------------------------
        // 5. PROSES SIMPAN PELUNASAN KE DATABASE (AJAX POST)
        // ---------------------------------------------------------------------
        $('#btnProsesBayar').on('click', function() {
            const checkedBoxes = $('.row-checkbox:checked');
            if (checkedBoxes.length === 0) {
                Swal.fire('Perhatian', 'Pilih minimal 1 data piutang yang ingin diproses pembayarannya!', 'warning');
                return;
            }

            const tglBayar = $('#tglBayarGlobal').val();
            const kdRekBayar = $('#kdRekBayarGlobal').val();
            const namaRekBayar = $('#kdRekBayarGlobal option:selected').text();
            const nipPetugas = $('#nipPetugasGlobal').val();
            const namaPetugas = $('#nipPetugasGlobal option:selected').text();

            if (!tglBayar) {
                Swal.fire('Perhatian', 'Tanggal pembayaran harus diisi!', 'warning');
                return;
            }
            if (!kdRekBayar) {
                Swal.fire('Perhatian', 'Silakan pilih rekening akun bayar!', 'warning');
                return;
            }
            if (!nipPetugas) {
                Swal.fire('Perhatian', 'Silakan pilih petugas pelunasan!', 'warning');
                return;
            }

            // Kumpulkan payload data baris
            const items = [];
            let hasNegativeSisa = false;

            checkedBoxes.each(function() {
                const tr = $(this).closest('tr');
                const index = tr.data('index');
                const noRawat = tr.data('no-rawat');
                const noRm = tr.data('no-rm');
                const kdRekKontra = tr.data('kd-rek-kontra') || '';
                const sisaAwal = parseFloat(tr.data('sisa-awal')) || 0;

                const bayar = parseFloat($(`#inputBayar-${index}`).val()) || 0;
                const diskon = parseFloat($(`#inputDiskon-${index}`).val()) || 0;
                const tidakTerbayar = parseFloat($(`#inputTidakTerbayar-${index}`).val()) || 0;

                if ((bayar + diskon + tidakTerbayar) > (sisaAwal + 0.01)) {
                    hasNegativeSisa = true;
                }

                if ((bayar + diskon + tidakTerbayar) > 0) {
                    items.push({
                        no_rawat: noRawat,
                        no_rkm_medis: noRm,
                        bayar: bayar,
                        diskon: diskon,
                        tidak_terbayar: tidakTerbayar,
                        kd_rek_kontra: kdRekKontra
                    });
                }
            });

            if (hasNegativeSisa) {
                Swal.fire('Validasi Gagal', 'Terdapat baris di mana nilai pelunasan melebihi sisa piutang. Harap periksa kembali!', 'error');
                return;
            }

            if (items.length === 0) {
                Swal.fire('Perhatian', 'Jumlah total bayar pada baris yang dipilih masih 0!', 'warning');
                return;
            }

            // Konfirmasi Pembayaran
            Swal.fire({
                title: 'Konfirmasi Pelunasan Piutang',
                html: `
                    <div class="text-left font-weight-normal text-sm">
                        <table class="table table-sm table-borderless mb-0">
                            <tr><td class="text-muted">Jumlah Pasien:</td><td><strong>${items.length} Data</strong></td></tr>
                            <tr><td class="text-muted">Tgl Pembayaran:</td><td><strong>${tglBayar}</strong></td></tr>
                            <tr><td class="text-muted">Akun Bayar:</td><td><strong>${namaRekBayar}</strong></td></tr>
                            <tr><td class="text-muted">Petugas Pelunasan:</td><td><strong class="text-primary">${namaPetugas}</strong></td></tr>
                            <tr><td class="text-muted">Total Masuk Kas/Bank:</td><td class="text-success font-weight-bold">${formatRupiah(totalAkanDibayar)}</td></tr>
                            <tr><td class="text-muted">Total Diskon:</td><td class="text-warning font-weight-bold">${formatRupiah(totalDiskon)}</td></tr>
                            <tr><td class="text-muted">Total Tidak Terbayar:</td><td class="text-danger font-weight-bold">${formatRupiah(totalTidakTerbayar)}</td></tr>
                        </table>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Simpan Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loader
                    Swal.fire({
                        title: 'Memproses Pembayaran...',
                        text: 'Menyimpan transaksi ke database Khanza, mohon tunggu.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim AJAX POST
                    $.ajax({
                        url: "{{ route('keuangan.piutang.simpan-bayar') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            tgl_bayar: tglBayar,
                            kd_rek: kdRekBayar,
                            nip_petugas: nipPetugas,
                            items: items
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errMsg = 'Terjadi kesalahan saat memproses data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Memproses',
                                text: errMsg
                            });
                        }
                    });
                }
            });
        });

        // ---------------------------------------------------------------------
        // 5B. LOGIKA IMPORT NOMOR RAWAT SAJA DARI EXCEL (SHEETJS & PASTE)
        // ---------------------------------------------------------------------
        let detectedRawatList = [];

        // Hapus Filter No. Rawat Khusus
        $('#btnClearRawatFilter').on('click', function() {
            $('#filterNoRawatList').val('');
            $('#formFilter').submit();
        });

        // Event saat file Excel diunggah
        $('#fileExcelNoRawat').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            $('#lblFileExcel').text(file.name);
            const reader = new FileReader();

            reader.onload = function(evt) {
                try {
                    const data = new Uint8Array(evt.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });

                    // Ambil sheet pertama
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];

                    // Convert sheet ke array data
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                    const rawats = [];
                    jsonData.forEach(row => {
                        if (Array.isArray(row)) {
                            row.forEach(cell => {
                                if (cell !== null && cell !== undefined) {
                                    let val = String(cell).trim();
                                    val = val.replace(/\s+/g, '');
                                    // Abaikan header teks umum
                                    if (/^(no|nomor|rawat|no_rawat|norawat|id|no\s*rawat)$/i.test(val)) return;

                                    // Cek apakah berupa nomor rawat (panjang minimal 8 karakter, ada garis miring atau strip)
                                    if (val.length >= 8 && (val.includes('/') || val.includes('-') || /^\d+$/.test(val))) {
                                        rawats.push(val);
                                    }
                                }
                            });
                        }
                    });

                    processDetectedRawatList(rawats);
                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'Gagal membaca file Excel. Pastikan file berformat .xlsx / .xls valid.', 'error');
                }
            };

            reader.readAsArrayBuffer(file);
        });

        // Event saat paste teks di textarea
        $('#pasteNoRawatText').on('input', function() {
            const text = $(this).val();
            if (!text.trim()) {
                processDetectedRawatList([]);
                return;
            }
            const lines = text.split(/[\r\n,;\t]+/);
            const rawats = [];
            lines.forEach(l => {
                let val = l.trim().replace(/\s+/g, '');
                if (val && !/^(no|nomor|rawat|no_rawat|norawat|id|no\s*rawat)$/i.test(val)) {
                    rawats.push(val);
                }
            });
            processDetectedRawatList(rawats);
        });

        function processDetectedRawatList(rawats) {
            // Ambil daftar unik saja
            detectedRawatList = [...new Set(rawats)];

            const count = detectedRawatList.length;
            $('#lblDetectedRawatCount').text(count + ' Pasien');

            if (count === 0) {
                $('#previewRawatContainer').addClass('d-none');
                $('#btnFilterByExcelList').prop('disabled', true);
                $('#alertExcelStatus').addClass('d-none');
                return;
            }

            const badgeList = $('#previewRawatBadgeList');
            badgeList.empty();

            detectedRawatList.forEach((nr, idx) => {
                if (idx < 60) {
                    badgeList.append(`<span class="badge badge-info mr-1 mb-1 font-weight-normal px-2 py-1"><i class="fas fa-file-medical mr-1"></i>${nr}</span>`);
                }
            });

            if (count > 60) {
                badgeList.append(`<span class="badge badge-light border text-muted">+ ${count - 60} No. Rawat lainnya</span>`);
            }

            $('#lblBtnCount').text(count);
            $('#previewRawatContainer').removeClass('d-none');
            $('#btnFilterByExcelList').prop('disabled', false);

            $('#alertExcelStatus').removeClass('d-none').addClass('alert-success');
            $('#alertExcelStatusText').html(`<i class="fas fa-check-circle mr-1"></i> Terbaca <strong>${count}</strong> No. Rawat dari file. Klik tombol <strong>"Tampilkan & Filter Data di Tabel"</strong> di bawah untuk menampilkan seluruh data piutang pasien tersebut.`);
        }

        // Tampilkan semua data piutang untuk daftar No. Rawat ini di tabel (Filter database)
        $('#btnFilterByExcelList').on('click', function() {
            if (detectedRawatList.length === 0) return;
            $('#filterNoRawatList').val(detectedRawatList.join(','));
            $('input[name="tgl1"]').val('');
            $('input[name="tgl2"]').val('');
            $('input[name="cari"]').val('');
            $('#selectPenjab').val('');
            $('#selectNoTagihan').val('');
            $('#formFilter').submit();
        });

        // Quick Filter Excel langsung dari filter bar atas tanpa buka modal
        $('#quickFilterExcel').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            $('#lblQuickExcel').text(file.name);
            const reader = new FileReader();

            reader.onload = function(evt) {
                try {
                    const data = new Uint8Array(evt.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                    const rawats = [];
                    jsonData.forEach(row => {
                        if (Array.isArray(row)) {
                            row.forEach(cell => {
                                if (cell !== null && cell !== undefined) {
                                    let val = String(cell).trim().replace(/\s+/g, '');
                                    if (/^(no|nomor|rawat|no_rawat|norawat|id|no\s*rawat)$/i.test(val)) return;
                                    if (val.length >= 8 && (val.includes('/') || val.includes('-') || /^\d+$/.test(val))) {
                                        rawats.push(val);
                                    }
                                }
                            });
                        }
                    });

                    const uniqueRawats = [...new Set(rawats)];
                    if (uniqueRawats.length > 0) {
                        $('#filterNoRawatList').val(uniqueRawats.join(','));
                        $('input[name="tgl1"]').val('');
                        $('input[name="tgl2"]').val('');
                        $('input[name="cari"]').val('');
                        $('#selectPenjab').val('');
                        $('#selectNoTagihan').val('');
                        $('#formFilter').submit();
                    } else {
                        Swal.fire('Perhatian', 'Tidak ditemukan nomor rawat valid di file Excel ini.', 'warning');
                    }
                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'Gagal membaca file Excel.', 'error');
                }
            };
            reader.readAsArrayBuffer(file);
        });

        // ---------------------------------------------------------------------
        // HELPER FUNCTIONS
        // ---------------------------------------------------------------------
        function formatRupiah(angka) {
            return 'Rp ' + Number(angka).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }
    });
</script>
@endpush
