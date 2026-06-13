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
                        <input type="text"
                               id="nomorkartu"
                               class="form-control"
                               placeholder="Masukkan Nomor Kartu BPJS">

                        <button type="button"
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
                    <input type="text"
                           id="nama"
                           class="form-control mb-2"
                           readonly>

                    <label>NIK</label>
                    <input type="text"
                           id="nik"
                           class="form-control mb-2"
                           readonly>

                    <label>No RM</label>
                    <input type="text"
                           id="norm"
                           class="form-control mb-2"
                           readonly>

                    <label>No HP</label>
                    <input type="text"
                           id="nohp"
                           class="form-control mb-2">

                </div>

                <div class="col-md-6">

                    <label>Nomor Surat Kontrol</label>
                    <input type="text"
                           id="nomorreferensi"
                           class="form-control mb-2">

                    <label>Kode Poli BPJS</label>
                    <input type="text"
                           id="kodepoli"
                           class="form-control mb-2"
                           readonly>

                    <label>Kode Dokter BPJS</label>
                    <input type="text"
                           id="kodedokter"
                           class="form-control mb-2"
                           readonly>
                    <label>Kode Booking</label>
                    <input type="text"
                        id="kodebooking"
                        class="form-control mb-2">
                        

                </div>

            </div>

            <div class="row">

                <div class="col-md-4">
                    <label>Tanggal Periksa</label>
                    <input type="date"
                           id="tanggalperiksa"
                           class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Jam Praktek</label>
                    <input type="text"
                           id="jampraktek"
                           class="form-control"
                           placeholder="08:00-12:00">
                </div>

                <div class="col-md-4">
                    <label>Jenis Kunjungan</label>
                    <select id="jeniskunjungan"
                            class="form-control">
                        <option value="1">1 - Rujukan FKTP</option>
                        <option value="2">2 - Rujukan Internal</option>
                        <option value="3" selected>3 - Kontrol</option>
                        <option value="4">4 - Rujukan Antar RS</option>
                    </select>
                </div>

            </div>

            <div class="mt-3">

                <button type="button"
                        class="btn btn-success"
                        onclick="ambilAntrean()">
                    Ambil Antrean
                </button>

                <button type="button"
                        class="btn btn-primary"
                        onclick="checkinAntrean()">
                    Checkin
                </button>

                <button type="button"
                        class="btn btn-warning"
                        onclick="sisaAntrean()">
                    Sisa Antrean
                </button>

                <button type="button"
                        class="btn btn-danger"
                        onclick="batalAntrean()">
                    Batal Antrean
                </button>

            </div>

            <hr>

            <textarea id="hasil"
                      rows="15"
                      class="form-control"></textarea>

        </div>
    </div>

</div>

<script>

const csrf = document.querySelector('meta[name="csrf-token"]').content;

async function cariPasien()
{
    let nomorkartu = document.getElementById('nomorkartu').value;

    if(!nomorkartu){
        alert('Nomor kartu kosong');
        return;
    }

    try {

        const response = await fetch('/mjkn/caripasien', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                nomorkartu: nomorkartu
            })
        });

        const text = await response.text();

        console.log(text);

        document.getElementById('hasil').value = text;

        const data = JSON.parse(text);

        if(!data.status){
            alert(data.message);
            return;
        }

        document.getElementById('nama').value = data.nama || '';
        document.getElementById('nik').value = data.nik || '';
        document.getElementById('norm').value = data.norm || '';
        document.getElementById('nohp').value = data.nohp || '';
        document.getElementById('nomorreferensi').value = data.nomorsurat || '';
        document.getElementById('kodepoli').value = data.kodepoli || '';
        document.getElementById('kodedokter').value = data.kodedokter || '';
        document.getElementById('tanggalperiksa').value = data.tanggal || '';
        document.getElementById('jampraktek').value =
             data.jampraktek || '';

    } catch(err) {

        console.error(err);

        document.getElementById('hasil').value =
            'ERROR : ' + err.message;

        alert(err.message);

    }
}

async function ambilAntrean()
{
    try {

        const response = await fetch('/mjkn/ambilantrean', {

            method:'POST',

            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN':csrf
            },

            body:JSON.stringify({

                nomorkartu: document.getElementById('nomorkartu').value,
                nik: document.getElementById('nik').value,
                nohp: document.getElementById('nohp').value,
                norm: document.getElementById('norm').value,
                kodepoli: document.getElementById('kodepoli').value,
                tanggalperiksa: document.getElementById('tanggalperiksa').value,
                kodedokter: document.getElementById('kodedokter').value,
                jampraktek: document.getElementById('jampraktek').value,
                jeniskunjungan: document.getElementById('jeniskunjungan').value,
                nomorreferensi: document.getElementById('nomorreferensi').value

            })

        });

        const data = await response.json();

        document.getElementById('hasil').value =
            JSON.stringify(data,null,2);

        console.log(data);

        let kodebooking =
            data?.response?.kodebooking ||
            data?.response?.bookingcode ||
            data?.response?.kode_booking ||
            data?.response?.booking_code ||
            '';

        if(kodebooking){

            document.getElementById('kodebooking').value =
                kodebooking;

            alert(
                'Berhasil Ambil Antrean\n' +
                'Kode Booking : ' +
                kodebooking
            );
        }else{

            alert(
                'Kode booking tidak ditemukan pada response.\n' +
                'Lihat textarea HASIL.'
            );
        }

    } catch(err){

        console.error(err);

        document.getElementById('hasil').value =
            'ERROR : ' + err.message;

        alert(err.message);

    }

} // <-- TAMBAHKAN INI

    async function checkinAntrean()
{
    const response = await fetch('/mjkn/checkin',{

        method:'POST',

        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':csrf
        },

        body:JSON.stringify({

            kodebooking:
                document.getElementById('kodebooking').value,

            waktu: Date.now()

        })
    });

    const data = await response.json();

    document.getElementById('hasil').value =
        JSON.stringify(data,null,2);
}

async function sisaAntrean()
{
    const response = await fetch('/mjkn/sisa',{

        method:'POST',

        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':csrf
        },

        body:JSON.stringify({

            kodebooking:
                document.getElementById('kodebooking').value

        })
    });

    const data = await response.json();

    document.getElementById('hasil').value =
        JSON.stringify(data,null,2);

    if(data.metadata?.code == 200){

        alert(
            'Sisa Antrean : ' +
            data.response.sisaantrean +
            '\nDipanggil : ' +
            data.response.antreanpanggil
        );
    }
}

// async function batalAntrean()
//     {
//         let alasan = prompt('Alasan pembatalan');

//         if(!alasan){
//             return;
//         }

//         const response = await fetch('/mjkn/batal',{

//             method:'POST',

//             headers:{
//                 'Content-Type':'application/json',
//                 'Accept':'application/json',
//                 'X-CSRF-TOKEN':csrf
//             },

//             body:JSON.stringify({

//                 kodebooking:
//                     document.getElementById('kodebooking').value,

//                 keterangan: alasan

//             })
//         });

//         const data = await response.json();

//         document.getElementById('hasil').value =
//             JSON.stringify(data,null,2);
//     }

async function batalAntrean()
{
    let kodebooking =
        document.getElementById('kodebooking').value;

    if(!kodebooking){
        alert('Kode Booking kosong');
        return;
    }

    let alasan = prompt('Alasan pembatalan');

    if(!alasan){
        return;
    }

    try {

        const response = await fetch('/mjkn/batal',{

            method:'POST',

            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN':csrf
            },

            body:JSON.stringify({

                kodebooking: kodebooking,
                keterangan: alasan

            })
        });

        const data = await response.json();

        document.getElementById('hasil').value =
            JSON.stringify(data,null,2);

        alert(data.metadata?.message || 'Selesai');

    } catch(err){

        alert(err.message);

    }
}

</script>

@endsection