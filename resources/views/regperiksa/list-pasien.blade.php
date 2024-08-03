@extends('..layout.layoutDashboard')
@section('title', 'Pasien')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            @livewire('regperiksa.listpasien')
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
