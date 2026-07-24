@extends('layout.layoutDashboard')
@section('title', 'Data Akses User')

@section('konten')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card">
    <div class="card-body">

        {{-- FILTER --}}
        <form method="GET" action="{{ route('ai.user') }}">
            <div class="row mb-3 align-items-center">

                <div class="col-md-4">
                    <input type="text"
                           name="cari"
                           class="form-control"
                           placeholder="Cari nama / username..."
                           value="{{ request('cari') }}">
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary">Cari</button>
                    <a href="{{ route('ai.user') }}" class="btn btn-secondary">Reset</a>
                </div>

                <div class="col-md-5 text-end">
                    <button type="button" class="btn btn-success me-2" id="btnTambahUser">Tambah User</button>
                    <b>Total Data : {{ count($data) }}</b>
                </div>

            </div>
        </form>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">

                <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Nama Petugas</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>

                <tbody>
                @foreach($data as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->nama_petugas ?? '-' }}</td>
                        <td>
                            @if($item->username_ada_spasi == 1)
                                <span class="badge bg-danger" title="Peringatan: Ada spasi tersembunyi di username ini!">⚠️ Spasi</span>
                            @endif
                            {{ $item->username_asli }}
                        </td>

                        <td class="text-center">
                            @if($item->password_ada_spasi == 1)
                                <span class="badge bg-danger mr-1" title="Peringatan: Ada spasi tersembunyi di password ini!">⚠️ Spasi</span>
                            @endif
                            <span id="pwd_{{ $loop->iteration }}"
                                  data-password="{{ $item->password_asli }}">
                                ••••••••
                            </span>

                            <button type="button"
                                    class="btn btn-sm btn-info btnLihatPassword"
                                    data-target="pwd_{{ $loop->iteration }}">
                                👁
                            </button>
                        </td>

                        <td class="text-center">{{ $item->status ?? '-' }}</td>

                        <td class="text-center">
                            @if($item->username_ada_spasi == 1 || $item->password_ada_spasi == 1)
                                <button type="button"
                                        class="btn btn-sm btn-danger btnPerbaikiSpasi"
                                        data-id="{{ $item->username_asli }}" title="Perbaiki Spasi Tersembunyi">
                                    <i class="fas fa-magic"></i> Perbaiki
                                </button>
                            @endif
                            <button type="button"
                                    class="btn btn-sm btn-warning lihatAkses"
                                    data-id="{{ $item->username_asli }}" title="Edit Akses">
                                ⚙
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-danger btnHapusUser"
                                    data-id="{{ $item->username_asli }}" title="Hapus User">
                                🗑️
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="modalAkses" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Akses User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="id_user">

                <div class="d-flex gap-2 mb-3">
                    <input type="text" id="cariAkses" class="form-control" placeholder="Cari akses...">
                    <button type="button" id="checkAll" class="btn btn-success btn-sm">✔ All</button>
                    <button type="button" id="uncheckAll" class="btn btn-danger btn-sm">✖ All</button>
                </div>

                <div id="isiAkses">Loading...</div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnSimpanAkses">
                    Simpan
                </button>
            </div>

        </div>
    </div>
</div>

{{-- MODAL TAMBAH USER --}}
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Cari Pegawai / Dokter</label>
                    <input type="text" id="search_pegawai" class="form-control mb-2" placeholder="Ketik nama atau NIP...">
                    <div id="list_pegawai" class="list-group" style="max-height: 200px; overflow-y: auto;">
                        <!-- list items -->
                    </div>
                    <input type="hidden" id="add_username">
                </div>
                <div class="mb-3">
                    <label class="form-label">Copy Hak Akses Dari (Opsional)</label>
                    <select id="copy_from" class="form-select">
                        <option value="">-- Pilih User (Kosongkan jika tidak disalin) --</option>
                        @foreach($data as $u)
                            <option value="{{ $u->username_asli }}">{{ $u->nama_petugas ?? $u->username_asli }} ({{ $u->username_asli }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSimpanNewUser">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
let modalAkses;
let modalTambahUser;

$(function(){

    modalAkses = new bootstrap.Modal(document.getElementById('modalAkses'));
    modalTambahUser = new bootstrap.Modal(document.getElementById('modalTambahUser'));

});


// TAMBAH USER MODAL
$('#btnTambahUser').click(function(){
    $('#add_username').val('');
    $('#search_pegawai').val('');
    $('#list_pegawai').html('');
    $('#copy_from').val('');
    $('#btnSimpanNewUser').prop('disabled', false).text('Simpan');
    modalTambahUser.show();
});

let searchTimeout;
$('#search_pegawai').keyup(function(){
    clearTimeout(searchTimeout);
    let kw = $(this).val();
    if(kw.length < 2) {
        $('#list_pegawai').html('');
        return;
    }
    
    $('#list_pegawai').html('<div class="text-center py-2"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>');
    
    searchTimeout = setTimeout(function() {
        $.get('/ai/user/search-pegawai?q=' + kw, function(res){
            let html = '';
            if (res.data.length === 0) {
                html = '<div class="text-center py-2 text-muted">Tidak ditemukan</div>';
            } else {
                res.data.forEach(item => {
                    html += `<button type="button" class="list-group-item list-group-item-action btn-pilih-pegawai" data-nip="${item.nip}">
                        <b>${item.nama}</b><br><small>${item.nip} - ${item.jenis}</small>
                    </button>`;
                });
            }
            $('#list_pegawai').html(html);
        });
    }, 500);
});

$(document).on('click', '.btn-pilih-pegawai', function(){
    $('.btn-pilih-pegawai').removeClass('active bg-primary text-white');
    $(this).addClass('active bg-primary text-white');
    $('#add_username').val($(this).data('nip'));
});

// SIMPAN USER BARU
$('#btnSimpanNewUser').click(function(){
    let username = $('#add_username').val();
    let copy_from = $('#copy_from').val();

    if(!username){
        alert('Pilih pegawai atau dokter terlebih dahulu!');
        return;
    }

    $(this).prop('disabled', true).text('Menyimpan...');

    $.ajax({
        url: '/ai/user/store',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            username: username,
            copy_from: copy_from
        },
        success: function(res){
            alert(res.message);
            if(res.status){
                modalTambahUser.hide();
                location.reload();
            } else {
                $('#btnSimpanNewUser').prop('disabled', false).text('Simpan');
            }
        },
        error: function(xhr){
            alert('Gagal menambahkan user baru: ' + xhr.responseText);
            $('#btnSimpanNewUser').prop('disabled', false).text('Simpan');
        }
    });
});


// LIHAT PASSWORD
$(document).on('click','.btnLihatPassword',function(){

    let target = $(this).data('target');
    let el = $('#' + target);

    if(el.text() === '••••••••'){
        el.text(el.data('password'));
    } else {
        el.text('••••••••');
    }
});


// OPEN MODAL
$(document).on('click','.lihatAkses',function(){

    let id = $(this).data('id');

    $('#id_user').val(id);
    $('#isiAkses').html('Loading...');

    $.get('/ai/user/akses/' + id, function(res){

        if(!res.status){
            alert(res.message);
            return;
        }

        let html = '<div class="row">';

        Object.keys(res.akses).forEach(function(key){

            let checked = res.akses[key] === 'true' ? 'checked' : '';

            html += `
                <div class="col-md-3 mb-2 item-akses">
                    <div class="form-check">

                        <input type="checkbox"
                               class="form-check-input akses-item"
                               data-key="${key}"
                               ${checked}>

                        <label class="form-check-label">${key}</label>

                    </div>
                </div>
            `;
        });

        html += '</div>';

        $('#isiAkses').html(html);
        modalAkses.show();
    });
});


// SEARCH AKSES
$(document).on('keyup','#cariAkses',function(){
    let k = $(this).val().toLowerCase();

    $('.item-akses').each(function(){
        $(this).toggle($(this).text().toLowerCase().includes(k));
    });
});


// CHECK ALL
$('#checkAll').click(function(){
    $('.akses-item').prop('checked', true);
});

$('#uncheckAll').click(function(){
    $('.akses-item').prop('checked', false);
});


// 🔥 SIMPAN (FIX FINAL TANPA JSON)
$('#btnSimpanAkses').click(function(){

    let formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        id_user: $('#id_user').val()
    };

    $('.akses-item').each(function(){
        let key = $(this).data('key');

        formData[`akses[${key}]`] = $(this).is(':checked') ? 'true' : 'false';
    });

    $.ajax({
        url:'/ai/user/akses/update',
        type:'POST',
        data: formData,
        success:function(res){
            alert(res.message);
            if(res.status) modalAkses.hide();
        },
        error:function(xhr){
            console.log(xhr.responseText);
            alert("ERROR:\n" + xhr.responseText);
        }
    });

});

// PERBAIKI SPASI
$(document).on('click', '.btnPerbaikiSpasi', function(){
    let username = $(this).data('id');
    if(!confirm("Yakin ingin membersihkan spasi tersembunyi pada user: " + username + "?")) return;

    $.ajax({
        url: '{{ url("/ai/user/perbaiki-spasi") }}',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id_user: username
        },
        success: function(res){
            if(res.status){
                alert(res.message);
                location.reload();
            } else {
                alert(res.message);
            }
        },
        error: function(xhr){
            alert("ERROR:\n" + xhr.responseText);
        }
    });
});

// HAPUS USER
$(document).on('click', '.btnHapusUser', function(){
    let username = $(this).data('id');
    if(!confirm("Yakin ingin menghapus user: " + username + "?")) return;

    $.ajax({
        url: '{{ url("/ai/user/destroy") }}',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            username: username
        },
        success: function(res){
            if(res.status){
                alert(res.message);
                location.reload();
            } else {
                alert(res.message);
            }
        },
        error: function(xhr){
            alert("ERROR:\n" + xhr.responseText);
        }
    });
});
</script>

@endsection