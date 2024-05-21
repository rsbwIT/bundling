@extends('..layout.layoutDashboard')
@section('title', 'Informasi Jadwal Dokter HFIS')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            @livewire('briging-bpjs.update-jadwal-dokter')
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
