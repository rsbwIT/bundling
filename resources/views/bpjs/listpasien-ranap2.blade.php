@extends('..layout.layoutDashboard')
@section('title', 'List Pasien Ranap 2')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            @livewire('bpjs.lispasien-ranap2')
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
