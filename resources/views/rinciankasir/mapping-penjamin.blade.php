@extends('layout.layoutDashboard')
@section('title', 'Mapping Penjamin Kasir')

@section('konten')
<div class="container-fluid py-3">

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-sitemap text-primary mr-1"></i> Mapping Penjamin Kasir (Khanza)
            </h5>
            <small class="text-muted">Atur kategori penjamin (BPJS Murni, BPJS COB, Asuransi Swasta, Umum) dan akun piutangnya untuk kasir.</small>
        </div>
        <div>
            <a href="{{ url('/list-nama-kasir') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Kasir
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('rincian.mapping-penjamin') }}" class="form-inline flex-wrap" style="gap:10px;">
                <div class="form-group mr-2">
                    <div class="input-group input-group-sm" style="width: 280px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="cari" class="form-control" placeholder="Cari Kode / Nama Penjamin..." value="{{ $cari }}">
                    </div>
                </div>

                <div class="form-group mr-2">
                    <label class="mr-2 text-muted small font-weight-bold">Kategori:</label>
                    <select name="kategori" class="form-control form-control-sm">
                        <option value="">Semua Kategori</option>
                        <option value="BPJS Murni" {{ $filterKategori == 'BPJS Murni' ? 'selected' : '' }}>BPJS Murni</option>
                        <option value="BPJS COB" {{ $filterKategori == 'BPJS COB' ? 'selected' : '' }}>BPJS COB (KAI COB, dll)</option>
                        <option value="Asuransi" {{ $filterKategori == 'Asuransi' ? 'selected' : '' }}>Asuransi / Perusahaan</option>
                        <option value="Umum" {{ $filterKategori == 'Umum' ? 'selected' : '' }}>Umum / Mandiri</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('rincian.mapping-penjamin') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm mb-0">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th style="width: 100px;">Kode PJ</th>
                            <th>Nama Penjamin (SIMRS Khanza)</th>
                            <th style="width: 200px;">Kategori Penjamin</th>
                            <th style="width: 320px;">Akun Piutang Terpeta</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mappings as $index => $row)
                        <tr>
                            <td class="text-center text-muted align-middle font-weight-bold">
                                {{ $mappings->firstItem() + $index }}
                            </td>
                            <td class="align-middle font-weight-bold font-monospace text-primary">
                                {{ $row->kd_pj }}
                            </td>
                            <td class="align-middle">
                                <strong>{{ $row->png_jawab }}</strong>
                            </td>
                            <td class="align-middle">
                                <select class="form-control form-control-sm form-kategori font-weight-bold" 
                                        id="kat_{{ $row->kd_pj }}"
                                        style="font-size: 11px; {{ $row->kategori == 'BPJS Murni' ? 'color:#15803d;' : ($row->kategori == 'BPJS COB' ? 'color:#0284c7;' : ($row->kategori == 'Asuransi' ? 'color:#b45309;' : 'color:#475569;')) }}">
                                    <option value="BPJS Murni" {{ $row->kategori == 'BPJS Murni' ? 'selected' : '' }}>🟢 BPJS Murni</option>
                                    <option value="BPJS COB" {{ $row->kategori == 'BPJS COB' ? 'selected' : '' }}>🔵 BPJS COB</option>
                                    <option value="Asuransi" {{ $row->kategori == 'Asuransi' ? 'selected' : '' }}>🟠 Asuransi / Perusahaan</option>
                                    <option value="Umum" {{ $row->kategori == 'Umum' ? 'selected' : '' }}>⚪ Umum / Mandiri</option>
                                </select>
                            </td>
                            <td class="align-middle">
                                <select class="form-control form-control-sm form-akun select2-akun" 
                                        id="akun_{{ $row->kd_pj }}" 
                                        style="font-size: 11px;">
                                    <option value="">-- Tidak Menggunakan Akun Piutang (Rp 0) --</option>
                                    @foreach($akunPiutangList as $ap)
                                        <option value="{{ $ap->nama_bayar }}" {{ (trim($row->nama_akun_piutang) == trim($ap->nama_bayar)) ? 'selected' : '' }}>
                                            {{ $ap->nama_bayar }} ({{ $ap->kd_pj ?? $ap->kd_rek }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center align-middle">
                                <button type="button" 
                                        class="btn btn-xs btn-success font-weight-bold px-2 py-1 btn-simpan-row" 
                                        data-kdpj="{{ $row->kd_pj }}">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Tidak ada data penjamin ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($mappings->hasPages())
        <div class="card-footer py-2 d-flex justify-content-between align-items-center">
            <span class="small text-muted">
                Menampilkan {{ $mappings->firstItem() }} - {{ $mappings->lastItem() }} dari {{ $mappings->total() }} data
            </span>
            <div>
                {{ $mappings->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>

</div>

<script>
document.querySelectorAll('.btn-simpan-row').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const kdPj = this.getAttribute('data-kdpj');
        const kategori = document.getElementById('kat_' + kdPj).value;
        const namaAkun = document.getElementById('akun_' + kdPj).value;

        const originalHtml = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        this.disabled = true;

        fetch('{{ route("rincian.mapping-penjamin.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                kd_pj: kdPj,
                kategori: kategori,
                nama_akun_piutang: namaAkun
            })
        })
        .then(res => res.json())
        .then(res => {
            this.innerHTML = '<i class="fas fa-check"></i> Tersimpan';
            this.classList.remove('btn-success');
            this.classList.add('btn-primary');
            setTimeout(() => {
                this.innerHTML = originalHtml;
                this.classList.remove('btn-primary');
                this.classList.add('btn-success');
                this.disabled = false;
            }, 1200);
        })
        .catch(err => {
            alert('Gagal menyimpan mapping');
            this.innerHTML = originalHtml;
            this.disabled = false;
        });
    });
});
</script>
@endsection

