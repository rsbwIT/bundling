@extends('..layout.layoutDashboard')
@section('title', 'Validasi Icare')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            @livewire('briging-bpjs.icare')
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
