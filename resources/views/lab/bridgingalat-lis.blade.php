@extends('..layout.layoutDashboard')
@section('title', 'LIS Lab')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            <div class="card p-0 card-primary card-tabs" style="height: 550px;"">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="TabLab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                                href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                aria-selected="true">Pasien</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                                href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile"
                                aria-selected="false">Peserta</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content table-responsive" id="TabLab">
                    <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                        aria-labelledby="custom-tabs-one-home-tab">
                        @livewire('lab.bridgingalatlat-lis')
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel"
                        aria-labelledby="custom-tabs-one-profile-tab">
                        p
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
