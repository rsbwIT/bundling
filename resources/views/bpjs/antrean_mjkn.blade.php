@extends('layout.layoutDashboard')
@section('title','Kirim Task Id Mobile JKN')

@section('konten')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid mt-3">

    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> Kirim Task Id Mobile JKN (BPJS)</h5>
            <button class="btn btn-warning btn-sm fw-bold text-dark" id="btn-kirim-manual">
                <i class="fas fa-paper-plane"></i> Kirim Antrean Sekarang
            </button>
        </div>

        <div class="card-body">
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Tanggal Awal</label>
                    <input type="date" id="tanggal1" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label>Tanggal Akhir</label>
                    <input type="date" id="tanggal2" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label>Cari</label>
                    <input type="text" id="keyword" class="form-control" placeholder="Cari No Booking / RM / Nama...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="btn-cari"><i class="fas fa-search"></i> Tampilkan</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-sm" id="table-antrean">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>No Booking</th>
                            <th>RM / Pasien</th>
                            <th>Poli / Dokter</th>
                            <th>Tanggal / Jam</th>
                            <th>Status Kirim</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data Ajax -->
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal Log Output -->
<div class="modal fade" id="modalLog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Output Eksekusi Pengiriman</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-dark text-light p-3" style="max-height: 400px; overflow-y: auto;">
                <pre id="log-output" style="white-space: pre-wrap; font-size: 12px; font-family: monospace;"></pre>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        loadData();

        $('#btn-cari').click(function() {
            loadData();
        });
        
        $('#keyword').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#table-antrean tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('#btn-kirim-manual').click(function() {
            Swal.fire({
                title: 'Kirim Antrean?',
                text: "Fitur ini akan mengeksekusi Command secara manual untuk mengirim antrean yang statusnya 'Belum'.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kirim!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Tunggu sebentar, sedang mengirim ke server BPJS.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "{{ route('bpjs.mjkn.antrean.kirim') }}",
                        type: "POST",
                        success: function(response) {
                            Swal.close();
                            if(response.success) {
                                $('#log-output').text(response.log);
                                var modal = new bootstrap.Modal(document.getElementById('modalLog'));
                                modal.show();
                                loadData();
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                        }
                    });
                }
            })
        });

        // Event listener untuk tombol kirim per baris (single)
        $(document).on('click', '.btn-kirim-single', function() {
            var kodebooking = $(this).data('booking');
            
            Swal.fire({
                title: 'Kirim Antrean Ini?',
                text: "Kirim data antrean dengan No Booking: " + kodebooking,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kirim!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Tunggu sebentar, sedang mengirim ke server BPJS.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "{{ route('bpjs.mjkn.antrean.kirim.single') }}",
                        type: "POST",
                        data: {
                            kodebooking: kodebooking
                        },
                        success: function(response) {
                            Swal.close();
                            if(response.success) {
                                $('#log-output').text(response.log);
                                var modal = new bootstrap.Modal(document.getElementById('modalLog'));
                                modal.show();
                                loadData();
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                        }
                    });
                }
            })
        });

        // Event listener untuk tombol kirim Task ID
        $(document).on('click', '.btn-kirim-task', function(e) {
            e.preventDefault();
            var kodebooking = $(this).data('booking');
            var taskid = $(this).data('task');
            
            Swal.fire({
                title: 'Kirim Task ID ' + taskid + '?',
                text: "Update waktu antrean untuk No Booking: " + kodebooking,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#17a2b8',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kirim Task!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengirim Task ID ' + taskid + ' ke server BPJS.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "{{ route('bpjs.mjkn.antrean.updateTask') }}",
                        type: "POST",
                        data: {
                            kodebooking: kodebooking,
                            taskid: taskid
                        },
                        success: function(response) {
                            Swal.close();
                            
                            // Menampilkan response aslinya
                            $('#log-output').text("Response BPJS:\n" + JSON.stringify(response, null, 2));
                            var modal = new bootstrap.Modal(document.getElementById('modalLog'));
                            modal.show();
                        },
                        error: function(xhr) {
                            Swal.close();
                            var err = 'Terjadi kesalahan sistem';
                            if(xhr.responseJSON) {
                                err = JSON.stringify(xhr.responseJSON, null, 2);
                            }
                            $('#log-output').text("Error BPJS:\n" + err);
                            var modal = new bootstrap.Modal(document.getElementById('modalLog'));
                            modal.show();
                        }
                    });
                }
            })
        });

        function loadData() {
            var tgl1 = $('#tanggal1').val();
            var tgl2 = $('#tanggal2').val();

            $('#table-antrean tbody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');

            $.ajax({
                url: "{{ route('bpjs.mjkn.antrean.data') }}",
                type: "GET",
                data: {
                    tanggal1: tgl1,
                    tanggal2: tgl2
                },
                success: function(response) {
                    var html = '';
                    if (response.success && response.data.length > 0) {
                        var no = 1;
                        $.each(response.data, function(i, v) {
                            var statusBadge = v.statuskirim == 'Sudah' ? 
                                '<span class="badge bg-success rounded-pill px-3 py-2 shadow-sm"><i class="fas fa-check-circle me-1"></i> Terkirim</span>' : 
                                '<span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm"><i class="fas fa-times-circle me-1"></i> Belum</span>';
                                
                            var btnAksi = '<div>';
                            if (v.statuskirim == 'Belum') {
                                btnAksi += '<button class="btn btn-sm btn-primary btn-kirim-single shadow-sm mb-1 w-100" data-booking="' + v.nobooking + '"><i class="fas fa-paper-plane me-1"></i> Kirim</button>';
                            } else {
                                btnAksi += '<button class="btn btn-sm btn-secondary btn-kirim-single shadow-sm mb-1 w-100" data-booking="' + v.nobooking + '"><i class="fas fa-redo-alt me-1"></i> Kirim Ulang</button>';
                            }

                            // Tambahkan tombol dropdown untuk Kirim Task ID
                            btnAksi += '<div class="dropdown mt-1">' +
                                '<button class="btn btn-sm btn-info dropdown-toggle w-100 text-white shadow-sm" type="button" data-bs-toggle="dropdown" data-toggle="dropdown" aria-expanded="false">' +
                                    '<i class="fas fa-tasks me-1"></i> Task ID' +
                                '</button>' +
                                '<ul class="dropdown-menu shadow border-0" style="border-radius: 10px; font-size: 13px;">' +
                                    '<li><a class="dropdown-item py-2 btn-kirim-task" href="#" data-booking="' + v.nobooking + '" data-task="1"><i class="fas fa-ticket-alt text-secondary me-2"></i>Task 1 (Tunggu Admisi)</a></li>' +
                                    '<li><a class="dropdown-item py-2 btn-kirim-task" href="#" data-booking="' + v.nobooking + '" data-task="2"><i class="fas fa-clipboard-check text-primary me-2"></i>Task 2 (Layan Admisi)</a></li>' +
                                    '<li><a class="dropdown-item py-2 btn-kirim-task" href="#" data-booking="' + v.nobooking + '" data-task="3"><i class="fas fa-chair text-warning me-2"></i>Task 3 (Tunggu Poli)</a></li>' +
                                    '<li><a class="dropdown-item py-2 btn-kirim-task" href="#" data-booking="' + v.nobooking + '" data-task="4"><i class="fas fa-stethoscope text-info me-2"></i>Task 4 (Layan Poli)</a></li>' +
                                    '<li><a class="dropdown-item py-2 btn-kirim-task" href="#" data-booking="' + v.nobooking + '" data-task="5"><i class="fas fa-door-open text-secondary me-2"></i>Task 5 (Akhir Layan Poli)</a></li>' +
                                    '<li><a class="dropdown-item py-2 btn-kirim-task" href="#" data-booking="' + v.nobooking + '" data-task="6"><i class="fas fa-pills text-danger me-2"></i>Task 6 (Tunggu Farmasi)</a></li>' +
                                    '<li><a class="dropdown-item py-2 btn-kirim-task" href="#" data-booking="' + v.nobooking + '" data-task="7"><i class="fas fa-box-open text-success me-2"></i>Task 7 (Selesai Obat)</a></li>' +
                                '</ul>' +
                            '</div></div>';
                                
                            html += '<tr>' +
                                '<td>' + no++ + '</td>' +
                                '<td>' + (v.nobooking || '-') + '</td>' +
                                '<td><b>' + (v.no_rkm_medis || '-') + '</b><br>' + (v.nm_pasien || '-') + '</td>' +
                                '<td><b>' + (v.nm_poli || '-') + '</b><br>' + (v.nm_dokter || '-') + '</td>' +
                                '<td>' + (v.tanggalperiksa || '-') + '<br>' + (v.jampraktek || '-') + '</td>' +
                                '<td>' + statusBadge + '</td>' +
                                '<td>' + btnAksi + '</td>' +
                                '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="6" class="text-center">Tidak ada data ditemukan</td></tr>';
                    }
                    $('#table-antrean tbody').html(html);
                },
                error: function() {
                    $('#table-antrean tbody').html('<tr><td colspan="6" class="text-center text-danger">Gagal mengambil data</td></tr>');
                }
            });
        }
    });
</script>

@endsection
