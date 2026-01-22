@extends('layout.layoutDashboard')
@section('title', 'List Pasien Farmasi')

@section('konten')

{{-- ================= RINGKASAN ================= --}}
<div class="row mb-3">

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-info text-white rounded p-3 mr-3">
                    <i class="fas fa-clipboard-list fa-2x"></i>
                </div>
                <div>
                    <div class="text-muted small">Total List Pasien</div>
                    <h4 class="mb-0">{{ $totalPasien ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded p-3 mr-3">
                    <i class="fas fa-check fa-2x"></i>
                </div>
                <div>
                    <div class="text-muted small">Sudah Terbundling</div>
                    <h4 class="mb-0">{{ $totalSudahTerbundling ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning text-dark rounded p-3 mr-3">
                    <i class="fas fa-pen fa-2x"></i>
                </div>
                <div>
                    <div class="text-muted small">Belum Terbundling</div>
                    <h4 class="mb-0">{{ $totalBelumTerbundling ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

</div>
{{-- ================= END RINGKASAN ================= --}}

<div class="card p-3 mb-3">
    <form action="{{ url('cari-list-pasien-farmasi') }}" method="GET">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="small">Cari</label>
                <input type="text" name="cariNomor"
                    value="{{ request('cariNomor') }}"
                    class="form-control form-control-sm"
                    placeholder="Nama / RM / No Rawat">
            </div>

            <div class="col-md-2">
                <label class="small">Tanggal Awal</label>
                <input type="date" name="tgl1"
                    value="{{ request('tgl1', now()->format('Y-m-d')) }}"
                    class="form-control form-control-sm">
            </div>

            <div class="col-md-2">
                <label class="small">Tanggal Akhir</label>
                <input type="date" name="tgl2"
                    value="{{ request('tgl2', now()->format('Y-m-d')) }}"
                    class="form-control form-control-sm">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-search"></i> Cari
                </button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead class="text-center bg-light">
                <tr>
                    <th>#</th>
                    <th>RM</th>
                    <th>No Rawat</th>
                    <th>No SEP</th>
                    <th>Pasien | Status</th>
                    <th>Poli</th>
                    <th>Jenis Jual</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPasien as $item)
                    @php
                        $isBundling = $downloadBerkas
                            ->where('no_rawat', $item->no_rawat)
                            ->isNotEmpty();
                    @endphp

                    <tr class="{{ $isBundling ? 'table-success' : '' }}">
                        <td class="text-center">
                            {{-- ✅ FIXED: GET --}}
                            <form action="{{ url('view-sep-resep') }}" method="GET">
                                <input type="hidden" name="cariNoRawat" value="{{ $item->no_rawat }}">
                                <input type="hidden" name="cariNoSep" value="{{ $item->no_sep }}">
                                <button class="btn btn-link p-0">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </td>

                        <td class="text-center">{{ $item->no_rkm_medis }}</td>
                        <td class="text-center">{{ $item->no_rawat }}</td>

                        <td class="text-center">
                            {{ $item->no_sep ?? '-' }}
                        </td>

                        <td>
                            {{ $item->nm_pasien }} |
                            @if ($item->status_bayar === 'Sudah Bayar')
                                <span class="text-success">
                                    <i class="fas fa-check"></i> Lunas
                                </span>
                            @else
                                <span class="text-dark">
                                    <i class="fas fa-dollar-sign"></i> Belum
                                </span>
                            @endif
                        </td>

                        <td>{{ $item->nm_poli }}</td>
                        <td>{{ $item->jns_jual }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Data tidak ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
