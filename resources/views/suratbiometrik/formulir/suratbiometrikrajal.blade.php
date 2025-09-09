@extends('layout.layoutDashboard')

@section('title', 'Surat Biometrik Rajal')

@push('styles')
<style>
    .surat-container {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .kop-surat {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }
    .kop-surat h5, .kop-surat h6 {
        margin: 0;
    }
    .table-surat th {
        width: 200px;
    }
</style>
@endpush

@section('konten')
<div class="surat-container">
    <div class="kop-surat">
        <h5>RUMAH SAKIT BHAKTI WARAS</h5>
        <h6>Jl. Contoh Alamat No. 123, Kota</h6>
        <hr>
        <h5><u>SURAT BIOMETRIK RAJAL</u></h5>
        <p>No: {{ $nomor_surat }}</p>
    </div>

    <p>Yang bertanda tangan di bawah ini menerangkan bahwa pasien berikut:</p>

    <table class="table table-borderless table-surat">
        <tr><th>Nama Pasien</th><td>: {{ $nm_pasien }}</td></tr>
        <tr><th>No Kartu BPJS</th><td>: {{ $no_peserta }}</td></tr>
        <tr><th>No SEP</th><td>: {{ $no_sep }}</td></tr>
        <tr><th>Poli Tujuan</th><td>: {{ $nm_poli }}</td></tr>
        <tr><th>Diagnosis</th><td>: {{ $diagnosis }}</td></tr>
        <tr><th>Tanggal Registrasi</th><td>: {{ \Carbon\Carbon::parse($tgl_registrasi)->format('d-m-Y') }}</td></tr>
    </table>

    <p>Demikian surat biometrik ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

    <div class="text-end mt-5">
        <p>{{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        <p><strong>Dokter Penanggung Jawab</strong></p>
        <br><br>
        <p>________________________</p>
    </div>

    <div class="text-center mt-4">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fa fa-print"></i> Cetak Surat
        </button>
        <a href="{{ route('formulir.biometrik.rajal.create') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection
