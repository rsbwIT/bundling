@extends('..layout.layoutDashboard')
@section('title', 'BOR LOS etc')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            @livewire('r-m.bor')
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
