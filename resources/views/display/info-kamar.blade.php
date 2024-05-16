@extends('..layout.layoutpendaftaran')
@section('title', 'Pendaftaran')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
<div class="d-flex justify-content-between align-items-center container-fluid mt-3">
    <img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                alt="Girl in a jacket" width="120" height="120">
    <div class="pricing-header ">
        <h1 class="display-4 font-weight-bold">Informasi Kamar</h1>
    </div>
    <div class="pricing-header ">
    </div>
</div>
<hr style="border: 1px solid">
@livewire('info-kamar.info-kamar')
@endsection
@push('scripts')
    @livewireScripts
@endpush
