@extends('layout.layoutDashboard')

@section('title', 'Seting Hak Akses Menu Bundling')

@section('content')
    <!-- Content Header -->
    <!-- <div class="content-header px-0">
        <div class="container-fluid px-0">
            <div class="row mb-2 ml-1">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Hak Akses Menu Bundling</h1>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Main content -->
    <section class="content px-0">
        <div class="container-fluid px-0">
            <div class="card card-primary card-outline shadow-sm m-0 border-0 rounded-0">
                <div class="card-header border-0">
                    <h3 class="card-title"><i class="fas fa-users-cog"></i> Daftar Pengguna</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <input type="text" id="searchUser" class="form-control float-right" placeholder="Cari Pegawai...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive" style="max-height: calc(100vh - 180px);">
                    <table class="table table-hover table-striped table-head-fixed text-nowrap w-100">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="20%">NIK</th>

                                <th>Nama Pegawai / Dokter</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            @foreach($users as $index => $u)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $u->username_asli }}</td>
                                <td>{{ $u->nama_petugas }}</td>
                                <td class="text-center">
                                    @if(session('user') && session('user')->nik == '01091999')
                                    <button class="btn btn-sm btn-info btn-atur-akses" 
                                            data-nik="{{ $u->username_asli }}" 
                                            data-nama="{{ $u->nama_petugas }}">
                                        <i class="fas fa-key"></i> Atur Akses
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

<!-- Modal Atur Akses -->
<div class="modal fade" id="modalAkses" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formAkses">
                @csrf
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Atur Akses Menu: <span id="modalNamaPegawai" class="font-weight-bold"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="username" id="inputUsername">
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <button type="button" class="btn btn-sm btn-outline-success" id="btnCheckAll">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="btnUncheckAll">Hapus Semua</button>
                        </div>
                    </div>

                    <div class="row" style="max-height: 400px; overflow-y: auto;">
                        @foreach($menus->groupBy('parent_id') as $parentId => $group)
                            <div class="col-md-6 mb-4">
                                <div class="card bg-light">
                                    <div class="card-header py-2 font-weight-bold">
                                        @php
                                            $parentName = "Menu Utama";
                                            if($parentId) {
                                                $parent = $menus->where('id', $parentId)->first();
                                                if($parent) $parentName = $parent->nama_menu;
                                            }
                                        @endphp
                                        <i class="fas fa-folder-open text-warning"></i> {{ $parentName }}
                                    </div>
                                    <div class="card-body py-2">
                                        @foreach($group as $m)
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox" class="custom-control-input menu-checkbox" 
                                                   id="menu_{{ $m->id }}" 
                                                   name="menu_ids[]" 
                                                   value="{{ $m->id }}">
                                            <label class="custom-control-label font-weight-normal" for="menu_{{ $m->id }}">
                                                <i class="{{ $m->icon ?? 'far fa-circle' }} mr-1"></i> {{ $m->nama_menu }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanAkses"><i class="fas fa-save"></i> Simpan Akses</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Fungsi Pencarian
    $("#searchUser").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#userTableBody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Check/Uncheck All
    $("#btnCheckAll").click(function(){
        $(".menu-checkbox").prop('checked', true);
    });
    $("#btnUncheckAll").click(function(){
        $(".menu-checkbox").prop('checked', false);
    });

    // Buka Modal Atur Akses
    $('.btn-atur-akses').click(function() {
        let nik = $(this).data('nik');
        let nama = $(this).data('nama');
        
        $('#modalNamaPegawai').text(nama + ' (' + nik + ')');
        $('#inputUsername').val(nik);
        
        // Reset Checkbox
        $('.menu-checkbox').prop('checked', false);
        
        // Loading state
        Swal.fire({
            title: 'Memuat data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        // Ambil hak akses user dari database
        $.ajax({
            url: "{{ url('/akses-bundling') }}/" + nik,
            type: "GET",
            success: function(res) {
                Swal.close();
                if(res.status) {
                    // Centang menu yang statusnya 'true'
                    $.each(res.akses, function(menu_id, data) {
                        if(data.status === 'true') {
                            $('#menu_' + menu_id).prop('checked', true);
                        }
                    });
                    $('#modalAkses').modal('show');
                } else {
                    Swal.fire('Error', 'Gagal memuat hak akses.', 'error');
                }
            },
            error: function(err) {
                Swal.close();
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
            }
        });
    });

    // Submit Form Akses
    $('#formAkses').submit(function(e) {
        e.preventDefault();
        
        let formData = $(this).serialize();
        
        // Disable button
        $('#btnSimpanAkses').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
        
        $.ajax({
            url: "{{ url('/akses-bundling/update') }}",
            type: "POST",
            data: formData,
            success: function(res) {
                $('#btnSimpanAkses').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Akses');
                
                if(res.status) {
                    $('#modalAkses').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(err) {
                $('#btnSimpanAkses').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Akses');
                Swal.fire('Error', 'Terjadi kesalahan saat menyimpan.', 'error');
            }
        });
    });
});
</script>
@endpush
