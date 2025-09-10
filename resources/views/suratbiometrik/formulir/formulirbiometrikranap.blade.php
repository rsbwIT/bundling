@extends('layout.layoutDashboard')

@section('title', 'Formulir Surat Biometrik Ranap')

@push('styles')
<style>
    .card { border-radius: 12px; }
    .table-info th { width: 180px; background: #f8f9fa; }
</style>
@endpush

@section('konten')
<div class="card shadow-sm">
    <div class="card-body">

        {{-- 🔍 Form Cari Pasien --}}
        <form action="{{ route('formulir.biometrik.ranap.create') }}" method="GET" class="mb-4">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="no_peserta" class="form-control"
                           placeholder="Masukkan No Kartu BPJS"
                           value="{{ request('no_peserta') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Cari Pasien</button>
                </div>
            </div>
        </form>

        {{-- 📋 Jika data pasien ditemukan --}}
        @if($pasien)
            <form action="{{ route('formulir.biometrik.ranap.store') }}" method="POST">
                @csrf
                {{-- hidden field pasien --}}
                <input type="hidden" name="no_rawat" value="{{ $pasien->no_rawat }}">
                <input type="hidden" name="nm_pasien" value="{{ $pasien->nm_pasien }}">
                <input type="hidden" name="no_peserta" value="{{ $pasien->no_peserta }}">
                <input type="hidden" name="no_sep" value="{{ $pasien->no_sep }}">
                <input type="hidden" name="ruang_rawat" value="{{ $pasien->ruang_rawat }}">
                <input type="hidden" name="diagnosis" value="{{ $pasien->diagnosis }}">
                <input type="hidden" name="tgl_masuk" value="{{ $pasien->tgl_masuk }}">

                <div class="alert alert-info">
                    <strong>Nomor Surat (Preview):</strong> {{ $nomorSurat }}
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-info">
                        <tr><th>Nama Pasien</th><td>{{ $pasien->nm_pasien }}</td></tr>
                        <tr><th>No Kartu BPJS</th><td>{{ $pasien->no_peserta }}</td></tr>
                        <tr><th>No SEP</th><td>{{ $pasien->no_sep }}</td></tr>
                        <tr><th>Ruang Rawat</th><td>{{ $pasien->ruang_rawat }}</td></tr>
                        <tr><th>Diagnosis</th><td>{{ $pasien->diagnosis }}</td></tr>
                        <tr>
                            <th>Tanggal Masuk</th>
                            <td>{{ \Carbon\Carbon::parse($pasien->tgl_masuk)->format('d-m-Y') }}</td>
                        </tr>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan Surat
                    </button>
                </div>

            </form>
        @elseif(request('no_peserta'))
            <div class="alert alert-warning">
                Data pasien dengan No Kartu <strong>{{ request('no_peserta') }}</strong> tidak ditemukan.
            </div>
        @endif

    </div>
</div>
@endsection
