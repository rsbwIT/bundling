@extends('layout.layoutDashboard')

@section('title', 'Daftar Surat Biometrik Rajal')

@section('konten')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<style>
    .card-header {
        background: linear-gradient(135deg, #0062cc, #0056b3);
        color: #fff;
        border-bottom: none;
        padding: 15px 20px;
        border-radius: .5rem .5rem 0 0;
    }
    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: .5rem;
        margin-bottom: 20px;
    }
    .text-truncate {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .dt-center { text-align: center; }
</style>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="fa fa-file-medical-alt"></i> Daftar Surat Biometrik Rajal</h5>
    </div>
    <div class="card-body">
        {{-- 🔍 Filter Section --}}
        <div class="filter-section">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Nama pasien / No SEP / Poli">
                </div>
                <div class="col-md-3">
                    <input type="date" id="startDate" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="date" id="endDate" class="form-control">
                </div>
                <div class="col-md-3 d-grid">
                    <button id="resetFilter" class="btn btn-outline-secondary">
                        <i class="fas fa-sync-alt"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- 📋 Tabel daftar surat --}}
        <div class="table-responsive">
            <table id="suratTable" class="table table-striped table-bordered table-hover table-sm align-middle">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>No</th>
                        <th>No SEP</th>
                        <th>No Peserta</th>
                        <th>Nama Pasien</th>
                        <th>Poli</th>
                        <th>Tgl SEP</th>
                        <th>Diagnosis</th>
                        <th>Nomor Surat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratList as $index => $surat)
                    <tr>
                        <td class="dt-center">{{ $index + 1 }}</td>
                        <td>{{ $surat->no_sep }}</td>
                        <td>{{ $surat->no_peserta }}</td>
                        <td class="text-truncate">{{ $surat->nm_pasien }}</td>
                        <td>{{ $surat->nm_poli }}</td>
                        <td class="dt-center" data-tglsep="{{ $surat->tglsep }}">
                            {{ \Carbon\Carbon::parse($surat->tglsep)->format('d-m-Y') }}
                        </td>
                        <td class="text-truncate">{{ $surat->diagnosis }}</td>
                        <td><span class="badge bg-success">{{ $surat->nomor_surat }}</span></td>
                        <td class="dt-center">
                            <a href="{{ route('biometrik.rajal.print', $surat->id) }}"
                               class="btn btn-warning btn-sm" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">Belum ada surat yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- DataTables JS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    var table = $('#suratTable').DataTable({
        responsive: true,
        paging: true,
        pageLength: 10,
        lengthChange: false,
        ordering: true,
        order: [[0, 'asc']],
        columnDefs: [
            { targets: [0,5,8], className: 'dt-center' } // kolom No, Tgl SEP, Aksi center
        ]
    });

    // Default tanggal hari ini
    const today = new Date();
    const formatYMD = date => date.toISOString().split('T')[0];
    $('#startDate').val(formatYMD(today));
    $('#endDate').val(formatYMD(today));

    // Filter custom
    function filterTable() {
        const searchVal = $('#searchInput').val().toLowerCase();
        const start = $('#startDate').val();
        const end   = $('#endDate').val();

        table.rows().every(function() {
            const data = this.data();
            const rowText = data.join(' ').toLowerCase();

            // Tanggal dari dataset
            const tglSepAttr = $(this.node()).find('td[data-tglsep]').data('tglsep');
            let show = true;

            // Filter search
            if(searchVal && !rowText.includes(searchVal)) show = false;

            // Filter tanggal
            if(tglSepAttr && start && tglSepAttr < start) show = false;
            if(tglSepAttr && end && tglSepAttr > end) show = false;

            $(this.node()).toggle(show);
        });
    }

    $('#searchInput').on('keyup', filterTable);
    $('#startDate, #endDate').on('change', filterTable);

    $('#resetFilter').on('click', function(e){
        e.preventDefault();
        $('#searchInput').val('');
        $('#startDate').val(formatYMD(today));
        $('#endDate').val(formatYMD(today));
        filterTable();
    });
});
</script>
@endsection
