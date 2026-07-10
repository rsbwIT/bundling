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

    {{-- ── Tabs Navigation ── --}}
    <div class="card card-primary card-tabs shadow-sm">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="farmasiTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="nonracik-tab" data-toggle="pill" href="#nonracik" role="tab" aria-controls="nonracik" aria-selected="true">
                        <i class="fas fa-pills mr-1 text-info"></i> Obat Non-Racik
                        <span class="badge badge-info ml-1" id="badge-count-nonracik">{{ count($nonRacik) }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="racik-tab" data-toggle="pill" href="#racik" role="tab" aria-controls="racik" aria-selected="false">
                        <i class="fas fa-mortar-pestle mr-1 text-warning"></i> Obat Racikan
                        <span class="badge badge-warning text-dark ml-1" id="badge-count-racik">{{ count($racik) }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="farmasiTabContent">
                
                {{-- TAB 1: NON RACIK --}}
                <div class="tab-pane fade show active" id="nonracik" role="tabpanel" aria-labelledby="nonracik-tab">
                    <div class="table-responsive" style="max-height:600px; overflow:auto;">
                        <table class="table table-bordered table-hover table-sm mb-0">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th class="text-center" style="width:44px; position:sticky; top:0; z-index:2; background:#f8fafc;">#</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">No. RM</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc;">Nama Pasien</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Tanggal</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc;">Keterangan</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Waktu Daftar</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Waktu Selesai</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Status</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Waktu Tunggu</th>
                                </tr>
                            </thead>
                            <tbody class="tabelBody" id="bodyNonRacik">
                                @forelse ($nonRacik as $i => $row)
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
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                                        Tidak ada antrian obat non-racik.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: RACIK --}}
                <div class="tab-pane fade" id="racik" role="tabpanel" aria-labelledby="racik-tab">
                    <div class="table-responsive" style="max-height:600px; overflow:auto;">
                        <table class="table table-bordered table-hover table-sm mb-0">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th class="text-center" style="width:44px; position:sticky; top:0; z-index:2; background:#f8fafc;">#</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">No. RM</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc;">Nama Pasien</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Tanggal</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc;">Keterangan</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Waktu Daftar</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Waktu Selesai</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Status</th>
                                    <th style="position:sticky; top:0; z-index:2; background:#f8fafc; white-space:nowrap;">Waktu Tunggu</th>
                                </tr>
                            </thead>
                            <tbody class="tabelBody" id="bodyRacik">
                                @forelse ($racik as $i => $row)
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
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                                        Tidak ada antrian obat racik.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
(function () {
    const input            = document.getElementById('searchInput');
    const rowsNonRacik     = document.querySelectorAll('#bodyNonRacik tr');
    const rowsRacik        = document.querySelectorAll('#bodyRacik tr');
    const badgeNonRacik    = document.getElementById('badge-count-nonracik');
    const badgeRacik       = document.getElementById('badge-count-racik');

    if (!input) return;

    input.addEventListener('input', function () {
        const kw = this.value.trim().toLowerCase();
        
        // Filter Non-Racik
        let visibleNonRacik = 0;
        rowsNonRacik.forEach(row => {
            if (row.cells.length < 3) return;
            const match = !kw || row.innerText.toLowerCase().includes(kw);
            row.style.display = match ? '' : 'none';
            if (match) visibleNonRacik++;
        });
        if (badgeNonRacik) badgeNonRacik.textContent = visibleNonRacik;

        // Filter Racik
        let visibleRacik = 0;
        rowsRacik.forEach(row => {
            if (row.cells.length < 3) return;
            const match = !kw || row.innerText.toLowerCase().includes(kw);
            row.style.display = match ? '' : 'none';
            if (match) visibleRacik++;
        });
        if (badgeRacik) badgeRacik.textContent = visibleRacik;
    });
})();
</script>
@endsection
