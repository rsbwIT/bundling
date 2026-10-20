@extends('layout.layoutDashboard')

@section('title', 'Hasil Pemeriksaan Gambaran Darah Tepi')

@push('styles')
<style>
    /* Styling Super Elegan & Modern Form Laboratorium */
    :root {
        --gdt-primary: #1d7969;
        --gdt-primary-dark: #135348;
        --gdt-primary-light: #e8f5f3;
        --gdt-accent: #0ea5e9;
        --gdt-surface: #ffffff;
        --gdt-bg-subtle: #f8fafc;
        --gdt-border: #e2e8f0;
    }

    .gdt-main-card {
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 12px 35px -5px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.04) !important;
        background: #ffffff;
        overflow: hidden;
    }

    .gdt-card-header {
        background: linear-gradient(135deg, #1d7969 0%, #155b4f 60%, #0f4037 100%);
        padding: 1.35rem 1.85rem;
        position: relative;
    }
    .gdt-card-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #10b981, #06b6d4, #10b981);
    }

    /* Patient Hero Banner */
    .patient-hero-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        position: relative;
        box-shadow: inset 0 1px 2px rgba(255,255,255,0.8), 0 2px 6px rgba(0,0,0,0.02);
    }
    .patient-hero-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(180deg, #1d7969 0%, #0d9488 100%);
        border-radius: 14px 0 0 14px;
    }
    .patient-badge-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
    }
    .patient-badge-value {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.2px;
    }

    /* Parameter Box Card */
    .param-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        margin-bottom: 1.25rem;
        position: relative;
    }
    .param-box:hover, .param-box:focus-within {
        border-color: #1d7969;
        box-shadow: 0 8px 24px -4px rgba(29, 121, 105, 0.12);
        transform: translateY(-2px);
    }
    .param-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .param-title-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .param-icon-pill {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--gdt-primary-light);
        color: var(--gdt-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }

    /* Form Controls */
    .param-textarea {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 0.93rem;
        line-height: 1.5;
        padding: 10px 14px;
        transition: all 0.2s ease;
        background-color: #fcfcfd;
        color: #1e293b;
    }
    .param-textarea:focus {
        background-color: #ffffff;
        border-color: #1d7969;
        box-shadow: 0 0 0 3.5px rgba(29, 121, 105, 0.16);
        outline: none;
    }

    /* Darah Lengkap Lab Indicator Pill */
    .darah-lengkap-card {
        background: linear-gradient(135deg, #f0fdf9 0%, #e6f7f3 100%);
        border: 1px solid #99f6e4;
        border-radius: 14px;
        padding: 1.15rem 1.35rem;
        position: relative;
    }
    .darah-lengkap-card .input-group-text {
        background-color: #ffffff;
        border-color: #99f6e4;
        color: #0f766e;
        font-weight: 700;
        font-size: 0.8rem;
    }
    .darah-lengkap-card input.form-control {
        border-color: #99f6e4;
        background-color: #ffffff;
        font-size: 0.95rem;
    }
    .darah-lengkap-card input.form-control:focus {
        border-color: #1d7969;
        box-shadow: 0 0 0 3px rgba(29, 121, 105, 0.2);
    }

    /* Template Chips / Quick Snippets */
    .template-chip {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
        padding: 3px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
        border: 1px solid #e2e8f0;
    }
    .template-chip:hover {
        background: #1d7969;
        color: #ffffff;
        border-color: #1d7969;
        transform: translateY(-1px);
    }

    /* Premium Action Buttons - Compact & Sleek */
    .btn-action-premium {
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.82rem;
        letter-spacing: 0.1px;
        padding: 0.35rem 0.85rem;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-action-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px -2px rgba(0,0,0,0.12);
    }
    .btn-save-main {
        background: linear-gradient(135deg, #1d7969 0%, #155b4f 100%);
        border: none;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(29, 121, 105, 0.3);
    }
    .btn-save-main:hover {
        background: linear-gradient(135deg, #228f7d 0%, #1a6d5f 100%);
        color: #ffffff;
    }

    .badge-status-glow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .badge-status-glow::before {
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }
    .badge-status-glow.status-done {
        background: #dcfce7;
        color: #15803d;
    }
    .badge-status-glow.status-done::before {
        background: #22c55e;
        box-shadow: 0 0 6px #22c55e;
    }
    .badge-status-glow.status-pending {
        background: #fef3c7;
        color: #b45309;
    }
    .badge-status-glow.status-pending::before {
        background: #f59e0b;
        box-shadow: 0 0 6px #f59e0b;
    }
</style>
@endpush

@section('konten')
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="card gdt-main-card">
            {{-- Header Elegan --}}
            <div class="gdt-card-header text-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px; box-shadow: 0 4px 12px rgba(0,0,0,0.18);">
                        <i class="fas fa-microscope fa-lg" style="color: #1d7969;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center">
                            <h5 class="font-weight-bold mb-0 text-white mr-2" style="letter-spacing: 0.3px;">FORM HASIL GAMBARAN DARAH TEPI</h5>
                            @if($hasil)
                                <span class="badge-status-glow status-done"><i class="fas fa-check"></i> Sudah Diisi</span>
                            @else
                                <span class="badge-status-glow status-pending"><i class="fas fa-clock"></i> Belum Diisi</span>
                            @endif
                        </div>
                        <small class="text-white-50">Instalasi Laboratorium Patologi Klinik & Anatomi RS Bumi Waras</small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('lab.darah-tepi.index') }}" class="btn btn-sm btn-light btn-action-premium mr-2 text-dark font-weight-bold shadow-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    @if($hasil)
                        <button type="button" class="btn btn-sm btn-danger btn-action-premium font-weight-bold shadow-sm mr-2" onclick="if(confirm('Apakah Anda yakin ingin menghapus data hasil pemeriksaan ini?')) { document.getElementById('formHapusHasil').submit(); }">
                            <i class="fas fa-trash-alt"></i> Hapus Hasil
                        </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-warning btn-action-premium font-weight-bold text-dark shadow-sm" id="btnDirectPrint">
                        <i class="fas fa-print"></i> Cetak Lembar (Ctrl + P)
                    </button>
                </div>
            </div>

            <form action="{{ route('lab.darah-tepi.store', $permintaan->noorder) }}" method="POST">
                @csrf
                <input type="hidden" name="no_rawat" value="{{ $permintaan->no_rawat }}">

                <div class="card-body p-4">
                    {{-- Alert Notifikasi --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-lg shadow-sm border-0 mb-4" style="background-color: #d1fae5; color: #065f46;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-lg mr-2"></i>
                                <div><strong>Sukses!</strong> {{ session('success') }}</div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    {{-- Hero Card Informasi Pasien --}}
                    <div class="patient-hero-card mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 mb-2 mb-md-0 border-right">
                                <div class="patient-badge-title"><i class="fas fa-user-circle mr-1 text-primary"></i> Pasien & No. RM</div>
                                <div class="patient-badge-value text-truncate" title="{{ $permintaan->nm_pasien }}">{{ ucwords(strtolower($permintaan->nm_pasien)) }}</div>
                                <div class="text-muted small">No. RM: <strong style="color: #1d7969;">{{ $permintaan->no_rkm_medis }}</strong> &bull; {{ $permintaan->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0 border-right pl-md-3">
                                <div class="patient-badge-title"><i class="fas fa-barcode mr-1 text-primary"></i> No. Order Lab & Rawat</div>
                                <div class="patient-badge-value text-dark">{{ $permintaan->noorder }}</div>
                                <div class="text-muted small">No. Rawat: {{ $permintaan->no_rawat }}</div>
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0 border-right pl-md-3">
                                <div class="patient-badge-title"><i class="fas fa-hospital mr-1 text-primary"></i> Ruang & Pengirim</div>
                                <div class="patient-badge-value text-truncate" title="{{ $ruang }}">{{ $ruang }}</div>
                                <div class="text-muted small text-truncate" title="{{ $permintaan->nm_dokter_pengirim }}">Dokter: {{ $permintaan->nm_dokter_pengirim ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 pl-md-3">
                                <div class="patient-badge-title"><i class="fas fa-calendar-alt mr-1 text-primary"></i> Tanggal Pemeriksaan</div>
                                <input type="date" name="tanggal" class="form-control form-control-sm font-weight-bold shadow-sm"
                                    value="{{ old('tanggal', $hasil->tanggal ?? ($permintaan->tgl_permintaan ?? date('Y-m-d'))) }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Form Parameter Pemeriksaan --}}
                    <div class="row">
                        {{-- Kolom Kiri: Eritrosit, Leukosit, Trombosit, Darah Lengkap --}}
                        <div class="col-lg-6">
                            <div class="param-box">
                                <div class="param-title">
                                    <div class="param-title-left">
                                        <span class="param-icon-pill"><i class="fas fa-tint"></i></span>
                                        <span>Eritrosit</span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <span class="template-chip" onclick="insertSnippet('eritrosit', 'Normositik normokromik.')">+ Normositik normokromik</span>
                                        <span class="template-chip" onclick="insertSnippet('eritrosit', 'Anisopoikilositosis.')">+ Aniso</span>
                                    </div>
                                </div>
                                <textarea name="eritrosit" id="inp_eritrosit" rows="3" class="form-control param-textarea" placeholder="Contoh: Normositik normokromik.">{{ old('eritrosit', $hasil->eritrosit ?? '') }}</textarea>
                            </div>

                            <div class="param-box">
                                <div class="param-title">
                                    <div class="param-title-left">
                                        <span class="param-icon-pill"><i class="fas fa-shield-virus"></i></span>
                                        <span>Leukosit</span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <span class="template-chip" onclick="insertSnippet('leukosit', 'Jumlah cukup, granulasi toksik netrofil, tidak ditemukan sel blast.')">+ Granulasi toksik</span>
                                        <span class="template-chip" onclick="insertSnippet('leukosit', 'Jumlah meningkat, kesan leukositosis.')">+ Leukositosis</span>
                                    </div>
                                </div>
                                <textarea name="leukosit" id="inp_leukosit" rows="3" class="form-control param-textarea" placeholder="Contoh: Jumlah cukup, granulosit imatur (stab 11%), granulasi toksik netrofil, tidak ditemukan sel blast">{{ old('leukosit', $hasil->leukosit ?? '') }}</textarea>
                            </div>

                            <div class="param-box">
                                <div class="param-title">
                                    <div class="param-title-left">
                                        <span class="param-icon-pill"><i class="fas fa-bahai"></i></span>
                                        <span>Trombosit</span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <span class="template-chip" onclick="insertSnippet('trombosit', 'Jumlah cukup, penyebaran merata, morfologi dalam batas normal.')">+ Normal</span>
                                        <span class="template-chip" onclick="insertSnippet('trombosit', 'Jumlah menurun, trombositopenia.')">+ Menurun</span>
                                    </div>
                                </div>
                                <textarea name="trombosit" id="inp_trombosit" rows="3" class="form-control param-textarea" placeholder="Contoh: Jumlah cukup, penyebaran merata, morfologi dalam batas normal.">{{ old('trombosit', $hasil->trombosit ?? '') }}</textarea>
                            </div>

                            {{-- Keterangan Nilai Darah Lengkap --}}
                            <div class="darah-lengkap-card mb-4">
                                <label class="param-title mb-2 text-dark">
                                    <div class="param-title-left">
                                        <span class="param-icon-pill" style="background: #ccfbf1; color: #0f766e;"><i class="fas fa-vial"></i></span>
                                        <span class="font-weight-bold" style="color: #0f766e;">Parameter Darah Lengkap Terkait</span>
                                    </div>
                                </label>
                                <div class="row">
                                    <div class="col-4">
                                        <label class="small text-muted font-weight-bold mb-1">WBC</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="wbc" class="form-control font-weight-bold" placeholder="5.310" value="{{ old('wbc', $hasil->wbc ?? '') }}">
                                            <div class="input-group-append"><span class="input-group-text">/µl</span></div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label class="small text-muted font-weight-bold mb-1">Hb</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="hb" class="form-control font-weight-bold" placeholder="13.5" value="{{ old('hb', $hasil->hb ?? '') }}">
                                            <div class="input-group-append"><span class="input-group-text">mg%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label class="small text-muted font-weight-bold mb-1">PLT</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="plt" class="form-control font-weight-bold" placeholder="203.000" value="{{ old('plt', $hasil->plt ?? '') }}">
                                            <div class="input-group-append"><span class="input-group-text">/µl</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Kesan, Kesimpulan, Saran, Manual Diff, Dokter --}}
                        <div class="col-lg-6">
                            <div class="param-box">
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <div class="param-title">
                                            <div class="param-title-left">
                                                <span class="param-icon-pill"><i class="fas fa-clipboard-list"></i></span>
                                                <span>Kesan</span>
                                            </div>
                                        </div>
                                        <textarea name="kesan" id="inp_kesan" rows="3" class="form-control param-textarea" placeholder="- Peningkatan ringan reaktifitas netrofil.">{{ old('kesan', $hasil->kesan ?? '') }}</textarea>
                                        <div class="mt-1">
                                            <span class="template-chip" onclick="insertSnippet('kesan', 'Peningkatan ringan reaktifitas netrofil.')">+ Reaktif netrofil</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="param-title">
                                            <div class="param-title-left">
                                                <span class="param-icon-pill"><i class="fas fa-check-double"></i></span>
                                                <span>Kesimpulan</span>
                                            </div>
                                        </div>
                                        <textarea name="kesimpulan" id="inp_kesimpulan" rows="3" class="form-control param-textarea" placeholder="- Gambaran proses inflamasi">{{ old('kesimpulan', $hasil->kesimpulan ?? '') }}</textarea>
                                        <div class="mt-1">
                                            <span class="template-chip" onclick="insertSnippet('kesimpulan', 'Gambaran proses inflamasi.')">+ Inflamasi</span>
                                            <span class="template-chip" onclick="insertSnippet('kesimpulan', 'Gambaran proses infeksi bakteri.')">+ Infeksi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="param-box">
                                <div class="param-title">
                                    <div class="param-title-left">
                                        <span class="param-icon-pill"><i class="fas fa-lightbulb"></i></span>
                                        <span>Saran</span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <span class="template-chip" onclick="insertSnippet('saran', '- Monitor darah rutin\n- CRP')">+ Darah rutin & CRP</span>
                                        <span class="template-chip" onclick="insertSnippet('saran', '- Evaluasi klinis lebih lanjut')">+ Evaluasi Klinis</span>
                                    </div>
                                </div>
                                <textarea name="saran" id="inp_saran" rows="3" class="form-control param-textarea" placeholder="- Monitor darah rutin&#10;- CRP">{{ old('saran', $hasil->saran ?? '') }}</textarea>
                            </div>

                            <div class="param-box">
                                <div class="param-title">
                                    <div class="param-title-left">
                                        <span class="param-icon-pill"><i class="fas fa-calculator"></i></span>
                                        <span>Manual Diff</span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <span class="template-chip" onclick="insertSnippet('manual_diff', 'Diff manual : Blast like 0%, stab 11%, Segmen 59%, Limfosit 27%, Monosit 3%')">+ Preset Diff Lengkap</span>
                                    </div>
                                </div>
                                <textarea name="manual_diff" id="inp_manual_diff" rows="3" class="form-control param-textarea" placeholder="Diff manual : Blast like 0%, stab 11%, Segmen 59%, Limfosit 27%...">{{ old('manual_diff', $hasil->manual_diff ?? '') }}</textarea>
                            </div>

                            <div class="param-box">
                                <div class="param-title">
                                    <div class="param-title-left">
                                        <span class="param-icon-pill"><i class="fas fa-user-md"></i></span>
                                        <span>Dokter Pemeriksa Laboratorium</span>
                                    </div>
                                </div>
                                <select name="kd_dokter_pemeriksa" class="form-control form-control-sm select2">
                                    @foreach($dokterList as $dok)
                                        <option value="{{ $dok->kd_dokter }}"
                                            {{ old('kd_dokter_pemeriksa', $hasil->kd_dokter_pemeriksa ?? 'D0000058') == $dok->kd_dokter ? 'selected' : '' }}>
                                            {{ $dok->nm_dokter }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="d-flex align-items-center mt-2" style="font-size: 0.8rem; color: #0d9488;">
                                    <i class="fas fa-qrcode mr-1.5 fa-lg"></i>
                                    <span>Otomatis menghasilkan Barcode QR Tanda Tangan Elektronik resmi pada lembar cetak</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Simpan & Cetak --}}
                <div class="card-footer bg-white border-top px-4 py-3 d-flex justify-content-between align-items-center" style="border-radius: 0 0 18px 18px;">
                    <div>
                        <button type="button" class="btn btn-warning btn-action-premium font-weight-bold text-dark shadow-sm mr-2" id="btnDirectPrintFooter">
                            <i class="fas fa-print"></i> Cetak Lembar (Ctrl + P)
                        </button>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="{{ route('lab.darah-tepi.index') }}" class="btn btn-light btn-action-premium mr-2 border">Batal</a>
                        @if($hasil)
                            <button type="button" class="btn btn-outline-danger btn-action-premium mr-2" onclick="if(confirm('Apakah Anda yakin ingin menghapus hasil pemeriksaan ini?')) { document.getElementById('formHapusHasil').submit(); }">
                                <i class="fas fa-trash-alt"></i> Hapus Hasil
                            </button>
                        @endif
                        <button type="submit" class="btn btn-action-premium btn-save-main font-weight-bold px-4">
                            <i class="fas fa-save"></i> Simpan Hasil Pemeriksaan
                        </button>
                    </div>
                </div>
            </form>

            @if($hasil)
                <form id="formHapusHasil" action="{{ route('lab.darah-tepi.destroy', $permintaan->noorder) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>
</div>

{{-- Template Tersembunyi Khusus untuk Capture Lembar Cetak Menjadi Gambar/Teks Clipboard --}}
<div style="position: absolute; left: -9999px; top: -9999px;">
    <div id="renderPrintArea" style="width: 780px; padding: 30px; background: #ffffff; color: #000; font-family: Arial, sans-serif; font-size: 13px; line-height: 1.35;">
        {{-- Kop RS --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px;">
            <tr>
                <td width="85" style="text-align: center; vertical-align: middle;">
                    @if(!empty($setting->logo))
                        <img src="data:image/png;base64,{{ base64_encode($setting->logo) }}" width="70" height="70" alt="Logo">
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle; padding-right: 85px;">
                    <div style="font-size: 17px; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 2px;">
                        {{ $setting->nama_instansi ?? 'RUMAH SAKIT BUMI WARAS' }}
                    </div>
                    <div style="font-size: 11px; line-height: 1.3;">
                        {{ $setting->alamat_instansi ?? 'Jln. Wolter Monginsidi no.235, Bandar Lampung' }}<br>
                        {{ $setting->kontak ?? '(0721) 254589' }} &nbsp;|&nbsp; E-mail : {{ $setting->email ?? 'www.rsbw.co.id' }}
                    </div>
                </td>
            </tr>
        </table>

        <div style="border-bottom: 2px solid #000; margin-top: 6px; margin-bottom: 12px;"></div>

        <div style="text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 14px;">
            HASIL PEMERIKSAAN LABORATORIUM KLINIK
        </div>

        {{-- Meta Pasien --}}
        <table style="width: 100%; border-collapse: collapse; font-size: 12.5px; margin-bottom: 12px;">
            <tr>
                <td width="18%">Tanggal</td><td width="2%">:</td>
                <td width="80%" id="pv_tanggal">{{ date('d- m- Y', strtotime($hasil->tanggal ?? ($permintaan->tgl_permintaan ?? date('Y-m-d')))) }}</td>
            </tr>
            <tr>
                <td>No. Medrec</td><td>:</td>
                <td>{{ $permintaan->no_rkm_medis }}</td>
            </tr>
            <tr>
                <td>Nama Pasien</td><td>:</td>
                <td style="font-weight: bold;">{{ strtoupper($permintaan->nm_pasien) }}</td>
            </tr>
            <tr>
                <td>Tgl Lahir</td><td>:</td>
                <td>{{ $permintaan->tgl_lahir ? date('d-m-Y', strtotime($permintaan->tgl_lahir)) : '-' }}</td>
            </tr>
            <tr>
                <td>Ruang</td><td>:</td>
                <td>{{ $ruang }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td><td>:</td>
                <td>{{ $permintaan->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td>Dokter pengirim</td><td>:</td>
                <td>{{ $permintaan->nm_dokter_pengirim ?? '-' }}</td>
            </tr>
        </table>

        {{-- Tabel Hasil --}}
        <table style="width: 100%; border-collapse: collapse; font-size: 12.5px; margin-bottom: 0;">
            <tr>
                <td style="width: 25%; border: 1px solid #000; padding: 6px 8px; font-weight: bold;">Eritrosit</td>
                <td style="border: 1px solid #000; padding: 6px 8px; white-space: pre-line;" id="pv_eritrosit">-</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold;">Leukosit</td>
                <td style="border: 1px solid #000; padding: 6px 8px; white-space: pre-line;" id="pv_leukosit">-</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold;">Trombosit</td>
                <td style="border: 1px solid #000; padding: 6px 8px; white-space: pre-line;" id="pv_trombosit">-</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold;">
                    Kesan<br>Kesimpulan
                </td>
                <td style="border: 1px solid #000; padding: 6px 8px; white-space: pre-line;" id="pv_kesan_kesimpulan">-</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold;">Saran</td>
                <td style="border: 1px solid #000; padding: 6px 8px; white-space: pre-line;" id="pv_saran">-</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px 8px; font-weight: bold;">Manual</td>
                <td style="border: 1px solid #000; padding: 6px 8px; white-space: pre-line;" id="pv_manual">-</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 5px 8px;">keterangan</td>
                <td colspan="3" style="border: 1px solid #000; padding: 5px 8px;" id="pv_keterangan"></td>
            </tr>
        </table>

        {{-- Baris Bawah WBC, Hb, PLT --}}
        <table style="width: 100%; border-collapse: collapse; font-size: 12.5px; border: 1px solid #000; border-top: none; text-align: center; font-weight: bold;">
            <tr>
                <td width="25%" style="border-right: 1px solid #000; padding: 6px 8px;" id="pv_wbc">WBC : - /µl</td>
                <td width="37%" style="border-right: 1px solid #000; padding: 6px 8px;" id="pv_hb">Hb : - mg%</td>
                <td width="38%" style="padding: 6px 8px;" id="pv_plt">PLT : - /µl</td>
            </tr>
        </table>

        {{-- Tanda Tangan Pemeriksa dengan Barcode Elektronik --}}
        @php
            $settingName = $setting->nama_instansi ?? 'RS. BUMI WARAS';
            $kab = $setting->kabupaten ?? 'Bandar Lampung';
            $dokName = $hasil->nm_dokter_pemeriksa ?? 'dr. Muhammad Nur, MSc.SpPK';
            $kdDoc = $hasil->kd_dokter_pemeriksa ?? 'D0000058';
            $tglHasil = $hasil->tanggal ?? ($permintaan->tgl_permintaan ?? date('Y-m-d'));
            $qrText = "Dikeluarkan di {$settingName}, Kabupaten/Kota {$kab} Ditandatangani secara elektronik oleh {$dokName} ID {$kdDoc} {$tglHasil}";
            $qrBase64 = DNS2D::getBarcodePNG($qrText, 'QRCODE');
        @endphp
        <div style="width: 100%; margin-top: 25px;">
            <div style="float: right; width: 240px; text-align: center; font-size: 12.5px;">
                <div>Pemeriksa</div>
                <div style="margin: 8px 0;">
                    <img src="data:image/png;base64,{{ $qrBase64 }}" width="75" height="75" alt="Barcode TTD">
                </div>
                <div style="font-weight: bold; text-decoration: underline;" id="pv_dokter">
                    {{ $dokName }}
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
$(document).ready(function() {
    function copyHasilToClipboard() {
        // Update data preview dari form saat ini
        var tglVal = $('input[name="tanggal"]').val();
        if(tglVal) {
            var d = new Date(tglVal);
            var dd = String(d.getDate()).padStart(2, '0');
            var mm = String(d.getMonth() + 1).padStart(2, '0');
            var yyyy = d.getFullYear();
            $('#pv_tanggal').text(dd + '- ' + mm + '- ' + yyyy);
        }

        $('#pv_eritrosit').text($('textarea[name="eritrosit"]').val() || '-');
        $('#pv_leukosit').text($('textarea[name="leukosit"]').val() || '-');
        $('#pv_trombosit').text($('textarea[name="trombosit"]').val() || '-');

        var kesan = $('textarea[name="kesan"]').val();
        var kesimpulan = $('textarea[name="kesimpulan"]').val();
        var kkHtml = '';
        if(kesan) kkHtml += '-   ' + kesan + '\n';
        if(kesimpulan) kkHtml += '-   ' + kesimpulan;
        $('#pv_kesan_kesimpulan').text(kkHtml || '-');

        $('#pv_saran').text($('textarea[name="saran"]').val() || '-');
        $('#pv_manual').text($('textarea[name="manual_diff"]').val() || '-');
        $('#pv_keterangan').text($('textarea[name="keterangan_tambahan"]').val() || '');

        var wbc = $('input[name="wbc"]').val() || '-';
        var hb = $('input[name="hb"]').val() || '-';
        var plt = $('input[name="plt"]').val() || '-';
        $('#pv_wbc').text('WBC : ' + wbc + ' /µl');
        $('#pv_hb').text('Hb: ' + hb + ' mg%');
        $('#pv_plt').text('PLT: ' + plt + ' /µl');

        var dokText = $('select[name="kd_dokter_pemeriksa"] option:selected').text();
        if(dokText && dokText.indexOf('--') === -1) {
            $('#pv_dokter').text(dokText.trim());
        }

        var btn1 = $('#btnCopyHasil');
        var btn2 = $('#btnCopyHasilFooter');
        btn1.html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyalin...');
        btn2.html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyalin...');

        // Render area cetak menjadi gambar dan masukkan ke Clipboard
        var printArea = document.getElementById('renderPrintArea');
        html2canvas(printArea, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }).then(function(canvas) {
            canvas.toBlob(function(blob) {
                try {
                    // Clipboard API untuk menyalin Image (bisa di paste di WA Web, Word, Telegram, Chat, SIMRS, dll)
                    var data = [new ClipboardItem({ 'image/png': blob })];
                    navigator.clipboard.write(data).then(function() {
                        btn1.html('<i class="fas fa-check mr-1"></i> Berhasil Disalin!');
                        btn2.html('<i class="fas fa-check mr-1"></i> Berhasil Disalin!');
                        alert('✅ Lembar Cetak Hasil Gambaran Darah Tepi Berhasil Disalin ke Clipboard!\n\nSekarang Anda bisa langsung tekan Ctrl + V di WhatsApp, Word, Catatan Medis, atau aplikasi lain.');
                        setTimeout(function() {
                            btn1.html('<i class="fas fa-copy mr-1"></i> <strong>Salin Hasil (Langsung Ctrl + V)</strong>');
                            btn2.html('<i class="fas fa-clipboard-check mr-1"></i> Cetak / Salin Lembar (Langsung Ctrl + V)');
                        }, 2500);
                    }).catch(function(err) {
                        fallbackCopyText(blob);
                    });
                } catch(e) {
                    fallbackCopyText(blob);
                }
            });
        });
    }

    function fallbackCopyText(blob) {
        // Jika browser membatasi image clipboard, salin format teks rapi
        var teks = "HASIL PEMERIKSAAN GAMBARAN DARAH TEPI\n" +
                   "Nama Pasien: {{ strtoupper($permintaan->nm_pasien) }}\n" +
                   "No. RM: {{ $permintaan->no_rkm_medis }}\n" +
                   "-------------------------------------------\n" +
                   "Eritrosit: " + ($('textarea[name="eritrosit"]').val() || '-') + "\n" +
                   "Leukosit: " + ($('textarea[name="leukosit"]').val() || '-') + "\n" +
                   "Trombosit: " + ($('textarea[name="trombosit"]').val() || '-') + "\n" +
                   "Kesan: " + ($('textarea[name="kesan"]').val() || '-') + "\n" +
                   "Kesimpulan: " + ($('textarea[name="kesimpulan"]').val() || '-') + "\n" +
                   "Saran: " + ($('textarea[name="saran"]').val() || '-') + "\n" +
                   "Manual: " + ($('textarea[name="manual_diff"]').val() || '-') + "\n" +
                   "WBC: " + ($('input[name="wbc"]').val() || '-') + " /µl | Hb: " + ($('input[name="hb"]').val() || '-') + " mg% | PLT: " + ($('input[name="plt"]').val() || '-') + "/µl\n" +
                   "Pemeriksa: " + $('#pv_dokter').text();

        navigator.clipboard.writeText(teks).then(function() {
            alert('✅ Teks Hasil Berhasil Disalin ke Clipboard! Silakan langsung tekan Ctrl + V.');
            $('#btnCopyHasil').html('<i class="fas fa-copy mr-1"></i> <strong>Salin Hasil (Langsung Ctrl + V)</strong>');
            $('#btnCopyHasilFooter').html('<i class="fas fa-clipboard-check mr-1"></i> Cetak / Salin Lembar (Langsung Ctrl + V)');
        });
    }

    // Fungsi cetak langsung (Ctrl + P) dari lembar hasil yang rapi
    function directPrint() {
        // Update data preview
        var tglVal = $('input[name="tanggal"]').val();
        if(tglVal) {
            var d = new Date(tglVal);
            var dd = String(d.getDate()).padStart(2, '0');
            var mm = String(d.getMonth() + 1).padStart(2, '0');
            var yyyy = d.getFullYear();
            $('#pv_tanggal').text(dd + '- ' + mm + '- ' + yyyy);
        }

        $('#pv_eritrosit').text($('textarea[name="eritrosit"]').val() || '-');
        $('#pv_leukosit').text($('textarea[name="leukosit"]').val() || '-');
        $('#pv_trombosit').text($('textarea[name="trombosit"]').val() || '-');

        var kesan = $('textarea[name="kesan"]').val();
        var kesimpulan = $('textarea[name="kesimpulan"]').val();
        var kkHtml = '';
        if(kesan) kkHtml += '-   ' + kesan + '\n';
        if(kesimpulan) kkHtml += '-   ' + kesimpulan;
        $('#pv_kesan_kesimpulan').text(kkHtml || '-');

        $('#pv_saran').text($('textarea[name="saran"]').val() || '-');
        $('#pv_manual').text($('textarea[name="manual_diff"]').val() || '-');
        $('#pv_keterangan').text($('textarea[name="keterangan_tambahan"]').val() || '');

        var wbc = $('input[name="wbc"]').val() || '-';
        var hb = $('input[name="hb"]').val() || '-';
        var plt = $('input[name="plt"]').val() || '-';
        $('#pv_wbc').text('WBC : ' + wbc + ' /µl');
        $('#pv_hb').text('Hb: ' + hb + ' mg%');
        $('#pv_plt').text('PLT: ' + plt + ' /µl');

        var dokText = $('select[name="kd_dokter_pemeriksa"] option:selected').text();
        if(dokText && dokText.indexOf('--') === -1) {
            $('#pv_dokter').text(dokText.trim());
        }

        var printContent = document.getElementById('renderPrintArea').innerHTML;
        var printFrame = document.createElement('iframe');
        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = '0';
        document.body.appendChild(printFrame);

        var frameDoc = printFrame.contentWindow.document;
        frameDoc.open();
        frameDoc.write(`
            <html>
                <head>
                    <title>HASIL PEMERIKSAAN GAMBARAN DARAH TEPI</title>
                    <style>
                        @page { size: A4 portrait; margin: 1.2cm 1.5cm; }
                        body { font-family: Arial, sans-serif; font-size: 11pt; color: #000; margin: 0; padding: 0; }
                    </style>
                </head>
                <body>${printContent}</body>
            </html>
        `);
        frameDoc.close();

        setTimeout(function() {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
            setTimeout(function() {
                document.body.removeChild(printFrame);
            }, 1000);
        }, 300);
    }

    $('#btnDirectPrint, #btnDirectPrintFooter').on('click', function() {
        directPrint();
    });

    $('#btnCopyHasil, #btnCopyHasilFooter').on('click', function() {
        copyHasilToClipboard();
    });
});

// Helper Quick Template Chips
function insertSnippet(fieldName, text) {
    var el = $('#inp_' + fieldName);
    if (!el.length) {
        el = $('textarea[name="' + fieldName + '"]');
    }
    var current = el.val();
    if (!current || current.trim() === '') {
        el.val(text);
    } else {
        if (text.startsWith('-') || current.includes('\n')) {
            el.val(current + '\n' + text);
        } else {
            el.val(current + ' ' + text);
        }
    }
    el.focus();
}
</script>
@endsection

