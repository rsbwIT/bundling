@extends('layout.layoutDashboard')
@section('title', 'Bayar Piutang')

@section('konten')
<div class="card shadow-sm">
    <div class="card-body">
        {{-- 🔍 Form Filter --}}
        <form action="{{ route('bayar.piutang.khanza') }}" method="GET" class="mb-3">
            <div class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="cariNomor"
                           class="form-control form-control-sm"
                           placeholder="Cari Nama / RM / No Rawat"
                           value="{{ request('cariNomor') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="tgl1"
                           class="form-control form-control-sm"
                           value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="tgl2"
                           class="form-control form-control-sm"
                           value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <button type="button"
                            class="btn btn-outline-secondary btn-sm w-100 d-flex justify-content-between align-items-center"
                            data-toggle="modal" data-target="#modal-lg">
                        <span>Pilih Penjamin</span>
                        <i class="fas fa-credit-card"></i>
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fa fa-search"></i> Cari
                    </button>
                </div>
            </div>

            {{-- 📌 Modal Pilih Penjamin --}}
            <div class="modal fade" id="modal-lg">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Pilih Penjamin / Jenis Bayar</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
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
            {{-- End Modal --}}

            {{-- 📊 Summary --}}
        <div class="row mt-4">
            <div class="col-md-2 col-6 mb-2">
                <div class="card p-2 text-center shadow-sm">
                    <div class="text-muted small">Total (Tabel)</div>
                    <div class="fw-bold text-primary fs-6">{{ $totalBaris ?? 0 }}</div>
                </div>
            </div>
            {{-- <div class="col-md-2 col-6 mb-2">
                <div class="card p-2 text-center shadow-sm">
                    <div class="text-muted small">Pasien Unik (Halaman)</div>
                    <div class="fw-bold text-info fs-6">{{ $totalPasien ?? 0 }}</div>
                </div>
            </div> --}}
            {{-- <div class="col-md-2 col-6 mb-2">
                <div class="card p-2 text-center shadow-sm">
                    <div class="text-muted small">Pasien Unik (Semua)</div>
                    <div class="fw-bold text-secondary fs-6">{{ $totalPasienAll ?? 0 }}</div>
                </div>
            </div> --}}
            <div class="col-md-2 col-6 mb-2">
                <div class="card p-2 text-center shadow-sm">
                    <div class="text-muted small">Total Cicilan</div>
                    <div class="fw-bold text-success">
                        Rp {{ number_format($totalCicilan ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-2">
                <div class="card p-2 text-center shadow-sm">
                    <div class="text-muted small">Total Diskon</div>
                    <div class="fw-bold text-warning">
                        Rp {{ number_format($totalDiskon ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-2">
                <div class="card p-2 text-center shadow-sm">
                    <div class="text-muted small">Tidak Terbayar</div>
                    <div class="fw-bold text-danger">
                        Rp {{ number_format($totalTidakTerbayar ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-2">
                <div class="card p-2 text-center shadow-sm">
                    <div class="text-muted small">Total Seluruh</div>
                    <div class="fw-bold text-dark fs-6">
                        Rp {{ number_format($totalKeseluruhan ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- 📋 Tombol Copy --}}
        <div class="row no-print mt-3">
            <div class="col-12 text-end">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="copyButton">
                    <i class="fas fa-copy"></i> Salin Tabel
                </button>
            </div>
        </div>

        {{-- 📑 Pagination --}}
        <div class="mt-3">
            {{ $bayarPiutang->appends(request()->input())->links('pagination::bootstrap-4') }}
        </div>

        {{-- 📌 Tabel Data --}}
        <div class="table-responsive mt-3">
            <table class="table table-sm table-bordered table-striped text-xs mb-3" id="tableToCopy">
                <thead class="thead-light sticky-top">
                    <tr>
                        <th>No</th>
                        <th>Tgl. Bayar</th>
                        <th>Nama Pasien</th>
                        <th>No. Rawat</th>
                        <th>Cicilan (Rp)</th>
                        <th>Keterangan</th>
                        <th>Diskon Bayar (Rp)</th>
                        <th>Tidak Terbayar (Rp)</th>
                        <th>Jenis Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bayarPiutang as $key => $item)
                        <tr>
                            <td>{{ $bayarPiutang->firstItem() + $key }}</td>
                            <td>{{ $item->tgl_bayar }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ number_format($item->besar_cicilan, 0, ',', '.') }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>{{ number_format($item->diskon_piutang, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->tidak_terbayar, 0, ',', '.') }}</td>
                            <td>{{ $item->png_jawab }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 📋 Script --}}
@push('scripts')
<script>
    $(function () {
        // ✅ Init DualListBox
        if ($.fn.bootstrapDualListbox) {
            let dual = $('select[name="duallistbox[]"]').bootstrapDualListbox();
            $('form').on('submit', function() {
                $('input[name="kdPenjamin"]').val(
                    $('select[name="duallistbox[]"]').val()?.join(',') ?? ''
                );
            });
        }

        // ✅ Copy Table
        $('#copyButton').on('click', function() {
            let table = document.getElementById("tableToCopy");
            if (!table) return;

            let range = document.createRange();
            range.selectNode(table);
            let selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);

            try {
                document.execCommand("copy");
                selection.removeAllRanges();
                alert("✅ Tabel berhasil disalin ke clipboard!");
            } catch (err) {
                console.error("❌ Gagal menyalin tabel:", err);
            }
        });
    });
</script>
@endpush
@endsection
