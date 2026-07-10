@extends('layout.layoutDashboard')
@section('title', 'List Pasien Rincian Kasir')

@section('konten')
<div class="container-fluid py-3">

    {{-- ── Header ── --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <small class="text-muted">Daftar registrasi pasien untuk rincian biaya kasir</small>
        </div>
        <span class="badge badge-primary px-3 py-2" style="font-size:.85rem;">
            <i class="fas fa-calendar-alt mr-1"></i>
            {{ \Carbon\Carbon::parse($tgl1)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($tgl2)->format('d/m/Y') }}
        </span>
    </div>

    {{-- ── Filter Card ── --}}
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ url('/list-nama-kasir') }}" class="form-inline flex-wrap" style="gap:10px;">
                <div class="form-group mr-2">
                    <label class="mr-2 text-muted small font-weight-bold">
                        <i class="fas fa-calendar-alt"></i> Tanggal Registrasi
                    </label>
                    <input type="date"
                           name="tgl1"
                           id="tgl1"
                           class="form-control form-control-sm"
                           value="{{ $tgl1 }}">
                </div>

                <div class="form-group mr-2">
                    <label class="mr-2 text-muted small font-weight-bold">
                        s.d
                    </label>
                    <input type="date"
                           name="tgl2"
                           id="tgl2"
                           class="form-control form-control-sm"
                           value="{{ $tgl2 }}">
                </div>

                <div class="form-group mr-2">
                    <label class="mr-2 text-muted small font-weight-bold">
                        <i class="fas fa-hospital-user"></i> Status Lanjut
                    </label>
                    <select name="status_lanjut" id="status_lanjut" class="form-control form-control-sm">
                        <option value="" {{ $statusLanjut == '' ? 'selected' : '' }}>Semua</option>
                        <option value="Ralan" {{ $statusLanjut == 'Ralan' ? 'selected' : '' }}>Rawat Jalan</option>
                        <option value="Ranap" {{ $statusLanjut == 'Ranap' ? 'selected' : '' }}>Rawat Inap</option>
                    </select>
                </div>

                <div class="form-group mr-2">
                    <div class="input-group input-group-sm" style="width:240px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text"
                               id="searchInput"
                               class="form-control"
                               placeholder="Cari nama, No.Rawat, Poli...">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search mr-1"></i> Tampilkan
                </button>
                <a href="{{ url('/list-nama-kasir') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
            </form>
        </div>
    </div>

    {{-- ── Info Bar ── --}}
    <div class="d-flex align-items-center mb-2" style="gap:8px;">
        <span class="badge badge-light border text-secondary" style="font-size:.82rem;">
            <i class="fas fa-users mr-1"></i>Total:
            <strong id="totalCount">{{ count($pasien) }}</strong> pasien
        </span>
        <span class="badge badge-light border text-secondary" style="font-size:.82rem;" id="filteredBadge" style="display:none;"></span>
    </div>

    {{-- ── Table Card ── --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height:600px; overflow:auto;">
                <table class="table table-bordered table-hover table-sm mb-0" id="tabelPasien">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="text-center" style="width:44px; position:sticky; top:0; z-index:2; background:#f8fafc;">#</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">No. Rawat</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Tgl. Registrasi</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc;">Nama Pasien</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc;">Poliklinik</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Tgl. Masuk Kamar</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Status</th>
                            <th class="text-center" style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabelBody">
                        @forelse($pasien as $index => $row)
                        <tr data-status="{{ $row->status_lanjut }}">
                            <td class="text-center font-weight-bold text-muted">{{ $index + 1 }}</td>
                            <td class="font-weight-bold" style="font-family:monospace; white-space:nowrap;">{{ $row->no_rawat }}</td>
                            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($row->tgl_registrasi)->format('d-m-Y') }}</td>
                            <td><strong class="text-dark">{{ $row->nm_pasien }}</strong></td>
                            <td>
                                <span class="badge badge-light border text-dark">
                                    {{ $row->nm_poli }}
                                </span>
                            </td>
                            <td class="text-center">{{ $row->tgl_masuk ? \Carbon\Carbon::parse($row->tgl_masuk)->format('d-m-Y') : '-' }}</td>
                            <td class="text-center">
                                @if($row->status_lanjut === 'Ranap')
                                    <span class="badge badge-success">Rawat Inap</span>
                                @else
                                    <span class="badge badge-info">Rawat Jalan</span>
                                @endif
                            </td>
                            <td class="text-center" style="white-space:nowrap;">
                                <a href="{{ url('/rincian-kasir') }}?no_rawat={{ urlencode($row->no_rawat) }}" 
                                   class="btn btn-xs btn-primary font-weight-bold px-2 py-1"
                                   target="_blank">
                                    <i class="fas fa-receipt mr-1"></i> Rincian Biaya
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                                Tidak ada data pasien pada tanggal
                                <strong>{{ \Carbon\Carbon::parse($tgl1)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($tgl2)->format('d/m/Y') }}</strong>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(count($pasien) > 0)
        <div class="card-footer py-2 text-muted small">
            Menampilkan <strong id="shownCount">{{ count($pasien) }}</strong> dari
            <strong>{{ count($pasien) }}</strong> data
        </div>
        @endif
    </div>

</div>

<script>
(function () {
    const input         = document.getElementById('searchInput');
    const statusSelect  = document.getElementById('status_lanjut');
    const rows          = document.querySelectorAll('#tabelBody tr');
    const total         = rows.length;
    const shown         = document.getElementById('shownCount');
    const badge         = document.getElementById('filteredBadge');

    function filterTable() {
        if (!rows.length || (rows.length === 1 && rows[0].cells.length < 3)) return;

        const kw = input ? input.value.trim().toLowerCase() : '';
        const status = statusSelect ? statusSelect.value : '';
        let visible = 0;

        rows.forEach(row => {
            if (row.cells.length < 3) return; // skip empty row
            
            const noRawat = row.cells[1].textContent.toLowerCase();
            const nama    = row.cells[3].textContent.toLowerCase();
            const poli    = row.cells[4].textContent.toLowerCase();

            const textMatch = !kw || noRawat.includes(kw) || nama.includes(kw) || poli.includes(kw);
            const rowStatus = row.getAttribute('data-status') || '';
            const statusMatch = !status || rowStatus === status;

            if (textMatch && statusMatch) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        if (shown) {
            shown.textContent = visible;
        }

        if (badge) {
            if (kw || status) {
                badge.style.display = '';
                badge.innerHTML = `<i class="fas fa-filter mr-1"></i>Filter pencarian: <strong>${visible}</strong> ditemukan`;
            } else {
                badge.style.display = 'none';
            }
        }
    }

    if (input) {
        input.addEventListener('input', filterTable);
    }
    if (statusSelect) {
        statusSelect.addEventListener('change', filterTable);
    }

    // Trigger filter once at load time in case there is a default value from backend
    filterTable();
})();
</script>
@endsection
