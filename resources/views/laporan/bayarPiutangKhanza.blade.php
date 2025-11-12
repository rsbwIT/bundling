@extends('layout.layoutDashboard')
@section('title', 'Bayar Piutang')

@section('konten')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">

        {{-- 🔍 FORM FILTER --}}
        <form action="{{ route('bayar.piutang.khanza') }}" method="GET" class="mb-3">
            <div class="row g-2 align-items-end">

                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">Cari Data</label>
                    <input type="text" name="cariNomor"
                           class="form-control form-control-sm"
                           placeholder="Nama / RM / No Rawat"
                           value="{{ request('cariNomor') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">Tanggal Awal</label>
                    <input type="date" name="tgl1"
                           class="form-control form-control-sm"
                           value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">Tanggal Akhir</label>
                    <input type="date" name="tgl2"
                           class="form-control form-control-sm"
                           value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold text-secondary small mb-2 d-flex align-items-center gap-1">
                        <i class="fas fa-layer-group text-primary"></i>
                        <span>Status / Jenis</span>
                    </label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-hospital-user text-muted"></i>
                        </span>
                        <select name="status_lanjut" class="form-select border-start-0 shadow-sm-sm rounded-end">
                            <option value="">🌐 Semua Status</option>
                            <option value="RALAN" {{ request('status_lanjut') == 'RALAN' ? 'selected' : '' }}>🏥 Rawat Jalan</option>
                            <option value="RANAP" {{ request('status_lanjut') == 'RANAP' ? 'selected' : '' }}>🛏️ Rawat Inap</option>
                            <option value="PD" {{ request('status_lanjut') == 'PD' ? 'selected' : '' }}>💊 Piutang Obat</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">Penjamin</label>
                    <button type="button"
                            class="btn btn-outline-secondary btn-sm w-100"
                            data-toggle="modal" data-target="#modal-lg">
                        <i class="fas fa-credit-card me-1"></i> Pilih Penjamin
                    </button>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold text-muted small mb-1">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fa fa-search me-1"></i> Cari Data
                    </button>
                </div>
            </div>

            {{-- 📌 Modal Pilih Penjamin --}}
            <div class="modal fade" id="modal-lg">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-handshake me-2"></i>Pilih Penjamin / Jenis Bayar</h5>
                            <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <select multiple="multiple" size="10" name="duallistbox[]">
                                @foreach ($penjab ?? [] as $item)
                                    <option value="{{ $item->kd_pj }}"
                                        {{ in_array($item->kd_pj, explode(',', request('kdPenjamin', ''))) ? 'selected' : '' }}>
                                        {{ $item->png_jawab }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="kdPenjamin" value="{{ request('kdPenjamin') }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- 📊 RINGKASAN --}}
        <div class="row mt-4 text-center">
            @php
                $cards = [
                    ['label' => 'Total (Tabel)', 'val' => $totalBaris ?? 0, 'class' => 'text-primary'],
                    ['label' => 'Total Cicilan', 'val' => 'Rp ' . number_format($totalCicilan ?? 0, 0, ',', '.'), 'class' => 'text-success'],
                    ['label' => 'Total Diskon', 'val' => 'Rp ' . number_format($totalDiskon ?? 0, 0, ',', '.'), 'class' => 'text-warning'],
                    ['label' => 'Tidak Terbayar', 'val' => 'Rp ' . number_format($totalTidakTerbayar ?? 0, 0, ',', '.'), 'class' => 'text-danger'],
                    ['label' => 'Total Seluruh', 'val' => 'Rp ' . number_format($totalKeseluruhan ?? 0, 0, ',', '.'), 'class' => 'text-dark'],
                ];
            @endphp

            @foreach ($cards as $c)
                <div class="col-md-2 col-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-3 p-2">
                        <div class="text-muted small">{{ $c['label'] }}</div>
                        <div class="fw-bold {{ $c['class'] }} fs-6">{{ $c['val'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 🧾 TOMBOL COPY & PAGINATION --}}
        <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="copyButton">
                <i class="fas fa-copy me-1"></i> Salin Tabel
            </button>
            <div>
                {{ $bayarPiutang->appends(request()->input())->links('pagination::bootstrap-4') }}
            </div>
        </div>

        {{-- 📋 TABEL DATA --}}
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered align-middle" id="tableToCopy">
                <thead class="table-light sticky-top text-center">
                    <tr>
                        <th>No</th>
                        <th>Tgl. Bayar</th>
                        <th>Nama Pasien</th>
                        <th>Tgl Piutang</th>
                        <th>No. Rawat</th>
                        <th>Status</th>
                        <th>Cicilan (Rp)</th>
                        <th>Keterangan</th>
                        <th>Diskon (Rp)</th>
                        <th>Tidak Terbayar (Rp)</th>
                        <th>Jenis Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bayarPiutang as $key => $item)
                        <tr>
                            <td class="text-center">{{ $bayarPiutang->firstItem() + $key }}</td>
                            <td>{{ $item->tgl_bayar }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->tgl_piutang_pasien ?? $item->tgl_piutang_piutang ?? '-' }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td class="text-center">{{ $item->status_lanjut ?? ($item->nota_piutang ? 'PD' : '-') }}</td>
                            <td class="text-end">{{ number_format($item->besar_cicilan, 0, ',', '.') }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td class="text-end">{{ number_format($item->diskon_piutang, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($item->tidak_terbayar, 0, ',', '.') }}</td>
                            <td>{{ $item->png_jawab }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">Tidak ada data ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    // ✅ DualListBox Penjamin
    if ($.fn.bootstrapDualListbox) {
        let dual = $('select[name="duallistbox[]"]').bootstrapDualListbox();
        $('form').on('submit', function() {
            $('input[name="kdPenjamin"]').val(
                $('select[name="duallistbox[]"]').val()?.join(',') ?? ''
            );
        });
    }

    // ✅ Copy tabel ke clipboard agar bisa di-paste langsung ke Excel
    $('#copyButton').on('click', function() {
        let table = document.getElementById("tableToCopy");
        if (!table) return;

        let rows = [];
        table.querySelectorAll("tr").forEach(tr => {
            let cols = [];
            tr.querySelectorAll("th, td").forEach(td => {
                let text = td.innerText.trim();

                // Hilangkan titik di nominal (misal 1.234.500 → 1234500)
                if (/^\d{1,3}(\.\d{3})*(,\d+)?$/.test(text)) {
                    text = text.replace(/\./g, '');
                }

                // Ganti tab dan newline agar tidak rusak di Excel
                text = text.replace(/\t/g, ' ').replace(/\n/g, ' ');
                cols.push(text);
            });
            rows.push(cols.join("\t")); // Pisah kolom pakai tab
        });

        let tsv = rows.join("\n");
        navigator.clipboard.writeText(tsv).then(() => {
            alert("✅ Tabel berhasil disalin! Sekarang bisa langsung tempel di Excel (Ctrl+V).");
        }).catch(err => {
            console.error(err);
            alert("❌ Gagal menyalin tabel!");
        });
    });
});
</script>
@endpush
@endsection
