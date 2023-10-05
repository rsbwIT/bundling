@extends('..layout.layoutDashboard')
@section('title', 'Pasein Rawat Jalan')

@section('konten')
    <div class="row">
        <div class="col-md-4 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="nav-icon fas fa-receipt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Total List Pasien</b></span>
                    <span class="info-box-number">
                        <h4>{{ $jmldaftarPasien }}</h4>
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
                        <h4>{{ $jmldownloadBerkas }}</h4>
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
                        <h4>{{ $jmldaftarPasien - $jmldownloadBerkas }}</h4>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <form action="{{ url('/cari-list-pasein-ralan') }}" action="POST">
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
                    @php
                        $penjab = $penjamnin === 'BPJ' ? 'BPJS' : '';
                    @endphp
                    <h3 class="card-title">List Pasien <b>{{ $penjab }}</b>, Dari Tanggal: <b>{{ $tanggl1 }}</b>
                        sampai <b>{{ $tanggl2 }} Rawat Jalan</b></h3>
                </div>
                <table class="card-body table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>RM</th>
                            <th>No.Rawat</th>
                            <th>No.Sep</th>
                            <th>Pasein | Terkirim E-Klaim</th>
                            <th>Bayar</th>
                            <th>Tgl.Sep</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarPasien as $item)
                            <tr>
                                @php
                                    $matchingBerksDownload = $downloadBerkas->where('no_rawat', $item->no_rawat);
                                    $matchingBerksInacbg = $cekTerkirimInacbg->where('no_sep', $item->no_sep);
                                @endphp
                                <td class="text-center">
                                    @if ($matchingBerksDownload->isNotEmpty())
                                        @foreach ($matchingBerksDownload as $berkas)
                                            <a href="{{ url('hasil_pdf/' . $berkas->file) }}" download class="text-success">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endforeach
                                    @else
                                        <form action="{{ url('casemix-home-cari') }}" method="">
                                            @csrf
                                            <input name="cariNorawat" value="{{ $item->no_sep }}" hidden>
                                            <input name="cariNorawat" value="{{ $item->no_rawat }}" hidden>
                                            <button class="" style="background: none; border: none;">
                                                <i class="nav-icon fas fa-receipt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td>{{ $item->no_rkm_medis }}</td>
                                <td>{{ $item->no_rawat }}</td>
                                <td>{{ $item->no_sep }}</td>
                                <td>
                                    {{ $item->nm_pasien }} &nbsp;
                                    @if ($matchingBerksInacbg->isNotEmpty())
                                        <span class="text-success" data-toggle="tooltip" data-placement="top"
                                        title="Terikirm E-Klaim" style="cursor: pointer;"><i class="fas fa-check"></i></span>
                                    @else
                                        <span class="text-danger" data-toggle="tooltip" data-placement="top"
                                        title="Belum Terkirim E-Klaim" style="cursor: pointer;"><i class="fas fa-pen-nib"></i></span>
                                    @endif
                                </td>
                                <td>{{ $item->status_bayar }}</td>
                                <td>{{ $item->tglsep }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
