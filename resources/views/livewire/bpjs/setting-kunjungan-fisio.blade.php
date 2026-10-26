<div>
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="card-title font-weight-bold mb-0">
                <i class="fas fa-stethoscope mr-2"></i> Setting Nilai Kunjungan Fisio (Suhu Tubuh)
            </h5>
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
                    @foreach (['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'] as $nomor)
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
                    <i class="fas fa-database mr-1"></i> Tersimpan di setting sistem
                </small>
            </div>
        </div>
    </div>
</div>
