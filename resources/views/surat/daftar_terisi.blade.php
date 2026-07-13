@extends('layout.layoutDashboard')
@section('title', 'Daftar Surat Terisi')

@section('konten')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 font-weight-bold text-dark"><i class="fas fa-file-alt mr-2 text-success"></i>Daftar Surat Terisi</h4>
            <small class="text-muted">Daftar Surat Keterangan Dokter (SKD) & Vaksinasi (SKV) yang telah terisi</small>
        </div>
        <a href="{{ route('listnama.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke List Pasien
        </a>
    </div>

    <!-- Search/Filter -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('surat.terisi') }}" class="form-inline flex-wrap" style="gap:15px;">
                <div class="form-group">
                    <label class="mr-2 text-muted small font-weight-bold">Dari Tanggal</label>
                    <input type="date" name="tgl1" class="form-control form-control-sm" value="{{ $tgl1 }}">
                </div>
                <div class="form-group">
                    <label class="mr-2 text-muted small font-weight-bold">Sampai Tanggal</label>
                    <input type="date" name="tgl2" class="form-control form-control-sm" value="{{ $tgl2 }}">
                </div>
                <div class="form-group">
                    <div class="input-group input-group-sm" style="width:250px;">
                        <input type="text" name="cari" class="form-control" placeholder="Cari No. Surat, No. Rawat, Pasien..." value="{{ $cari }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search mr-1"></i> Tampilkan</button>
                <a href="{{ route('surat.terisi') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo mr-1"></i> Reset</a>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm mb-0">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="text-center" style="width:50px;">#</th>
                            <th>No. Surat</th>
                            <th>Jenis Surat</th>
                            <th>No. Rawat</th>
                            <th>Nama Pasien</th>
                            <th>Dokter Pemeriksa</th>
                            <th>Tanggal Buat</th>
                            <th class="text-center" style="width:150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $row)
                        <tr>
                            <td class="text-center text-muted small">{{ $i + 1 }}</td>
                            <td><strong>{{ $row->no_surat }}</strong></td>
                            <td>
                                @if($row->jenis_surat == 'SKD')
                                    <span class="badge badge-info">Dokter (SKD)</span>
                                @else
                                    <span class="badge badge-success">Vaksin (SKV)</span>
                                @endif
                            </td>
                            <td><code>{{ $row->no_rawat }}</code></td>
                            <td>{{ $row->nm_pasien ?? '-' }}</td>
                            <td>{{ $row->nm_dokter ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</td>
                            <td class="text-center">
                                @if($row->jenis_surat == 'SKD')
                                    <a href="{{ route('surat.ket_dokter', ['no_rawat' => $row->no_rawat]) }}" class="btn btn-xs btn-primary" title="Buka / Edit">
                                        <i class="fas fa-edit"></i> Buka
                                    </a>
                                @elseif($row->jenis_surat == 'SKV')
                                    <a href="{{ route('surat.ket_vaksin', ['no_rawat' => $row->no_rawat]) }}" class="btn btn-xs btn-primary" title="Buka / Edit">
                                        <i class="fas fa-edit"></i> Buka
                                    </a>
                                @endif
                                
                                <button class="btn btn-xs btn-danger btnHapusSurat" data-id="{{ $row->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Belum ada surat yang terisi/ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    $('.btnHapusSurat').click(function(){
        let id = $(this).data('id');
        if(confirm('Yakin ingin menghapus data surat ini?')) {
            $.ajax({
                url: '{{ url("/surat/terisi") }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res){
                    alert(res.message);
                    if(res.status) {
                        location.reload();
                    }
                },
                error: function(xhr){
                    alert('ERROR: ' + xhr.responseText);
                }
            });
        }
    });
});
</script>
@endsection
