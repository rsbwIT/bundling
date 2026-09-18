@extends('..layout.layoutDashboard')
@section('title', 'Mapping Kategori Non Medis')

@section('konten')
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Mapping Kategori / Kelompok Beban</h5>
    </div>
    <div class="card-body">
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <div class="col-md-5">
                <form action="{{ route('laporan.mapping-suplier.simpan') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Import dari Excel</label>
                        <p class="text-muted small">Copy 2 kolom dari Excel (Nama Supplier & Kelompok/Kategori), lalu paste di kotak bawah ini:</p>
                        <textarea name="raw_data" rows="10" class="form-control" placeholder="PT. ABC&#9;Beban Administrasi&#10;Toko XYZ&#9;Beban Dapur"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan / Update Mapping</button>
                    <a href="{{ route('laporan.pengeluaran-harian') }}" class="btn btn-secondary">Kembali ke Laporan</a>
                </form>
            </div>
            
            <div class="col-md-7">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-bordered table-sm table-striped">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>No</th>
                                <th>Nama Supplier / Keterangan</th>
                                <th>Kelompok / Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mappings as $index => $map)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $map->nama_suplier }}</td>
                                    <td><span class="badge bg-info text-dark">{{ $map->kategori }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data mapping.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
