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
    </style>

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
                                        <th style="width: 100px;" class="text-center">Aksi</th>
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
                                                    class="btn btn-view btn-sm" title="Lihat Berkas Pegawai">
                                                    <i class="fas fa-folder-open"></i> Lihat Detail
                                                </a>
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
