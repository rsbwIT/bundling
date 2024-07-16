@extends('..layout.layoutDashboard')
@section('title', 'Piutang obat & alkes')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            @livewire('farmasi.bundling-resepobat2')
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
