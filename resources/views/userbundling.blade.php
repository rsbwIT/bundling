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
                        <td>{{ $item->username_asli }}</td>

                        <td class="text-center">
                            <span id="pwd_{{ $loop->iteration }}"
                                  data-password="{{ $item->password_asli }}">
                                ••••••••
                            </span>

                            <button type="button"
                                    class="btn btn-sm btn-info btnLihatPassword"
                                    data-target="pwd_{{ $loop->iteration }}">
                                👁
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-secondary btnEditPassword"
                                    data-username="{{ $item->username_asli }}"
                                    title="Ubah Password">
                                🔑
                            </button>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-success">Aktif</span>
                        </td>

                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-sm btn-success btnCopyAkses"
                                    data-id="{{ $item->username_asli }}"
                                    data-nama="{{ $item->nama_petugas ?? $item->username_asli }}">
                                📋 Copy
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- MODAL TAMBAH USER --}}
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Username / NIP</label>
                    <input type="text" id="add_username" class="form-control" placeholder="Masukkan NIP atau Username">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" id="add_password" class="form-control" placeholder="Masukkan Password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSimpanNewUser">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT PASSWORD --}}
<div class="modal fade" id="modalEditPassword" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" id="edit_pwd_username_label" class="form-control" readonly>
                    <input type="hidden" id="edit_pwd_username">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" id="edit_pwd_password" class="form-control" placeholder="Masukkan Password Baru">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanPassword">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL AKSES MENU --}}
<div class="modal fade" id="modalAkses" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Akses User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_user">

                <div class="d-flex gap-2 mb-3">
                    <input type="text" id="cariAkses" class="form-control" placeholder="Cari akses...">
                    <button type="button" id="checkAll" class="btn btn-success btn-sm">✔ All</button>
                    <button type="button" id="uncheckAll" class="btn btn-danger btn-sm">✖ All</button>
                </div>

                <div id="isiAkses" style="max-height: 500px; overflow-y: auto;">Loading...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanAkses">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL COPY AKSES --}}
<div class="modal fade" id="modalCopyAkses" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Copy Akses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Copy Dari:</strong> <span id="copyFromLabel">-</span>
                </div>
                <input type="hidden" id="copyFromUser">

                <div class="mb-3">
                    <input type="text" id="cariUserCopy" class="form-control" placeholder="Cari nama / username...">
                </div>

                <div class="d-flex gap-2 mb-3">
                    <button type="button" id="checkAllCopy" class="btn btn-success btn-sm">✔ All</button>
                    <button type="button" id="uncheckAllCopy" class="btn btn-danger btn-sm">✖ All</button>
                    <span class="ms-auto badge bg-primary py-2 px-3 align-self-center" id="countSelected">0 dipilih</span>
                </div>

                <div id="listUserCopy" style="max-height:300px; overflow-y:auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                    Loading...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnExecCopy">Proses Copy</button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
let modalAkses;
let modalCopyAkses;
let modalTambahUser;
let modalEditPassword;

$(function(){
    modalAkses = new bootstrap.Modal(document.getElementById('modalAkses'));
    modalCopyAkses = new bootstrap.Modal(document.getElementById('modalCopyAkses'));
    modalTambahUser = new bootstrap.Modal(document.getElementById('modalTambahUser'));
    modalEditPassword = new bootstrap.Modal(document.getElementById('modalEditPassword'));
});

// TAMBAH USER MODAL
$('#btnTambahUser').click(function(){
    $('#add_username').val('');
    $('#add_password').val('');
    modalTambahUser.show();
});

// SIMPAN USER BARU
$('#btnSimpanNewUser').click(function(){
    let username = $('#add_username').val();
    let password = $('#add_password').val();

    if(!username || !password){
        alert('Username dan password harus diisi!');
        return;
    }

    $.ajax({
        url: '/ai/user/store',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            username: username,
            password: password
        },
        success: function(res){
            alert(res.message);
            if(res.status){
                modalTambahUser.hide();
                location.reload();
            }
        },
        error: function(xhr){
            alert('Gagal menambahkan user baru: ' + xhr.responseText);
        }
    });
});

// EDIT PASSWORD MODAL
$(document).on('click', '.btnEditPassword', function(){
    let username = $(this).data('username');
    $('#edit_pwd_username').val(username);
    $('#edit_pwd_username_label').val(username);
    $('#edit_pwd_password').val('');
    modalEditPassword.show();
});

// SIMPAN PASSWORD BARU
$('#btnSimpanPassword').click(function(){
    let username = $('#edit_pwd_username').val();
    let password = $('#edit_pwd_password').val();

    if(!password){
        alert('Password baru wajib diisi!');
        return;
    }

    $.ajax({
        url: '/ai/user/update-password',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            username: username,
            password: password
        },
        success: function(res){
            alert(res.message);
            if(res.status){
                modalEditPassword.hide();
                location.reload();
            }
        },
        error: function(xhr){
            alert('Gagal mengganti password: ' + xhr.responseText);
        }
    });
});

// LIHAT PASSWORD
$(document).on('click','.btnLihatPassword',function(){
    let target = $(this).data('target');
    let el = $('#' + target);
    if(el.text().trim() === '••••••••'){
        el.text(el.data('password'));
    } else {
        el.text('••••••••');
    }
});

// BUKA AKSES MENU MODAL
$(document).on('click','.lihatAkses',function(){
    let id = $(this).data('id');
    $('#id_user').val(id);
    $('#isiAkses').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><span class="d-block mt-2">Memuat Hak Akses...</span></div>');

    $.get('/ai/user/akses/' + id, function(res){
        if(!res.status){
            alert(res.message);
            return;
        }

        let html = '';
        Object.keys(res.akses).forEach(function(kategori){
            html += `
                <div class="mb-4 category-section border p-3 rounded bg-light mb-3">
                    <h6 class="text-primary border-bottom pb-2 font-weight-bold" style="font-size:1.05rem;">
                        <i class="fas fa-folder-open mr-2 text-warning"></i> ${kategori}
                    </h6>
                    <div class="row">
            `;

            res.akses[kategori].forEach(function(menu){
                let checked = menu.checked === 'true' ? 'checked' : '';
                html += `
                    <div class="col-md-3 mb-2 item-akses">
                        <div class="form-check">
                            <input type="checkbox"
                                   class="form-check-input akses-item"
                                   data-key="${menu.id}"
                                   ${checked}>
                            <label class="form-check-label text-dark font-weight-normal" style="cursor:pointer; font-size:.85rem;">
                                ${menu.sub_menu}
                            </label>
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        });

        $('#isiAkses').html(html);
        modalAkses.show();
    });
});

// CARI AKSES MENU
$(document).on('keyup','#cariAkses',function(){
    let k = $(this).val().toLowerCase();
    $('.item-akses').each(function(){
        $(this).toggle($(this).text().toLowerCase().includes(k));
    });
    $('.category-section').each(function(){
        let visibleCount = $(this).find('.item-akses:visible').length;
        $(this).toggle(visibleCount > 0);
    });
});

// CENTANG SEMUA / BATAL SEMUA
$('#checkAll').click(function(){
    $('.akses-item:visible').prop('checked', true);
});
$('#uncheckAll').click(function(){
    $('.akses-item:visible').prop('checked', false);
});

// SIMPAN AKSES MENU
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
            alert("Gagal menyimpan: " + xhr.responseText);
        }
    });
});

// MODAL COPY AKSES OPEN
$(document).on('click', '.btnCopyAkses', function(){
    let username = $(this).data('id');
    let nama = $(this).data('nama');

    $('#copyFromUser').val(username);
    $('#copyFromLabel').text(nama + ' (' + username + ')');
    $('#listUserCopy').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><span class="d-block mt-2">Memuat daftar petugas...</span></div>');
    $('#countSelected').text('0 dipilih');

    $.get('/ai/user/list', function(res){
        if(!res.status){
            alert(res.message);
            return;
        }

        let html = '<div class="row">';
        res.data.forEach(function(u){
            if(u.username_asli === username) return; // skip diri sendiri
            html += `
                <div class="col-md-4 mb-2 item-user-copy">
                    <div class="form-check border p-2 rounded">
                        <input type="checkbox" class="form-check-input user-copy-item" value="${u.username_asli}">
                        <label class="form-check-label font-weight-normal ms-1 text-sm text-truncate" style="max-width:180px;">
                            ${u.nama_petugas ?? u.username_asli}
                        </label>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        $('#listUserCopy').html(html);
        modalCopyAkses.show();
    });
});

// HITUNG PILIHAN COPY
$(document).on('change', '.user-copy-item', function(){
    let count = $('.user-copy-item:checked').length;
    $('#countSelected').text(count + ' dipilih');
});

// SEARCH USER COPY
$(document).on('keyup', '#cariUserCopy', function(){
    let k = $(this).val().toLowerCase();
    $('.item-user-copy').each(function(){
        $(this).toggle($(this).text().toLowerCase().includes(k));
    });
});

// COPY ACTIONS
$('#checkAllCopy').click(function(){
    $('.user-copy-item:visible').prop('checked', true).trigger('change');
});
$('#uncheckAllCopy').click(function(){
    $('.user-copy-item').prop('checked', false).trigger('change');
});

// EXECUTE COPY AKSES
$('#btnExecCopy').click(function(){
    let from = $('#copyFromUser').val();
    let toUsers = [];
    $('.user-copy-item:checked').each(function(){
        toUsers.push($(this).val());
    });

    if(toUsers.length === 0){
        alert('Pilih minimal satu user tujuan!');
        return;
    }

    $.ajax({
        url: '/ai/user/akses/copy',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            from_user: from,
            to_users: toUsers
        },
        success: function(res){
            alert(res.message);
            if(res.status) modalCopyAkses.hide();
        },
        error: function(xhr){
            alert('Gagal menyalin hak akses: ' + xhr.responseText);
        }
    });
});
</script>

@endsection
