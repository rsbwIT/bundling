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
                           placeholder="Cari nama / username / jabatan..."
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
                    <th>Jabatan</th>
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
                        <td>{{ $item->jabatan ?? '-' }}</td>
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
                                    class="btn btn-sm btn-primary btnSalinAkses"
                                    data-id="{{ $item->username_asli }}"
                                    data-nama="{{ $item->nama_petugas ?? $item->username_asli }}"
                                    title="Salin / Copy Akses dari user ini ke user lain">
                                <i class="fas fa-copy"></i> Salin Akses
                            </button>
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

{{-- MODAL SALIN / COPY AKSES --}}
<div class="modal fade" id="modalSalinAkses" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-copy me-2"></i>Salin Hak Akses User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="copy_from_user">
                <div class="alert alert-info py-2">
                    <strong>Sumber Hak Akses :</strong> <span id="label_from_user" class="fw-bold"></span>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Satu atau Beberapa User Tujuan :</label>
                    <div class="input-group mb-2">
                        <input type="text" id="cariUserTujuan" class="form-control" placeholder="Cari nama atau username user tujuan...">
                        <button type="button" class="btn btn-outline-secondary" id="btnCheckAllTujuan">Pilih Semua</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnUncheckAllTujuan">Batal Semua</button>
                    </div>
                    <div id="listUserTujuan" class="border rounded p-2" style="max-height: 280px; overflow-y: auto; background: #fafafa;">
                        <div class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat daftar user...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnProsesCopyAkses">
                    <i class="fas fa-check-circle me-1"></i> Terapkan Salin Akses
                </button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
let modalAkses;
let modalTambahUser;
let modalSalinAkses;

$(function(){

    modalAkses = new bootstrap.Modal(document.getElementById('modalAkses'));
    modalTambahUser = new bootstrap.Modal(document.getElementById('modalTambahUser'));
    modalSalinAkses = new bootstrap.Modal(document.getElementById('modalSalinAkses'));

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

// SALIN / COPY AKSES
let cacheListUser = null;
$(document).on('click', '.btnSalinAkses', function(){
    let fromUser = $(this).data('id');
    let fromNama = $(this).data('nama');

    $('#copy_from_user').val(fromUser);
    $('#label_from_user').html(fromNama + ' <span class="badge bg-secondary font-monospace">' + fromUser + '</span>');
    $('#cariUserTujuan').val('');
    $('#btnProsesCopyAkses').prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Terapkan Salin Akses');

    modalSalinAkses.show();

    if(cacheListUser) {
        renderListUserTujuan(fromUser);
    } else {
        $('#listUserTujuan').html('<div class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin"></i> Memuat daftar user...</div>');
        $.get('/ai/user/list', function(res){
            if(res.status && res.data){
                cacheListUser = res.data;
                renderListUserTujuan(fromUser);
            } else {
                $('#listUserTujuan').html('<div class="text-center py-3 text-danger">Gagal memuat daftar user</div>');
            }
        }).fail(function(){
            $('#listUserTujuan').html('<div class="text-center py-3 text-danger">Koneksi gagal memuat user</div>');
        });
    }
});

function renderListUserTujuan(excludeUser){
    let html = '';
    let count = 0;
    cacheListUser.forEach(function(u){
        if(u.username_asli === excludeUser) return; // jangan tampilkan user sumber itu sendiri
        count++;
        html += `
            <div class="form-check item-user-tujuan py-1 border-bottom">
                <input class="form-check-input cb-user-tujuan" type="checkbox" value="${u.username_asli}" id="cb_user_${count}">
                <label class="form-check-label w-100 cursor-pointer" for="cb_user_${count}">
                    <strong>${u.nama_petugas ?? u.username_asli}</strong>
                    <span class="text-muted small ms-1 font-monospace">(${u.username_asli})</span>
                </label>
            </div>
        `;
    });

    if(count === 0){
        html = '<div class="text-center py-3 text-muted">Tidak ada user tujuan lain yang tersedia.</div>';
    }
    $('#listUserTujuan').html(html);
}

// FILTER PENCARIAN USER TUJUAN
$(document).on('keyup', '#cariUserTujuan', function(){
    let k = $(this).val().toLowerCase();
    $('.item-user-tujuan').each(function(){
        let txt = $(this).text().toLowerCase();
        $(this).toggle(txt.includes(k));
    });
});

// CHECK / UNCHECK ALL TUJUAN
$('#btnCheckAllTujuan').click(function(){
    $('.item-user-tujuan:visible .cb-user-tujuan').prop('checked', true);
});
$('#btnUncheckAllTujuan').click(function(){
    $('.item-user-tujuan:visible .cb-user-tujuan').prop('checked', false);
});

// PROSES SUBMIT COPY AKSES
$('#btnProsesCopyAkses').click(function(){
    let fromUser = $('#copy_from_user').val();
    let toUsers = [];

    $('.cb-user-tujuan:checked').each(function(){
        toUsers.push($(this).val());
    });

    if(toUsers.length === 0){
        alert('Silakan pilih minimal satu user tujuan yang ingin disalin hak aksesnya!');
        return;
    }

    if(!confirm('Anda yakin ingin menyalin seluruh hak akses dari (' + fromUser + ') ke ' + toUsers.length + ' user yang dipilih? Hak akses user tujuan akan diperbarui sesuai user sumber ini.')){
        return;
    }

    let btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyalin Hak Akses...');

    $.ajax({
        url: '/ai/user/akses/copy',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            from_user: fromUser,
            to_users: toUsers
        },
        success: function(res){
            alert(res.message);
            if(res.status){
                modalSalinAkses.hide();
            } else {
                btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Terapkan Salin Akses');
            }
        },
        error: function(xhr){
            alert('Gagal menyalin hak akses: ' + xhr.responseText);
            btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Terapkan Salin Akses');
        }
    });
});
</script>

@endsection