@extends('layout.layoutDashboard')
@section('title', 'Laporan Waktu Tunggu Pelayanan Farmasi')

@section('konten')
<div class="container-fluid py-3">

    {{-- ── Header ── --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <small class="text-muted">Laporan waktu tunggu penyelesaian obat (resep) di Instalasi Farmasi</small>
        </div>
        <span class="badge badge-primary px-3 py-2" style="font-size:.85rem;">
            <i class="fas fa-calendar-alt mr-1"></i>
            {{ \Carbon\Carbon::parse($tgl1)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($tgl2)->format('d/m/Y') }}
        </span>
    </div>

    {{-- ── Filter Card ── --}}
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ url('/waktu-tunggu-farmasi') }}" class="form-inline flex-wrap" style="gap:10px;">
                <div class="form-group mr-2">
                    <label class="mr-2 text-muted small font-weight-bold">
                        <i class="fas fa-calendar-alt"></i> Tanggal Mulai
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
                    <div class="input-group input-group-sm" style="width:260px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text"
                               id="searchInput"
                               class="form-control"
                               placeholder="Cari pasien, No. RM, keterangan...">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search mr-1"></i> Tampilkan
                </button>
                <a href="{{ route('waktutunggufarmasi.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="{{ url('/waktu-tunggu-farmasi') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
            </form>
        </div>
    </div>

    {{-- ── Info Bar ── --}}
    <div class="d-flex align-items-center mb-2" style="gap:8px;">
        <span class="badge badge-light border text-secondary" style="font-size:.82rem;">
            <i class="fas fa-users mr-1"></i>Total Antrian:
            <strong id="totalCount">{{ count($data) }}</strong> resep
        </span>
        <span class="badge badge-light border text-secondary" style="font-size:.82rem;" id="filteredBadge" style="display:none;"></span>
    </div>

    {{-- ── Table Card ── --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height:600px; overflow:auto;">
                <table class="table table-bordered table-hover table-sm mb-0" id="tabelAntrian">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="text-center" style="width:44px; position:sticky; top:0; z-index:2; background:#f8fafc;">#</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">No. RM</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc;">Nama Pasien</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Tanggal</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc;">Keterangan</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Racik/Non-Racik</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Waktu Daftar</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Waktu Selesai</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Status</th>
                            <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Waktu Tunggu</th>
                        </tr>
                    </thead>
                    <tbody id="tabelBody">
                        @forelse ($data as $i => $row)
                        <tr>
                            <td class="text-center text-muted small">{{ $i + 1 }}</td>
                            <td>
                                <span class="badge badge-secondary" style="font-size:.75rem;">
                                    {{ $row->rekam_medik }}
                                </span>
                            </td>
                            <td class="font-weight-bold text-dark" style="white-space:nowrap;">
                                {{ $row->nama_pasien }}
                            </td>
                            <td class="text-muted small" style="white-space:nowrap;">
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}
                            </td>
                            <td class="small">{{ $row->keterangan ?: '-' }}</td>
                            <td class="text-center">
                                @if(strtolower($row->racik_non_racik) === 'racik')
                                    <span class="badge badge-warning text-dark px-2" style="font-size:.72rem;">Racik</span>
                                @elseif(strtolower($row->racik_non_racik) === 'non racik')
                                    <span class="badge badge-info px-2" style="font-size:.72rem;">Non-Racik</span>
                                @else
                                    <span class="badge badge-light border px-2" style="font-size:.72rem;">{{ $row->racik_non_racik ?: '-' }}</span>
                                @endif
                            </td>
                            <td class="small" style="white-space:nowrap;">
                                {{ $row->waktu_daftar ? \Carbon\Carbon::parse($row->waktu_daftar)->format('d-m-Y H:i:s') : '-' }}
                            </td>
                            <td class="small" style="white-space:nowrap;">
                                {{ $row->waktu_selesai ? \Carbon\Carbon::parse($row->waktu_selesai)->format('d-m-Y H:i:s') : '-' }}
                            </td>
                            <td class="text-center">
                                @if(strtolower($row->status) === 'selesai' || $row->status == '3')
                                    <span class="badge badge-success px-2" style="font-size:.72rem;">Selesai</span>
                                @else
                                    <span class="badge badge-danger px-2" style="font-size:.72rem;">{{ $row->status }}</span>
                                @endif
                            </td>
                            <td class="font-weight-bold text-primary small" style="white-space:nowrap;">
                                @if($row->waktu_tunggu_menit !== null && $row->waktu_tunggu_menit >= 0)
                                    <i class="far fa-clock mr-1"></i> {{ $row->waktu_tunggu_jam_menit }}
                                    <span class="text-muted font-weight-normal">({{ $row->waktu_tunggu_menit }} Menit)</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                                Tidak ada data antrian farmasi pada rentang tanggal
                                <strong>{{ \Carbon\Carbon::parse($tgl1)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($tgl2)->format('d/m/Y') }}</strong>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(count($data) > 0)
        <div class="card-footer py-2 text-muted small">
            Menampilkan <strong id="shownCount">{{ count($data) }}</strong> dari
            <strong>{{ count($data) }}</strong> data
        </div>
        @endif
    </div>

</div>

<script>
(function () {
    const input   = document.getElementById('searchInput');
    const rows    = document.querySelectorAll('#tabelBody tr');
    const shown   = document.getElementById('shownCount');
    const badge   = document.getElementById('filteredBadge');

    if (!input) return;

    input.addEventListener('input', function () {
        const kw = this.value.trim().toLowerCase();
        let visible = 0;

        rows.forEach(row => {
            if (row.cells.length < 3) return; // skip empty row
            
            const match = !kw || row.innerText.toLowerCase().includes(kw);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        if (shown) shown.textContent = visible;

        if (badge) {
            if (kw) {
                badge.style.display = '';
                badge.innerHTML = `<i class="fas fa-filter mr-1"></i>Ditemukan: <strong>${visible}</strong>`;
            } else {
                badge.style.display = 'none';
            }
        }
    });
})();
</script>
@endsection
