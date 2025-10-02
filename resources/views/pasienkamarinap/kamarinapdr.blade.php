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

                <!-- Reset, Print, Save, WA Buttons -->
                <div class="col-md-6 d-grid gap-2">
                    <button id="resetFilter" class="btn btn-outline-danger">🔄 Reset</button>
                    <button id="printTable" class="btn btn-outline-primary">🖨️ Print</button>
                    <button id="saveImage" class="btn btn-outline-success">💾 Save as Image</button>
                    <button id="sendToWA" class="btn btn-outline-warning">📤 Kirim ke WA</button>
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
                        <th style="width: 40px;">No</th>
                        <th style="width: 120px;">No. Rawat</th>
                        <th style="width: 100px;">No. RM</th>
                        <th style="min-width: 180px;">Nama Pasien</th>
                        <th style="min-width: 120px;">Penjamin</th>
                        <th style="min-width: 120px;">Tanggal Masuk</th>
                        <th style="width: 100px;">Jam Masuk</th>
                        <th style="min-width: 180px;">Diagnosa Awal</th>
                        <th style="min-width: 160px;">Kamar - Bangsal</th>
                        <th style="min-width: 160px;">Dokter DPJP</th>
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

<!-- DataTables & html2canvas -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
$(document).ready(function() {

    // ===== DataTable =====
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

    // ===== Filter =====
    $('#searchInput').on('keyup', function(){ table.search(this.value).draw(); });
    $('#dokterFilter').on('change', function(){ table.column(9).search(this.value).draw(); });
    $('#resetFilter').on('click', function(){
        $('#searchInput,#dokterFilter').val('');
        table.search('').columns().search('').draw();
    });

    // ===== Print =====
    $('#printTable').on('click', function(){
        var currentPage = table.page(); table.page.len(-1).draw();
        var now = new Date();
        var tanggal = now.toLocaleDateString('id-ID',{ day:'2-digit', month:'short', year:'numeric' });
        var jam = now.toLocaleTimeString('id-ID',{ hour:'2-digit', minute:'2-digit' });
        var tableClone = $('#suratRanapTable').clone();
        var newWin = window.open('','_blank','width=1200,height=800');

        newWin.document.write(`
            <html><head><title>Print Pasien Ranap</title>
            <style>
                body{font-family:Arial,sans-serif;margin:20px;}
                table{width:100%;border-collapse:collapse;}
                th,td{border:1px solid #000;padding:6px;font-size:12px;}
            </style></head>
            <body>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <h2 style="margin:0;font-size:16pt;">DAFTAR PASIEN RANAP</h2>
                    <span><strong>Tanggal:</strong>${tanggal} &nbsp;&nbsp; <strong>Jam:</strong>${jam}</span>
                </div>
                ${tableClone.prop('outerHTML')}
            </body></html>
        `);
        newWin.document.close(); newWin.print();
        table.page.len(10).draw(); table.page(currentPage).draw(false);
    });

    // ===== Save as Image =====
    $('#saveImage').on('click', function(){
        captureTableAsImage('Daftar_Pasien_Ranap.png');
    });

    // ===== Kirim ke WA =====
    $('#sendToWA').on('click', function(){
        captureTableAsImage(null, function(imageData){
            $.ajax({
                url: "{{ route('ranap.save_wa') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    image: imageData,
                    phone: '6289601112787' // ganti nomor WA tujuan
                },
                success: function(res){
                    alert(res.message);
                },
                error: function(err){
                    alert('Gagal mengirim gambar ke WA!');
                }
            });
        });
    });

    // ===== Function capture table as image =====
    function captureTableAsImage(filename = null, callback = null){
        var currentPage = table.page(); table.page.len(-1).draw();
        var now = new Date();
        var tanggal = now.toLocaleDateString('id-ID',{ day:'2-digit', month:'short', year:'numeric' });
        var jam = now.toLocaleTimeString('id-ID',{ hour:'2-digit', minute:'2-digit' });

        var container = $('<div>').css({ padding:'20px', fontFamily:'Arial,sans-serif' });
        var header = $('<div>').css({ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:'10px' });
        header.append(`<h2 style="margin:0;font-size:16pt;">DAFTAR PASIEN RANAP</h2>`);
        header.append(`<span><strong>Tanggal:</strong> ${tanggal} &nbsp;&nbsp; <strong>Jam:</strong> ${jam}</span>`);
        container.append(header);
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
            table.page.len(10).draw(); table.page(currentPage).draw(false);
        });
    }

});
</script>
@endsection
