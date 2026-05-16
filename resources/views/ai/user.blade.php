@extends('layout.layoutDashboard')
@section('title', 'Data Akses User')

@section('konten')

<div class="card">
    <div class="card-body">

        <form method="GET" action="{{ route('ai.user') }}">
            @csrf
            <div class="row mb-3 align-items-center">
                <div class="col-md-4">
                    <input type="text"
                           name="cari"
                           class="form-control"
                           placeholder="Cari nama / username / password..."
                           value="{{ request('cari') }}">
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <a href="{{ route('ai.user') }}" class="btn btn-secondary">Reset</a>
                </div>

                <div class="col-md-5 text-end">
                    <b>Total Data : {{ count($data) }}</b>
                </div>
            </div>
        </form>

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
                    @forelse($data as $item)
                    <tr>

                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item->nama_petugas ?? '-' }}
                        </td>

                        <td>
                            {{ $item->username_asli }}
                        </td>

                        <td class="text-center">

                            <span id="pwd_{{ $loop->iteration }}"
                                  data-password="{{ $item->password_asli }}">
                                ••••••••
                            </span>

                            <button type="button"
                                    class="btn btn-sm btn-info btnLihatPassword"
                                    data-target="pwd_{{ $loop->iteration }}">
                                <i class="fa fa-eye"></i>
                            </button>

                        </td>

                        <td class="text-center">
                            {{ $item->status ?? '-' }}
                        </td>

                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-sm btn-warning lihatAkses"
                                    data-id="{{ $item->username_asli }}">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Data tidak ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>



<div class="modal fade" id="modalAkses" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Akses User</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">
                <form id="formAkses">
                    @csrf

                    <input type="hidden"
                           id="id_user"
                           name="id_user">

                    <div class="mb-3">
                        <input type="text"
                               id="cariAkses"
                               class="form-control"
                               placeholder="Cari akses...">
                    </div>

                    <div id="isiAkses"></div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary"
                        id="btnSimpanAkses">
                    Simpan
                </button>
            </div>

        </div>
    </div>
</div>



<div class="modal fade" id="modalPassword" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Verifikasi Password Admin
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <input type="password"
                       id="verifikasiPassword"
                       class="form-control"
                       placeholder="Masukkan password">

                <input type="hidden"
                       id="targetPassword">

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary"
                        id="btnVerifikasiPassword">
                    Lihat Password
                </button>
            </div>

        </div>
    </div>
</div>



<script>

let modalAkses, modalPassword;

$(function(){

    modalAkses = new bootstrap.Modal(
        document.getElementById('modalAkses')
    );

    modalPassword = new bootstrap.Modal(
        document.getElementById('modalPassword')
    );

});



$(document).on('click','.btnLihatPassword',function(){

    $('#targetPassword').val(
        $(this).data('target')
    );

    $('#verifikasiPassword').val('');

    modalPassword.show();

});



$('#btnVerifikasiPassword').click(function(){

    $.ajax({

        url:'/ai/user/verifikasi-password',

        type:'POST',

        data:{

            _token:$('input[name=_token]').first().val(),

            password:$('#verifikasiPassword').val()

        },


        success:function(response){

            if(!response.status){

                alert(
                    response.message
                );

                return;

            }


            let target =
                $('#targetPassword').val();


            let ele =
                $('#' + target);


            clearTimeout(
                ele.data('timer')
            );


            ele.text(
                ele.data('password')
            );


            modalPassword.hide();


            let timer =
                setTimeout(function(){

                    ele.text(
                        '••••••••'
                    );

                },10000);


            ele.data(
                'timer',
                timer
            );

        }

    });

});



$(document).on('click','.lihatAkses',function(){

    let id =
        $(this).data('id');


    $('#isiAkses').html(
        'Loading...'
    );


    $('#cariAkses').val('');


    $.get('/ai/user/akses/' + id,function(response){

        if(!response.status){
            return;
        }


        $('#id_user').val(id);


        let html =
            '<div class="row">';


        Object.keys(response.data).forEach(function(key){

            if(
                key != 'id_user' &&
                key != 'password'
            ){

                let checked =
                    response.data[key] == 'true'
                    ? 'checked'
                    : '';


                html += `
                    <div class="col-md-3 mb-2 item-akses">
                        <div class="form-check">

                            <input
                                type="checkbox"
                                class="form-check-input"
                                name="${key}"
                                ${checked}
                            >

                            <label class="form-check-label">
                                ${key}
                            </label>

                        </div>
                    </div>
                `;

            }

        });


        html += '</div>';


        $('#isiAkses').html(html);


        modalAkses.show();

    });

});



$(document).on('keyup','#cariAkses',function(){

    let keyword =
        $(this)
        .val()
        .toLowerCase();


    $('.item-akses').each(function(){

        $(this).toggle(

            $(this)
            .text()
            .toLowerCase()
            .indexOf(keyword) > -1

        );

    });

});



$('#btnSimpanAkses').click(function(){

    $.ajax({

        url:'/ai/user/akses/update',

        type:'POST',

        data:$('#formAkses').serialize(),


        success:function(response){

            if(response.status){

                alert(
                    'Akses berhasil diupdate'
                );

                modalAkses.hide();

            }

        }

    });

});

</script>

@endsection