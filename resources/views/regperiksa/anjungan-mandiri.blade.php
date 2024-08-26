@extends('..layout.layoutAnjungan')
@section('title', 'Pasien')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    @livewire('regperiksa.anjungan-mandiri')
@endsection
@push('scripts')
    @livewireScripts
@endpush
