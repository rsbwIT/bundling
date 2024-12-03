@extends('..layout.layoutDashboard')
@section('title', 'Home BPJS')
@push('styles')
    @livewireStyles
@endpush

@section('konten')
    <div class="row">
        <div class="col-md-12">
            @livewire('bpjs.home-cari-casemix')
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
