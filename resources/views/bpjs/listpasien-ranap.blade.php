@extends('..layout.layoutDashboard')
@section('title', 'Pasein Rawat Inap')

@section('konten')
    <div class="row">
        <div class="col-md-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="nav-icon fas fa-receipt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Total List Pasien</b></span>
                    <span class="info-box-number">
                        <h4>{{ $daftarPasien->count() }}</h4>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Total Yang Sudah Terbundling</b></span>
                    <span class="info-box-number">
                        <h4>
                            @php
                                $sudahBundling = $daftarPasien
                                    ->filter(function ($item) {
                                        return !is_null($item->file);
                                    })
                                    ->count();
                            @endphp
                            {{ $sudahBundling }}
                        </h4>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-pen-nib"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"><b>Total Yang Belum Terbundling</b></span>
                    <span class="info-box-number">
                        <h4>
                            {{ abs($sudahBundling - $daftarPasien->count()) }}
                        </h4>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-12 card">
            <label for="" class="label mt-2">Cari berdasarkan tanggal pulang</label>
            <form action="{{ url('/cari-list-pasein-ranap') }}" action="POST">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="date" name="tgl1" class="form-control form-control-xs"
                                    value="{{ request('tgl1', now()->format('Y-m-d')) }}">
                                <div class="input-group-append">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <input type="date" name="tgl2" class="form-control form-control-xs"
                                    value="{{ request('tgl2', now()->format('Y-m-d')) }}">
                                <div class="input-group-append">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="input-group input-group-xs">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-md btn-primary">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">List Pasien Berdasarkan <b>Tanggal Pulang</b>, Dari
                        Tanggal:
                        <b>{{ date('d/m/Y', strtotime($tanggl1)) }}</b>
                        sampai <b>{{ date('d/m/Y', strtotime($tanggl2)) }} Rawat Inap</b>
                    </h3>
                </div>
                <table class="card-body table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>RM</th>
                            <th>No.Rawat</th>
                            <th>No.Sep</th>
                            <th>Pasein</th>
                            <th>Bayar</th>
                            <th>Asal</th>
                            <th>Tgl Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarPasien as $key => $item)
                            @php
                                $colortr = $item->jnspelayanan == '1' ? '' : 'text-danger';
                            @endphp
                            <tr class="{{ $colortr }} color-palette">
                                <td class="text-center">
                                    @if ($item->file)
                                        <a href="{{ url('hasil_pdf/' . $item->file) }}" download class="text-success">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @else
                                        <form action="{{ url('casemix-home-cari') }}" method="">
                                            @csrf
                                            <input name="cariNorawat" value="{{ $item->no_sep }}" hidden>
                                            <input name="cariNorawat" value="{{ $item->no_rawat }}" hidden>
                                            <button type="submit" style="background: none; border: none;">
                                                <i class="nav-icon fas fa-receipt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td>{{ $item->no_rkm_medis }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td>{{ $item->no_sep }}</td>
                                <td>{{ $item->nm_pasien }}</td>
                                <td class="text-center">
                                    @if ($item->status_bayar === 'Sudah Bayar')
                                        <a href="#" class="" data-toggle="tooltip" data-placement="top"
                                            title="Sudah Bayar" style="color: inherit;">
                                            <i class="text-success nav-icon fas fa-check"></i>
                                        </a>
                                    @else
                                        <a class="" href="#" data-toggle="tooltip" data-placement="top"
                                            title="Belum Bayar" style="color: inherit;">
                                            <i class="nav-icon fas fa-dollar-sign"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $item->nm_poli }}</td>
                                <td>{{ date('d/m/Y', strtotime($item->tgl_masuk)) }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
