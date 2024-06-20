@extends('..layout.layoutDashboard')
@section('title', 'Cross Check Coding')
@push('styles')
    @livewireStyles
@endpush

@section('konten')
    @livewire('bpjs.crosscheck-coding')
@endsection
@push('scripts')
    @livewireScripts
@endpush
