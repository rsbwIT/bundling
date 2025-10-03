@extends('layout.layoutDashboard')

@section('title', 'Daftar Pasien Kamar Inap')

@section('konten')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">

    <!-- Header -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header text-white" style="background-color: #0066cc;">
            <h5 class="mb-0">
                <i class="fa fa-procedures"></i> Daftar Pasien Ranap DPJP
            </h5>
        </div>
        <div class="card-body py-3 d-flex justify-content-between align-items-center bg-white">
            <span class="badge fs-6 px-3 py-2 shadow-sm text-white" style="background-color: #0066cc;">
                <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('d M Y') }}
            </span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">

                <!-- Search Pasien -->
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari nama pasien / No RM">
                    </div>
                </div>

                <!-- Filter Dokter -->
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text text-white" style="background-color: #0066cc;">
                            <i class="fas fa-user-md"></i>
                        </span>
                        <select id="dokterFilter" class="form-select border-primary">
                            <option value="">👩‍⚕️ Semua Dokter</option>
                            @php
                                $dokters = collect($data)->pluck('nm_dokter')->unique()->sort();
                            @endphp
                            @foreach ($dokters as $dokter)
                                <option value="{{ $dokter }}">{{ $dokter }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="col-md-6 d-grid gap-2">
                    <button id="resetFilter" class="btn btn-outline-danger">🔄 Reset</button>
                    <button id="printTable" class="btn btn-outline-primary">🖨️ Print</button>
                    <button id="saveImage" class="btn btn-outline-success">💾 Save</button>
                    <button id="sendToWA" class="btn btn-outline-warning">📤 Kirim WA</button>
                </div>

            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table id="suratRanapTable" class="table table-bordered table-hover table-sm align-middle text-center">
                <thead class="text-dark bg-white border-bottom fw-bold">
                    <tr>
                        <th>No</th>
                        <th>No. Rawat</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Penjamin</th>
                        <th>Tanggal Masuk</th>
                        <th>Jam Masuk</th>
                        <th>Diagnosa Awal</th>
                        <th>Kamar - Bangsal</th>
                        <th>Dokter DPJP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $i => $row)
                        <tr @if($i % 2 == 0) style="background-color: #f9f9f9;" @endif>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $row->no_rawat }}</td>
                            <td>{{ $row->no_rkm_medis }}</td>
                            <td class="text-start fw-semibold">{{ $row->nm_pasien }}</td>
                            <td>
                                <span class="badge
                                    @if($row->png_jawab=='BPJS') bg-success
                                    @elseif($row->png_jawab=='Umum') bg-warning text-dark
                                    @else bg-info @endif">
                                    {{ $row->png_jawab }}
                                </span>
                            </td>
                            <td>{{ $row->tgl_masuk }}</td>
                            <td>{{ $row->jam_masuk }}</td>
                            <td class="text-start">{{ $row->diagnosa_awal }}</td>
                            <td>{{ $row->kamar_bangsal }}</td>
                            <td>{{ $row->nm_dokter }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="waToast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">WA berhasil dikirim!</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {

    var table = $('#suratRanapTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50],
        language: {
            search: "🔍 Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ pasien",
            paginate: { previous: "← Prev", next: "Next →" },
            zeroRecords: "🚫 Tidak ada data"
        }
    });

    // Filter
    $('#searchInput').on('keyup', function(){ table.search(this.value).draw(); });
    $('#dokterFilter').on('change', function(){ table.column(9).search(this.value).draw(); });
    $('#resetFilter').on('click', function(){
        $('#searchInput,#dokterFilter').val('');
        table.search('').columns().search('').draw();
    });

    // Print
    $('#printTable').on('click', function(){
        captureTableAsImage(null, function(){ window.print(); });
    });

    // Save as Image
    $('#saveImage').on('click', function(){ captureTableAsImage('Daftar_Pasien_Ranap.png'); });

    // Send WA
    $('#sendToWA').on('click', function(){ captureTableAsImage(null, sendWA); });

    // Auto send WA every 5 minutes
    setInterval(function(){ captureTableAsImage(null, sendWA); }, 5 * 60 * 1000);

    // Capture table as image
    function captureTableAsImage(filename=null, callback=null){
        var currentPage = table.page(); table.page.len(-1).draw();

        var dokterNama = $('#dokterFilter').val() || 'Semua Dokter';
        var pasienCount = table.rows({ filter: 'applied' }).data().length;

        var now = new Date();
        var tanggal = now.toLocaleDateString('id-ID',{ day:'2-digit', month:'short', year:'numeric' });
        var jam = now.toLocaleTimeString('id-ID',{ hour:'2-digit', minute:'2-digit' });

        var container = $('<div>').css({ padding:'20px', fontFamily:'Arial,sans-serif' });
        container.append(`
            <div style="margin-bottom:10px;">
                <h2 style="margin:0;font-size:16pt;">DAFTAR PASIEN RANAP</h2>
                <p style="margin:0; font-size:12pt;">
                    Dokter DPJP: <strong>${dokterNama}</strong> &nbsp;&nbsp;|&nbsp;&nbsp; Jumlah Pasien: <strong>${pasienCount}</strong>
                </p>
                <p style="margin:0; font-size:10pt;">
                    Tanggal: ${tanggal} &nbsp;&nbsp; Jam: ${jam}
                </p>
            </div>
        `);
        container.append($('#suratRanapTable').clone());
        $('body').append(container);

        html2canvas(container[0], { scale:2 }).then(function(canvas){
            if(filename){
                var link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = filename;
                link.click();
            }
            if(callback) callback(canvas.toDataURL('image/png'));
            container.remove();
            table.page.len(10).draw();
            table.page(currentPage).draw(false);
        });
    }

    // Send WA function
    function sendWA(imageData){
        $.ajax({
            url: "{{ route('ranap.save_wa') }}",
            method: "POST",
            data: { _token:'{{ csrf_token() }}', image:imageData, phone:'6289601112787' },
            success: function(res){
                var toastEl = new bootstrap.Toast(document.getElementById('waToast'));
                toastEl.show();
                console.log('WA terkirim:', res);
            },
            error: function(err){
                alert('Gagal mengirim WA!');
                console.error(err);
            }
        });
    }

});
</script>
@endsection
