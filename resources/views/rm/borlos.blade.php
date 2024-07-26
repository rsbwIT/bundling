@extends('..layout.layoutDashboard')
@section('title', 'Laporan Pasien')
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
                                aria-controls="tab_Bor" aria-selected="true">BOR</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="Los" data-toggle="pill" href="#tab_Los" role="tab"
                                aria-controls="tab_Los" aria-selected="false">LOS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="Toi" data-toggle="pill" href="#tab_toi" role="tab"
                                aria-controls="tab_toi" aria-selected="false">TOI</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="Bto" data-toggle="pill" href="#tab_bto" role="tab"
                                aria-controls="tab_bto" aria-selected="false">BTO</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="Ndr" data-toggle="pill" href="#tab_ndr" role="tab"
                                aria-controls="tab_ndr" aria-selected="false">NDR</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="Gdr" data-toggle="pill" href="#tab_gdr" role="tab"
                                aria-controls="tab_gdr" aria-selected="false">GDR</a>
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
                    <div class="tab-pane fade" id="tab_bto" role="tabpanel" aria-labelledby="Bto">
                        @livewire('r-m.bto')
                    </div>
                    <div class="tab-pane fade" id="tab_ndr" role="tabpanel" aria-labelledby="Ndr">
                        @livewire('r-m.ndr')
                    </div>
                    <div class="tab-pane fade" id="tab_gdr" role="tabpanel" aria-labelledby="Gdr">
                        @livewire('r-m.gdr')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @livewireScripts
@endpush
