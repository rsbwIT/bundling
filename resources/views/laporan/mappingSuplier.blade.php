@extends('..layout.layoutDashboard')
@section('title', 'Mapping Kategori Non Medis')

@section('konten')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i> Mapping & Master Kategori Non Medis</h5>
        <a href="{{ route('laporan.pengeluaran-harian') }}" class="btn btn-light btn-sm text-primary font-weight-bold">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Laporan
        </a>
    </div>
    <div class="card-body">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs nav-pills mb-4" id="mappingTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold" id="mapping-list-tab" data-toggle="tab" data-bs-toggle="tab" href="#mappingList" role="tab">
                    <i class="fas fa-link me-1"></i> Mapping Supplier ke Kategori ({{ $mappings->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="master-kategori-tab" data-toggle="tab" data-bs-toggle="tab" href="#masterKategoriTab" role="tab">
                    <i class="fas fa-tags me-1"></i> Master Kategori ({{ $masterKategori->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="import-excel-tab" data-toggle="tab" data-bs-toggle="tab" href="#importExcelTab" role="tab">
                    <i class="fas fa-file-excel me-1"></i> Import Excel Sekaligus
                </a>
            </li>
        </ul>

        <div class="tab-content" id="mappingTabContent">
            
            <!-- TAB 1: MAPPING SUPPLIER -->
            <div class="tab-pane fade show active" id="mappingList" role="tabpanel">
                <div class="row">
                    <!-- Form Tambah/Ubah Mapping -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border border-primary shadow-sm h-100">
                            <div class="card-header bg-light font-weight-bold text-primary">
                                <i class="fas fa-edit me-1"></i> Atur Mapping Supplier
                            </div>
                            <div class="card-body">
                                <form action="{{ route('laporan.mapping-suplier.update') }}" method="POST" id="formMapping">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Nama Supplier <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_suplier" id="input_nama_suplier" class="form-control" list="suplierList" placeholder="Ketik atau pilih supplier..." required autocomplete="off">
                                        <datalist id="suplierList">
                                            @foreach($allSupliers as $sup)
                                                <option value="{{ $sup }}">
                                            @endforeach
                                        </datalist>
                                        <small class="text-muted">Pilih supplier dari SIMRS Khanza atau ketik nama baru.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Pilih Kategori Master <span class="text-danger">*</span></label>
                                        <select name="kategori" id="select_kategori" class="form-control" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($masterKategori as $kat)
                                                <option value="{{ $kat->nama_kategori }}">
                                                    [{{ $kat->jenis }}] {{ $kat->nama_kategori }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Kategori diambil dari Master Kategori.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-1"></i> Simpan Mapping
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Daftar Mapping -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold text-dark"><i class="fas fa-list me-1"></i> Daftar Mapping Terdaftar</span>
                                <input type="text" id="searchMappingTable" class="form-control form-control-sm w-50" placeholder="Cari supplier atau kategori...">
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                                    <table class="table table-bordered table-sm table-hover mb-0" id="tableMappings">
                                        <thead class="bg-light sticky-top">
                                            <tr class="text-center">
                                                <th width="50">No</th>
                                                <th>Nama Supplier</th>
                                                <th>Kategori Beban/Investasi</th>
                                                <th width="100">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($mappings as $index => $map)
                                                <tr>
                                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                                    <td class="align-middle fw-semibold text-dark nama-suplier-cell">{{ $map->nama_suplier }}</td>
                                                    <td class="align-middle kategori-cell">
                                                        <span class="badge {{ stripos($map->kategori, 'investasi') !== false || stripos($map->kategori, 'alat') !== false ? 'bg-primary text-white' : 'bg-info text-dark' }} px-2 py-1">
                                                            {{ $map->kategori }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 btn-edit-map" 
                                                            data-nama="{{ $map->nama_suplier }}" 
                                                            data-kategori="{{ $map->kategori }}" title="Edit">
                                                            <i class="fas fa-pen"></i>
                                                        </button>
                                                        <form action="{{ route('laporan.mapping-suplier.destroy', $map->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus mapping untuk {{ $map->nama_suplier }}?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data mapping supplier.</td>
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

            <!-- TAB 2: MASTER KATEGORI -->
            <div class="tab-pane fade" id="masterKategoriTab" role="tabpanel">
                <div class="row">
                    <!-- Form Tambah Kategori Master -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border border-success shadow-sm">
                            <div class="card-header bg-light font-weight-bold text-success">
                                <i class="fas fa-plus-circle me-1"></i> Tambah Master Kategori
                            </div>
                            <div class="card-body">
                                <form action="{{ route('laporan.master-kategori.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Nama Kategori <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Beban Pemeliharaan" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Jenis Pengeluaran <span class="text-danger">*</span></label>
                                        <select name="jenis" class="form-control" required>
                                            <option value="BEBAN">BEBAN (Operasional)</option>
                                            <option value="INVESTASI">INVESTASI (Alat / Modal)</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-plus me-1"></i> Simpan Kategori Baru
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Master Kategori -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold text-dark"><i class="fas fa-tags me-1"></i> Daftar Master Kategori Pengeluaran</span>
                                <input type="text" id="searchMasterTable" class="form-control form-control-sm w-50" placeholder="Cari master kategori...">
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                                    <table class="table table-bordered table-sm table-hover mb-0" id="tableMasterKategori">
                                        <thead class="bg-light sticky-top">
                                            <tr class="text-center">
                                                <th width="50">No</th>
                                                <th>Nama Kategori</th>
                                                <th width="140">Jenis</th>
                                                <th width="120">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($masterKategori as $idx => $kat)
                                                <tr>
                                                    <td class="text-center align-middle">{{ $idx + 1 }}</td>
                                                    <td class="align-middle fw-bold master-nama-cell">{{ $kat->nama_kategori }}</td>
                                                    <td class="text-center align-middle">
                                                        @if($kat->jenis === 'INVESTASI')
                                                            <span class="badge bg-primary text-white px-2 py-1">INVESTASI</span>
                                                        @else
                                                            <span class="badge bg-danger text-white px-2 py-1">BEBAN</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 btn-edit-kat"
                                                            data-id="{{ $kat->id }}"
                                                            data-nama="{{ $kat->nama_kategori }}"
                                                            data-jenis="{{ $kat->jenis }}"
                                                            data-url="{{ route('laporan.master-kategori.update', $kat->id) }}"
                                                            data-toggle="modal" data-target="#modalEditKategori"
                                                            data-bs-toggle="modal" data-bs-target="#modalEditKategori">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('laporan.master-kategori.destroy', $kat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori {{ $kat->nama_kategori }} dari master?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">Belum ada master kategori.</td>
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

            <!-- TAB 3: IMPORT EXCEL -->
            <div class="tab-pane fade" id="importExcelTab" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light font-weight-bold">
                                <i class="fas fa-paste me-1"></i> Paste dari Excel
                            </div>
                            <div class="card-body">
                                <form action="{{ route('laporan.mapping-suplier.simpan') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Copy 2 Kolom dari Excel</label>
                                        <p class="text-muted small">Kolom 1: Nama Supplier &nbsp;|&nbsp; Kolom 2: Nama Kategori</p>
                                        <textarea name="raw_data" rows="12" class="form-control" placeholder="PT. ABC&#9;Beban Pemeliharaan&#10;Toko XYZ&#9;Pembelian alat"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i> Import & Simpan Data</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="alert alert-info">
                            <h6 class="font-weight-bold"><i class="fas fa-info-circle me-1"></i> Petunjuk Import:</h6>
                            <ol class="mb-0 ps-3">
                                <li>Buka file Excel Anda yang berisi data nama supplier dan kategori.</li>
                                <li>Blok / seleksi 2 kolom (Supplier dan Kategori).</li>
                                <li>Tekan <kbd>Ctrl + C</kbd> untuk menyalin (copy).</li>
                                <li>Paste di kotak teks di sebelah kiri (<kbd>Ctrl + V</kbd>).</li>
                                <li>Klik tombol <strong>Import & Simpan Data</strong>. Kategori baru yang belum ada di master akan otomatis terdaftar.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Modal Edit Master Kategori -->
<div class="modal fade" id="modalEditKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit me-1"></i> Edit Master Kategori</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="formEditKategori">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="edit_nama_kategori" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Jenis Pengeluaran</label>
                        <select name="jenis" id="edit_jenis" class="form-control" required>
                            <option value="BEBAN">BEBAN (Operasional)</option>
                            <option value="INVESTASI">INVESTASI (Alat / Modal)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Tombol Edit Mapping di tabel
        document.querySelectorAll('.btn-edit-map').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var nama = this.getAttribute('data-nama');
                var kat = this.getAttribute('data-kategori');
                
                document.getElementById('input_nama_suplier').value = nama;
                document.getElementById('select_kategori').value = kat;
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
                document.getElementById('select_kategori').focus();
            });
        });

        // Tombol Edit Master Kategori
        document.querySelectorAll('.btn-edit-kat').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var url = this.getAttribute('data-url');
                var nama = this.getAttribute('data-nama');
                var jenis = this.getAttribute('data-jenis');
                
                document.getElementById('formEditKategori').action = url;
                document.getElementById('edit_nama_kategori').value = nama;
                document.getElementById('edit_jenis').value = jenis;
            });
        });

        // Filter Live Search Tabel Mapping
        var searchMap = document.getElementById('searchMappingTable');
        if (searchMap) {
            searchMap.addEventListener('keyup', function() {
                var val = this.value.toLowerCase();
                var rows = document.querySelectorAll('#tableMappings tbody tr');
                rows.forEach(function(row) {
                    var text = row.innerText.toLowerCase();
                    row.style.display = text.indexOf(val) > -1 ? '' : 'none';
                });
            });
        }

        // Filter Live Search Tabel Master Kategori
        var searchMaster = document.getElementById('searchMasterTable');
        if (searchMaster) {
            searchMaster.addEventListener('keyup', function() {
                var val = this.value.toLowerCase();
                var rows = document.querySelectorAll('#tableMasterKategori tbody tr');
                rows.forEach(function(row) {
                    var text = row.innerText.toLowerCase();
                    row.style.display = text.indexOf(val) > -1 ? '' : 'none';
                });
            });
        }
    });
</script>
@endsection
