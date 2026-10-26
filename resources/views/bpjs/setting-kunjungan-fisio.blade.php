@extends('..layout.layoutDashboard')
@section('title', 'Setting Kunjungan Fisio')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    @livewire('bpjs.setting-kunjungan-fisio')
@endsection
@push('scripts')
    @livewireScripts
@endpush
