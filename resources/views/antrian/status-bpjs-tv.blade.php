@extends('layout.layoutDashboard')
@section('title', 'Kontrol Gangguan BPJS - TV Antrian')

@push('styles')
<style>
    .bpjs-control-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0,0,0,0.06);
        transition: all 0.3s ease;
    }
    body.dark-mode .bpjs-control-card {
        border-color: #374151;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    }

    /* Switch toggle custom */
    .switch-toggle-lg {
        position: relative;
        display: inline-block;
        width: 80px;
        height: 40px;
    }
    .switch-toggle-lg input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider-toggle-lg {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #10b981;
        transition: .4s;
        border-radius: 40px;
        box-shadow: inset 0 2px 6px rgba(0,0,0,0.2);
    }
    .slider-toggle-lg:before {
        position: absolute;
        content: "";
        height: 32px;
        width: 32px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    input:checked + .slider-toggle-lg {
        background-color: #ef4444;
    }
    input:checked + .slider-toggle-lg:before {
        transform: translateX(40px);
    }

    /* Pulse animation */
    @keyframes pulseAlert {
        0% { transform: scale(0.98); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
        70% { transform: scale(1); box-shadow: 0 0 0 14px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.98); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    .pulse-danger {
        animation: pulseAlert 2s infinite ease-in-out;
    }

    /* Mini TV Preview Frame */
    .tv-preview-frame {
        border-radius: 14px;
        overflow: hidden;
        border: 4px solid #1e293b;
        background: #0f172a;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        aspect-ratio: 16 / 9;
        display: flex;
        flex-direction: column;
        color: #fff;
        position: relative;
    }
    .tv-preview-header {
        background: rgba(255,255,255,0.08);
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-size: 11px;
    }
    .tv-preview-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 12px;
        text-align: center;
        background: radial-gradient(circle at 50% 30%, #1e293b 0%, #090d16 100%);
    }
    .tv-preview-footer {
        background: #991b1b;
        color: #fff;
        font-size: 10px;
        padding: 4px 8px;
        white-space: nowrap;
        overflow: hidden;
    }
    .preview-badge-status {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 3px 8px;
        border-radius: 20px;
    }
</style>
@endpush

@section('konten')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-broadcast-tower text-warning mr-2"></i> Kontrol Status BPJS TV Antrian
                </h1>
                <p class="text-muted mb-0 small">Kelola status gangguan BPJS Kesehatan untuk tampilan layar penuh di TV Antrian Pendaftaran</p>
            </div>
            <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
                <a href="{{ route('antrian.tv') }}" target="_blank" class="btn btn-outline-primary btn-sm shadow-sm mr-1">
                    <i class="fas fa-external-link-alt mr-1"></i> Buka TV Antrian
                </a>
                <button type="button" class="btn btn-secondary btn-sm shadow-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        {{-- KOLOM KIRI: STATUS UTAMA & FORM EDIT --}}
        <div class="col-lg-7">
            {{-- CARD 1: SAKLAR UTAMA --}}
            <div class="card bpjs-control-card mb-4 {{ !empty($status['is_gangguan']) ? 'border-danger pulse-danger' : 'border-success' }}" id="mainStatusCard">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div id="statusIconWrap" class="p-3 rounded-circle mr-3 {{ !empty($status['is_gangguan']) ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                <i id="statusIcon" class="fas {{ !empty($status['is_gangguan']) ? 'fa-exclamation-triangle fa-2x' : 'fa-check-circle fa-2x' }}"></i>
                            </div>
                            <div>
                                <span class="text-uppercase font-weight-bold text-xs letter-spacing-1 text-muted">Status Siaran TV</span>
                                <h3 class="font-weight-bold mb-1" id="statusTitle">
                                    {{ !empty($status['is_gangguan']) ? 'MODE GANGGUAN AKTIF' : 'MODE NORMAL' }}
                                </h3>
                                <p class="text-muted mb-0 small" id="statusDesc">
                                    {{ !empty($status['is_gangguan']) ? 'TV Antrian sedang menampilkan pemberitahuan gangguan BPJS secara full layar.' : 'TV Antrian sedang menampilkan antrian loket pendaftaran biasa.' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-center ml-auto pt-2 pt-sm-0">
                            <label class="switch-toggle-lg mb-1">
                                <input type="checkbox" id="toggleBpjsMaster" {{ !empty($status['is_gangguan']) ? 'checked' : '' }} onchange="handleMasterToggle(this.checked)">
                                <span class="slider-toggle-lg"></span>
                            </label>
                            <div class="small font-weight-bold text-muted" id="toggleLabel">
                                {{ !empty($status['is_gangguan']) ? 'KLIK MATIKAN' : 'KLIK AKTIFKAN' }}
                            </div>
                        </div>
                    </div>

                    <div class="alert {{ !empty($status['is_gangguan']) ? 'alert-danger' : 'alert-info' }} mt-3 mb-0 small py-2 px-3 rounded-lg" id="statusNotice">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span id="statusNoticeText">
                            {{ !empty($status['is_gangguan']) 
                                ? 'Pemberitahuan gangguan BPJS sedang tayang di TV. Pasien di ruang tunggu melihat peringatan layar penuh.' 
                                : 'Sistem normal. Jika BPJS sedang error/offline, aktifkan saklar di atas agar TV otomatis menampilkan pengumuman.' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- CARD 2: KUSTOMISASI PESAN PENGUMUMAN --}}
            <div class="card bpjs-control-card">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title font-weight-bold m-0 text-dark">
                        <i class="fas fa-edit text-primary mr-2"></i> Pengaturan Konten Pengumuman TV
                    </h5>
                </div>
                <form id="formBpjsSettings" action="{{ route('antrian.status-bpjs.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="is_gangguan" id="inputIsGangguan" value="{{ !empty($status['is_gangguan']) ? '1' : '0' }}">

                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold text-sm">
                                <i class="fas fa-heading text-secondary mr-1"></i> Judul Utama Pengumuman
                            </label>
                            <input type="text" name="judul" id="inputJudul" class="form-control form-control-lg font-weight-bold" 
                                value="{{ $status['judul'] ?? 'PEMBERITAHUAN GANGGUAN SISTEM BPJS KESEHATAN' }}" required oninput="updateLivePreview()">
                            <small class="text-muted">Teks besar yang muncul di bagian paling atas layar TV.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-sm">
                                <i class="fas fa-comment-alt text-secondary mr-1"></i> Pesan Penjelasan Utama
                            </label>
                            <textarea name="pesan" id="inputPesan" rows="3" class="form-control" required oninput="updateLivePreview()">{{ $status['pesan'] ?? 'Mohon maaf atas ketidaknyamanannya. Saat ini sistem / server BPJS Kesehatan pusat sedang mengalami kendala teknis jaringan.' }}</textarea>
                            <small class="text-muted">Penjelasan kepada pasien mengenai penyebab dan situasi gangguan.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-sm">
                                <i class="fas fa-info-circle text-secondary mr-1"></i> Arahan Petugas / Catatan Tambahan
                            </label>
                            <textarea name="sub_pesan" id="inputSubPesan" rows="2" class="form-control" oninput="updateLivePreview()">{{ $status['sub_pesan'] ?? 'Pelayanan pendaftaran tetap dilayani secara manual oleh petugas loket pendaftaran. Silakan menunggu antrian Anda dipanggil.' }}</textarea>
                            <small class="text-muted">Petunjuk bagi pasien mengenai alur pendaftaran manual.</small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-sm">
                                <i class="fas fa-stream text-secondary mr-1"></i> Running Text (Teks Berjalan di Bawah TV)
                            </label>
                            <textarea name="running_text" id="inputRunningText" rows="2" class="form-control" oninput="updateLivePreview()">{{ $status['running_text'] ?? 'PENGUMUMAN RESMI: SISTEM BRIDGING BPJS KESEHATAN SEDANG MENGALAMI GANGGUAN TEKNIS. PENDAFTARAN PASIEN TETAP DILAYANI SECARA MANUAL DI LOKET PENDAFTARAN. TERIMA KASIH ATAS KESABARAN ANDA 🙏' }}</textarea>
                            <small class="text-muted">Teks berjalan di footer layar TV yang berulang secara terus-menerus.</small>
                        </div>
                    </div>

                    <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetDefaultTexts()">
                            <i class="fas fa-undo mr-1"></i> Kembalikan Teks Standar
                        </button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSimpan">
                            <i class="fas fa-save mr-1"></i> Simpan & Terapkan ke TV
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- KOLOM KANAN: LIVE PREVIEW & INFORMASI --}}
        <div class="col-lg-5">
            {{-- LIVE PREVIEW CARD --}}
            <div class="card bpjs-control-card mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title font-weight-bold m-0 text-dark">
                        <i class="fas fa-desktop text-success mr-2"></i> Live Preview Layar TV
                    </h5>
                    <span id="previewStatusBadge" class="badge {{ !empty($status['is_gangguan']) ? 'badge-danger' : 'badge-success' }} preview-badge-status">
                        {{ !empty($status['is_gangguan']) ? 'FULLSCREEN GANGGUAN' : 'ANTRIAN NORMAL' }}
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="tv-preview-frame" id="tvPreviewFrame">
                        {{-- PREVIEW GANGGUAN --}}
                        <div id="previewGangguanWrap" style="display: {{ !empty($status['is_gangguan']) ? 'flex' : 'none' }}; flex-direction:column; height:100%; width:100%;">
                            <div class="tv-preview-header">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('img/bw2.png') }}" style="height:22px; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.5));" alt="RSBW">
                                </div>
                                <div class="text-warning font-weight-bold" style="font-size:10px;">
                                    <i class="fas fa-circle text-danger mr-1" style="font-size:7px;"></i> GANGGUAN BRIDGING BPJS
                                </div>
                                <div>
                                    <img src="{{ asset('img/bpjs.png') }}" style="height:14px;" alt="BPJS">
                                </div>
                            </div>

                            <div class="tv-preview-body">
                                <div class="mb-1">
                                    <span class="badge badge-warning px-2 py-1 text-dark" style="font-size:8px; font-weight:bold;">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> PEMBERITAHUAN PELAYANAN
                                    </span>
                                </div>
                                <div class="font-weight-bold text-warning mb-1" id="prevJudul" style="font-size:12px; line-height:1.2;">
                                    {{ $status['judul'] ?? 'PEMBERITAHUAN GANGGUAN SISTEM BPJS KESEHATAN' }}
                                </div>
                                <div class="text-white mb-2" id="prevPesan" style="font-size:9px; opacity:0.9; max-width:90%;">
                                    {{ $status['pesan'] ?? 'Mohon maaf atas ketidaknyamanannya. Saat ini sistem / server BPJS Kesehatan pusat sedang mengalami kendala teknis jaringan.' }}
                                </div>

                                {{-- MINI 3 BOX --}}
                                <div class="d-flex justify-content-center gap-1 w-100 px-2 mb-2" style="gap: 6px;">
                                    <div class="p-1 rounded text-center flex-fill" style="background:rgba(255,255,255,0.08); font-size:7.5px;">
                                        <i class="fas fa-hospital-user text-success mb-1"></i>
                                        <div class="font-weight-bold">Tetap Buka</div>
                                        <div class="text-muted" style="font-size:6.5px;">Pelayanan Manual</div>
                                    </div>
                                    <div class="p-1 rounded text-center flex-fill" style="background:rgba(255,255,255,0.08); font-size:7.5px;">
                                        <i class="fas fa-id-card text-warning mb-1"></i>
                                        <div class="font-weight-bold">Siapkan KTP</div>
                                        <div class="text-muted" style="font-size:6.5px;">Kartu BPJS/Rujukan</div>
                                    </div>
                                    <div class="p-1 rounded text-center flex-fill" style="background:rgba(255,255,255,0.08); font-size:7.5px;">
                                        <i class="fas fa-user-clock text-info mb-1"></i>
                                        <div class="font-weight-bold">Mohon Sabar</div>
                                        <div class="text-muted" style="font-size:6.5px;">Antrian Dipanggil</div>
                                    </div>
                                </div>

                                <div class="text-info font-italic" id="prevSubPesan" style="font-size:7.5px;">
                                    <i class="fas fa-info-circle mr-1"></i> {{ $status['sub_pesan'] ?? 'Pelayanan pendaftaran tetap dilayani secara manual oleh petugas loket pendaftaran.' }}
                                </div>
                            </div>

                            <div class="tv-preview-footer">
                                <span id="prevRunningText">{{ $status['running_text'] ?? 'PENGUMUMAN RESMI: SISTEM BRIDGING BPJS KESEHATAN SEDANG MENGALAMI GANGGUAN TEKNIS.' }}</span>
                            </div>
                        </div>

                        {{-- PREVIEW NORMAL (ANTRIAN) --}}
                        <div id="previewNormalWrap" style="display: {{ !empty($status['is_gangguan']) ? 'none' : 'flex' }}; flex-direction:column; height:100%; width:100%; background:#e9f7ef; color:#000;">
                            <div class="d-flex align-items-center justify-content-between p-2 bg-white border-bottom">
                                <img src="{{ asset('img/bw2.png') }}" style="height:20px;" alt="RSBW">
                                <span class="font-weight-bold text-success" style="font-size:10px;">ANTRIAN PENDAFTARAN</span>
                                <img src="{{ asset('img/bpjs.png') }}" style="height:12px;" alt="BPJS">
                            </div>
                            <div class="d-flex flex-wrap p-2 justify-content-center align-items-center flex-fill" style="gap:6px;">
                                <div class="bg-white p-2 rounded shadow-sm text-center" style="width:70px; border:1px solid #198754;">
                                    <div class="badge bg-success text-white mb-1" style="font-size:6.5px;">LOKET 1</div>
                                    <div class="text-danger font-weight-bold" style="font-size:12px;">001</div>
                                    <div class="text-truncate text-xs font-weight-bold" style="font-size:7px;">PASIEN A</div>
                                </div>
                                <div class="bg-white p-2 rounded shadow-sm text-center" style="width:70px; border:1px solid #198754;">
                                    <div class="badge bg-success text-white mb-1" style="font-size:6.5px;">LOKET 2</div>
                                    <div class="text-danger font-weight-bold" style="font-size:12px;">002</div>
                                    <div class="text-truncate text-xs font-weight-bold" style="font-size:7px;">PASIEN B</div>
                                </div>
                                <div class="bg-white p-2 rounded shadow-sm text-center" style="width:70px; border:1px solid #198754;">
                                    <div class="badge bg-success text-white mb-1" style="font-size:6.5px;">LOKET 3</div>
                                    <div class="text-danger font-weight-bold" style="font-size:12px;">003</div>
                                    <div class="text-truncate text-xs font-weight-bold" style="font-size:7px;">PASIEN C</div>
                                </div>
                            </div>
                            <div class="p-1 text-center text-white" style="background:#157347; font-size:7.5px;">
                                Selamat datang di Rumah Sakit Bumi Waras | Layanan cepat, nyaman, dan profesional 🙏
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFORMASI PENGGUNAAN --}}
            <div class="card bpjs-control-card">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title font-weight-bold m-0 text-dark">
                        <i class="fas fa-question-circle text-info mr-2"></i> Panduan & Informasi
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-3 small">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            <strong>Realtime Tanpa Refresh:</strong> Layar TV di ruang tunggu mengecek status setiap 3-5 detik secara otomatis. Ketika tombol dinyalakan, TV langsung berubah seketika.
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            <strong>Tombol Cepat di Navbar:</strong> Anda juga dapat mengaktifkan / menonaktifkan status gangguan langsung dari tombol cepat <code>BPJS TV</code> di pojok kanan atas menu layout.
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            <strong>Pengumuman Suara:</strong> Saat status gangguan diaktifkan pertama kali, TV akan membunyikan bel dan mengumumkan secara audio kepada seluruh pasien yang menunggu.
                        </li>
                    </ul>

                    <div class="p-3 bg-light rounded-lg border small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Terakhir Diperbarui:</span>
                            <strong id="infoUpdatedAt">{{ $status['updated_at'] ?? '-' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Diubah Oleh:</span>
                            <strong id="infoUpdatedBy">{{ $status['updated_by'] ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const DEFAULT_TEXTS = {
        judul: 'PEMBERITAHUAN GANGGUAN SISTEM BPJS KESEHATAN',
        pesan: 'Mohon maaf atas ketidaknyamanannya. Saat ini sistem / server BPJS Kesehatan pusat sedang mengalami kendala teknis jaringan.',
        sub_pesan: 'Pelayanan pendaftaran tetap dilayani secara manual oleh petugas loket pendaftaran. Silakan menunggu antrian Anda dipanggil.',
        running_text: 'PENGUMUMAN RESMI: SISTEM BRIDGING BPJS KESEHATAN SEDANG MENGALAMI GANGGUAN TEKNIS. PENDAFTARAN PASIEN TETAP DILAYANI SECARA MANUAL DI LOKET PENDAFTARAN. TERIMA KASIH ATAS KESABARAN ANDA 🙏'
    };

    function updateLivePreview() {
        const judul = document.getElementById('inputJudul').value || DEFAULT_TEXTS.judul;
        const pesan = document.getElementById('inputPesan').value || DEFAULT_TEXTS.pesan;
        const subPesan = document.getElementById('inputSubPesan').value || DEFAULT_TEXTS.sub_pesan;
        const runningText = document.getElementById('inputRunningText').value || DEFAULT_TEXTS.running_text;

        document.getElementById('prevJudul').innerText = judul;
        document.getElementById('prevPesan').innerText = pesan;
        document.getElementById('prevSubPesan').innerHTML = '<i class="fas fa-info-circle mr-1"></i> ' + subPesan;
        document.getElementById('prevRunningText').innerText = runningText;
    }

    function resetDefaultTexts() {
        Swal.fire({
            title: 'Kembalikan Teks Standar?',
            text: 'Isian form akan diisi ulang dengan teks baku pengumuman.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kembalikan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('inputJudul').value = DEFAULT_TEXTS.judul;
                document.getElementById('inputPesan').value = DEFAULT_TEXTS.pesan;
                document.getElementById('inputSubPesan').value = DEFAULT_TEXTS.sub_pesan;
                document.getElementById('inputRunningText').value = DEFAULT_TEXTS.running_text;
                updateLivePreview();
                Swal.fire({
                    icon: 'success',
                    title: 'Teks Direset',
                    text: 'Silakan klik Simpan jika ingin menerapkannya ke TV.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    function handleMasterToggle(isChecked) {
        const actionText = isChecked ? 'Mengaktifkan MODE GANGGUAN (Layar Penuh di TV)' : 'Mengembalikan ke MODE NORMAL (Antrian TV Loket)';
        
        Swal.fire({
            title: 'Ubah Status BPJS TV?',
            text: actionText,
            icon: isChecked ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: isChecked ? '#d33' : '#28a745',
            confirmButtonText: isChecked ? 'Ya, Aktifkan Gangguan!' : 'Ya, Normal Kembali',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim AJAX toggle
                fetch("{{ route('antrian.status-bpjs.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        applyVisualStatus(data.is_gangguan, data.data);
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Berhasil Diubah!',
                            text: data.message,
                            timer: 2500,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Gagal', 'Terjadi kesalahan jaringan saat mengubah status.', 'error');
                    // kembalikan checkbox
                    document.getElementById('toggleBpjsMaster').checked = !isChecked;
                });
            } else {
                // kembalikan checkbox jika batal
                document.getElementById('toggleBpjsMaster').checked = !isChecked;
            }
        });
    }

    function applyVisualStatus(isGangguan, metaData) {
        document.getElementById('toggleBpjsMaster').checked = isGangguan;
        document.getElementById('inputIsGangguan').value = isGangguan ? '1' : '0';

        const mainCard = document.getElementById('mainStatusCard');
        const statusIconWrap = document.getElementById('statusIconWrap');
        const statusIcon = document.getElementById('statusIcon');
        const statusTitle = document.getElementById('statusTitle');
        const statusDesc = document.getElementById('statusDesc');
        const toggleLabel = document.getElementById('toggleLabel');
        const statusNotice = document.getElementById('statusNotice');
        const statusNoticeText = document.getElementById('statusNoticeText');
        const previewStatusBadge = document.getElementById('previewStatusBadge');
        const previewGangguanWrap = document.getElementById('previewGangguanWrap');
        const previewNormalWrap = document.getElementById('previewNormalWrap');

        if (isGangguan) {
            mainCard.classList.remove('border-success');
            mainCard.classList.add('border-danger', 'pulse-danger');

            statusIconWrap.classList.remove('bg-success');
            statusIconWrap.classList.add('bg-danger');

            statusIcon.classList.remove('fa-check-circle');
            statusIcon.classList.add('fa-exclamation-triangle');

            statusTitle.innerText = 'MODE GANGGUAN AKTIF';
            statusDesc.innerText = 'TV Antrian sedang menampilkan pemberitahuan gangguan BPJS secara full layar.';
            toggleLabel.innerText = 'KLIK MATIKAN';

            statusNotice.classList.remove('alert-info');
            statusNotice.classList.add('alert-danger');
            statusNoticeText.innerText = 'Pemberitahuan gangguan BPJS sedang tayang di TV. Pasien di ruang tunggu melihat peringatan layar penuh.';

            previewStatusBadge.className = 'badge badge-danger preview-badge-status';
            previewStatusBadge.innerText = 'FULLSCREEN GANGGUAN';

            previewGangguanWrap.style.display = 'flex';
            previewNormalWrap.style.display = 'none';
        } else {
            mainCard.classList.remove('border-danger', 'pulse-danger');
            mainCard.classList.add('border-success');

            statusIconWrap.classList.remove('bg-danger');
            statusIconWrap.classList.add('bg-success');

            statusIcon.classList.remove('fa-exclamation-triangle');
            statusIcon.classList.add('fa-check-circle');

            statusTitle.innerText = 'MODE NORMAL';
            statusDesc.innerText = 'TV Antrian sedang menampilkan antrian loket pendaftaran biasa.';
            toggleLabel.innerText = 'KLIK AKTIFKAN';

            statusNotice.classList.remove('alert-danger');
            statusNotice.classList.add('alert-info');
            statusNoticeText.innerText = 'Sistem normal. Jika BPJS sedang error/offline, aktifkan saklar di atas agar TV otomatis menampilkan pengumuman.';

            previewStatusBadge.className = 'badge badge-success preview-badge-status';
            previewStatusBadge.innerText = 'ANTRIAN NORMAL';

            previewGangguanWrap.style.display = 'none';
            previewNormalWrap.style.display = 'flex';
        }

        if (metaData) {
            if (metaData.updated_at) document.getElementById('infoUpdatedAt').innerText = metaData.updated_at;
            if (metaData.updated_by) document.getElementById('infoUpdatedBy').innerText = metaData.updated_by;
        }

        // Sinkronisasi dengan navbar tombol cepat jika ada
        if (typeof syncQuickNavbarBtn === 'function') {
            syncQuickNavbarBtn(isGangguan);
        }
    }

    // Submit form kustomisasi teks via AJAX
    document.getElementById('formBpjsSettings').addEventListener('submit', function(e) {
        e.preventDefault();

        const btnSimpan = document.getElementById('btnSimpan');
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan & Terapkan ke TV';

            if (data.success) {
                if (data.data) {
                    if (data.data.updated_at) document.getElementById('infoUpdatedAt').innerText = data.data.updated_at;
                    if (data.data.updated_by) document.getElementById('infoUpdatedBy').innerText = data.data.updated_by;
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan!',
                    text: 'Pengaturan dan teks pengumuman telah langsung diterapkan ke TV.',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        })
        .catch(err => {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan & Terapkan ke TV';
            console.error(err);
            Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan pengaturan.', 'error');
        });
    });
</script>
@endpush
