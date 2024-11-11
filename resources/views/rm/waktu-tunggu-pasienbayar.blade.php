@extends('..layout.layoutDashboard')
@section('title', 'Waktu Tunggu Pasien Bayar Umum')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @livewire('r-m.waktu-tunggu-pasienbayar')
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
