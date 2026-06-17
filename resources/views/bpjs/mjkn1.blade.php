@extends('layout.layoutDashboard')

@section('konten')

<div class="container">

    <div class="card">

        <div class="card-header">
            Ambil Antrean MJKN
        </div>

        <div class="card-body">

            {{-- ERROR --}}
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- RESPONSE DARI API --}}
            @if(isset($hasil['response']))
                <div class="alert alert-success">
                    Nomor Antrean : {{ $hasil['response']['noantrean'] ?? '-' }} <br>
                    Kode Booking : {{ $hasil['response']['kodebooking'] ?? '-' }} <br>
                    Status : {{ $hasil['metadata']['message'] ?? '-' }}
                </div>
            @endif

            <form method="POST" action="{{ route('mjkn.ambil-antrian') }}">
                @csrf

                <div class="row">

                    <div class="col-md-6">
                        <label>No Kartu</label>
                        <input type="text" name="nomorkartu" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>No HP</label>
                        <input type="text" name="nohp" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>No RM</label>
                        <input type="text" name="norm" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Kode Poli</label>
                        <input type="text" name="kodepoli" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Kode Dokter</label>
                        <input type="text" name="kodedokter" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Tanggal Periksa</label>
                        <input type="date" name="tanggalperiksa" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Jam Praktek</label>
                        <input type="text" name="jampraktek" class="form-control" required>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Jenis Kunjungan</label>
                        <select name="jeniskunjungan" class="form-control">
                            <option value="1">Rujukan FKTP</option>
                            <option value="2">Rujukan Internal</option>
                            <option value="3">Kontrol</option>
                            <option value="4">Rujukan Antar RS</option>
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Nomor Referensi</label>
                        <input type="text" name="nomorreferensi" class="form-control" required>
                    </div>

                </div>

                <button class="btn btn-primary mt-4">
                    Ambil Antrean
                </button>

            </form>

        </div>
    </div>

</div>

@endsection