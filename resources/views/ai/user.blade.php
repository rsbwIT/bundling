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
                        </td>

                        <td class="text-center">{{ $item->status ?? '-' }}</td>

                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-sm btn-warning lihatAkses"
                                    data-id="{{ $item->username_asli }}">
                                ⚙
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

{{-- SCRIPT --}}
<script>
let modalAkses;

$(function(){

    modalAkses = new bootstrap.Modal(document.getElementById('modalAkses'));

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
</script>

@endsection