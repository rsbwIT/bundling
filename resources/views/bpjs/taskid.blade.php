@extends('layout.layoutDashboard')
@section('title','Task ID Mobile JKN')

@section('konten')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid mt-3">

    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list-alt"></i> Detail List Task ID Mobile JKN</h5>
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
                    <label>Cari (No. Rawat / RM / Nama / HP / NIK / Poli / Dokter)</label>
                    <input type="text" id="keyword" class="form-control" placeholder="Kata Kunci Pencarian...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" onclick="cariTaskID()" id="btnCari">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>

            <div class="alert alert-info" id="statusBox" style="display: none;">
                <span id="statusText">Mencari data...</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-sm" id="tableTaskID">
                    <thead class="bg-light">
                        <tr>
                            <th style="white-space: nowrap;">No.Rawat</th>
                            <th style="white-space: nowrap;">No.RM</th>
                            <th style="white-space: nowrap;">Nama Pasien</th>
                            <th style="white-space: nowrap;">No.HP</th>
                            <th style="white-space: nowrap;">No.Kartu</th>
                            <th style="white-space: nowrap;">NIK</th>
                            <th style="white-space: nowrap;">Tanggal</th>
                            <th style="white-space: nowrap;">Poliklinik</th>
                            <th style="white-space: nowrap;">Dokter</th>
                            <th style="white-space: nowrap;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyTaskID">
                        <tr>
                            <td colspan="13" class="text-center">Belum ada data. Silakan klik tombol Cari.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-2 text-muted">
                Total Baris: <span id="lblCount" class="font-weight-bold text-dark">0</span>
            </div>

        </div>
    </div>
</div>

<!-- Modal untuk Menampilkan Task ID BPJS -->
<div class="modal fade" id="modalTaskID" tabindex="-1" role="dialog" aria-labelledby="modalTaskIDLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTaskIDLabel"><i class="fas fa-list"></i> Detail Task ID BPJS</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="$('#modalTaskID').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="mb-3">
              <strong>Nama Pasien: </strong> <span id="modalPasienNama"></span> <br>
              <strong>No. Booking: </strong> <span id="modalKodeBooking"></span>
          </div>
          <div class="table-responsive">
              <table class="table table-bordered table-striped text-sm">
                  <thead class="bg-light">
                      <tr>
                          <th>Task ID</th>
                          <th>Task Name</th>
                          <th>Waktu RS</th>
                          <th>Waktu BPJS</th>
                      </tr>
                  </thead>
                  <tbody id="modalTbodyTasks">
                      <!-- Data tasks akan diisi lewat JS -->
                  </tbody>
              </table>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#modalTaskID').modal('hide')">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function cariTaskID() {
        let btnCari = $('#btnCari');
        let tbody = $('#tbodyTaskID');
        let statusBox = $('#statusBox');
        let statusText = $('#statusText');
        
        let tanggal1 = $('#tanggal1').val();
        let tanggal2 = $('#tanggal2').val();
        let keyword = $('#keyword').val();
        
        if (!tanggal1 || !tanggal2) {
            alert('Tanggal awal dan akhir harus diisi!');
            return;
        }

        btnCari.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mencari...');
        statusBox.show();
        statusText.html('Mengambil daftar pasien dari database lokal...');
        tbody.html('<tr><td colspan="13" class="text-center"><i class="fas fa-spinner fa-spin"></i> Sedang mengambil data...</td></tr>');
        $('#lblCount').text('0');

        $.ajax({
            url: "{{ route('mjkn.taskid.search') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                tanggal1: tanggal1,
                tanggal2: tanggal2,
                keyword: keyword
            },
            success: function(res) {
                if(res.success) {
                    let patients = res.data;
                    
                    if (patients.length === 0) {
                        tbody.html('<tr><td colspan="13" class="text-center">Tidak ada data ditemukan.</td></tr>');
                        statusBox.hide();
                        btnCari.prop('disabled', false).html('<i class="fas fa-search"></i> Cari');
                        return;
                    }

                    statusText.html('Ditemukan ' + patients.length + ' antrean.');
                    tbody.empty();
                    
                    $.each(patients, function(i, p) {
                        let html = '<tr>' +
                            '<td style="white-space: nowrap;">'+p.no_rawat+'</td>' +
                            '<td>'+p.no_rkm_medis+'</td>' +
                            '<td style="white-space: nowrap;">'+p.nm_pasien+'</td>' +
                            '<td>'+p.nohp+'</td>' +
                            '<td>'+p.nomorkartu+'</td>' +
                            '<td>'+p.nik+'</td>' +
                            '<td style="white-space: nowrap;">'+p.tanggalperiksa+'</td>' +
                            '<td style="white-space: nowrap;">'+p.nm_poli+'</td>' +
                            '<td style="white-space: nowrap;">'+p.nm_dokter+'</td>' +
                            '<td class="text-center">' +
                                '<button class="btn btn-sm btn-info" onclick="lihatTask(\''+p.nobooking+'\', \''+p.nm_pasien+'\')">' +
                                    '<i class="fas fa-eye"></i> Lihat Task' +
                                '</button>' +
                            '</td>' +
                            '</tr>';
                            
                        tbody.append(html);
                    });

                    statusText.html('Selesai memuat ' + patients.length + ' antrean.');
                    setTimeout(function() { statusBox.hide(); }, 3000);
                    btnCari.prop('disabled', false).html('<i class="fas fa-search"></i> Cari');
                    $('#lblCount').text(patients.length);

                } else {
                    tbody.html('<tr><td colspan="13" class="text-center text-danger">Gagal memuat data</td></tr>');
                    statusBox.hide();
                    btnCari.prop('disabled', false).html('<i class="fas fa-search"></i> Cari');
                }
            },
            error: function(err) {
                console.error(err);
                tbody.html('<tr><td colspan="13" class="text-center text-danger">Terjadi kesalahan sistem</td></tr>');
                statusBox.hide();
                btnCari.prop('disabled', false).html('<i class="fas fa-search"></i> Cari');
            }
        });
    }

    function lihatTask(kodebooking, namaPasien) {
        $('#modalPasienNama').text(namaPasien);
        $('#modalKodeBooking').text(kodebooking);
        
        let modalTbody = $('#modalTbodyTasks');
        modalTbody.html('<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> Mengambil data dari BPJS...</td></tr>');
        
        $('#modalTaskID').modal('show');

        $.ajax({
            url: "{{ route('mjkn.taskid.getlisttask') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                kodebooking: kodebooking
            },
            success: function(res) {
                if (res.success && res.data && res.data.length > 0) {
                    let tasks = res.data;
                    modalTbody.empty();
                    
                    $.each(tasks, function(i, task) {
                        let html = '<tr>' +
                            '<td class="text-center"><b>'+(task.taskid || '')+'</b></td>' +
                            '<td>'+(task.taskname || '')+'</td>' +
                            '<td>'+(task.wakturs || '')+'</td>' +
                            '<td>'+(task.waktu || '')+'</td>' +
                            '</tr>';
                        modalTbody.append(html);
                    });
                } else {
                    modalTbody.html('<tr><td colspan="4" class="text-center text-danger">Task ID tidak ditemukan ('+(res.message || 'Data kosong')+')</td></tr>');
                }
            },
            error: function(err) {
                modalTbody.html('<tr><td colspan="4" class="text-center text-danger">Gagal menghubungi server BPJS</td></tr>');
            }
        });
    }
</script>

@endsection
