@extends('layout.layoutDashboard')
@section('title', 'Detail Berkas Pegawai')

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

        .kategori-group-header td {
            background: #f0fdf4 !important;
            font-weight: 700;
            color: #1d7969;
            font-size: 0.95rem;
            padding: 10px 16px !important;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        body.dark-mode .kategori-group-header td {
            background: #1a2f2a !important;
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
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="fas fa-exclamation-triangle mr-2"></i><strong>Gagal Upload:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="row">
        {{-- KOLOM KIRI: INFO PEGAWAI --}}
        <div class="col-lg-4">
            <div class="pegawai-card">
                <div class="d-flex align-items-center mb-3">
                    @if ($pegawai->photo && $pegawai->photo != '' && $pegawai->photo != 'pages/pegawai/photo/')
                        <img src="{{ env('URL_KHANZA') }}/webapps/penggajian/{{ $pegawai->photo }}"
                            class="pegawai-avatar mr-3" alt="Foto">
                    @else
                        <div class="pegawai-avatar mr-3 d-flex align-items-center justify-content-center"
                            style="background: rgba(255,255,255,0.25); font-size: 2.2rem; border-radius: 50%; width: 80px; height: 80px; border: 3px solid rgba(255,255,255,0.5);">
                            <i class="fas fa-user"></i>
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
            <a href="{{ route('berkas.pegawai.semua') }}" class="btn btn-secondary btn-block" style="border-radius: 10px;">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Pegawai
            </a>
        </div>

        {{-- KOLOM KANAN: DAFTAR BERKAS --}}
        <div class="col-lg-8">
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
                <div class="card-header" style="background: transparent; border-bottom: 2px solid #f1f5f9;">
                    <h5 class="card-title mb-0" style="font-weight: 700; color: #1e293b;">
                        <i class="fas fa-folder-open mr-2" style="color: #1d7969;"></i>
                        Berkas Pegawai: {{ $pegawai->nama }}
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
                                                    {{ basename($b->berkas) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" onclick="window.open('{{ env('URL_KHANZA') }}/webapps/penggajian/{{ $b->berkas }}', '_blank')"
                                                    class="btn btn-view btn-sm mr-1" title="Lihat Berkas">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </button>
                                                <form action="{{ route('berkas.pegawai.destroy-admin') }}" method="POST"
                                                    style="display: inline-block;"
                                                    onsubmit="return confirm('Yakin ingin menghapus berkas ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="nik" value="{{ $b->nik }}">
                                                    <input type="hidden" name="kode_berkas" value="{{ $b->kode_berkas }}">
                                                    <button type="submit" class="btn btn-delete btn-sm" title="Hapus Berkas">
                                                        <i class="fas fa-trash-alt"></i> Hapus
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
                            <i class="fas fa-folder-open d-block" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                            <h5 style="color: #64748b; font-weight: 600;">Belum ada berkas terupload</h5>
                            <p style="color: #94a3b8;">Pegawai ini belum mengupload berkas apapapun.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
