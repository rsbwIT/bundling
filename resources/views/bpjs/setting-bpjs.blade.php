@extends('..layout.layoutDashboard')
@section('title', 'List File Bundling')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
            @livewire('bpjs.setting-bpjs')
@endsection
@push('scripts')
    @livewireScripts
@endpush
