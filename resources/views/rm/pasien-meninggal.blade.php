@extends('..layout.layoutDashboard')
@section('title', 'Laporan Pasien Meninggal')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div>
 {{$getPasien}}
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
