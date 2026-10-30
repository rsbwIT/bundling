<div>
    <div class="card card-outline card-success shadow-sm mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="card-title font-weight-bold mb-0">
                <i class="fas fa-stethoscope mr-2"></i> Setting Nilai Kunjungan Fisio (Suhu Tubuh)
            </h5>
            <div class="card-tools">
                <a href="{{ url('/setting-bpjs-casemix') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-layer-group mr-1"></i> Ke Setting Bundling
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (Session::has('message'))
                <div class="alert alert-{{ Session::get('color') }} alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-{{ Session::get('icon') }} mr-2"></i> {{ Session::get('message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="callout callout-info mb-4">
                <h6 class="font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Penjelasan Pengaturan:</h6>
                <p class="mb-1 text-muted">
                    Di SIMRS Khanza, nomor siklus / evaluasi kunjungan Fisioterapi (Rehabilitasi Medik BPJS) dicatat pada kolom <code>suhu_tubuh</code> (tabel <code>pemeriksaan_ralan</code>).
                </p>
                <p class="mb-0 text-muted">
                    Daftar angka yang dipilih di bawah ini akan diidentifikasi sistem untuk memunculkan lembar <strong>Resume Medis Fisioterapi</strong>. Pasien dengan nilai di luar settingan ini otomatis dialihkan ke template <strong>Resume Rawat Jalan Biasa</strong>.
                </p>
            </div>

            <div class="card bg-light border p-3 mb-4">
                <label class="font-weight-bold mb-2">
                    <i class="fas fa-check-square mr-1 text-success"></i> Pilihan Cepat Nomor Kunjungan (Klik untuk Aktifkan / Nonaktifkan):
                </label>
                <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                    @foreach (['1', '2', '3', '4', '5', '6', '7', '8'] as $nomor)
                        @php
                            $isActive = in_array((string)$nomor, $suhuTubuhFisio ?? [], true);
                        @endphp
                        <button type="button" 
                            wire:click="toggleCheckboxSuhu('{{ $nomor }}')"
                            class="btn btn-sm {{ $isActive ? 'btn-success font-weight-bold shadow-sm' : 'btn-outline-secondary' }}"
                            style="border-radius: 8px; min-width: 120px; padding: 7px 12px;">
                            <i class="fas {{ $isActive ? 'fa-check-circle' : 'fa-circle' }} mr-1"></i>
                            Kunjungan {{ $nomor }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">
                    <i class="fas fa-edit mr-1 text-primary"></i> Daftar Nilai Kunjungan Aktif (Bisa Diketik Manual, Pisahkan dengan Koma):
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-tags"></i></span>
                    </div>
                    <input type="text" class="form-control" wire:model.defer="customSuhuFisio"
                        placeholder="Contoh: 2, 3, 5, 6, 7 atau 1, 4, 8">
                    <div class="input-group-append">
                        <button class="btn btn-success px-4" wire:click="saveSetting" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveSetting">
                                <i class="fas fa-save mr-1"></i> Simpan Setting
                            </span>
                            <span wire:loading wire:target="saveSetting">
                                <span class="spinner-border spinner-border-sm mr-1"></span> Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="alert alert-secondary d-flex align-items-center justify-content-between mt-3 mb-0">
                <div>
                    <span class="text-muted mr-2">Nilai yang saat ini aktif digunakan:</span>
                    <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 15px; letter-spacing: 1px;">
                        {{ !empty($suhuTubuhFisio) ? '[' . implode(', ', $suhuTubuhFisio) . ']' : '(Kosong - Semua Menggunakan Resume Ralan Biasa)' }}
                    </span>
                </div>
                <small class="text-muted">
                    <i class="fas fa-database mr-1"></i> Tersimpan di tabel <code>bw_setting_fisio</code>
                </small>
            </div>
        </div>
    </div>

    <!-- 📊 TRACKER LOG AKTIVITAS PERUBAHAN SETTING KUNJUNGAN FISIO (KHUSUS USER: 01091999) -->
    @if ($canViewTracker)
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-history mr-2"></i> Tracker Log Aktivitas Setting Kunjungan Fisio
                </h5>
                <div class="card-tools d-flex align-items-center">
                    <div class="input-group input-group-sm" style="width: 260px;">
                        <input type="text" class="form-control bg-light border-0" 
                            wire:model.debounce.400ms="searchTracker" 
                            placeholder="Cari user / keterangan...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-light border-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered mb-0 align-middle">
                        <thead class="bg-light">
                            <tr class="text-center font-weight-bold text-secondary" style="font-size: 13px;">
                                <th style="width: 50px;">NO</th>
                                <th style="width: 170px;">WAKTU</th>
                                <th style="width: 220px;">PENGGUNA / USER</th>
                                <th style="width: 140px;">AKSI</th>
                                <th style="width: 160px;">NILAI SEBELUMNYA</th>
                                <th style="width: 160px;">NILAI BARU</th>
                                <th>KETERANGAN AKTIVITAS</th>
                                <th style="width: 130px;">IP ADDRESS</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13.5px;">
                            @if ($logs && count($logs) > 0)
                                @foreach ($logs as $index => $log)
                                    @php
                                        $oldArr = json_decode($log->nilai_lama, true) ?? [];
                                        $newArr = json_decode($log->nilai_baru, true) ?? [];
                                    @endphp
                                    <tr>
                                        <td class="text-center font-weight-bold text-muted">
                                            {{ $logs->firstItem() + $index }}
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <i class="far fa-clock text-primary mr-1"></i>
                                            <span class="font-weight-bold">
                                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y') }}
                                            </span>
                                            <small class="text-muted d-block">
                                                {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }} WIB
                                            </small>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">
                                                <i class="fas fa-user-circle text-info mr-1"></i>
                                                {{ $log->nama_user ?: '-' }}
                                            </div>
                                            <small class="text-muted">
                                                NIK / ID: <code>{{ $log->id_user ?: '-' }}</code>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary px-2 py-1">
                                                <i class="fas fa-edit mr-1"></i> {{ $log->aksi }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if (!empty($oldArr))
                                                <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 12px;">
                                                    [{{ implode(', ', $oldArr) }}]
                                                </span>
                                            @else
                                                <span class="text-muted font-italic">[Kosong]</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (!empty($newArr))
                                                <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 12px;">
                                                    [{{ implode(', ', $newArr) }}]
                                                </span>
                                            @else
                                                <span class="text-muted font-italic">[Kosong]</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-dark font-weight-500">
                                                {{ $log->keterangan }}
                                            </span>
                                        </td>
                                        <td class="text-center text-muted font-monospace" style="font-size: 12px;">
                                            <i class="fas fa-network-wired text-secondary mr-1"></i>
                                            {{ $log->ip_address ?: '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle fa-2x d-block mb-2 text-secondary"></i>
                                    Belum ada data tracker log aktivitas perubahan setting kunjungan fisio.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($logs && $logs->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between align-items-center py-2">
                    <small class="text-muted">
                        Menampilkan {{ $logs->firstItem() }} s/d {{ $logs->lastItem() }} dari total {{ $logs->total() }} log aktivitas
                    </small>
                    <div>
                        {{ $logs->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
