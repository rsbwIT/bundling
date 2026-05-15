@extends('layout.layoutDashboard')
@section('title', 'Data Akses User')

@section('konten')

<div class="card">

    <div class="card-body">


        <form method="GET"
              action="{{ route('ai.user') }}">

            <div class="row mb-3 align-items-center">


                <div class="col-md-4">

                    <input type="text"
                           name="cari"
                           class="form-control"
                           placeholder="Cari nama / username / password..."
                           value="{{ request('cari') }}">

                </div>



                <div class="col-md-3">

                    <button type="submit"
                            class="btn btn-primary">

                        Cari

                    </button>


                    <a href="{{ route('ai.user') }}"
                       class="btn btn-secondary">

                        Reset

                    </a>

                </div>



                <div class="col-md-5 text-end">

                    <b>
                        Total Data :
                        {{ count($data) }}
                    </b>

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


                            <td>

                                {{ $item->password_asli }}

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

                            <td colspan="6"
                                class="text-center">

                                Data tidak ditemukan

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


    </div>

</div>








<div class="modal fade"
     id="modalAkses"
     tabindex="-1">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">


            <div class="modal-header">

                <h5 class="modal-title">

                    Edit Akses User

                </h5>


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



                    <div id="isiAkses">

                    </div>


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








<script>

let modalAkses;



$(document).ready(function(){


    modalAkses =
        new bootstrap.Modal(
            document.getElementById(
                'modalAkses'
            )
        );


});








$(document).on(
    'click',
    '.lihatAkses',
    function(){


        let id =
            $(this)
            .data(
                'id'
            );



        $('#isiAkses')
        .html(
            'Loading...'
        );



        $('#cariAkses')
        .val('');



        $.get(

            '/ai/user/akses/' + id,

            function(response){


                if(
                    response.status
                ){


                    $('#id_user')
                    .val(
                        id
                    );



                    let data =
                        response.data;



                    let html =
                        '<div class="row">';




                    Object.keys(data)
                    .forEach(
                        function(key){


                            if(
                                key!='id_user'
                                &&
                                key!='password'
                            ){


                                let checked='';



                                if(
                                    data[key]=='true'
                                ){

                                    checked=
                                        'checked';

                                }




                                html += `

                                    <div class="col-md-3 mb-2 item-akses">

                                        <div class="form-check">


                                            <input
                                                type="checkbox"

                                                class="form-check-input"

                                                name="${key}"

                                                ${checked}
                                            >


                                            <label
                                                class="form-check-label">

                                                ${key}

                                            </label>


                                        </div>

                                    </div>

                                `;

                            }


                        }
                    );




                    html +=
                        '</div>';



                    $('#isiAkses')
                    .html(
                        html
                    );



                    modalAkses
                    .show();


                }


            }

        );


    }
);









$(document).on(
    'keyup',
    '#cariAkses',
    function(){


        let keyword =
            $(this)
            .val()
            .toLowerCase();




        $('.item-akses')
        .each(
            function(){


                let text =
                    $(this)
                    .text()
                    .toLowerCase();




                if(
                    text.indexOf(
                        keyword
                    ) > -1
                ){

                    $(this)
                    .show();

                }else{

                    $(this)
                    .hide();

                }


            }
        );


    }
);









$('#btnSimpanAkses')
.click(
    function(){


        $.ajax({

            url:
                '/ai/user/akses/update',

            type:
                'POST',


            data:
                $('#formAkses')
                .serialize(),




            success:
                function(response){


                    if(
                        response.status
                    ){

                        alert(
                            'Akses berhasil diupdate'
                        );


                        modalAkses
                        .hide();


                    }


                }

        });


    }
);

</script>


@endsection