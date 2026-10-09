@extends('..layout.layoutDashboard')
@section('title', 'Pantau Pasien UGD')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            @livewire('pantau-ugd')
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
