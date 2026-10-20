@extends('layout.layoutDashboard')

@section('title', 'Permintaan Lab - Gambaran Darah Tepi')

@section('konten')
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-microscope text-primary mr-1"></i> Permintaan Lab: Gambaran Darah Tepi
        </h3>
    </div>
    <div class="card-body">
        {{-- Filter & Search Form --}}
        <form action="{{ route('lab.darah-tepi.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label class="small text-muted font-weight-bold">Dari Tanggal Permintaan</label>
                    <input type="date" name="tgl1" class="form-control form-control-sm" value="{{ $tgl1 }}">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted font-weight-bold">Sampai Tanggal Permintaan</label>
                    <input type="date" name="tgl2" class="form-control form-control-sm" value="{{ $tgl2 }}">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted font-weight-bold">Pencarian Pasien / No. Order / No. Rawat</label>
                    <input type="text" name="cari" class="form-control form-control-sm" placeholder="No. Order / No. RM / Nama Pasien..." value="{{ $cari }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-search mr-1"></i> Filter Data
                    </button>
                </div>
            </div>
        </form>

        {{-- Table List Permintaan --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped text-xs">
                <thead class="bg-light">
                    <tr class="text-center">
                        <th width="3%">No</th>
                        <th width="12%">No. Order</th>
                        <th width="12%">No. Rawat</th>
                        <th width="7%">No. RM</th>
                        <th width="18%">Nama Pasien</th>
                        <th width="6%">Rawat</th>
                        <th width="12%">Tgl Permintaan</th>
                        <th>Dokter Pengirim</th>
                        <th width="11%">Status Hasil</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($list as $index => $item)
                        <tr>
                            <td class="text-center">{{ $list->firstItem() + $index }}</td>
                            <td class="font-weight-bold text-primary">{{ $item->noorder }}</td>
                            <td>{{ $item->no_rawat }}</td>
                            <td class="text-center">{{ $item->no_rkm_medis }}</td>
                            <td>
                                <div class="font-weight-bold text-dark" style="font-size: 12px; letter-spacing: 0.2px;">
                                    {{ ucwords(strtolower($item->nm_pasien)) }}
                                </div>
                                <span class="text-muted" style="font-size: 10.5px;">
                                    {{ $item->jk == 'L' ? 'Laki-laki' : 'Perempuan' }} &bull; {{ $item->tgl_lahir ? date('d-m-Y', strtotime($item->tgl_lahir)) : '-' }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge {{ $item->status_lanjut == 'Ranap' ? 'badge-warning' : 'badge-info' }} px-2 py-1" style="font-size: 10.5px;">
                                    {{ $item->status_lanjut }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{ date('d-m-Y', strtotime($item->tgl_permintaan)) }}<br>
                                <span class="text-muted">{{ $item->jam_permintaan }}</span>
                            </td>
                            <td>{{ $item->dokter_pengirim ?? '-' }}</td>
                            <td class="text-center">
                                @if($item->id_hasil)
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Sudah Diisi</span>
                                    <div class="text-muted" style="font-size: 10px;">{{ date('d-m-Y', strtotime($item->tgl_hasil_isi)) }}</div>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-clock mr-1"></i> Belum Diisi</span>
                                @endif
                            </td>
                            <td class="text-center align-middle" style="white-space: nowrap; width: 1%; padding: 4px 8px;">
                                @if($item->id_hasil)
                                    <div class="btn-group btn-group-xs shadow-sm" role="group">
                                        <a href="{{ route('lab.darah-tepi.form', $item->noorder) }}" class="btn btn-warning text-dark font-weight-bold px-2 py-1" style="font-size: 11px;" title="Edit Hasil">
                                            <i class="fas fa-edit mr-0.5"></i> Edit
                                        </a>
                                        <button type="button" onclick="printDirect('{{ route('lab.darah-tepi.cetak', $item->noorder) }}')" class="btn btn-default border px-2 py-1" style="font-size: 11px;" title="Cetak Lembar Langsung (Ctrl + P)">
                                            <i class="fas fa-print mr-0.5 text-secondary"></i> Cetak
                                        </button>
                                        <form action="{{ route('lab.darah-tepi.destroy', $item->noorder) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data hasil pemeriksaan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger px-2 py-1" style="font-size: 11px; border-top-left-radius: 0; border-bottom-left-radius: 0;" title="Hapus Hasil">
                                                <i class="fas fa-trash-alt mr-0.5"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <a href="{{ route('lab.darah-tepi.form', $item->noorder) }}" class="btn btn-xs btn-primary font-weight-bold shadow-sm px-2 py-1" style="font-size: 11px;">
                                        <i class="fas fa-plus mr-0.5"></i> Input Hasil
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                Tidak ada data permintaan pemeriksaan "Gambaran Darah Tepi" pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 float-right">
            {{ $list->links() }}
        </div>
    </div>
</div>

{{-- Hidden Iframe for Direct Printing from Index --}}
<iframe id="directPrintFrame" style="display:none; position:fixed; right:0; bottom:0; width:0; height:0; border:0;"></iframe>
@endsection

@push('scripts')
<script>
function printDirect(url) {
    var frame = document.getElementById('directPrintFrame');
    frame.src = url;
}
</script>
@endpush

