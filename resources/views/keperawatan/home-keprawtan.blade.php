@extends('..layout.layoutDashboard')
@section('title', 'Menu Keperawatan')
@section('konten')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Menu</h3>
                </div>
                <div class="card-body p-3">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>1</h3>
                                        <h5>Input Kegiatan Supervisi Keperawatan</h5>
                                    </div>
                                    <div class="icon">
                                        <i class="nav-icon fas fa-user-nurse"></i>
                                    </div>
                                    <a href="{{ url('logbook-keperawatan') }}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>2</h3>
                                        <h5>Input Kegiatan Lain Keperawatan</h5>
                                    </div>
                                    <div class="icon">
                                        <i class="nav-icon fas fa-file-medical"></i>
                                    </div>
                                    <a href="{{url('input-kegiatan-keperawatan-lain')}}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>3</h3>
                                        <h5>Input Kegiatan Kepala Ruangan Keperawatan</h5>
                                    </div>
                                    <div class="icon">
                                        <i class="nav-icon fas fa-file-medical"></i>
                                    </div>
                                    <a href="{{url('input-kegiatan-karu')}}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>4</h3>
                                        <h5>Laporan Kegiatan Supervisi Keperawatan</h5>
                                    </div>
                                    <div class="icon">
                                        <i class="nav-icon fas fa-receipt"></i>
                                    </div>
                                    <a href="{{url('laporan-logbook-keperawatan')}}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>5</h3>
                                        <h5>Jumlah Kegiatan Supervisi Perawat</h5>
                                    </div>
                                    <div class="icon">
                                        <i class="nav-icon fas fa-receipt"></i>
                                    </div>
                                    <a href="{{url('laporan-logbook-keperawatan2')}}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>6</h3>
                                        <h5>Jumlah Kegiatan Kepala Ruangan</h5>
                                    </div>
                                    <div class="icon">
                                        <i class="nav-icon fas fa-receipt"></i>
                                    </div>
                                    <a href="{{url('laporan-kegiatan-karu')}}" class="small-box-footer">More info <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
