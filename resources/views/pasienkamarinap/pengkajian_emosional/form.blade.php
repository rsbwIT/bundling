@extends('..layout.layoutDashboard')
@section('title', 'Pengkajian Gangguan Emosional atau Psikiatri')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    /* DESAIN FORM MEDIS ELEGAN & PROFESIONAL */
    .med-card-header {
        background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
        color: #ffffff;
        border-radius: 10px 10px 0 0;
        padding: 16px 22px;
    }

    .patient-hero {
        background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%);
        border: 1px solid #ccfbf1;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(15, 118, 110, 0.06);
    }

    .badge-no-rawat {
        background-color: #0f766e;
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-family: 'Consolas', monospace;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .form-section-title {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: #ffffff;
        padding: 8px 16px;
        font-weight: 700;
        font-size: 0.92rem;
        border-radius: 6px;
        margin-top: 24px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.3px;
    }

    .form-subsection-title {
        background-color: #f1f5f9;
        color: #0f766e;
        padding: 6px 12px;
        font-weight: 700;
        font-size: 0.88rem;
        border-left: 4px solid #0f766e;
        border-radius: 0 4px 4px 0;
        margin-top: 14px;
        margin-bottom: 12px;
    }

    .card-form-box {
        border: 0;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }

    .option-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px 12px;
        transition: all 0.15s ease;
        height: 100%;
        display: flex;
        align-items: center;
    }
    .option-card:hover {
        background: #f0fdfa;
        border-color: #99f6e4;
    }

    .form-check-input {
        cursor: pointer;
        width: 1.1rem;
        height: 1.1rem;
    }
    .form-check-label {
        cursor: pointer;
        margin-left: 4px;
        user-select: none;
        font-size: 0.88rem;
    }

    .custom-input-med {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.88rem;
        transition: all 0.2s ease;
    }
    .custom-input-med:focus {
        border-color: #0f766e;
        box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
    }

    .btn-simpan-utama {
        background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
        color: #ffffff;
        font-weight: 600;
        border: none;
        padding: 8px 24px;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(15, 118, 110, 0.3);
        transition: all 0.2s ease;
    }
    .btn-simpan-utama:hover {
        background: linear-gradient(135deg, #115e59 0%, #134e4a 100%);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(15, 118, 110, 0.4);
    }
</style>
@endpush

@section('konten')
<div class="container-fluid px-3 py-3">

    <!-- Header & Navigasi -->
    <div class="card card-form-box mb-3">
        <div class="med-card-header d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold text-white d-flex align-items-center">
                    <i class="bi bi-file-earmark-medical-fill me-2 fs-4"></i> PENGKAJIAN GANGGUAN EMOSIONAL ATAU PSIKIATRI
                </h5>
                <small class="text-white-50">Formulir Asesmen Medis &amp; Keperawatan Khusus Pasien Rawat Inap (Standar Akreditasi RS)</small>
            </div>
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ url('/rawat-inap') }}" class="btn btn-light btn-sm font-weight-bold text-dark shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Data Rawat Inap
                </a>
                <a href="{{ route('ranap.pengkajian-emosional.cetak', ['no_rawat' => $noRawatFormatted]) }}" target="_blank" class="btn btn-warning btn-sm font-weight-bold shadow-sm">
                    <i class="bi bi-printer me-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        <!-- Info Pasien Hero -->
        <div class="card-body p-3">
            <div class="patient-hero p-3">
                <div class="row g-2 align-items-center text-sm">
                    <div class="col-md-3 border-end">
                        <small class="text-muted d-block font-weight-bold text-uppercase" style="font-size: 10px;">No. Rawat &amp; No. RM:</small>
                        <span class="badge-no-rawat">{{ $pasien->no_rawat }}</span>
                        <div class="mt-1 font-weight-bold text-dark fs-6">{{ $pasien->no_rkm_medis }}</div>
                    </div>
                    <div class="col-md-3 border-end">
                        <small class="text-muted d-block font-weight-bold text-uppercase" style="font-size: 10px;">Nama Pasien &amp; Kelamin:</small>
                        <strong class="text-uppercase text-dark fs-6">{{ $pasien->nm_pasien }}</strong>
                        @php
                            $umur = \Carbon\Carbon::parse($pasien->tgl_lahir)->age;
                        @endphp
                        <div class="text-secondary small">{{ $pasien->jk == 'L' ? 'Laki-Laki' : 'Perempuan' }} ({{ $umur }} Tahun)</div>
                    </div>
                    <div class="col-md-3 border-end">
                        <small class="text-muted d-block font-weight-bold text-uppercase" style="font-size: 10px;">Ruang / Kamar / Kelas:</small>
                        <span class="fw-bold text-success">{{ $pasien->nm_bangsal ?? 'Bangsal' }} ({{ $pasien->kd_kamar }})</span>
                        <div class="text-muted small">Kelas: <span class="badge bg-info text-white">{{ $pasien->kelas ?? '-' }}</span></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block font-weight-bold text-uppercase" style="font-size: 10px;">Dokter DPJP &amp; Penjamin:</small>
                        <div class="fw-bold text-dark small">{{ $pasien->nama_dokter_dpjp ?? ($pasien->nm_dokter ?? '-') }}</div>
                        <span class="badge bg-secondary">{{ $pasien->png_jawab }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i> <strong>Sukses!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- FORM INPUT PENGKAJIAN -->
    <form action="{{ route('ranap.pengkajian-emosional.store') }}" method="POST">
        @csrf
        <input type="hidden" name="no_rawat" value="{{ $pasien->no_rawat }}">

        <div class="card card-form-box mb-4">
            <div class="card-body p-4">
                
                <!-- Tanggal & Sumber Informasi -->
                <div class="row g-3 pb-3 mb-2 border-bottom align-items-center">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold text-dark small mb-1">
                            <i class="bi bi-calendar-check text-primary me-1"></i> Tanggal &amp; Waktu Pengkajian <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local" name="tanggal" class="form-control form-control-sm custom-input-med"
                            value="{{ old('tanggal', isset($pengkajian->tanggal) ? \Carbon\Carbon::parse($pengkajian->tanggal)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label font-weight-bold text-dark small mb-1">
                            <i class="bi bi-info-circle text-primary me-1"></i> Informasi Asesmen Diperoleh Dari:
                        </label>
                        @php
                            $infoDari = old('informasi_dari', $pengkajian->informasi_dari ?? 'Pasien');
                        @endphp
                        <div class="d-flex flex-wrap gap-3 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="informasi_dari" id="info_pasien" value="Pasien" {{ $infoDari == 'Pasien' ? 'checked' : '' }}>
                                <label class="form-check-label font-weight-semibold" for="info_pasien">Pasien Sendiri</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="informasi_dari" id="info_keluarga" value="Keluarga pasien" {{ $infoDari == 'Keluarga pasien' ? 'checked' : '' }}>
                                <label class="form-check-label font-weight-semibold" for="info_keluarga">Keluarga Pasien</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="informasi_dari" id="info_oranglain" value="Orang lain" {{ $infoDari == 'Orang lain' ? 'checked' : '' }}>
                                <label class="form-check-label font-weight-semibold" for="info_oranglain">Orang Lain</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN A: ALASAN MASUK -->
                <div class="form-section-title">
                    <i class="bi bi-card-text"></i> A. ALASAN MASUK RUMAH SAKIT
                </div>
                <div class="mb-3">
                    <textarea name="alasan_masuk" rows="3" class="form-control custom-input-med" 
                        placeholder="Uraikan keluhan utama pasien atau alasan keluarga membawa pasien berobat/masuk rumah sakit...">{{ old('alasan_masuk', $pengkajian->alasan_masuk ?? '') }}</textarea>
                </div>

                <!-- BAGIAN B: FAKTOR PREDISPOSISI -->
                <div class="form-section-title">
                    <i class="bi bi-shield-exclamation"></i> B. FAKTOR PREDISPOSISI
                </div>

                <div class="row g-3 mb-3">
                    <!-- 1. Riwayat Jiwa -->
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold text-dark small mb-1">1. Riwayat Gangguan Jiwa di Masa Lalu:</label>
                        @php
                            $riwayatJiwa = old('predisposisi_riwayat_jiwa_lalu', $pengkajian->predisposisi_riwayat_jiwa_lalu ?? 'Tidak');
                        @endphp
                        <div class="d-flex gap-3 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="predisposisi_riwayat_jiwa_lalu" id="jiwa_ya" value="Ya" {{ $riwayatJiwa == 'Ya' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jiwa_ya">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="predisposisi_riwayat_jiwa_lalu" id="jiwa_tidak" value="Tidak" {{ $riwayatJiwa == 'Tidak' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jiwa_tidak">Tidak</label>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Pengobatan Sebelumnya -->
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold text-dark small mb-1">2. Pengobatan Sebelumnya:</label>
                        @php
                            $obatLalu = old('predisposisi_pengobatan_sebelumnya', $pengkajian->predisposisi_pengobatan_sebelumnya ?? 'Tidak berhasil');
                        @endphp
                        <div class="d-flex flex-wrap gap-3 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="predisposisi_pengobatan_sebelumnya" id="obat_berhasil" value="Berhasil" {{ $obatLalu == 'Berhasil' ? 'checked' : '' }}>
                                <label class="form-check-label" for="obat_berhasil">Berhasil</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="predisposisi_pengobatan_sebelumnya" id="obat_kurang" value="Kurang berhasil" {{ $obatLalu == 'Kurang berhasil' ? 'checked' : '' }}>
                                <label class="form-check-label" for="obat_kurang">Kurang Berhasil</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="predisposisi_pengobatan_sebelumnya" id="obat_tidak" value="Tidak berhasil" {{ $obatLalu == 'Tidak berhasil' ? 'checked' : '' }}>
                                <label class="form-check-label" for="obat_tidak">Tidak Berhasil</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Riwayat Trauma Masa Lalu -->
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-dark small mb-2">3. Riwayat Trauma Masa Lalu:</label>
                    @php
                        $traumaList = old('predisposisi_trauma_masa_lalu', $pengkajian->predisposisi_trauma_masa_lalu ?? []);
                    @endphp
                    <div class="row g-2">
                        <div class="col-md-4 col-sm-6">
                            <div class="option-card">
                                <input class="form-check-input me-2" type="checkbox" name="predisposisi_trauma_masa_lalu[]" value="Aniaya Fisik" id="tr_fisik" {{ in_array('Aniaya Fisik', $traumaList) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tr_fisik">Aniaya Fisik</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="option-card">
                                <input class="form-check-input me-2" type="checkbox" name="predisposisi_trauma_masa_lalu[]" value="Aniaya Seksual" id="tr_seksual" {{ in_array('Aniaya Seksual', $traumaList) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tr_seksual">Aniaya Seksual</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="option-card">
                                <input class="form-check-input me-2" type="checkbox" name="predisposisi_trauma_masa_lalu[]" value="Penolakan" id="tr_tolak" {{ in_array('Penolakan', $traumaList) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tr_tolak">Penolakan</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="option-card">
                                <input class="form-check-input me-2" type="checkbox" name="predisposisi_trauma_masa_lalu[]" value="Kekerasan dalam Rumah Tangga" id="tr_kdrt" {{ in_array('Kekerasan dalam Rumah Tangga', $traumaList) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tr_kdrt">Kekerasan dalam Rumah Tangga (KDRT)</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="option-card">
                                <input class="form-check-input me-2" type="checkbox" name="predisposisi_trauma_masa_lalu[]" value="Tindakan Kriminal" id="tr_kriminal" {{ in_array('Tindakan Kriminal', $traumaList) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tr_kriminal">Tindakan Kriminal</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="option-card">
                                <input class="form-check-input me-2" type="checkbox" name="predisposisi_trauma_masa_lalu[]" value="Lain-lain" id="tr_lain" {{ in_array('Lain-lain', $traumaList) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tr_lain">Lain-lain</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <input type="text" name="predisposisi_trauma_lain_ket" class="form-control form-control-sm custom-input-med" 
                            placeholder="Keterangan riwayat trauma lainnya..." 
                            value="{{ old('predisposisi_trauma_lain_ket', $pengkajian->predisposisi_trauma_lain_ket ?? '') }}">
                    </div>
                </div>

                <!-- 4 & 5. Keluarga & Pengalaman Buruk -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold text-dark small mb-1">4. Riwayat Anggota Keluarga Mengalami Gangguan Jiwa:</label>
                        @php
                            $keluargaJiwa = old('predisposisi_riwayat_keluarga', $pengkajian->predisposisi_riwayat_keluarga ?? 'Tidak ada');
                        @endphp
                        <div class="d-flex gap-3 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="predisposisi_riwayat_keluarga" id="keluarga_ada" value="Ada" {{ $keluargaJiwa == 'Ada' ? 'checked' : '' }}>
                                <label class="form-check-label" for="keluarga_ada">Ada</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="predisposisi_riwayat_keluarga" id="keluarga_tidak" value="Tidak ada" {{ $keluargaJiwa == 'Tidak ada' ? 'checked' : '' }}>
                                <label class="form-check-label" for="keluarga_tidak">Tidak Ada</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold text-dark small mb-1">5. Pengalaman Masa Lalu yang Tidak Menyenangkan:</label>
                        <input type="text" name="predisposisi_pengalaman_buruk" class="form-control form-control-sm custom-input-med" 
                            placeholder="Contoh: trauma perceraian, kegagalan finansial, kehilangan anggota keluarga..." 
                            value="{{ old('predisposisi_pengalaman_buruk', $pengkajian->predisposisi_pengalaman_buruk ?? '') }}">
                    </div>
                </div>

                <!-- 6 & 7. HUBUNGAN SOSIAL & SPIRITUAL -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="form-subsection-title">
                            <i class="bi bi-people me-1"></i> 6. Hubungan Sosial
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-semibold text-muted mb-1">a. Orang yang Paling Berarti / Dekat:</label>
                            <input type="text" name="sosial_orang_berarti" class="form-control form-control-sm custom-input-med" 
                                placeholder="Misal: Pasangan, Ibu, Anak kandung..." 
                                value="{{ old('sosial_orang_berarti', $pengkajian->sosial_orang_berarti ?? '') }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-semibold text-muted mb-1">b. Hambatan dalam Berhubungan dengan Orang Lain:</label>
                            <input type="text" name="sosial_hambatan_berhubungan" class="form-control form-control-sm custom-input-med" 
                                placeholder="Misal: Menarik diri, mudah tersinggung, curiga..." 
                                value="{{ old('sosial_hambatan_berhubungan', $pengkajian->sosial_hambatan_berhubungan ?? '') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-subsection-title">
                            <i class="bi bi-heart-pulse me-1"></i> 7. Spiritual
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-semibold text-muted mb-1">a. Nilai dan Keyakinan Terhadap Penyakit:</label>
                            <input type="text" name="spiritual_nilai_keyakinan" class="form-control form-control-sm custom-input-med" 
                                placeholder="Pandangan pasien terhadap sakit yang dialami..." 
                                value="{{ old('spiritual_nilai_keyakinan', $pengkajian->spiritual_nilai_keyakinan ?? '') }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-semibold text-muted mb-1">b. Kegiatan Ibadah yang Dijalankan:</label>
                            <input type="text" name="spiritual_kegiatan_ibadah" class="form-control form-control-sm custom-input-med" 
                                placeholder="Aktivitas ibadah yang biasa dijalankan..." 
                                value="{{ old('spiritual_kegiatan_ibadah', $pengkajian->spiritual_kegiatan_ibadah ?? '') }}">
                        </div>
                    </div>
                </div>

                <!-- STATUS MENTAL -->
                <div class="form-subsection-title">
                    <i class="bi bi-activity me-1"></i> STATUS MENTAL
                </div>

                <div class="row g-3 mb-3">
                    <!-- 8. Kesadaran -->
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold text-dark small mb-1">8. Kesadaran:</label>
                        @php
                            $kesadaran = old('mental_kesadaran', $pengkajian->mental_kesadaran ?? 'Composmentis');
                        @endphp
                        <div class="d-flex flex-wrap gap-2 pt-1">
                            @foreach(['Composmentis', 'Apatis', 'Somnolen', 'Sopor', 'Coma'] as $optKes)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mental_kesadaran" id="kes_{{ strtolower($optKes) }}" value="{{ $optKes }}" {{ $kesadaran == $optKes ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kes_{{ strtolower($optKes) }}">{{ $optKes }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 9. Penampilan -->
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold text-dark small mb-1">9. Penampilan:</label>
                        @php
                            $penampilan = old('mental_penampilan', $pengkajian->mental_penampilan ?? 'Rapi');
                        @endphp
                        <div class="d-flex flex-wrap gap-2 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mental_penampilan" id="pen_rapi" value="Rapi" {{ $penampilan == 'Rapi' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pen_rapi">Rapi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mental_penampilan" id="pen_tidak_rapi" value="Tidak Rapi" {{ $penampilan == 'Tidak Rapi' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pen_tidak_rapi">Tidak Rapi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mental_penampilan" id="pen_tidak_sesuai" value="Penggunaan pakaian tidak sesuai" {{ $penampilan == 'Penggunaan pakaian tidak sesuai' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pen_tidak_sesuai">Tidak Sesuai</label>
                            </div>
                        </div>
                        <input type="text" name="mental_penampilan_ket" class="form-control form-control-sm custom-input-med mt-2" 
                            placeholder="Ket penampilan..." value="{{ old('mental_penampilan_ket', $pengkajian->mental_penampilan_ket ?? '') }}">
                    </div>

                    <!-- 10. Pembicaraan -->
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold text-dark small mb-1">10. Pembicaraan:</label>
                        @php
                            $pembicaraan = old('mental_pembicaraan', $pengkajian->mental_pembicaraan ?? 'Cepat');
                        @endphp
                        <div class="d-flex flex-wrap gap-2 pt-1">
                            @foreach(['Cepat', 'Keras', 'Gagap', 'Apatis', 'Lambat', 'Membisu'] as $optPem)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mental_pembicaraan" id="pem_{{ strtolower($optPem) }}" value="{{ $optPem }}" {{ $pembicaraan == $optPem ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pem_{{ strtolower($optPem) }}">{{ $optPem }}</label>
                                </div>
                            @endforeach
                        </div>
                        <input type="text" name="mental_pembicaraan_ket" class="form-control form-control-sm custom-input-med mt-2" 
                            placeholder="Ket pembicaraan..." value="{{ old('mental_pembicaraan_ket', $pengkajian->mental_pembicaraan_ket ?? '') }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- 11. Persepsi (Halusinasi / Ilusi) -->
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold text-dark small mb-1">11. Persepsi (Halusinasi / Ilusi):</label>
                        @php
                            $persepsiList = old('mental_persepsi', $pengkajian->mental_persepsi ?? []);
                        @endphp
                        <div class="d-flex flex-wrap gap-3 pt-1">
                            @foreach(['Pendengaran', 'Penglihatan', 'Perabaan', 'Pengecapan', 'Penghidu'] as $optPer)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="mental_persepsi[]" value="{{ $optPer }}" id="per_{{ strtolower($optPer) }}" {{ in_array($optPer, $persepsiList) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="per_{{ strtolower($optPer) }}">{{ $optPer }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 12. Alam Perasaan -->
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold text-dark small mb-1">12. Alam Perasaan:</label>
                        @php
                            $alamPerasaan = old('mental_alam_perasaan', $pengkajian->mental_alam_perasaan ?? 'Sedih');
                        @endphp
                        <div class="d-flex flex-wrap gap-3 pt-1">
                            @foreach(['Sedih', 'Putus asa' => 'Putus Asa', 'Gembira berlebihan' => 'Gembira Berlebih', 'Ketakutan', 'Khawatir'] as $keyAlam => $valAlam)
                                @php $valKey = is_numeric($keyAlam) ? $valAlam : $keyAlam; @endphp
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mental_alam_perasaan" id="alam_{{ md5($valKey) }}" value="{{ $valKey }}" {{ $alamPerasaan == $valKey ? 'checked' : '' }}>
                                    <label class="form-check-label" for="alam_{{ md5($valKey) }}">{{ $valAlam }}</label>
                                </div>
                            @endforeach
                        </div>
                        <input type="text" name="mental_alam_perasaan_ket" class="form-control form-control-sm custom-input-med mt-2" 
                            placeholder="Ket alam perasaan..." value="{{ old('mental_alam_perasaan_ket', $pengkajian->mental_alam_perasaan_ket ?? '') }}">
                    </div>
                </div>

                <!-- BAGIAN C: MASALAH KEPERAWATAN -->
                <div class="form-section-title">
                    <i class="bi bi-clipboard-pulse"></i> C. MASALAH KEPERAWATAN
                </div>
                @php
                    $masalahList = old('masalah_keperawatan', $pengkajian->masalah_keperawatan ?? []);
                    $masalahOpsi = [
                        'Ansietas',
                        'Harga Diri Rendah',
                        'Gangguan Citra Tubuh',
                        'Koping Individu Tidak Efektif',
                        'Ketidakberdayaan',
                        'Keputusasaan',
                        'Gangguan Pola Tidur',
                        'Isolasi Sosial',
                        'Defisit Perawatan Diri',
                        'Risiko Perilaku Kekerasan',
                        'Risiko Bunuh Diri',
                        'Gangguan Persepsi Sensori: Halusinasi'
                    ];
                @endphp
                <div class="row g-2 mb-3">
                    @foreach($masalahOpsi as $itemMasalah)
                        <div class="col-md-4 col-sm-6">
                            <div class="option-card">
                                <input class="form-check-input me-2" type="checkbox" name="masalah_keperawatan[]" value="{{ $itemMasalah }}" id="mk_{{ md5($itemMasalah) }}" {{ in_array($itemMasalah, $masalahList) ? 'checked' : '' }}>
                                <label class="form-check-label font-weight-medium" for="mk_{{ md5($itemMasalah) }}">{{ $itemMasalah }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mb-3">
                    <label class="form-label small font-weight-bold text-muted mb-1">Masalah Keperawatan Lain-lain / Tambahan:</label>
                    <input type="text" name="masalah_keperawatan_lain_ket" class="form-control form-control-sm custom-input-med" 
                        placeholder="Tuliskan masalah keperawatan tambahan jika ada..." 
                        value="{{ old('masalah_keperawatan_lain_ket', $pengkajian->masalah_keperawatan_lain_ket ?? '') }}">
                </div>

                <!-- BAGIAN D: RENCANA KEPERAWATAN -->
                <div class="form-section-title">
                    <i class="bi bi-card-checklist"></i> D. RENCANA KEPERAWATAN
                </div>
                <div class="mb-4">
                    <textarea name="rencana_keperawatan" rows="4" class="form-control custom-input-med" 
                        placeholder="Tuliskan rencana intervensi dan tindakan keperawatan...">{{ old('rencana_keperawatan', $pengkajian->rencana_keperawatan ?? "1. Bina hubungan saling percaya.\n2. Kaji faktor pencetus emosional pasien.\n3. Bantu pasien mengenali dan mengekspresikan perasaannya.\n4. Ajarkan teknik relaksasi tarik nafas dalam / distraksi.\n5. Kolaborasi pemberian terapi medis / psikiatri dengan DPJP.") }}</textarea>
                </div>

                <!-- PENGKAJI & VERIFIKASI DPJP -->
                <div class="form-section-title">
                    <i class="bi bi-pen"></i> PENGKAJI &amp; VERIFIKASI DOKTER DPJP
                </div>
                <div class="row g-3 p-3 bg-light rounded-3 mb-4">
                    <!-- Petugas Pengkaji -->
                    <div class="col-md-6 border-end">
                        <h6 class="font-weight-bold text-dark mb-2 d-flex align-items-center">
                            <i class="bi bi-person-badge text-success me-2"></i> Petugas yang Melakukan Pengkajian
                        </h6>
                        <div class="mb-2">
                            <label class="form-label small text-muted font-weight-semibold">Petugas / Perawat Pengkaji:</label>
                            <select name="nip_petugas" class="form-control form-control-sm custom-input-med">
                                <option value="">-- Pilih Petugas / Perawat --</option>
                                @foreach($petugasList as $p)
                                    <option value="{{ $p->nip }}" {{ (old('nip_petugas', $pengkajian->nip_petugas ?? '') == $p->nip) ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->nip }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label small text-muted font-weight-semibold">Tanggal Asesmen Perawat:</label>
                            <input type="date" name="tgl_pengkajian_petugas" class="form-control form-control-sm custom-input-med" 
                                value="{{ old('tgl_pengkajian_petugas', isset($pengkajian->tgl_pengkajian_petugas) ? \Carbon\Carbon::parse($pengkajian->tgl_pengkajian_petugas)->format('Y-m-d') : now()->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <!-- DPJP Verifikasi -->
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-dark mb-2 d-flex align-items-center">
                            <i class="bi bi-person-check-fill text-primary me-2"></i> Dokter Penanggung Jawab (DPJP)
                        </h6>
                        <div class="mb-2">
                            <label class="form-label small text-muted font-weight-semibold">Dokter DPJP / Verifikator:</label>
                            <select name="kd_dokter_dpjp" class="form-control form-control-sm custom-input-med">
                                <option value="">-- Pilih Dokter DPJP --</option>
                                @foreach($dokterList as $d)
                                    <option value="{{ $d->kd_dokter }}" {{ (old('kd_dokter_dpjp', $pengkajian->kd_dokter_dpjp ?? ($pasien->kd_dokter ?? '')) == $d->kd_dokter) ? 'selected' : '' }}>
                                        {{ $d->nm_dokter }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label small text-muted font-weight-semibold">Tanggal Verifikasi DPJP:</label>
                            <input type="date" name="tgl_verifikasi_dpjp" class="form-control form-control-sm custom-input-med"
                                value="{{ old('tgl_verifikasi_dpjp', isset($pengkajian->tgl_verifikasi_dpjp) ? \Carbon\Carbon::parse($pengkajian->tgl_verifikasi_dpjp)->format('Y-m-d') : now()->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Simpan & Cetak -->
                <div class="d-flex flex-wrap justify-content-end gap-2 pt-2 border-top">
                    <a href="{{ url('/rawat-inap') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                    <a href="{{ route('ranap.pengkajian-emosional.cetak', ['no_rawat' => $noRawatFormatted]) }}" target="_blank" class="btn btn-outline-success px-3">
                        <i class="bi bi-printer me-1"></i> Cetak Hasil PDF
                    </a>
                    <button type="submit" class="btn btn-simpan-utama px-4">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Formulir
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
