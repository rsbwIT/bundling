@extends('layout.layoutDashboard')
@section('title', 'Input SEP Biometrik Rajal')

@section('konten')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- ✅ Card untuk Input SEP --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-fingerprint me-2"></i> Input SEP Biometrik Rajal</h5>
                </div>
                <div class="card-body">

                    {{-- ✅ Pesan sukses --}}
                    @if(session('successList'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach(session('successList') as $msg)
                                    <li>{!! $msg !!}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- ✅ Pesan gagal --}}
                    @if(session('failedList'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach(session('failedList') as $msg)
                                    <li>{!! $msg !!}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- ✅ Form input SEP --}}
                    <form action="{{ route('biometrik.rajal.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="no_sep" class="form-label">Masukkan Nomor SEP</label>
                            <textarea
                                name="no_sep"
                                id="no_sep"
                                rows="6"
                                class="form-control @error('no_sep') is-invalid @enderror"
                                placeholder="Contoh:
            0801R0020000V000001
            0801R0020000V000002
            0801R0020000V000003"
                                required>{{ old('no_sep') }}</textarea>

                            @error('no_sep')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">Pisahkan dengan Enter atau koma jika lebih dari satu SEP.</small>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i> Auto Create Surat
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
