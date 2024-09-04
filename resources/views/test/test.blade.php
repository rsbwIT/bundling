@extends('..layout.layoutDashboard')
@section('title', 'Bayar Piutang')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    @livewire('test.test')
@endsection
@push('scripts')
    @livewireScripts
@endpush
