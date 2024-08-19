@extends('..layout.layoutDashboard')
@section('title', 'Informasi Kamar')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            @livewire('info-kamar.setting-kamar')
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
