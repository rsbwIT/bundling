@extends('..layout.layoutDashboard')
@section('title', 'BOR LOS etc')
@push('styles')
    @livewireStyles
@endpush
@section('konten')
    <div class="row">
        <div class="col-md-12">
            <div class="card p-0 card-primary card-tabs" style="height: 600px;"">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="tabCetak" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="Bor" data-toggle="pill" href="#tab_Bor" role="tab"
                                aria-controls="tab_Bor" aria-selected="true">Bor</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="Los" data-toggle="pill" href="#tab_Los" role="tab"
                                aria-controls="tab_Los" aria-selected="false">Los</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="Toi" data-toggle="pill" href="#tab_toi" role="tab"
                                aria-controls="tab_toi" aria-selected="false">Toi</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content table-responsive" id="tabCetakContent">
                    <div class="tab-pane fade show active" id="tab_Bor" role="tabpanel" aria-labelledby="Bor">
                        @livewire('r-m.bor')
                    </div>
                    <div class="tab-pane fade" id="tab_Los" role="tabpanel" aria-labelledby="Los">
                        @livewire('r-m.los')
                    </div>
                    <div class="tab-pane fade" id="tab_toi" role="tabpanel" aria-labelledby="Toi">
                        @livewire('r-m.toi')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
