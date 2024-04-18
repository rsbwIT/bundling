@extends('layout.layoutpendaftaran')
@section('title', 'PENDAFTARAN 1')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="d-flex justify-content-between align-items-center container-fluid mt-3">
        <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                    alt="Girl in a jacket" width="120" height="120">
        <div class="pricing-header ">
            <h1 class="display-4 font-weight-bold">Antrian Poli</h1>
        </div>
        <img src="/img/bpjs.png" width="280px" height="50px" alt="" srcset="">
    </div>
    <hr>
    @livewire('antrian-poli.displaypoli')

@endsection
@push('scripts')
    @livewireScripts
@endpush
