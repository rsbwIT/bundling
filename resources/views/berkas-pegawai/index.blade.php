@extends('layout.layoutDashboard')
@section('title', 'Upload Berkas Pegawai')

@section('konten')
    <style>
        .pegawai-card {
            background: linear-gradient(135deg, #1d7969, #2aa58a);
            border-radius: 16px;
            color: #fff;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(29, 121, 105, 0.25);
        }

        .pegawai-card .pegawai-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.5);
            object-fit: cover;
        }

        .pegawai-card .pegawai-name {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .pegawai-card .pegawai-nik {
            font-size: 0.9rem;
            opacity: 0.85;
        }

        .upload-card {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            background: #fafbfc;
            cursor: pointer;
        }

        .upload-card:hover,
        .upload-card.dragover {
            border-color: #1d7969;
            background: #f0fdf4;
        }

        .upload-card .upload-icon {
            font-size: 3rem;
            color: #1d7969;
            margin-bottom: 12px;
        }

        .berkas-table {
            border-radius: 12px;
            overflow: hidden;
        }

        .berkas-table thead th {
            background: #1d7969;
            color: #fff;
            font-weight: 600;
            padding: 14px 16px;
            border: none;
        }

        .berkas-table tbody td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .berkas-table tbody tr:hover {
            background: #f0fdf4;
        }

        .badge-file {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
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

        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: #fff;
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

        .stat-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .empty-state h5 {
            color: #64748b;
            font-weight: 600;
        }

        .empty-state p {
            color: #94a3b8;
        }

        body.dark-mode .upload-card {
            background: #252527;
            border-color: #3a3a3c;
        }

        body.dark-mode .upload-card:hover {
            border-color: #1d7969;
            background: #1a2f2a;
        }

        .kategori-group-header td {
            background: #f0fdf4 !important;
            font-weight: 700;
            color: #1d7969;
            font-size: 0.95rem;
            padding: 10px 16px !important;
        }

        body.dark-mode .kategori-group-header td {
            background: #1a2f2a !important;
        }
    </style>

    <div class="row">
        {{-- KOLOM KIRI: INFO PEGAWAI & UPLOAD --}}
        <div class="col-lg-4">
            @if ($pegawai)
                {{-- Info Pegawai --}}
                <div class="pegawai-card">
                    <div class="d-flex align-items-center mb-3">
                        @if ($pegawai->photo && $pegawai->photo != '')
                            <img src="data:image/jpeg;base64,{{ base64_encode($pegawai->photo) }}"
                                class="pegawai-avatar mr-3" alt="Foto">
                        @else
                            <div class="pegawai-avatar mr-3 d-flex align-items-center justify-content-center"
                                style="background: rgba(255,255,255,0.25); font-size: 2.2rem;">
                                <i class="fas fa-user-circle"></i>
                            </div>
                        @endif
                        <div>
                            <div class="pegawai-name">{{ $pegawai->nama }}</div>
                            <div class="pegawai-nik">
                                <i class="fas fa-id-badge mr-1"></i>{{ $pegawai->nik }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        <div class="stat-badge">
                            <i class="fas fa-venus-mars mr-1"></i>
                            {{ $pegawai->jk == 'Pria' ? 'Laki-laki' : 'Perempuan' }}
                        </div>
                        <div class="stat-badge">
                            <i class="fas fa-folder-open mr-1"></i>
                            {{ $berkas->count() }} Berkas
                        </div>
                    </div>
                </div>

                {{-- Form Upload --}}
                <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                    <div class="card-body">
                        <h6 class="mb-3" style="font-weight: 700; color: #1e293b;">
                            <i class="fas fa-cloud-upload-alt mr-2" style="color: #1d7969;"></i>Upload Berkas Baru
                        </h6>
                        <form action="{{ route('berkas.pegawai.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- 1. Pilih Kategori --}}
                            <div class="form-group">
                                <label style="font-weight: 600; font-size: 0.9rem; color: #475569;">
                                    Kategori
                                </label>
                                <select id="selectKategori" class="form-control" style="border-radius: 10px;">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategoriList as $kat)
                                        <option value="{{ $kat }}">{{ $kat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Daftar berkas per kategori (muncul otomatis) --}}
                            <div id="berkasListContainer" style="display: none;">
                                <label class="mb-2" style="font-weight: 600; font-size: 0.9rem; color: #475569;">
                                    <i class="fas fa-list mr-1"></i> Pilih file untuk masing-masing berkas:
                                </label>
                                <div id="berkasItems"></div>
                            </div>

                            <button type="submit" id="btnSubmit" class="btn btn-upload-submit btn-block mt-3" style="display: none;">
                                <i class="fas fa-upload mr-2"></i>Upload Semua Berkas
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- KOLOM KANAN: DAFTAR BERKAS --}}
        <div class="col-lg-8">
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                <div class="card-header" style="background: transparent; border-bottom: 2px solid #f1f5f9;">
                    <h5 class="card-title mb-0" style="font-weight: 700; color: #1e293b;">
                        <i class="fas fa-folder-open mr-2" style="color: #1d7969;"></i>
                        Daftar Berkas Saya
                        <span class="badge" style="background: #dcfce7; color: #16a34a; font-size: 0.75rem;">
                            {{ $berkas->count() }} file
                        </span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if ($berkas->count() > 0)
                        <div class="table-responsive">
                            <table class="table berkas-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Berkas</th>
                                        <th>Tgl Upload</th>
                                        <th>File</th>
                                        <th style="width: 160px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $currentKategori = ''; $no = 1; @endphp
                                    @foreach ($berkas as $b)
                                        @if ($b->kategori !== $currentKategori)
                                            @php $currentKategori = $b->kategori; @endphp
                                            <tr class="kategori-group-header">
                                                <td colspan="5">
                                                    <i class="fas fa-tag mr-2"></i>{{ $b->kategori }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td>{{ $b->no_urut }}</td>
                                            <td>
                                                <span style="font-weight: 600; color: #1e293b;">{{ $b->nama_berkas }}</span>
                                            </td>
                                            <td>
                                                <small style="color: #64748b;">{{ $b->tgl_uploud }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $ext = pathinfo($b->berkas, PATHINFO_EXTENSION);
                                                    $iconMap = [
                                                        'pdf' => ['fas fa-file-pdf', '#dc2626', '#fee2e2'],
                                                        'jpg' => ['fas fa-file-image', '#ea580c', '#ffedd5'],
                                                        'jpeg' => ['fas fa-file-image', '#ea580c', '#ffedd5'],
                                                        'png' => ['fas fa-file-image', '#7c3aed', '#ede9fe'],
                                                    ];
                                                    $icon = $iconMap[strtolower($ext)] ?? ['fas fa-file', '#64748b', '#f1f5f9'];
                                                @endphp
                                                <span class="badge-file"
                                                    style="background: {{ $icon[2] }}; color: {{ $icon[1] }};">
                                                    <i class="{{ $icon[0] }}"></i>
                                                    {{ $b->berkas }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="http://192.168.5.88/webapps/penggajian/{{ $b->berkas }}"
                                                    target="_blank" class="btn btn-view btn-sm mr-1" title="Lihat Berkas">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('berkas.pegawai.destroy') }}" method="POST"
                                                    style="display: inline-block;"
                                                    onsubmit="return confirm('Yakin ingin menghapus berkas ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="kode_berkas" value="{{ $b->kode_berkas }}">
                                                    <button type="submit" class="btn btn-delete btn-sm" title="Hapus Berkas">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-folder-open d-block"></i>
                            <h5>Belum ada berkas</h5>
                            <p>Upload berkas Anda menggunakan form di sebelah kiri</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // === DATA MASTER BERKAS (GROUPED BY KATEGORI) ===
        const masterBerkasGrouped = @json($masterBerkasGrouped);

        const selectKategori = document.getElementById('selectKategori');
        const berkasContainer = document.getElementById('berkasListContainer');
        const berkasItems = document.getElementById('berkasItems');
        const btnSubmit = document.getElementById('btnSubmit');

        selectKategori.addEventListener('change', function() {
            const kategori = this.value;
            berkasItems.innerHTML = '';

            if (!kategori) {
                berkasContainer.style.display = 'none';
                btnSubmit.style.display = 'none';
                return;
            }

            const items = masterBerkasGrouped[kategori] || [];

            items.forEach((item, idx) => {
                const row = document.createElement('div');
                row.className = 'berkas-upload-row mb-3 drop-area';
                row.setAttribute('data-idx', idx);
                row.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">
                            <span style="color: #1d7969; font-weight: 700;">${item.no_urut}.</span> ${item.nama_berkas}
                        </span>
                        <span class="file-status-${idx}" style="font-size: 0.75rem; color: #94a3b8;">Belum dipilih</span>
                    </div>
                    <div class="drop-zone" data-idx="${idx}" onclick="document.getElementById('fileInput_${idx}').click()"
                        style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 10px; text-align: center;
                               cursor: pointer; transition: all 0.2s; background: #fff;">
                        <span class="drop-label-${idx}" style="color: #94a3b8; font-size: 0.8rem;">
                            <i class="fas fa-cloud-upload-alt mr-1"></i> Klik atau seret file ke sini
                        </span>
                        <span class="drop-file-${idx}" style="display: none; color: #1d7969; font-weight: 600; font-size: 0.8rem;">
                            <i class="fas fa-check-circle mr-1"></i> <span class="drop-filename-${idx}"></span>
                        </span>
                    </div>
                    <input type="hidden" name="kode_berkas[]" value="${item.kode}" disabled>
                    <input type="file" name="files[]" id="fileInput_${idx}"
                        accept=".pdf,.jpg,.jpeg,.png" style="display: none;"
                        onchange="onFileSelected(this, ${idx})">
                `;
                berkasItems.appendChild(row);

                // Setup drag & drop per row
                const dropZone = row.querySelector('.drop-zone');
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
                    const fileInput = document.getElementById('fileInput_' + idx);
                    fileInput.files = e.dataTransfer.files;
                    onFileSelected(fileInput, idx);
                });
            });

            berkasContainer.style.display = 'block';
            btnSubmit.style.display = 'block';
        });

        function onFileSelected(input, idx) {
            const statusEl = document.querySelector('.file-status-' + idx);
            const hiddenInput = input.previousElementSibling;
            const dropLabel = document.querySelector('.drop-label-' + idx);
            const dropFile = document.querySelector('.drop-file-' + idx);
            const dropFilename = document.querySelector('.drop-filename-' + idx);
            const dropZone = input.closest('.berkas-upload-row').querySelector('.drop-zone');

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

        // Sebelum submit, hapus row yang tidak ada file-nya
        document.querySelector('form')?.addEventListener('submit', function(e) {
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
    </script>
@endpush
