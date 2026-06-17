@extends('layout.layoutDashboard')
@section('title','Ambil Antrean M-JKN')

@section('konten')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid">

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Ambil Antrean Mobile JKN</h5>
    </div>

    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6">

                <label>Nomor Kartu BPJS</label>

                <div class="input-group">

                    <input
                        type="text"
                        id="nomorkartu"
                        class="form-control"
                        placeholder="Masukkan Nomor Kartu BPJS">

                    <button
                        type="button"
                        class="btn btn-info"
                        onclick="cariPasien()">

                        Cari

                    </button>

                </div>

            </div>
        </div>

        <div class="row">

            <div class="col-md-6">

                <label>Nama Pasien</label>
                <input
                    type="text"
                    id="nama"
                    class="form-control mb-2"
                    readonly>

                <label>NIK</label>
                <input
                    type="text"
                    id="nik"
                    class="form-control mb-2"
                    readonly>

                <label>No RM</label>
                <input
                    type="text"
                    id="norm"
                    class="form-control mb-2"
                    readonly>

                <label>No HP</label>
                <input
                    type="text"
                    id="nohp"
                    class="form-control mb-2">

            </div>

            <div class="col-md-6">

                <label>Nomor Surat Kontrol / Referensi</label>
                <input
                    type="text"
                    id="nomorreferensi"
                    class="form-control mb-2">

                <label>Kode Poli BPJS</label>
                <input
                    type="text"
                    id="kodepoli"
                    class="form-control mb-2"
                    readonly>

                <label>Kode Dokter BPJS</label>
                <input
                    type="text"
                    id="kodedokter"
                    class="form-control mb-2"
                    readonly>

                <label>Kode Booking</label>
                <input
                    type="text"
                    id="kodebooking"
                    class="form-control mb-2"
                    >

            </div>

        </div>

        <div class="row">

            <div class="col-md-4">

                <label>Tanggal Periksa</label>

                <input
                    type="date"
                    id="tanggalperiksa"
                    class="form-control">

            </div>

            <div class="col-md-4">

                <label>Jam Praktek</label>

                <input
                    type="text"
                    id="jampraktek"
                    class="form-control">

            </div>

            <div class="col-md-4">

                <label>Jenis Kunjungan</label>

                <select
                    id="jeniskunjungan"
                    class="form-control">

                    <option value="1">
                        1 - Rujukan FKTP
                    </option>

                    <option value="2">
                        2 - Rujukan Internal
                    </option>

                    <option value="3" selected>
                        3 - Kontrol
                    </option>

                    <option value="4">
                        4 - Rujukan Antar RS
                    </option>

                </select>

            </div>

        </div>

        <div class="mt-3">

            <button
                type="button"
                class="btn btn-success"
                onclick="ambilAntrean()">

                Ambil Antrean

            </button>

            <button
                type="button"
                class="btn btn-primary"
                onclick="checkinAntrean()">

                Checkin

            </button>

            <button
                type="button"
                class="btn btn-warning"
                onclick="sisaAntrean()">

                Sisa Antrean

            </button>

            <button
                type="button"
                class="btn btn-danger"
                onclick="batalAntrean()">

                Batal Antrean

            </button>

        </div>

        <hr>

        <textarea
            id="hasil"
            rows="15"
            class="form-control"></textarea>

    </div>
</div>

</div>

<script>

const csrf =
    document.querySelector(
        'meta[name="csrf-token"]'
    ).content;

document.addEventListener('DOMContentLoaded', () => {

    let today = new Date();

    let yyyy = today.getFullYear();

    let mm =
        String(today.getMonth()+1)
        .padStart(2,'0');

    let dd =
        String(today.getDate())
        .padStart(2,'0');

    document.getElementById(
        'tanggalperiksa'
    ).value =
    `${yyyy}-${mm}-${dd}`;

});

async function cariPasien()
{
    let nomorkartu =
        document.getElementById(
            'nomorkartu'
        ).value;

    if(!nomorkartu){

        alert('Nomor kartu kosong');

        return;
    }

    try {

        const response =
            await fetch('/mjkn/caripasien',{

                method:'POST',

                headers:{
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN':csrf
                },

                body:JSON.stringify({
                    nomorkartu
                })
            });

        const data =
            await response.json();

        document.getElementById('hasil').value =
            JSON.stringify(data,null,2);

        if(!data.status){

            alert(data.message);

            return;
        }

        nama.value = data.nama || '';
        nik.value = data.nik || '';
        norm.value = data.norm || '';
        nohp.value = data.nohp || '';

        nomorreferensi.value =
            data.nomorsurat || '';

        kodepoli.value =
            data.kodepoli || '';

        kodedokter.value =
            data.kodedokter || '';

        tanggalperiksa.value =
            data.tanggal || '';

        jampraktek.value =
            data.jampraktek || '';

    }
    catch(err){

        hasil.value = err.message;

        alert(err.message);
    }
}

async function ambilAntrean()
{
    try {

        if(
            !nomorkartu.value ||
            !nik.value ||
            !norm.value ||
            !kodepoli.value ||
            !kodedokter.value ||
            !nomorreferensi.value
        ){
            alert(
                'Lengkapi data terlebih dahulu'
            );
            return;
        }

        const response =
            await fetch('/mjkn/ambilantrean',{

                method:'POST',

                headers:{
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN':csrf
                },

                body:JSON.stringify({

                    nomorkartu:
                        nomorkartu.value,

                    nik:
                        nik.value,

                    nohp:
                        nohp.value,

                    norm:
                        norm.value,

                    kodepoli:
                        kodepoli.value,

                    tanggalperiksa:
                        tanggalperiksa.value,

                    kodedokter:
                        kodedokter.value,

                    jampraktek:
                        jampraktek.value,

                    nomorreferensi:
                        nomorreferensi.value,

                    jeniskunjungan:
                        jeniskunjungan.value
                })
            });

        const data =
            await response.json();

        hasil.value =
            JSON.stringify(
                data,
                null,
                2
            );

        if(
            data.metadata &&
            data.metadata.code == 200
        ){

            let kode =
                data.response.kodebooking || '';

            document.getElementById('kodebooking').value = kode;

            alert(
                'Berhasil Ambil Antrean\nKode Booking : ' +
                kode
            );
        }
        else{

            alert(
                data.metadata?.message ||
                'Gagal Ambil Antrean'
            );
        }

    }
    catch(err){

        hasil.value = err.message;

        alert(err.message);
    }
}

async function checkinAntrean()
{
    if(!kodebooking.value){

        alert('Kode booking kosong');

        return;
    }

    try {

        const response =
            await fetch('/mjkn/checkin',{

                method:'POST',

                headers:{
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN':csrf
                },

                body:JSON.stringify({

                    kodebooking:
                        kodebooking.value

                })
            });

        const data =
            await response.json();

        hasil.value =
            JSON.stringify(
                data,
                null,
                2
            );

        alert(
            data.metadata?.message ||
            'Checkin berhasil'
        );

    }
    catch(err){

        alert(err.message);

    }
}

async function sisaAntrean()
{
    if(!kodebooking.value){

        alert('Kode booking kosong');

        return;
    }

    try {

        const response =
            await fetch('/mjkn/sisa',{

                method:'POST',

                headers:{
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN':csrf
                },

                body:JSON.stringify({

                    kodebooking:
                        kodebooking.value

                })
            });

        const data =
            await response.json();

        hasil.value =
            JSON.stringify(
                data,
                null,
                2
            );

        if(
            data.metadata &&
            data.metadata.code == 200
        ){

            alert(
                'Sisa Antrean : ' +
                data.response.sisaantrean +
                '\nAntrean Dipanggil : ' +
                data.response.antreanpanggil
            );
        }

    }
    catch(err){

        alert(err.message);

    }
}

async function batalAntrean()
{
    if(!kodebooking.value){

        alert('Kode booking kosong');

        return;
    }

    let alasan =
        prompt(
            'Masukkan alasan pembatalan'
        );

    if(!alasan){

        return;
    }

    try {

        const response =
            await fetch('/mjkn/batal',{

                method:'POST',

                headers:{
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN':csrf
                },

                body:JSON.stringify({

                    kodebooking:
                        kodebooking.value,

                    keterangan:
                        alasan

                })
            });

        const data =
            await response.json();

        hasil.value =
            JSON.stringify(
                data,
                null,
                2
            );

        alert(
            data.metadata?.message ||
            'Selesai'
        );

    }
    catch(err){

        alert(err.message);

    }
}

</script>

@if(isset($hasil))

<div class="alert alert-success">

    <h5>Antrean Berhasil Diambil</h5>

    Nomor Antrean :
    {{ $hasil['nomorantrean'] }}

    <br>

    Kode Booking :
    {{ $hasil['kodebooking'] }}

    <br>

    Poli :
    {{ $hasil['namapoli'] }}

    <br>

    Dokter :
    {{ $hasil['namadokter'] }}

</div>

@endif

@endsection
