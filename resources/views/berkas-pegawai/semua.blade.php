@extends('layout.layoutDashboard')
@section('title', 'Semua Berkas Pegawai')

@section('konten')
    <style>
        .berkas-table {
            border-radius: 12px;
            overflow: hidden;
            width: 100%;
        }

        .berkas-table thead th {
            background: #1d7969;
            color: #fff;
            font-weight: 600;
            padding: 14px 16px;
            border: none;
            white-space: nowrap;
        }

        .berkas-table tbody td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .berkas-table tbody tr:hover {
            background: #f0fdf4;
        }

        .btn-view {
            background: #dbeafe;
            color: #2563eb;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .btn-view:hover {
            background: #2563eb;
            color: #fff;
        }

        .btn-upload {
            background: #dcfce7;
            color: #16a34a;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .btn-upload:hover {
            background: #16a34a;
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state h5 {
            color: #64748b;
            font-weight: 600;
        }

        body.dark-mode .berkas-table tbody tr:hover {
            background: #2f2f32 !important;
        }

        .btn-upload-submit {
            background: linear-gradient(135deg, #1d7969, #2aa58a);
            border: none;
            color: #fff;
            padding: 10px 28px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-upload-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(29, 121, 105, 0.3);
            color: #fff;
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: transparent; border-bottom: 2px solid #f1f5f9;">
                    <h5 class="card-title mb-0" style="font-weight: 700; color: #1e293b;">
                        <i class="fas fa-users mr-2" style="color: #1d7969;"></i>
                        Daftar Pegawai
                        <span class="badge" style="background: #dcfce7; color: #16a34a; font-size: 0.75rem; margin-left: 8px;">
                            {{ $pegawaiList->count() }} Pegawai
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    @if ($pegawaiList->count() > 0)
                        <div class="table-responsive">
                            <table class="table berkas-table mb-0" id="tableSemuaBerkas">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Pegawai</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Berkas Diupload</th>
                                        <th style="width: 200px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach ($pegawaiList as $p)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>
                                                <div style="font-weight: 600; color: #1e293b;">{{ $p->nama }}</div>
                                                <small style="color: #64748b;"><i class="fas fa-id-badge mr-1"></i>{{ $p->nik }}</small>
                                            </td>
                                            <td>
                                                @if($p->jk == 'Pria')
                                                    <span style="color: #2563eb;"><i class="fas fa-male mr-1"></i> Laki-laki</span>
                                                @else
                                                    <span style="color: #ec4899;"><i class="fas fa-female mr-1"></i> Perempuan</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $p->jumlah_berkas > 0 ? 'badge-success' : 'badge-secondary' }}" style="font-size: 0.85rem; padding: 6px 12px;">
                                                    <i class="fas fa-folder-open mr-1"></i> {{ $p->jumlah_berkas }} Berkas
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('berkas.pegawai.detail', $p->nik) }}"
                                                    class="btn btn-view btn-sm mr-1" title="Lihat Berkas Pegawai">
                                                    <i class="fas fa-folder-open"></i> Lihat
                                                </a>
                                                <button type="button" class="btn btn-upload btn-sm"
                                                    onclick="openUploadModal('{{ $p->nik }}', '{{ addslashes($p->nama) }}')"
                                                    title="Upload Berkas">
                                                    <i class="fas fa-cloud-upload-alt"></i> Upload
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-users d-block" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                            <h5>Belum ada pegawai</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL UPLOAD --}}
    <div class="modal fade" id="modalUpload" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header" style="background: linear-gradient(135deg, #1d7969, #2aa58a); border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Berkas: <span id="modalNamaPegawai"></span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('berkas.pegawai.upload-admin') }}" method="POST" enctype="multipart/form-data" id="formUploadAdmin">
                    @csrf
                    <input type="hidden" name="nik" id="modalNik">
                    <div class="modal-body">
                        {{-- Pilih Kategori --}}
                        <div class="form-group">
                            <label style="font-weight: 600; font-size: 0.9rem; color: #475569;">Kategori</label>
                            <select id="modalSelectKategori" class="form-control" style="border-radius: 10px;">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriList as $kat)
                                    <option value="{{ $kat }}">{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Daftar berkas per kategori --}}
                        <div id="modalBerkasContainer" style="display: none;">
                            <label class="mb-2" style="font-weight: 600; font-size: 0.9rem; color: #475569;">
                                <i class="fas fa-list mr-1"></i> Pilih file untuk masing-masing berkas:
                            </label>
                            <div id="modalBerkasItems"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px;">Batal</button>
                        <button type="submit" id="modalBtnSubmit" class="btn btn-upload-submit" style="display: none;">
                            <i class="fas fa-upload mr-2"></i>Upload Semua Berkas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Datatables CSS & JS -->
    <link rel="stylesheet" href="/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    
    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script>
        // === DATA MASTER BERKAS ===
        const masterBerkasGrouped = @json($masterBerkasGrouped);

        function openUploadModal(nik, nama) {
            document.getElementById('modalNik').value = nik;
            document.getElementById('modalNamaPegawai').textContent = nama;
            document.getElementById('modalSelectKategori').value = '';
            document.getElementById('modalBerkasContainer').style.display = 'none';
            document.getElementById('modalBerkasItems').innerHTML = '';
            document.getElementById('modalBtnSubmit').style.display = 'none';
            $('#modalUpload').modal('show');
        }

        document.getElementById('modalSelectKategori').addEventListener('change', function() {
            const kategori = this.value;
            const container = document.getElementById('modalBerkasContainer');
            const items = document.getElementById('modalBerkasItems');
            const btnSubmit = document.getElementById('modalBtnSubmit');
            items.innerHTML = '';

            if (!kategori) {
                container.style.display = 'none';
                btnSubmit.style.display = 'none';
                return;
            }

            const list = masterBerkasGrouped[kategori] || [];

            list.forEach((item, idx) => {
                const row = document.createElement('div');
                row.className = 'mb-3';
                row.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">
                            <span style="color: #1d7969; font-weight: 700;">${item.no_urut}.</span> ${item.nama_berkas}
                        </span>
                        <span class="modal-file-status-${idx}" style="font-size: 0.75rem; color: #94a3b8;">Belum dipilih</span>
                    </div>
                    <div class="drop-zone-modal" data-idx="${idx}"
                        onclick="document.getElementById('modalFileInput_${idx}').click()"
                        style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 10px; text-align: center;
                               cursor: pointer; transition: all 0.2s; background: #fff;">
                        <span class="modal-drop-label-${idx}" style="color: #94a3b8; font-size: 0.8rem;">
                            <i class="fas fa-cloud-upload-alt mr-1"></i> Klik atau seret file ke sini
                        </span>
                        <span class="modal-drop-file-${idx}" style="display: none; color: #1d7969; font-weight: 600; font-size: 0.8rem;">
                            <i class="fas fa-check-circle mr-1"></i> <span class="modal-drop-filename-${idx}"></span>
                        </span>
                    </div>
                    <input type="hidden" name="kode_berkas[]" value="${item.kode}" disabled>
                    <input type="file" name="files[]" id="modalFileInput_${idx}"
                        accept=".pdf,.jpg,.jpeg,.png" style="display: none;"
                        onchange="onModalFileSelected(this, ${idx})">
                `;
                items.appendChild(row);

                // Drag & drop
                const dropZone = row.querySelector('.drop-zone-modal');
                ['dragenter', 'dragover'].forEach(t => {
                    dropZone.addEventListener(t, e => {
                        e.preventDefault();
                        dropZone.style.borderColor = '#1d7969';
                        dropZone.style.background = '#f0fdf4';
                    });
                });
                ['dragleave', 'drop'].forEach(t => {
                    dropZone.addEventListener(t, e => {
                        e.preventDefault();
                        dropZone.style.borderColor = '#cbd5e1';
                        dropZone.style.background = '#fff';
                    });
                });
                dropZone.addEventListener('drop', e => {
                    const fileInput = document.getElementById('modalFileInput_' + idx);
                    fileInput.files = e.dataTransfer.files;
                    onModalFileSelected(fileInput, idx);
                });
            });

            container.style.display = 'block';
            btnSubmit.style.display = 'block';
        });

        function onModalFileSelected(input, idx) {
            const statusEl = document.querySelector('.modal-file-status-' + idx);
            const hiddenInput = input.previousElementSibling;
            const dropLabel = document.querySelector('.modal-drop-label-' + idx);
            const dropFile = document.querySelector('.modal-drop-file-' + idx);
            const dropFilename = document.querySelector('.modal-drop-filename-' + idx);
            const dropZone = input.closest('.mb-3').querySelector('.drop-zone-modal');

            if (input.files[0]) {
                statusEl.textContent = formatBytes(input.files[0].size);
                statusEl.style.color = '#1d7969';
                hiddenInput.disabled = false;
                dropLabel.style.display = 'none';
                dropFile.style.display = 'inline';
                dropFilename.textContent = input.files[0].name;
                dropZone.style.borderColor = '#1d7969';
                dropZone.style.borderStyle = 'solid';
                dropZone.style.background = '#f0fdf4';
            } else {
                statusEl.textContent = 'Belum dipilih';
                statusEl.style.color = '#94a3b8';
                hiddenInput.disabled = true;
                dropLabel.style.display = 'inline';
                dropFile.style.display = 'none';
                dropZone.style.borderColor = '#cbd5e1';
                dropZone.style.borderStyle = 'dashed';
                dropZone.style.background = '#fff';
            }
        }

        // Before submit: disable empty file inputs
        document.getElementById('formUploadAdmin')?.addEventListener('submit', function(e) {
            const fileInputs = this.querySelectorAll('input[type="file"]');
            let hasFile = false;

            fileInputs.forEach((fi) => {
                const hidden = fi.previousElementSibling;
                if (!fi.files || fi.files.length === 0) {
                    fi.disabled = true;
                    hidden.disabled = true;
                } else {
                    hidden.disabled = false;
                    hasFile = true;
                }
            });

            if (!hasFile) {
                e.preventDefault();
                alert('Pilih minimal satu file untuk diupload');
            }
        });

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // DataTable
        $(function () {
            if ($("#tableSemuaBerkas").length) {
                $("#tableSemuaBerkas").DataTable({
                    "responsive": true,
                    "lengthChange": true,
                    "autoWidth": false,
                    "language": {
                        "search": "Cari Pegawai:",
                        "lengthMenu": "Tampilkan _MENU_ data",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                        "infoFiltered": "(difilter dari _MAX_ total data)",
                        "paginate": {
                            "first": "Awal",
                            "last": "Akhir",
                            "next": "Selanjutnya",
                            "previous": "Sebelumnya"
                        }
                    }
                });
            }
        });
    </script>
@endpush
